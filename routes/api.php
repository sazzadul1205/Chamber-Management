<?php

use App\Http\Controllers\OnlineBookingController;
use App\Models\Doctor;
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

Route::prefix('online-bookings')->group(function () {
    Route::get('/metadata', [OnlineBookingController::class, 'metadata']);
    Route::post('/', [OnlineBookingController::class, 'store']);
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



// If you want to include phone number in the dropdown display:
Route::get('/doctors', function (Request $request) {
    $search = $request->query('search', '');

    $query = Doctor::with(['user:id,full_name,phone']);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('specialization', 'like', "%{$search}%");
        });
    }

    $doctors = $query->limit(10)->get(['id', 'specialization', 'user_id']);

    return $doctors->map(function ($doctor) {
        return [
            'id' => $doctor->id,
            'full_name' => $doctor->user->full_name ?? 'N/A',
            'specialization' => $doctor->specialization ?? 'General',
            'phone' => $doctor->user->phone ?? null,
        ];
    });
});
