<?php

namespace App\Http\Controllers;

use App\Mail\BankTransferMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function sendBankTransferData (Request $request) {

        try {
            if(!$request->has(['mail'])){
                return response()->json([
                    'message' => "No se recibio el mail a donde enviar los datos",
                ], 400);
            }

            Mail::to($request->mail)->send(new BankTransferMail());
            return response()->json([
                'message' => "Se han enviado los datos a su mail.",
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
