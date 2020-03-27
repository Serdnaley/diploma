<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UserFile;
use Illuminate\Http\Request;

class UserFileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $files = UserFile::store_files($request);

        return response()->json([
            'status' => 'success',
            'data' => $files
        ], 200);
    }
}
