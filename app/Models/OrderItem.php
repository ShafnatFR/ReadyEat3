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
        'order_id',
        'menu_id',
        'quantity',
        'price_at_purchase'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Accessor untuk mengakomodasi penggunaan $item->price di berbagai view.
     * Mengembalikan nilai price_at_purchase jika price tidak ada di database/attributes.
     */
    public function getPriceAttribute($value)
    {
        // Jika kolom 'price' ada nilainya, kembalikan itu.
        // Jika tidak, fallback ke 'price_at_purchase'.
        return $this->attributes['price'] ?? $this->attributes['price_at_purchase'] ?? 0;
    }
}