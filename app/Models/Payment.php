<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Sesuaikan dengan tabel 'payment' di database
    protected $table = 'payment';

    protected $fillable = [
        'order_id',
        'proof_image',
        'amount',
        'status'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}