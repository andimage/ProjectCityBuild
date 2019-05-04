<?php

namespace App\Library\Stripe;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Checkout\Session;
use App\Entities\AcceptedCurrencyType;

final class StripePaymentProvider
{
    private $currency;

    public function __construct()
    {
        $this->setApiKey();
        $this->setCurrency(new AcceptedCurrencyType(AcceptedCurrencyType::CURRENCY_AUD));
    }

    private function setApiKey()
    {
        $key = config('services.stripe.secret');
        if (empty($key)) {
            throw new \Exception('No Stripe API secret set');
        }
        Stripe::setApiKey($key);
    }

    public function setCurrency(AcceptedCurrencyType $currency) : StripePaymentProvider
    {
        $this->currency = $currency;
        return $this;
    }
    
    public function charge(int $amount, string $token = null, ?int $customerId = null, string $receiptEmail = null, string $description = null) : Charge
    {
        return Charge::create([
            'amount' => $amount,
            'source' => $token,
            'receipt_email' => $receiptEmail,
            'description' => $description,
            'customer' => $customerId,
            'currency' => $this->currency,
        ]);
    }

    public function createCustomer(string $token, string $email) : Customer
    {
        return Customer::create([
            'email' => $email,
            'source' => $token,
        ]);
    }

    /**
     * Begins a payment session. Must be called before performing charges
     *
     * @see https://stripe.com/docs/api/checkout/sessions/create
     * 
     * @param string $successUrl The URL the customer will be directed to after the payment or subscription creation is successful.
     * @param string $cancelUrl The URL the customer will be directed to if they decide to cancel payment.
     * @param array[StripeLineItem] $stripeLineItems Item/s being purchased
     * 
     * @return string Session ID - needs to be passed to Stripe.js for front-end integration
     */
    public function beginSession(string $successUrl, string $cancelUrl, array $stripeLineItems) : string
    {
        $session = Session::create([
            // 'client_reference_id' => $accountPaymentId,
            'payment_method_types' => ['card'], // only `card` is currently supported by Stripe
            'line_items' => [
                array_map(function(StripeLineItem $stripeLineItem) {
                    return $stripeLineItem->toArray();
                }, $stripeLineItems),
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
        
        return $session->id;
    }
}