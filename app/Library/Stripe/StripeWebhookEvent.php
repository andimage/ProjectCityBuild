<?php

namespace App\Library\Stripe;

use App\Enum;

final class StripeWebhookEvent extends Enum
{
    public const CheckoutSessionCompleted = 'checkout.session.completed';
    public const PaymentIntentSucceeded = 'payment_intent.succeeded';
    public const CustomerCreated = 'customer.created';
}
