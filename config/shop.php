<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moeda
    |--------------------------------------------------------------------------
    |
    | 'currency' é o código ISO usado no Stripe (price_data.currency).
    | 'currency_symbol' é o que aparece nas telas (ex: "R$", "$", "€").
    | Os separadores controlam como os valores em centavos são formatados
    | (ex: pt-BR usa vírgula decimal e ponto de milhar; en-US é o oposto).
    |
    */
    'currency' => env('SHOP_CURRENCY', 'brl'),
    'currency_symbol' => env('SHOP_CURRENCY_SYMBOL', 'R$'),
    'currency_decimal_separator' => env('SHOP_CURRENCY_DECIMAL_SEPARATOR', ','),
    'currency_thousands_separator' => env('SHOP_CURRENCY_THOUSANDS_SEPARATOR', '.'),

    /*
    |--------------------------------------------------------------------------
    | Expiração do checkout
    |--------------------------------------------------------------------------
    |
    | Minutos que um pedido "pending" tem para ser pago antes de expirar
    | automaticamente (App\Console\Commands\ExpireStaleOrders, que roda a
    | cada 5 minutos). O mesmo valor é usado para expirar a sessão de
    | checkout no Stripe, para os dois prazos ficarem sincronizados.
    |
    | O Stripe exige um mínimo de 30 minutos para o "expires_at" de uma
    | Checkout Session — se este valor for menor, o lado do Stripe é
    | automaticamente elevado a 30 min (ver StripeController), então não
    | recomendamos configurar abaixo de 30.
    |
    */
    'checkout_expiration_minutes' => (int) env('CHECKOUT_EXPIRATION_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Upload de imagens
    |--------------------------------------------------------------------------
    |
    | Usado tanto para imagem de produto quanto para foto de perfil.
    |
    */
    'image_max_kb' => (int) env('IMAGE_MAX_KB', 2048),
    'image_mimes' => array_filter(array_map('trim', explode(',', env('IMAGE_MIMES', 'jpeg,png,webp')))),

    /*
    |--------------------------------------------------------------------------
    | Paginação / tamanhos de listagem
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'orders' => (int) env('PAGINATE_ORDERS', 10),
        'search' => (int) env('PAGINATE_SEARCH', 20),
        'tag_products' => (int) env('PAGINATE_TAG_PRODUCTS', 20),
    ],

    'home' => [
        // Tamanho do lote de produtos recentes buscado de uma vez para montar
        // a home (evita N+1 de uma query por categoria).
        'recent_pool' => (int) env('HOME_RECENT_POOL', 200),
        // Quantos produtos aparecem por categoria/tag na home.
        'per_category' => (int) env('HOME_PRODUCTS_PER_CATEGORY', 15),
    ],

    // Quantas avaliações recentes aparecem no perfil do usuário.
    'profile_recent_ratings' => (int) env('PROFILE_RECENT_RATINGS', 5),

    /*
    |--------------------------------------------------------------------------
    | Contato
    |--------------------------------------------------------------------------
    */
    'support_email' => env('SUPPORT_EMAIL', 'renato.deacaldas@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Rate limiting (requisições por minuto)
    |--------------------------------------------------------------------------
    |
    | Ver App\Providers\AppServiceProvider e App\Providers\FortifyServiceProvider.
    |
    */
    'rate_limits' => [
        'global' => (int) env('RATE_LIMIT_GLOBAL', 300),
        'search' => (int) env('RATE_LIMIT_SEARCH', 30),
        'cart' => (int) env('RATE_LIMIT_CART', 60),
        'checkout' => (int) env('RATE_LIMIT_CHECKOUT', 15),
        'withdraw' => (int) env('RATE_LIMIT_WITHDRAW', 5),
        'ratings' => (int) env('RATE_LIMIT_RATINGS', 10),
        'listings' => (int) env('RATE_LIMIT_LISTINGS', 20),
        'profile' => (int) env('RATE_LIMIT_PROFILE', 20),
        'login' => (int) env('RATE_LIMIT_LOGIN', 5),
        'two_factor' => (int) env('RATE_LIMIT_TWO_FACTOR', 5),
        'register' => (int) env('RATE_LIMIT_REGISTER', 5),
        'password_email' => (int) env('RATE_LIMIT_PASSWORD_EMAIL', 5),
        'password_update' => (int) env('RATE_LIMIT_PASSWORD_UPDATE', 10),
    ],

];
