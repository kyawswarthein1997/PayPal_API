<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'order_qty',
        'order_price',
    ];


    public function products(){
        return $this->belongsTo(Product::class, 'product_id','id');
    }
}
