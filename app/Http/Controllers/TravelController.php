<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Travels"
 * )
 */

class TravelController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/travels",
     *     summary="Get a list of travels",
     *     description="Retrieve a list of travels, optionally filtered by status.",
     *     tags={"Travels"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter travels by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"requested", "approved", "canceled"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of travels",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Travel")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/travels",
     *     summary="Create a new travel request",
     *     description="Create a new travel request for the authenticated user.",
     *     tags={"Travels"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"applicant_name", "destiny", "departure_date", "return_date", "status"},
     *             @OA\Property(property="applicant_name", type="string", example="Márcia"),
     *             @OA\Property(property="destiny", type="string", example="Paris"),
     *             @OA\Property(property="departure_date", type="string", format="date", example="2024-11-15"),
     *             @OA\Property(property="return_date", type="string", format="date", example="2024-11-20"),
     *             @OA\Property(property="status", type="string", enum={"requested", "approved", "canceled"}, example="requested")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Travel request created successfully.",
     *         @OA\JsonContent(ref="#/components/schemas/Travel")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="There is a trip for this person on the same date."
     *     )
     * )
     */
    
    public function store(Request $request)
    {
        $fields = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'destiny' => 'required|string|max:255',
            'departure_date'  => 'required|date',
            'return_date' => 'required|date|after:departure_date',
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
     * @OA\Get(
     *     path="/api/travels/{travel}",
     *     summary="Get a specific travel",
     *     description="Retrieve details of a specific travel request.",
     *     tags={"Travels"},
     *     @OA\Parameter(
     *         name="travel",
     *         in="path",
     *         description="ID of the travel request",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the travel request",
     *         @OA\JsonContent(ref="#/components/schemas/Travel")
     *     )
     * )
     */
    
    public function show(Travel $travel)
    {
        return response()->json(['travel' => $travel], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/travels/{travel}",
     *     summary="Update a travel status",
     *     description="Update the status of an existing travel request.",
     *     tags={"Travels"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="travel",
     *         in="path",
     *         description="ID of the travel request",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"requested", "approved", "canceled"}, example="approved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Travel status updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"requested", "approved", "canceled"})
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/travels/{travel}",
     *     summary="Delete a travel request",
     *     description="Delete a specific travel request.",
     *     tags={"Travels"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="travel",
     *         in="path",
     *         description="ID of the travel request",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Travel request deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="the travel was deleted")
     *         )
     *     )
     * )
     */
    
    public function destroy(Travel $travel)
    {
        Gate::authorize('modify', $travel);

        $travel->delete();

        return response()->json([ 'message' => 'the travel was deleted' ], 200);
    }
}
