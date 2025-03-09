<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchasing extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchasings';

    protected $fillable = [
        'id',
        'supplierName',
        'productId',
        'productName',
        'date',
        'purchaseQty',
        'purchaseUom',
        'purchasePrice',
        'purchaseStatus',
        'smallQty',
        'smallUom',
        'smallPrice',
        'pricePerUnit',
    ];
}
