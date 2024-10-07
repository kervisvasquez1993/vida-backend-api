<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('validate-role', auth()->user())) {
            return response()->json([
                'message' => 'Error en privilegio',
                'error' => 'No tienes permisos para realizar esta acción'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $data = Payment::all();
        return response()->json($data, Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validaciones
        $rules = [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'payment_methods_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'amount' => ['required', 'integer', 'min:1'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        // Crear el registro en la tabla Payment
        $dataToCreate = $request->only(['invoice_id', 'payment_methods_id', 'amount']);
        $dataToCreate['client_id'] = auth()->user()->profile->client->id;
        $dataToCreate['status'] = 'pending'; // Estado inicial como pendiente
        $payment = Payment::create($dataToCreate);

        // Preparar la transacción inicial con estado 'pending'
        $paymentTransaction = PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_id' => null, // Aquí podrías poner un identificador del servicio externo si lo necesitas
            'status' => 'pending',
        ]);
        $responseFromExternalService = $this->sendPaymentToExternalService($payment);

        // Actualizar el estado de la transacción según la respuesta del servicio externo
        if ($responseFromExternalService['success']) {
            // Si el pago fue exitoso
            $payment->update(['status' => 'completed']);
            $paymentTransaction->update([
                'transaction_id' => $responseFromExternalService['transaction_id'],
                'status' => 'completed',
            ]);
        } else {
            // Si el pago falló
            $payment->update(['status' => 'failed']);
            $paymentTransaction->update([
                'transaction_id' => $responseFromExternalService['transaction_id'],
                'status' => 'failed',
            ]);
        }

        return response()->json([
            'message' => 'Pago procesado exitosamente',
            'payment' => $payment,
            'transaction' => $paymentTransaction
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
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

            $data = Payment::findOrFail($id);
            $authClientId = auth()->user()->profile->client->id;

            // Verificar si el cliente autenticado tiene permiso
            if ($data->client_id != $authClientId && auth()->user()->role != "admin") {
                return response()->json([
                    'message' => 'Validación fallida',
                    'errors' => 'No posee permiso para realizar esta acción'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Actualizar solo el campo 'status' con los datos del request
            $data->update(['status' => $request->input('status')]);

            return response()->json($data, Response::HTTP_OK);
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
    public function destroy(Payment $payment)
    {
        //
    }

    private function sendPaymentToExternalService($payment)
    {
        // Simulación de una respuesta de un servicio externo de pago
        // En un caso real, aquí harías la solicitud HTTP al servicio.
        return [
            'success' => true, // o false si falló
            'transaction_id' => 'TRX123456789', // ID de la transacción generado por el servicio externo
        ];
    }
}
