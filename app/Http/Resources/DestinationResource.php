<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
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
            'nom' => $this->nom,
            'lieu_logement' => $this->lieu_logement,
            'endroits_a_visiter' => $this->endroits_a_visiter,
            'activites' => $this->activites,
            'plats_a_essayer' => $this->plats_a_essayer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}