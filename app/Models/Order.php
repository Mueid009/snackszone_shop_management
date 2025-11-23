<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_no','customer_name','customer_phone','customer_address','order_description',
        'total','paid','payment_method'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
