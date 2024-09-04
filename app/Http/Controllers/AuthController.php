<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Los datos suministrados son incorrectos'], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'data' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function me()
    {
        $user = Auth::user();
        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|unique:users',
            'role' => 'required|in:admin,client,tecnicos',
            'password' => 'required|min:6|confirmed',
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required|in:hombre,mujer'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $user = new User();
            $user->username = str_replace(' ', '_', $request->username);
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role = $request->role;
            $user->save();
            $user_id = auth()->user()->id;
            $request->merge(['user_id' => $user_id]);
            $profile = Profile::create($request->all());
            DB::commit();
            return response()->json(['data' => $profile]);
            return response()->json(['message' => "Hola, $user->username tu registro fue completado"], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error en el registro, por favor intenta nuevamente.'], 500);
        }
    }
}
