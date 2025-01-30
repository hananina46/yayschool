<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatController extends Controller
{
    public function handleHuggingFace(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Data API
        $apiKey = env('HF_API_KEY'); // Simpan API Key Hugging Face di .env
        $url = 'https://api-inference.huggingface.co/models/meta-llama/Llama-3.3-70B-Instruct/v1/chat/completions';

        // Default Parameter
        $payload = [
            "model" => "meta-llama/Llama-3.3-70B-Instruct",
            "messages" => [
                ["role" => "user", "content" => $validated['message']],
            ],
            "temperature" => 0.5,
            "max_tokens" => 2048,
            "top_p" => 0.7,
            "stream" => false,
        ];

        return $this->sendRequest($url, $apiKey, $payload);
    }

    public function handleTogether(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Data API
        $apiKey = env('TOGETHER_API_KEY'); // Simpan API Key Together di .env
        $url = 'https://api.together.xyz/v1/chat/completions';

        // Default Parameter
        $payload = [
            "model" => "meta-llama/Llama-3.3-70B-Instruct-Turbo-Free",
            "messages" => [
                ["role" => "user", "content" => $validated['message']],
            ],
            "temperature" => 0.5,
            "max_tokens" => 2048,
            "top_p" => 0.7,
            "top_k" => 50,
            "repetition_penalty" => 1,
            "stop" => ["<|eot_id|>", "<|eom_id|>"],
            "stream" => false,
        ];

        return $this->sendRequest($url, $apiKey, $payload);
    }

    private function sendRequest($url, $apiKey, $payload)
    {
        // Inisialisasi Guzzle Client
        $client = new Client();

        try {
            // Kirim request ke API
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            // Parsing response
            $result = json_decode($response->getBody(), true);

            return response()->json($result);

        } catch (\Exception $e) {
            // Error handling
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
