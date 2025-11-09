<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'shipping_amount',
        'shipping_method',
        'notes',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function items(){
        return $this->hasMany(OrderItem::class);
    }
    public function address(){
        return $this->hasOne(Address::class);
    }
    public function paymentMethods() {
        return [
            'credit_card' => 'Credit Card',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer',
            'cash_on_delivery' => 'Cash on Delivery',
        ];
    }
    public function getPaymentMethodLabelAttribute() {
        $methods = $this->paymentMethods();
        return $methods[$this->payment_method] ?? 'Unknown';
    }
}
