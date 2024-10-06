<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
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
        // Reglas de validación
        $rules = [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'amount' => ['required', 'integer'],
            'due_date' => ['required', 'date'],
            'status' => [
                'required',
                'string',
                Rule::in(['unpaid', 'paid', 'overdue']),
            ],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'], // archivo puede ser imagen o pdf, máximo 2 MB
        ];

        // Validación de los datos
        $validator = Validator::make($request->all(), $rules);

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validación de permisos
        if (!Gate::allows('validate-role', auth()->user())) {
            return response()->json([
                'message' => 'Error en privilegio',
                'error' => 'No tienes permisos para realizar esta acción'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Subida del archivo
        $file = $request->file('file');
        $path = Storage::disk('s3')->putFile('uploads', $file, 'public');
        $url = Storage::disk('s3')->url($path);

        // Creación de la factura
        $dataToCreate = $request->only(['client_id', 'amount', 'due_date', 'status']); // Aquí agregamos client_id
        $dataToCreate['file'] = $url; 

        // Crear el registro en la tabla invoices
        $data = Invoice::create($dataToCreate);

        // Respuesta de éxito
        return response()->json([
            'message' => 'Recurso creado exitosamente',
            'data' => $data
        ], Response::HTTP_CREATED);
    }



    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
