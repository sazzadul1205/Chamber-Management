<?php

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/patients', function (Request $request) {
    $search = $request->query('search', '');

    return Patient::where(function ($query) use ($search) {
        $query->where('full_name', 'like', "%{$search}%")
            ->orWhere('patient_code', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
    })
        ->select('id', 'full_name', 'patient_code', 'phone')
        ->limit(50)
        ->get();
});
