<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $model = 'models/gemini-flash-latest';
        $url = "https://generativelanguage.googleapis.com/v1beta/{$model}:generateContent?key={$apiKey}";

        $response = Http::post($url, [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $request->message]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.8,
                "topK" => 40,
                "topP" => 0.9,
                "maxOutputTokens" => 512
            ]
        ]);

        if ($response->failed()) {
            return response()->json([
                "error" => "Gemini API error",
                "details" => $response->json()
            ], 500);
        }

        $reply = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No reply';

        return response()->json([
            "reply" => $reply
        ]);
    }
}
