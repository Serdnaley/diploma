<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Report;
use App\User;
use App\UserFile;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $users = User::all();

        $reports = Report::with(['attachments', 'user'])->latest()->get();

        foreach ($users as &$user) {
            $report = $reports->firstWhere('user_id', $user->id);

            if ( $report ) {

            } else {
                $user->report = null;
            }

        }

        return response()->json($reports->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'type' => 'required|string',
            'attachment_ids' => 'required|array',
            'attachment_ids.*' => 'integer',
        ]);

        $report = Report::create($request->only([
            'user_id',
            'date',
            'type',
        ]));


        $users = UserFile::whereIn('id', $request->attachment_ids)->get();
        $report->attachments()->saveMany($users);

        $report = Report::with(['attachments', 'user'])->find($report->id);

        return response()->json($report);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $report = Report::with(['attachments', 'user'])->find($id);

        if (!$report) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        return response()->json($report);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'type' => 'required|string',
            'attachment_ids' => 'required|array',
            'attachment_ids.*' => 'integer',
        ]);

        $report = Report::with(['attachments'])->find($id);

        if (!$report) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        $report->update($request->only([
            'user_id',
            'date',
            'type',
        ]));

        // Удаляем старые файлы
        foreach ($report->attachments as $attachment) {
            if ( !in_array($attachment->id, $request->attachment_ids) ){
                $attachment->delete();
            }
        }

        // Прикреплаяем новые
        if ($request->attachment_ids) {
            $users = UserFile::whereIn('id', $request->attachment_ids)->get();
            $report->attachments()->saveMany($users);
        }

        $report->load(['attachments', 'user']);

        return response()->json($report);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {

        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        $report->attachments()->delete();
        $report->delete();

        return response()->json([
            'success' => 'success'
        ]);
    }
}
