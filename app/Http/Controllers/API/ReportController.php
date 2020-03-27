<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $reports = Report::all();

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
            'attachments' => 'required|array',
            'attachments.*' => 'integer',
        ]);

        $report = Report::create($request->only([
            'user_id',
            'date',
            'type',
        ]));

        $report->attachments()->sync($request->attachments);

        return response()->json($report->toArray());
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
        $report = Report::find($id);

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
            'attachments' => 'required|array',
            'attachments.*' => 'integer',
        ]);

        $report = Report::find($id);

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

        $report->attachments()->sync($request->attachments);

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
