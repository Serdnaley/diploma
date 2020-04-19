<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notification;
use App\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TelegramAPI;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = Notification::all();

        return response()
            ->json([
                'data' => $notifications,
            ], 200);
    }

    /**
     * Send notifications
     *
     * @return \Illuminate\Http\Response
     */
    public function send()
    {
        $now = Carbon::now();

        $reports = Report::with(['user'])
            ->where('date', '>', $now->clone()->subMonths('18')) // last 1.5 years
            ->get();

        foreach ($reports as $report) {

            if (!$report->user->telegram_chat_id) {
                continue;
            }

            $notifications = Notification::whereReportId($report->id)->get();

            $report->user->load(['telegram_chat']);

            $date = $report->date->clone()->add('1 year');
            $formatted_date = $date->toFormattedDateString();

            if ($report->type === 'fluorography') {
                $intervals = [
                    '-1 month' => "Через месяц вам нужно будет пройти флюорографию.\nОсмотр запланирован на $formatted_date",
                    '-1 week' => "Через неделю вам нужно будет пройти флюорографию.\nОсмотр запланирован на $formatted_date",
                    '-3 days' => "Через 3 дня вам нужно будет пройти флюорографию.\nОсмотр запланирован на $formatted_date"
                ];
            } else {
                $intervals = [
                    '-1 month' => "Через месяц вам нужно будет пройти мед.комиссию.\nОсмотр запланирован на $formatted_date",
                    '-1 week' => "Через неделю вам нужно будет пройти мед.комиссию.\nОсмотр запланирован на $formatted_date",
                    '-3 days' => "Через 3 дня вам нужно будет пройти мед.комиссию.\nОсмотр запланирован на $formatted_date"
                ];
            }

            foreach ($intervals as $interval => $text) {
                $interval = $date->clone()->add($interval);

                if ($interval >= $now) {

                    $latest_notifications = $notifications->where('date', $interval);

                    if ($latest_notifications->isEmpty()) {

                        TelegramAPI::sendMessage([
                            'chat_id' => $report->user->telegram_chat->id,
                            'text' => $text,
                        ]);

                        $notification = Notification::create([
                            'date' => $interval,
                            'report_id' => $report->id,
                            'message' => $text,
                        ]);

                        $notification->save();

                        break;
                    }
                }
            }

        }

        return response()
            ->json([
                'status' => 'success'
            ], 200);
    }
}
