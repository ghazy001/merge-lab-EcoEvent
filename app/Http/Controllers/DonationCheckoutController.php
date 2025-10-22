<?php

namespace App\Http\Controllers;

use App\Models\Cause;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class DonationCheckoutController extends Controller
{
    public function create(Request $request, Cause $cause)
    {
        // Block donations for completed/canceled
        if (in_array($cause->status, ['completed','canceled'], true)) {
            return back()->with('error','Donations are closed for this cause.');
        }

        // Keep this in sync with your form's min="1"
        $rules = [
            'amount'  => 'required|numeric|min:1',
            'message' => 'nullable|string|max:1000',
        ];
        if (!auth()->check()) {
            $rules['donor_name'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        $donorName   = auth()->check() ? auth()->user()->name : $data['donor_name'];
        $amountCents = (int) round($data['amount'] * 100);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => auth()->check() ? auth()->user()->email : null,

            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name'        => 'Donation: '.$cause->title,
                        'description' => 'Cause #'.$cause->id,
                    ],
                    'unit_amount' => $amountCents,
                ],
                'quantity' => 1,
            ]],

            // Only the metadata we actually use in the webhook
            'metadata' => [
                'cause_id'   => (string) $cause->id,
                'donor_name' => $donorName,
                'message'    => $data['message'] ?? '',
            ],

            'success_url' => route('donations.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('donations.cancel', ['cause' => $cause->id]),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        // Optionally, fetch and show a friendly thank-you using ?session_id=...
        return view('donations.success');
    }

    public function cancel(Request $request)
    {
        $causeId = $request->query('cause');
        return redirect()->route('causes.show', $causeId)->with('info','Donation canceled.');
    }
}
