<?php

if (! function_exists('format_money')) {
    /**
     * Formata um valor em centavos para uma string decimal (sem símbolo de
     * moeda), usando os separadores configurados em config('shop.php').
     */
    function format_money(int $cents): string
    {
        return number_format(
            $cents / 100,
            2,
            config('shop.currency_decimal_separator'),
            config('shop.currency_thousands_separator'),
        );
    }
}

if (! function_exists('image_mimes_label')) {
    /**
     * "JPEG, PNG ou WEBP" a partir de config('shop.image_mimes') — usado nas
     * mensagens de validação para não duplicar a lista de tipos aceitos.
     */
    function image_mimes_label(): string
    {
        $mimes = array_map('strtoupper', config('shop.image_mimes'));

        if (count($mimes) <= 1) {
            return $mimes[0] ?? '';
        }

        $last = array_pop($mimes);

        return implode(', ', $mimes).' ou '.$last;
    }
}

if (! function_exists('image_max_label')) {
    /**
     * Tamanho máximo de imagem formatado para exibição (ex: "2MB"), a partir
     * de config('shop.image_max_kb').
     */
    function image_max_label(): string
    {
        $kb = config('shop.image_max_kb');

        $mb = $kb / 1024;

        $formatted = floor($mb) == $mb ? (string) (int) $mb : rtrim(rtrim(number_format($mb, 1, ',', '.'), '0'), ',');

        return $formatted.'MB';
    }
}

if (! function_exists('image_upload_rules')) {
    /**
     * Regras de validação para upload de imagem (produto e foto de perfil),
     * construídas a partir de config('shop.image_max_kb'/'image_mimes') para
     * que limite de tamanho e tipos aceitos fiquem centralizados num só lugar.
     */
    function image_upload_rules(bool $required = true): array
    {
        return [
            $required ? 'required' : 'sometimes',
            'image',
            'mimes:'.implode(',', config('shop.image_mimes')),
            'max:'.config('shop.image_max_kb'),
        ];
    }
}
