<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PayHereNotifyController extends Controller
{
    /**
     * PayHere posts the payment result here. The browser return URL does not mark a payment paid.
     */
    public function __invoke(Request $request, PaymentService $payments): Response
    {
        $payments->verifyPayHereNotify($request->all());

        return response('OK');
    }
}
