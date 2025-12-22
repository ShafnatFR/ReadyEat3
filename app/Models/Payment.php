<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Perbaikan: sesuaikan dengan tabel 'payments' (plural)
    protected $table = 'payments';

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