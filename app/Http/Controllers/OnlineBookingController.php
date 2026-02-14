<?php

namespace App\Http\Controllers;

use App\Models\OnlineBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnlineBookingController extends Controller
{
    public function metadata(): JsonResponse
    {
        return response()->json([
            'services' => $this->services(),
            'time_slots' => $this->timeSlots(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'string', 'max:30', Rule::in($this->timeSlots())],
            'service' => ['required', 'string', 'max:120', Rule::in($this->services())],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking = OnlineBooking::create($validated + [
            'status' => 'pending',
            'source' => 'website',
        ]);

        return response()->json([
            'message' => 'Booking request submitted successfully.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'created_at' => $booking->created_at,
            ],
        ], 201);
    }

    private function services(): array
    {
        return [
            'General Checkup',
            'Teeth Cleaning',
            'Teeth Whitening',
            'Dental Implants',
            'Root Canal',
            'Braces/Invisalign',
            'Emergency Dental',
            'Other',
        ];
    }

    private function timeSlots(): array
    {
        return [
            '9:00 AM',
            '10:00 AM',
            '11:00 AM',
            '12:00 PM',
            '2:00 PM',
            '3:00 PM',
            '4:00 PM',
            '5:00 PM',
        ];
    }
}
