<?php

return [
    "subscription_table_prefix"             => "subscription",
    'current_active_subscription_cache_key' => "CURRENT_ACTIVE_SUBSCRIPTION_FOR_{OWNER_CLASS}-{OWNER_ID}",
    'paypal'                                => [
        'client_id'     => env("PAYPAL_CLIENT_ID"),
        'client_secret' => env("PAYPAL_CLIENT_SECRET"),
        'base_url'      => env("PAYPAL_BASE_URL", "https://api-m.sandbox.paypal.com/v1"),
        'endpoints'     => [
            'initiate_payment'   => "/gwprocess/v4/api.php",
            'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
            'order_validate'     => "/validator/api/validationserverAPI.php",
            'refund_payment'     => "/validator/api/merchantTransIDvalidationAPI.php",
            'refund_status'      => "/validator/api/merchantTransIDvalidationAPI.php",
        ],

        'application_context' => [
            'brand_name'          => 'MoveOn',
            'locale'              => 'en-US',
            'shipping_preference' => 'NO_SHIPPING',
            'user_action'         => 'SUBSCRIBE_NOW',
            'payment_method'      => [
                'payer_selected'  => 'PAYPAL',
                'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
            ],
            'return_url'          => 'https://example.com/returnUrl',
            'cancel_url'          => 'https://example.com/cancelUrl',
        ],

        'events' => [
            'webhook' => [
                "payment_sale_completed"                => "",
                "payment_sale_reversed"                 => "",
                "billing_plan_activated"                => "",
                "billing_plan_pricing_change_activated" => "",
                "billing_plan_deactivated"              => "",
                "billing_subscription_activated"        => "",
                "billing_subscription_updated"          => "",
                "billing_subscription_expired"          => "",
                "billing_subscription_cancelled"        => "",
                "billing_subscription_suspended"        => "",
                "billing_subscription_payment_failed"   => "",
            ]
        ],


        'webhook_id' => [
            'paypal_webhook_route_name1' => 'paypal-webhook-id-from-the-dashboard',
            'paypal_webhook_route_name2' => 'paypal-webhook-id-from-the-dashboard',
        ],
    ],


    'stripe' => [
        'client_id'           => env("STRIPE_CLIENT_ID"),
        'client_secret'       => env("STRIPE_CLIENT_SECRET"),
        'application_context' => [
            'brand_name'          => 'MoveOn',
            'locale'              => 'en-US',
            'shipping_preference' => 'NO_SHIPPING',
            'user_action'         => 'SUBSCRIBE_NOW',
            'payment_method'      => [
                'payer_selected'  => 'STRIPE',
                'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
            ],
            'return_url'          => 'https://example.com/returnUrl',
            'cancel_url'          => 'https://example.com/cancelUrl',
        ],

        'events' => [
            'webhook' => [
                'customer.subscription.created'                => "",
                'customer.subscription.deleted'                => "",
                'customer.subscription.pending_update_applied' => "",
                'customer.subscription.pending_update_expired' => "",
                'customer.subscription.trial_will_end'         => "",
                'customer.subscription.updated'                => "",
                'invoice.created'                              => "",
                'invoice.finalization_failed'                  => "",
                'invoice.finalized'                            => "",
                'invoice.paid'                                 => "",
                'invoice.payment_action_required'              => "",
                'invoice.payment_failed'                       => "",
                'invoice.upcoming'                             => "",
                'invoice.updated'                              => "",
                'payment_intent.created'                       => "",
                'payment_intent.succeeded'                     => "",
            ],
        ],

        'webhook_ids' => [
            'subscription' => 'stripe subscription webhook token from dashboard',
        ],
    ],
];