<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Cart = 'cart';
    case Pending = 'pending';
    case Paid = 'paid';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Cart => 'Carrinho',
            self::Pending => 'Aguardando pagamento',
            self::Paid => 'Pago',
            self::Completed => 'Concluído',
            self::Cancelled => 'Cancelado',
        };
    }
}
