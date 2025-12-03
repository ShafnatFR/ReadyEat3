<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Perbaikan: sesuaikan dengan tabel 'orderItems' (camelCase di migration)
    protected $table = 'orderItems';

    protected $fillable = [
        'order_id', 'menu_id', 'quantity', 'price_at_purchase'
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function menu() { return $this->belongsTo(Menu::class); }
}