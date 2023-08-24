<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
            protected $fillable = [
                'prod_name',
                'unit',
                'price',
                'expiration_date',
                'available',
                'image',
            ];
            protected $table = 'products';
}
