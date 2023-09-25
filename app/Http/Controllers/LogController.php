<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Models\Log;
use Illuminate\Http\JsonResponse;

class LogController extends Controller
{
    /**
     * Save new log in database.
     *
     * @param string $action
     * @param bool $isDone
     * @return Log
     */
    static public function addLog(string $action, bool $isDone): Log
    {
        return Log::create([
            'action' => $action,
            'isDone' => $isDone,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $logs = Log::orderBy('id', 'DESC')->limit(25)->get();

        return response()->json(LogResource::collection($logs), 200);
    }
}
