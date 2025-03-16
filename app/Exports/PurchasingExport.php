<?php

namespace App\Exports;

use App\Models\Purchasing;
use Maatwebsite\Excel\Concerns\FromCollection;

class PurchasingExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Purchasing::all();
    }
}
