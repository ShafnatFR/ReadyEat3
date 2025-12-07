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
        'order_id',
        'proof_image',  // Fixed typo: was 'prof_image'
        'amount',
        'status'        // Added missing status field
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}