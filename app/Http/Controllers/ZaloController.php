<?php

namespace App\Http\Controllers;

use App\Services\ZaloService;
use Illuminate\Http\Request;

class ZaloController extends Controller
{
    protected $zaloService;

    public function __construct(ZaloService $zaloService)
    {
        $this->zaloService = $zaloService;
    }

    public function sendMessage(Request $request)
    {
        $userId = $request->input('user_id');
        $message = $request->input('message');

        $response = $this->zaloService->sendMessage($userId, $message);

        return response()->json($response);
    }

    public function getProfile(Request $request)
    {
        $userId = $request->input('user_id');

        $response = $this->zaloService->getProfile($userId);

        return response()->json($response);
    }
}
