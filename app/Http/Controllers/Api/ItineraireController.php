<?php

namespace App\Http\Controllers\Api;

use App\Models\Itineraire;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ItineraireResource;

class ItineraireController extends Controller
{
    public function index(){
        $itineraires = Itineraire::get();
        if($itineraires->count() > 0){
            return ItineraireResource::collection($itineraires);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'titre' => 'required|string|max:255',
            'categorie' => 'required|string',
            'duree' => 'required|string',
            'image_path' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'All fields are mondatory', 
                'error' => $validator->messages(),
            ], 422);
        } 
        $itineraire = Itineraire::create([
            'titre' => $request->titre,
            'categorie' => $request->categorie,
            'duree' => $request->duree,
            'image_path' => $request->image_path,
        ]);
        return response()->json([
            'message' => 'Itineraire Created Successfully',
            'date' => new ItineraireResource($itineraire)
        ],200);
    }

    public function show(Itineraire $itineraire){
        return new ItineraireResource($itineraire);

    }

    public function update(Request $request, Itineraire $itineraire){

        $validator = Validator::make($request->all(),[
            'titre' => 'required|string|max:255',
            'categorie' => 'required|string',
            'duree' => 'required|string',
            'image_path' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'All fields are mondatory', 
                'error' => $validator->messages(),
            ], 422);
        } 
        $itineraire->update([
            'titre' => $request->titre,
            'categorie' => $request->categorie,
            'duree' => $request->duree,
            'image_path' => $request->image_path,
        ]);
        return response()->json([
            'message' => 'Itineraire Updated Successfully',
            'date' => new ItineraireResource($itineraire)
        ],200);
    }
    public function destroy(Itineraire $itineraire){
        $itineraire->delete();
        return response()->json([
            'message' => 'Itineraire deleted successfully',
        ],200);

    }
}
