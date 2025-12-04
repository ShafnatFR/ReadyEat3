<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Perbaikan: sesuaikan dengan nama tabel 'menus'
    protected $table = 'menus';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'isAvailable',
        'daily_limit'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}