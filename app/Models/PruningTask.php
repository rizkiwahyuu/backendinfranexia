<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PruningTask extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'due_date' => 'date:Y-m-d',
        ];
    }
}
