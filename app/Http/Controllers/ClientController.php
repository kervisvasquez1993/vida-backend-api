<?php

namespace App\Http\Controllers;

use App\Adapter\MikrotikAdapter;
use App\Models\ClientMikrotikAndDBLocal;
use GuzzleHttp\Client as HttpClient;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer'],
            'speed' => ['required', 'string', 'max:255', 'unique:plans'],
            'description' => ['required', 'string'],
        ];
        $validator = Validator::make($request->all(), $rules);

        // $client = Client::create([
        //     'is_active' => true,
        //     'activation_date' => Carbon::now(),
        //     'profile_id' => $profile->id,
        //     'plan_id' => $request->plan_id,
        // ]);
        // return response()->json(['message' => 'Client created successfully', 'client' => $$client], 201);
        // $adapter = new MikrotikAdapter();
        // $data = $adapter->adapt($client);
        // $httpClient = new HttpClient();
        // $response = $httpClient->request('POST', 'https://demo.mikrosystem.net/api/v1/NewUser', [
        //     'json' => $data,
        // ]);
        // $responseData = json_decode($response->getBody()->getContents());
        // $clientMikrotikAndDBLocal = ClientMikrotikAndDBLocal::create([
        //     'client_id' => $client->id,
        //     'client_mikrotik_id' => $responseData->idcliente,
        // ]);

        // return response()->json(['message' => 'Client created successfully', 'client' => $clientMikrotikAndDBLocal], 201);
    }

    /**
     * Display the specified resource.
     */

    public function getClient(Client $client)
    {
        $clientMikrotikAndDBLocal = $client->clientMikrotikAndDBLocal;

        $httpClient = new HttpClient();
        $response = $httpClient->request('POST', 'https://demo.mikrosystem.net/api/v1/GetClientsDetails', [
            'json' => [
                'token' => env('MIKROTIK_API_TOKEN'),
                'idcliente' => $clientMikrotikAndDBLocal->client_mikrotik_id,
            ],
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        return response()->json($responseData);
    }

    public function updateClient(Request $request, Client $client)
    {
        $request->validate([
            'nombre' => 'sometimes|required',
            'correo' => 'sometimes|required|email',
            'telefono' => 'sometimes|required',
            'movil' => 'sometimes|required',
            'cedula' => 'sometimes|required',
            'codigo' => 'sometimes|required',
            'direccion_principal' => 'sometimes|required',
            'campo_personalizado' => 'sometimes|required',
        ]);

        $clientMikrotikAndDBLocal = $client->clientMikrotikAndDBLocal;

        $httpClient = new HttpClient();
        $response = $httpClient->request('POST', 'https://demo.mikrosystem.net/api/v1/UpdateUser', [
            'json' => [
                'token' => env('MIKROTIK_API_TOKEN'),
                'idcliente' => $clientMikrotikAndDBLocal->client_mikrotik_id,
                'datos' => $request->only([
                    'nombre',
                    'correo',
                    'telefono',
                    'movil',
                    'cedula',
                    'codigo',
                    'direccion_principal',
                    'campo_personalizado',
                ]),
            ],
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        return response()->json($responseData);
    }

    public function activateClient(Client $client)
    {
        $clientMikrotikAndDBLocal = $client->clientMikrotikAndDBLocal;
        $httpClient = new HttpClient();
        $response = $httpClient->request('POST', 'https://demo.mikrosystem.net/api/v1/ActiveService', [
            'json' => [
                'token' => env('MIKROTIK_API_TOKEN'),
                'idcliente' => $clientMikrotikAndDBLocal->client_mikrotik_id,
            ],
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        return response()->json($responseData);
    }

    public function deactivateClient(Client $client)
    {
        $clientMikrotikAndDBLocal = $client->clientMikrotikAndDBLocal;

        $httpClient = new HttpClient();
        $response = $httpClient->request('POST', 'https://demo.mikrosystem.net/api/v1/SuspendService', [
            'json' => [
                'token' => env('MIKROTIK_API_TOKEN'),
                'idcliente' => $clientMikrotikAndDBLocal->client_mikrotik_id,
            ],
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        return response()->json($responseData);
    }
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
