<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        // 'pin_code',
        'status',
        'message',
        'total_price',
        'payment_mode',
        'payment_id',
        'tracking_no',
    ];

    public function orderitems(){
        return $this->hasMany(OrderItem::class);
    }
}
