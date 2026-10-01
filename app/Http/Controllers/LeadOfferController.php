<?php
// app/Http/Controllers/LeadOfferController.php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadOfferController extends Controller
{
    public function index(Lead $lead)
    {
        try {
            $offers = LeadOffer::where('lead_id', $lead->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $offers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch offers'
            ], 500);
        }
    }

    public function store(Request $request, Lead $lead)
    {
        try {
            $request->validate([
                'unit_id' => 'required|exists:units,id',
                'offered_amount' => 'required|numeric|min:0',
                'status' => 'required|in:pending,accepted,rejected,closed',
                'offer_type' => 'nullable|in:accepted,rejected',
                'reason' => 'nullable|string|max:1000'
            ]);

            $offer = LeadOffer::create([
                'lead_id' => $lead->id,
                'unit_id' => $request->unit_id,
                'offered_amount' => $request->offered_amount,
                'status' => $request->status,
                'offer_type' => $request->offer_type,
                'reason' => $request->reason,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Offer created successfully',
                'data' => $offer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create offer'
            ], 500);
        }
    }
}
