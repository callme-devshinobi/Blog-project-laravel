<?php

namespace App\Http\Controllers;

use App\Services\Paystack;
use Illuminate\Http\Request;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request, Paystack $paystack)
    {
        $secret = (string) config('services.paystack.secret');

        if ($secret === '') {
            return response('Not configured', 503);
        }

        // Paystack signs the raw body with your secret key (HMAC SHA512)
        $raw       = $request->getContent();
        $signature = (string) $request->header('x-paystack-signature', '');
        $expected  = hash_hmac('sha512', $raw, $secret);

        if ($signature === '' || ! hash_equals($expected, $signature)) {
            return response('Invalid signature', 401);
        }

        $payload = json_decode($raw, true);

        if (! is_array($payload) || empty($payload['event'])) {
            return response('ok');
        }

        try {
            $paystack->handleEvent($payload['event'], (array) ($payload['data'] ?? []));
        } catch (\Throwable $e) {
            logger()->error('Paystack webhook failed', [
                'event'   => $payload['event'],
                'error'   => class_basename($e),
                'message' => $e->getMessage(),
            ]);

            return response('Error', 500);   // Paystack will send it again
        }

        return response('ok');
    }
}