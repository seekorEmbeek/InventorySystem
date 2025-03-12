<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    //
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales';

    protected $fillable = [
        'buyerName',
        'date',
        'qty',
        'totalPrice',
        'totalPayment',
        'remainingPayment',
        'status',
        'stock_id'
    ];

    public function items()
    {
        return $this->hasMany(SalesItem::class, 'sales_id');
    }
}
