<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'user_id' => $this->user_id,
            'titre' => $this->titre,
            'categorie' => $this->categorie,
            'duree' => $this->duree,
            'image_path' => $this->image_path,
            'destinations' => DestinationResource::collection($this->whenLoaded('destinations')),
            'nombre_favoris' => $this->whenCounted('favorisParUsers'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}