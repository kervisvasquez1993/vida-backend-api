<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Plan::all();
        return response()->json($data, Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:plans'],
            'price' => ['required', 'integer', 'unique:plans'],
            'speed' => ['required', 'string', 'max:255', 'unique:plans'],
            'description' => ['required', 'string'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        if (!Gate::allows('validate-role', auth()->user())) {
            return response()->json([
                'message' => 'Error en privilegio',
                'error' => 'No tienes permisos para realizar esta acción'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $data = Plan::create($request->all());
        return response()->json([
            'message' => 'Recurso creado exitosamente',
            'data' => $data
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Plan::findOrFail($id);
            return response()->json($data, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $modelName = class_basename($e->getModel());
            return response()->json(['message' => "No query results for id $id of model {$modelName} "], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error interno', 'error' =>  $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }




    public function update(Request $request, $id)
    {
        try {
            // Encuentra el plan que se está actualizando
            $plan = Plan::findOrFail($id);

            // Prepara las reglas de validación
            $rules = [
                'name' => ['sometimes', 'required', 'string', 'max:255',  Rule::unique('plans')->ignore($id),],
                'price' => ['sometimes', 'required', 'integer',  Rule::unique('plans')->ignore($id),],
                'speed' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('plans')->ignore($id),
                ],
                'description' => ['sometimes', 'required', 'string'],
            ];

            // Realiza la validación
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validación fallida',
                    'errors' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            // Verifica los permisos del usuario
            if (!Gate::allows('validate-role', auth()->user())) {
                return response()->json(['message' => 'Error en privilegio', 'error' => 'No tienes permisos para realizar esta acción'], Response::HTTP_UNAUTHORIZED);
            }

            // Solo actualiza los campos que se han proporcionado
            $validatedData = $request->only(['name', 'price', 'speed', 'description']);
            $plan->update(array_filter($validatedData)); // Filtra valores vacíos

            return response()->json($plan, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $modelName = class_basename($e->getModel());
            return response()->json(['message' => "No query results for model {$modelName} {$id}"], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error', 'error' =>  $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            if (!Gate::allows('validate-role', auth()->user())) {
                return response()->json(['message' => 'Error en privilegio', 'error' => 'No tienes permisos para realizar esta acción'], Response::HTTP_UNAUTHORIZED);
            }
            $data = Plan::findOrFail($id);
            $data->delete();
            return response()->json(["message" => "Recurso eliminada de forma exitosa"], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => "Error", 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
