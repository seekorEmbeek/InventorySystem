<?php

namespace App\Exports;

use App\Models\Sales;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $dateFrom;
    protected $dateTo;
    protected $productName;
    protected $status;

    public function __construct($dateFrom, $dateTo, $productName, $status)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->productName = $productName;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $sales = Sales::with('items')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereHas('items', function ($query) {
                if (!empty($this->productName)) {
                    $query->where('productName', $this->productName);
                }

                if (!empty($this->status)) {
                    $query->where('status', $this->status);
                }
            })
            ->get();

        $data = [];
        $totalProfit = 0; // Track total profit

        foreach ($sales as $sale) {
            $rowSpan = count($sale->items);
            foreach ($sale->items as $index => $item) {
                // Calculate profit only for "LUNAS" status
                $profit = ($sale->status == "LUNAS") ? ($item->sellingPricePerUnit - $item->pricePerUnit) * $item->qty : 0;
                $totalProfit += $profit; // Add profit to total

                $data[] = [
                    'buyerName' => $index == 0 ? $sale->buyerName : '',
                    'date' => $index == 0 ? $sale->date : '',
                    'productName' => $item->productName,
                    'qty' => $item->qty,
                    'uom' => $item->uom,
                    'pricePerUnit' => number_format($item->pricePerUnit, 2, ',', '.'),
                    'sellingPricePerUnit' => number_format($item->sellingPricePerUnit, 2, ',', '.'),
                    'totalPricePerItem' => number_format($item->totalSellingPrice, 2, ',', '.'),
                    'totalPrice' => $index == 0 ? number_format($sale->totalPrice, 2, ',', '.') : '',
                    'totalPayment' => $index == 0 ? number_format($sale->totalPayment, 2, ',', '.') : '',
                    'status' => $index == 0 ? $sale->status : '',
                    'totalProfit' => number_format($profit, 2, ',', '.'),
                ];
            }
        }

        // Append total profit summary row
        $data[] = [
            'buyerName' => '',
            'date' => '',
            'productName' => '',
            'qty' => '',
            'uom' => '',
            'pricePerUnit' => '',
            'sellingPricePerUnit' => '',
            'totalPricePerItem' => '',
            'totalPrice' => '',
            'totalPayment' => '',
            'status' => 'TOTAL PROFIT:',
            'totalProfit' => number_format($totalProfit, 2, ',', '.'),
        ];


        return $data;
    }
    public function headings(): array
    {
        return [
            'Nama Pembeli',
            'Tgl',
            'Barang',
            'Qty',
            'Satuan',
            'Harga Per Unit',
            'Harga Jual Per Unit',
            'Total Harga Per Barang',
            'Total Harga',
            'Total Pembayaran',
            'Status',
            'Total Profit'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Bold Total Profit Summary Row
        $lastRow = count($this->array()) + 1; // Find last row
        $sheet->getStyle("K{$lastRow}:L{$lastRow}")->getFont()->setBold(true);
    }
}
