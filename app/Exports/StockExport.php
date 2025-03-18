<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $productName;

    public function __construct($productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $query = Stock::select('productName', 'remainingStock', 'uom', 'pricePerUnit', 'sellingPricePerUnit');

        if (!empty($this->productName)) {
            $query->where('productName', 'LIKE', '%' . $this->productName . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ["Nama Barang", "Sisa Barang", "Satuan", "Harga Per Unit", "Harga Jual Per Unit"];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    }
}
