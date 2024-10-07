<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
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
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'payment_methods_id' => ['required', 'integer', 'exists:payment_methods,id'], // Cambia aquí
            'amount' => ['required', 'integer', 'min:1'], 
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'completed', 'failed']),
            ],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $dataToCreate = $request->only(['invoice_id', 'payment_methods_id', 'amount', 'status']); // Cambia aquí
        $dataToCreate['client_id'] = auth()->user()->profile->client->id;
        $data = Payment::create($dataToCreate);
        return response()->json([
            'message' => 'Recurso creado exitosamente',
            'data' => $data
        ], Response::HTTP_CREATED);
    }
    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
