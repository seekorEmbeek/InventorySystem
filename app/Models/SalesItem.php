<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesItem extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sales_items';

    protected $fillable = [
        'sales_id',
        'productId',
        'productName',
        'uom',
        'qty',
        'pricePerUnit',
        'sellingPricePerUnit',
        'totalSellingPrice',
        'stock_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
