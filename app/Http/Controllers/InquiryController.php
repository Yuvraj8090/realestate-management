<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreInquiryRequest;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Property;
use App\Notifications\InquiryReceivedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request, Property $property): RedirectResponse|JsonResponse
    {
        $recipient = $property->user;

        $inquiry = Inquiry::create([
            'property_id' => $property->id,
            'recipient_user_id' => $recipient->id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
            'preferred_contact_method' => $request->input('preferred_contact_method'),
            'submitted_at' => now(),
        ]);

        Lead::create([
            'property_id' => $property->id,
            'inquiry_id' => $inquiry->id,
            'broker_user_id' => $recipient->isRole(UserRole::Broker) ? $recipient->id : null,
            'assigned_to_user_id' => $recipient->id,
            'status' => LeadStatus::New->value,
        ]);

        $recipient->notify(new InquiryReceivedNotification($inquiry->load('property')));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Inquiry submitted successfully.',
                'inquiry_id' => $inquiry->id,
            ], 201);
        }

        return back()->with('status', 'Your inquiry was sent successfully.');
    }
}
