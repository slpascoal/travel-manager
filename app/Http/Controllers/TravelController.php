<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class TravelController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Travel::query();
    
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $travels = $query->get()->makeHidden(['created_at', 'updated_at']);

        return response()->json(['travels' => $travels], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'destiny' => 'required|string|max:255',
            'departure_date'  => 'required|date',
            'return_date' => 'required|date',
            'status' => 'nullable|in:requested,approved,canceled'
        ]);

        $duplicateTravel = Travel::where('applicant_name', $fields['applicant_name'])
        ->where('departure_date', $fields['departure_date'])
        ->exists();

        if ($duplicateTravel) {
            return response()->json([
                'message' => 'there is a trip for this person on the same date.'
            ], 422);
        }

        $travel = $request->user()->travels()->create($fields);

        return response()->json(['message' => 'travel was created successfully', 'travel' => $travel], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Travel $travel)
    {
        return response()->json(['travel' => $travel], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Travel $travel)
    {
        Gate::authorize('modify', $travel);

        $fields = $request->validate([
            'status' => 'required|in:requested,approved,canceled'
        ]);

        $travel->update(['status' => $fields['status']]);

        return response()->json(['message' => 'status updated successfully', 'status' => $travel->status], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Travel $travel)
    {
        Gate::authorize('modify', $travel);

        $travel->delete();

        return response()->json([ 'message' => 'the travel was deleted' ], 200);
    }
}
