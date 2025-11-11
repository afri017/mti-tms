<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Tampilkan halaman map tracking (Blade)
     */
    public function index()
    {
        return view('tracking.index', [
            'pageTitle' => 'Live Vehicle Tracking'
        ]);
    }

    /**
     * Ambil data tracking terkini dari API Dyegoo
     */
    public function getLatest()
    {
        $body = [
            "DeviceId" => 532960,
            "MapType" => "Google",
            "Token" => "997D707F7C2CF55E88237FBB40CE274617233283F8BDB9916DCF36848AF008D0B7170C5AEE6CFDDA",
            "Language" => "en",
            "TimeZone" => "China Standard Time",
            "AppId" => "MyApp"
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://api.dyegoo.net/api/Location/Tracking', $body);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'API error',
                'status' => $response->status()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        return view('tracking.history', [
            'pageTitle' => 'Tracking History'
        ]);
    }

    public function getHistory(Request $request)
    {
        $tz = 'Asia/Shanghai'; // +08:00
        $startInput = $request->input('start_time', null); // misal "2025-11-10T05:40"
        $endInput   = $request->input('end_time', null);   // misal "2025-11-10T08:19"

        // Buat Carbon dari input, tambahkan detik 01 jika tidak ada, set timezone Asia/Shanghai
        $start = $startInput
            ? Carbon::parse($startInput, $tz)->subHours(7)->second(1)->format('Y-m-d\TH:i:sP')
            : Carbon::now($tz)->subHours(6)->second(1)->format('Y-m-d\TH:i:sP');

        $end = $endInput
            ? Carbon::parse($endInput, $tz)->subHours(7)->second(46)->format('Y-m-d\TH:i:sP') // contoh: 46 detik
            : Carbon::now($tz)->second(46)->format('Y-m-d\TH:i:sP');

        $body = [
            "DeviceId" => 532960,
            "StartTime" => $start,
            "EndTime" => $end,
            "ShowLbs" => 1,
            "MapType" => "Google",
            "SelectCount" => 2000,
            "Token" => "997D707F7C2CF55E88237FBB40CE274617233283F8BDB9916DCF36848AF008D0B7170C5AEE6CFDDA",
            "Language" => "en",
            "AppId" => "MyApp"
        ];

        \Log::info('Body dikirim ke API:', $body);
        \Log::info('Input user lokal:', ['start' => $startInput, 'end' => $endInput]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://api.dyegoo.net/api/Location/History', $body);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'API error',
                'status' => $response->status(),
                'body' => $response->body()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
