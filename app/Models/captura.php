<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class captura extends Model
{
    protected $fillable = [
        'code',
        'decimal',
        'number',
        'currency',
        'location',
        'icon',
    ];
}
