<?php

namespace App\Http\Controllers;

use App\Adapter\MikrotikAdapter;
use App\Http\Resources\ClientResource;
use App\Models\ClientMikrotikAndDBLocal;
use GuzzleHttp\Client as HttpClient;
use App\Models\Client;
use App\Models\HistoryActivation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

    public function myPlanClient()
    {
        $auth = auth()->user()->load('profile.client.plan');
        $client = $auth->profile->client;

        if (!$client) {
            return response()->json(['message' => 'No client found for this user'], 404);
        }
        return new ClientResource($client);
    }
    public function store(Request $request)
    {

        $rules = [
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'profile_id' => ['required', 'integer', 'exists:profiles,id'],
            'client_mikrowisp_id' => ['nullable', 'integer', 'unique:clients,client_mikrowisp_id'], // Verifica que sea único si no es nulo
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $client = Client::where('profile_id', $request->profile_id)->first();
        if ($client) {
            return response()->json(['message' => 'Error al crear registro', 'errors' => 'Client already exists'], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $client = Client::create([
                'is_active' => true,
                'profile_id' => $request->profile_id,
                'plan_id' => $request->plan_id,
                'client_mikrowisp_id' => $request->client_mikrowisp_id
            ]);

            HistoryActivation::create([
                'client_id' => $client->id,
                'change_status_data' => Carbon::now(),
                'status' => 'active',
            ]);

            DB::commit();

            return response()->json(['message' => 'Client created successfully', 'data' => $client], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear registro', 'errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
