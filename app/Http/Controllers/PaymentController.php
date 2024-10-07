<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
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
        // Verificar si el usuario tiene los permisos necesarios
        if (!Gate::allows('validate-role', auth()->user())) {
            return response()->json([
                'message' => 'Error en privilegio',
                'error' => 'No tienes permisos para realizar esta acción'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Eager loading de las relaciones: invoice y paymentTransaction
        $data = Payment::with(['invoice', 'paymentTransaction'])->get();

        // Retornar la respuesta con los datos cargados
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

        // Verificar si la factura ya está pagada
        $invoice = Invoice::where('id', $request->invoice_id)->first();
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Esta factura ya está pagada'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar si ya existe un pago completado para esta factura
        $existingPayment = Payment::where('invoice_id', $request->invoice_id)
            ->where('status', 'completed')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'message' => 'Ya existe un pago completado para esta factura'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar si ya existe una transacción completada para este pago
        $existingTransaction = PaymentTransaction::where('payment_id', $existingPayment->id ?? 0)
            ->where('status', 'completed')
            ->first();

        if ($existingTransaction) {
            return response()->json([
                'message' => 'La transacción ya ha sido completada para esta factura'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Crear el registro en la tabla Payment
        $dataToCreate = $request->only(['invoice_id', 'payment_methods_id', 'amount']);
        $dataToCreate['client_id'] = auth()->user()->profile->client->id;
        $dataToCreate['status'] = 'pending';
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
            $invoice->update([
                'status' => 'paid'
            ]);
        } else {
            // Si el pago falló
            $payment->update(['status' => 'failed']);
            $paymentTransaction->update([
                'transaction_id' => $responseFromExternalService['transaction_id'],
                'status' => 'failed',
            ]);
            $invoice->update([
                'status' => 'overdue'
            ]);
        }

        return response()->json([
            'message' => 'Pago procesado exitosamente',
            'payment' => $payment,
            'transaction' => $paymentTransaction,
            'invoice' => $invoice
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Payment::with(['invoice', 'paymentTransaction'])->findOrFail($id);
            if (auth()->user()->role !== 'admin' || auth()->user()->profile->client->id !== $data->client_id) {
                return response()->json([
                    'message' => 'Error en privilegio',
                    'error' => 'No tienes permisos para realizar esta acción'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json($data, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            $modelName = class_basename($e->getModel());
            return response()->json(['message' => "No query results for id $id of model {$modelName}"], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error interno', 'error' =>  $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


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
