<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('login', function () {
    abort(402);
})->name('login'); // must have defualt name login.

Route::post('login', function () {
    $credentials = request()->only(['email', 'password']); // รับค่าเฉพาะ email กับ password

    if(!auth()->validate($credentials)) {
        abort(401);                         // Invalid logedin 
    } else {             // Login success
        $user = User::where('email', $credentials['email'])->first();
        $user->tokens()->delete();              // delete old token then => create new token
        $token = $user->createToken('postman', ['admin']);  // Create Token   // กำหนด['admin'] Authenticate (Abilities)       // 'postman' is name token  (sanctum token - long life token)
        return response()->json([
            'status' => 'OK',
            'token' => $token->plainTextToken // แนบ token ส่งไปที่ response ด้วย
        ]); 
    }
});

Route::group(['middleware' => 'auth:sanctum'], function () { //api sanctum route  
    Route::get('number', function () {
        $user = auth()->user();
        // dd($user);
        if($user->tokenCan('admin')) { // เมิ่อตรวจสอบ token ที่มีอยู่ใน database ที่่มีสิทธิ์เป็น admin
            return response()->json([1, 2, 3, 4, 5]);
        } else {
            abort(403);
        }

    });
});

