<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceClients;
use App\Http\Resources\MyResourceInvoice;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function index(Request $request)
    {
        // Verificar permisos
        if (!Gate::allows('validate-role', auth()->user())) {
            return response()->json([
                'message' => 'Error en privilegio',
                'error' => 'No tienes permisos para realizar esta acción'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $invoices = Invoice::with('client.profile')->get();
        return InvoiceClients::collection($invoices);
    }
    public function myInvoices()
    {
        // Cargamos el perfil, cliente y facturas
        $invoices = auth()->user()
            ->profile
            ->client
            ->invoice;

        // Retornamos solo la información de las facturas
        return MyResourceInvoice::collection($invoices);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        $file = $request->file('file');
        $path = Storage::disk('s3')->putFile('uploads', $file, 'public');
        $url = Storage::disk('s3')->url($path);
        $dataToCreate = $request->only(['client_id', 'amount', 'due_date', 'status']); // Aquí agregamos client_id
        $dataToCreate['file'] = $url;
        $data = Invoice::create($dataToCreate);
        return response()->json([
            'message' => 'Recurso creado exitosamente',
            'data' => $data
        ], Response::HTTP_CREATED);
    }

    public function invoiceStatusChange(Request $request, $id)
    {
        try {
            $rules = ['status' => [
                'required',
                'string',
                Rule::in(['unpaid', 'paid', 'overdue']),
            ]];
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
            $data = Invoice::findOrFail($id);
            $validatedData = $request->only(['status']);
            $data->update($validatedData);
            return response()->json($data, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $modelName = class_basename($e->getModel());
            return response()->json(['message' => "No query results for model {$modelName} {$id}"], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error', 'error' =>  $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
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
