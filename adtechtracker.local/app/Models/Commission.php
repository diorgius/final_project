<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

// заполняемые поля
#[Fillable(['commission'])]

class Commission extends Model
{
    /**
     * Устанавливаем тип поля
     * @return array{price: string}
     */
    protected function casts(): array
    {
        return [
            'commission' => 'decimal:2'
        ];
    }
}
