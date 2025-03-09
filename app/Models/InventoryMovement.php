<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryMovement extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'inventory_movements';

    protected $fillable = [
        'id',
        'productId',
        'productName',
        'uom',
        'qty',
        'movementType',
        'date',
        'pricePerUnit',
        'totalPrice',
        'purchase_id',
        'sale_id',
    ];
}
