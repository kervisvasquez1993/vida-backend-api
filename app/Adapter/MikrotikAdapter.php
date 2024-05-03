<?php

namespace App\Adapter;

use App\Models\Client;

class MikrotikAdapter
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function adapt(Client $client): array
    {
        return [
            'token' => env('MIKROTIK_API_TOKEN'),
            'nombre' => $client->profile->name,
            'cedula' => $client->profile->cedula,
            'correo' => $client->profile->user->email,
            'telefono' => $client->profile->phone,
            'movil' => $client->profile->phone,
            'direccion_principal' => $client->profile->address,
        ];
    }
}
