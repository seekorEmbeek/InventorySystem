<?php

namespace App\Exports;

use App\Models\Purchasing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchasingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $dateFrom;
    protected $dateTo;
    protected $productName;

    public function __construct($dateFrom, $dateTo, $productName)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->productName = $productName;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Purchasing::select('supplierName', 'date', 'productName', 'smallQty', 'pricePerUnit', 'smallUom', 'smallPrice');

        if ($this->dateFrom && $this->dateTo) {
            $query->whereBetween('date', [$this->dateFrom, $this->dateTo]);
        }

        if ($this->productName) {
            $query->where('productName', 'LIKE', '%' . $this->productName . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ["Nama Supplier", "Tanggal", "Barang", "Qty", "Harga Per Unit", "Satuan", "Total Harga"];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    }
}
