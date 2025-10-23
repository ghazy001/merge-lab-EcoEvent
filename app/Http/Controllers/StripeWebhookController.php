<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Donation;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent(); // raw bytes
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        if (!$sigHeader) {
            \Log::warning('Stripe webhook: missing Stripe-Signature header');
            return response('Missing signature', 400);
        }
        if (!$secret) {
            \Log::warning('Stripe webhook: webhook_secret not configured');
            return response('Secret not configured', 400);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            \Log::warning('Stripe signature failed: '.$e->getMessage());
            return response('Bad signature', 400);
        } catch (\Throwable $e) {
            \Log::error('constructEvent error: '.$e->getMessage());
            return response('Invalid payload', 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event->data->object);
                    break;
                default:
                    \Log::info('Unhandled event: '.$event->type);
            }
            return response()->noContent(); // 204
        } catch (\Throwable $e) {
            \Log::error('Handler error: '.$e->getMessage().' | '.$e->getTraceAsString());
            return response('', 500);
        }
    }


    // app/Http/Controllers/StripeWebhookController.php
    private function handleCheckoutCompleted($session): void
    {
        $metadata  = $session->metadata ?? (object) [];
        $causeId   = $metadata->cause_id   ?? null;
        $donorName = $metadata->donor_name ?? 'Anonymous';
        $message   = $metadata->message    ?? '';

        // authoritative amount from PI
        $amount = 0.0;
        if (!empty($session->payment_intent)) {
            $pi = \Stripe\PaymentIntent::retrieve($session->payment_intent);
            $amount = (($pi->amount_received ?? $pi->amount ?? 0) / 100.0);
        }

        // ✅ idempotent insert keyed by checkout_session_id (your column)
        \App\Models\Donation::firstOrCreate(
            ['checkout_session_id' => $session->id],
            [
                'cause_id'   => $causeId,
                'donor_name' => $donorName,
                'amount'     => $amount,
                'date'       => now()->toDateString(),
                'message'    => $message ?: null,
            ]
        );
    }

}
