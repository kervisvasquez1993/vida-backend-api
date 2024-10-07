<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceClients extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'invoice' => [
                'id' => $this->id,
                'client_id' => $this->client_id,
                'amount' => $this->amount,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'client_profile' => [
                'name' => $this->client->profile->name,
                'last_name' => $this->client->profile->last_name,
                'phone' => $this->client->profile->phone,
                'address' => $this->client->profile->address,
                'date_of_birth' => $this->client->profile->date_of_birth,
                'gender' => $this->client->profile->gender,
            ],
        ];
    }
}
