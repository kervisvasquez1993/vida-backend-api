<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'client_mikrowisp_id' => $this->client_mikrowisp_id,
            
            // Incluir los datos del perfil del cliente
            'profile' => [
                'name' => $this->profile->name,
                'last_name' => $this->profile->last_name,
                'phone' => $this->profile->phone,
                'img_url' => $this->profile->img_url,
                'address' => $this->profile->address,
                'date_of_birth' => $this->profile->date_of_birth,
                'gender' => $this->profile->gender,
            ],

            // Incluir los datos del plan del cliente
            'plan' => [
                'id' => $this->plan->id,
                'name' => $this->plan->name,
                'price' => $this->plan->price,
                'speed' => $this->plan->speed,
                'description' => $this->plan->description,
            ],
        ];
    }
}
