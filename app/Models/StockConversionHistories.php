<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockConversionHistories extends Model
{
    //
    use HasFactory;
    use SoftDeletes;
    protected $table = 'stock_conversion_histories';

    protected $fillable = [
        'id',
        'stockId',
        'productId',
        'productName',
        'uomBefore',
        'qtyBefore',
        'pricePerUnitBefore',
        'uomAfter',
        'qtyAfter',
        'pricePerUnitAfter',
    ];
}
