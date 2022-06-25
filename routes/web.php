<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/data', function () {
    return datatables(\App\Models\Log::all())->toJson();
});

Route::middleware(['auth:sanctum', 'verified'])->get('/data/{code}', function () {
    $page = '<html><body></body><script>';
    $page .= 'function output(inp) {';
    $page .= 'document.body.appendChild(document.createElement("pre")).innerHTML = inp;';
    $page .= '}';
    $page .= 'var obj = '.\App\Models\Log::where('code', request()->code)->first()->toJson().';';
    $page .= 'var str = JSON.stringify(obj, undefined, 4);';
    $page .= 'output(str);';
    $page .= '</script></html>';
    return $page;
});

Route::post('/data', function () {

    // PROD ENV LOGS TWO TYPES OF ERRORS... 404 AND GENERIC ERRORS
    // THAN SENDS EMAILS WITH TWO DIFFERENT TRIGGERS (3 TIMES FOR 404, FIRST TIMES FOR GENERIC ERRORS)
    // LOGS RETANTION IS SET TO 90 DAYS
    if(request()->header('apikey') == config('app.apikeyprod')) {

        // 404 / ERROR SWICH TO POPULATE DATABASE DIFFERENTLY
        switch (request()->header('type')) {
            case 'not-found':
                $code = '404-'.uniqid();
                $error = 'File not found.';
                $file = request()->input('file').' - '.request()->input('url');
                $errorCheck = sha1(request()->input('url').date('YmdH'));
                break;

            default:
                $code = 'prod-'.uniqid();
                $error = request()->input('error');
                $file = request()->input('file');
                $errorCheck = sha1(request()->input('error').request()->input('file').request()->input('line').date('YmdH'));
                break;
        }

        // LOG CREATION
        \App\Models\Log::create([
            'code' => $code,
            'datetime' => date('Y-m-d H:i:s'),
            'error' => $error,
            'file' => substr($file, -255),
            'line' => request()->input('line'),
            'url' => request()->input('url'),
            'method' => request()->input('method'),
            'request' => json_encode(request()->input('request')),
            'header' => json_encode(request()->input('header')),
            'ip' => request()->input('ip'),
            'user_agent' => request()->input('user_agent'),
            'user_id' => request()->input('user_id'),
            'error_check' => $errorCheck,
        ]);

        // COUNT HOW MANY SIMILAR ERRORS OCCOURED IN LAST HOUR (THANKS TO date('YmdH') in $errorCheck)
        $count = \App\Models\Log::where('error_check', $errorCheck)->count();


        // 404 / ERROR SWICH WITH TWO DIFFERENT MAIL TRIGGERS AND CONTENT
        switch (request()->header('type')) {
            case 'not-found':
                if($count == 3) {
                    $details = [
                        'error' => $code,
                        'title' => 'Error 404 @ '.request()->input('url'),
                        'body' => 'This URL has not been found at least three times in the last hour!',
                    ];
                    Mail::to(\App\Models\User::first()->email)->send(new \App\Mail\ErrorMail($details));
                }
                break;

            default:
                if($count < 2) {
                    $details = [
                        'error' => $code,
                        'title' => 'Exception @ '.request()->input('url'),
                        'body' => request()->input('error').' in ...'.substr(request()->input('file'), -16).':'.request()->input('line')
                    ];
                    Mail::to(\App\Models\User::first()->email)->send(new \App\Mail\ErrorMail($details));
                }
                break;
        }

        // LOG RETENTION
        $date = new DateTime;
        $date->modify('-90 days');
        $formatted = $date->format('Y-m-d H:i:s');
        \App\Models\Log::where('datetime','<=', $formatted)->delete();

        return $code;
    }

    // DEV ENV DOES NOT NEED LOG 404 ERRORS, JUST GENERIC ERRORS AND DOES NOT SEND EMAILS
    // LOGS RETANTION IS SET TO 3 DAYS
    if(request()->header('apikey') == config('app.apikeydev') && request()->header('type') == 'error') {

        // ERROR CODE
        $code = 'dev-'.uniqid();

        // LOG CREATION
        \App\Models\Log::create([
            'code' => $code,
            'datetime' => date('Y-m-d H:i:s'),
            'error' => request()->input('error'),
            'file' => substr(request()->input('file'), -255),
            'line' => request()->input('line'),
            'url' => request()->input('url'),
            'method' => request()->input('method'),
            'request' => json_encode(request()->input('request')),
            'header' => json_encode(request()->input('header')),
            'ip' => request()->input('ip'),
            'user_agent' => request()->input('user_agent'),
            'user_id' => request()->input('user_id'),
            'error_check' => sha1($code),
        ]);

        // LOG RETENTION
        $date = new DateTime;
        $date->modify('-3 days');
        $formatted = $date->format('Y-m-d H:i:s');
        \App\Models\Log::where('datetime','<=', $formatted)->delete();

        return $code;
    }

    abort(403);
});
