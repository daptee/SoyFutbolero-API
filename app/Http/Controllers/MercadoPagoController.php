<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago;

class MercadoPagoController extends Controller
{
    public function createPay (Request $request) {

//        dd(config('services.mercadopago'));

        // SDK de Mercado Pago
        require base_path('vendor/autoload.php');
        // Agrega credenciales
        MercadoPago\SDK::setAccessToken(config('services.mercadopago.prod.token'));

        // Crea un objeto de preferencia
        $preference = new MercadoPago\Preference();
        $preference->back_urls = array(
            "success" => "http://192.168.0.103:3000/payment/success",
            "failure" => "http://192.168.0.103:3000/payment/failure",
            "pending" => "http://192.168.0.103:3000/payment/pending"
        );
        $preference->auto_return = "approved";

        // Crea un ítem en la preferencia
        $item = new MercadoPago\Item();
//        $item->id = $request->id;
        $item->title = $request->title;
//        $item->currency_id = $request->currency_id;
//        $item->description = $request->description;
        $item->quantity = $request->quantity;
        $item->unit_price = $request->unit_price;
        $preference->items = array($item);
        $preference->save();

        // dd($preference);
        return response()->json(['preference' => $preference->id], 200);
    }
}
