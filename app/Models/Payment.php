<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Perbaikan: sesuaikan dengan tabel 'payment' (singular di migration)
    protected $table = 'payment';

    protected $fillable = [
        'order_id', 'prof_image', 'amount'
    ];

    public function order() { return $this->belongsTo(Order::class); }
}