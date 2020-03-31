<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use App\UserCategory;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = UserCategory::all();

        return response()->json($categories->toArray());
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
            'name' => 'required|string'
        ]);

        $category = UserCategory::create([
            'name' => $request->name
        ]);

        return response()->json($category->toArray());
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
        $category = UserCategory::find($id);

        if ( !$category ) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        return response()->json($category->toArray());
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
            'name' => 'required|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
        ]);

        $category = UserCategory::find($id);

        if ( !$category ) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        $category->update([
            'name' => $request->name
        ]);


        $category->users()->delete();

        if ($request->user_ids) {
            $users = User::whereIn('id', $request->user_ids)->get();
            $category->users()->saveMany($users);
        }

        return response()->json($category->toArray());
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
        $category = UserCategory::find($id);

        if ( !$category ) {
            return response()->json([
                'error' => 'error',
                'message' => 'Объект не найден. Возможно он был удалён.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => 'success'
        ]);
    }
}
