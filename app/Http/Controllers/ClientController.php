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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'role' => 'required',
            'password' => 'required',
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'cedula' => 'required',
            'plan_id' => 'required|exists:plans,id',
        ]);
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'role' => "cliente",
            'password' => Hash::make($request->password),
        ]);
        $profile = Profile::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'cedula' => $request->cedula,
            'phone' => $request->phone,
            'img_url' => $request->img_url,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'user_id' => $user->id,
        ]);
        $client = Client::create([
            'is_active' => true,
            'activation_date' => Carbon::now(),
            'profile_id' => $profile->id,
            'plan_id' => $request->plan_id,
        ]);
        return response()->json(['message' => 'Client created successfully', 'client' => $$client], 201);
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
