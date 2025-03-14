<?php

namespace App\Http\Controllers\Api;

use App\Models\Itineraire;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ItineraireResource;

class ItineraireController extends Controller
{
    public function index(Request $request)
    {
        $query = Itineraire::with('destinations');
        
        if ($request->has('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        
        if ($request->has('duree')) {
            $query->where('duree', $request->duree);
        }
        
        if ($request->has('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('populaires') && $request->populaires) {
            $query->withCount('favorisParUsers')
                  ->orderBy('favoris_par_users_count', 'desc');
        }
        
        $itineraires = $query->get();
        
        if ($itineraires->count() > 0) {
            return ItineraireResource::collection($itineraires);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'categorie' => 'required|string',
            'duree' => 'required|string',
            'image_path' => 'required|string|max:255',
            'destinations' => 'required|array|min:2',
            'destinations.*.nom' => 'required|string|max:255',
            'destinations.*.lieu_logement' => 'required|string|max:255',
            'destinations.*.endroits_a_visiter' => 'nullable|array',
            'destinations.*.activites' => 'nullable|array',
            'destinations.*.plats_a_essayer' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->messages(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $itineraire = Itineraire::create([
                'titre' => $request->titre,
                'categorie' => $request->categorie,
                'duree' => $request->duree,
                'image_path' => $request->image_path,
                'user_id' => auth()->id(), 
            ]);
            
            foreach ($request->destinations as $destinationData) {
                $itineraire->destinations()->create([
                    'nom' => $destinationData['nom'],
                    'lieu_logement' => $destinationData['lieu_logement'],
                    'endroits_a_visiter' => $destinationData['endroits_a_visiter'] ?? [],
                    'activites' => $destinationData['activites'] ?? [],
                    'plats_a_essayer' => $destinationData['plats_a_essayer'] ?? [],
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Itineraire Created Successfully',
                'data' => new ItineraireResource($itineraire->load('destinations'))
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while creating the itinerary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Itineraire $itineraire)
    {
        $itineraire->load('destinations');
        return new ItineraireResource($itineraire);
    }

    public function update(Request $request, Itineraire $itineraire)
    {
        if (auth()->id() !== $itineraire->user_id) {
            return response()->json([
                'message' => 'You are not authorized to update this itinerary'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'categorie' => 'required|string',
            'duree' => 'required|string',
            'image_path' => 'required|string|max:255',
            'destinations' => 'required|array|min:2',
            'destinations.*.id' => 'nullable|exists:destinations,id',
            'destinations.*.nom' => 'required|string|max:255',
            'destinations.*.lieu_logement' => 'required|string|max:255',
            'destinations.*.endroits_a_visiter' => 'nullable|array',
            'destinations.*.activites' => 'nullable|array',
            'destinations.*.plats_a_essayer' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->messages(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $itineraire->update([
                'titre' => $request->titre,
                'categorie' => $request->categorie,
                'duree' => $request->duree,
                'image_path' => $request->image_path,
            ]);
            
            $existingIds = collect($request->destinations)
                ->pluck('id')
                ->filter()
                ->toArray();
            
            $itineraire->destinations()
                ->whereNotIn('id', $existingIds)
                ->delete();
            
            foreach ($request->destinations as $destinationData) {
                if (isset($destinationData['id'])) {
                    $destination = Destination::find($destinationData['id']);
                    $destination->update([
                        'nom' => $destinationData['nom'],
                        'lieu_logement' => $destinationData['lieu_logement'],
                        'endroits_a_visiter' => $destinationData['endroits_a_visiter'] ?? [],
                        'activites' => $destinationData['activites'] ?? [],
                        'plats_a_essayer' => $destinationData['plats_a_essayer'] ?? [],
                    ]);
                } else {
                    $itineraire->destinations()->create([
                        'nom' => $destinationData['nom'],
                        'lieu_logement' => $destinationData['lieu_logement'],
                        'endroits_a_visiter' => $destinationData['endroits_a_visiter'] ?? [],
                        'activites' => $destinationData['activites'] ?? [],
                        'plats_a_essayer' => $destinationData['plats_a_essayer'] ?? [],
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Itineraire Updated Successfully',
                'data' => new ItineraireResource($itineraire->load('destinations'))
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while updating the itinerary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Itineraire $itineraire)
    {
        if (auth()->id() !== $itineraire->user_id) {
            return response()->json([
                'message' => 'You are not authorized to delete this itinerary'
            ], 403);
        }
        
        $itineraire->delete();
        
        return response()->json([
            'message' => 'Itineraire deleted successfully',
        ], 200);
    }
    
    public function addToWishlist(Itineraire $itineraire)
    {
        $user = auth()->user();
        
        if ($user->itinerairesAVisiter()->where('itineraire_id', $itineraire->id)->exists()) {
            return response()->json([
                'message' => 'This itinerary is already in your wishlist',
            ], 409);
        }
        
        $user->itinerairesAVisiter()->attach($itineraire->id);
        
        return response()->json([
            'message' => 'Itinerary added to your wishlist successfully',
        ], 200);
    }
    
    public function removeFromWishlist(Itineraire $itineraire)
    {
        $user = auth()->user();
        
        $user->itinerairesAVisiter()->detach($itineraire->id);
        
        return response()->json([
            'message' => 'Itinerary removed from your wishlist',
        ], 200);
    }
    
    public function getWishlist()
    {
        $user = auth()->user();
        $itineraires = $user->itinerairesAVisiter()->with('destinations')->get();
        
        return ItineraireResource::collection($itineraires);
    }
    
    public function getCategoriesStats()
    {
        $stats = Itineraire::select('categorie', DB::raw('count(*) as total'))
                           ->groupBy('categorie')
                           ->get();
                           
        return response()->json([
            'data' => $stats
        ], 200);
    }
}