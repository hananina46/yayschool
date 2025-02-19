<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

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

    public function handleYay(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'messages' => 'required|array', // Menggunakan array untuk riwayat chat
    ]);

    // API Key dari .env
    $apiKey = env('TOGETHER_API_KEY');
    $url = 'https://api.together.xyz/v1/chat/completions';

    // Tambahkan pesan sistem di awal percakapan
    $systemMessage = [
        "role" => "system",
        "content" => "Anda adalah YaySchool AI yang selalu memberikan jawaban singkat, jelas, dan padat dan hanya menjawab seputar pendidikan dan administrasi sekolah. Pertanyaan diluar topik akan kamu balas maaf tidak bisa menjawab.",
    ];

    // Gabungkan pesan sistem dengan riwayat chat pengguna
    $messages = array_merge([$systemMessage], $validated['messages']);

    // Payload default dengan seluruh riwayat chat
    $payload = [
        "model" => "meta-llama/Llama-3.3-70B-Instruct-Turbo-Free",
        "messages" => $messages, // Gunakan semua chat history dari frontend dengan pesan sistem di awal
        "temperature" => 0.5,
        "max_tokens" => 2048,
        "top_p" => 0.7,
        "top_k" => 50,
        "repetition_penalty" => 1,
        "stop" => ["<|eot_id|>", "<|eom_id|>"],
        "stream" => false, // Stream selalu false
    ];

    return $this->sendRequest2($url, $apiKey, $payload);
}

private function sendRequest2($url, $apiKey, $payload)
{
    try {
        // Kirim request ke API menggunakan Laravel HTTP Client
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // Ambil bagian 'content' dari respons
        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? 'No content available';

        // Kembalikan hanya 'content'
        return response()->json(['content' => $content]);
    } catch (\Exception $e) {
        // Error handling
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
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
