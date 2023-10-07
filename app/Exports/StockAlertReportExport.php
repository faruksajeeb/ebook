<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Carbon\Carbon;


class StockAlertReportExport implements FromArray, WithHeadings, Responsable, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    use Exportable;
    public $data;
    public $title;
    public function __construct(object $objData,String $title)
    {
        ini_set('max_execution_time', 30*60); // 30 min
        ini_set('memory_limit', '2048M');
        $this->data = $objData;
        $this->title =$title;
        // $this->dateRange =$dateRange;
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue('A1', $this->title);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->mergeCells('A1:E2');
        // $sheet->setCellValue('C4', "Date:");
        // $sheet->setCellValue('D4',  $this->dateRange );
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 20,
                'color' => array('rgb' => '000000'),
            ],
            'alignment' => [
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::EORIZONTAL_RIGHT,
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::EORIZONTAL_CENTER,
            ],
            // 'borders' => [
            //     'outline' => [
            //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            //         'color' => ['argb' => '000000'],
            //     ],
            // ],
            'fill' => [
                // 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::EILL_GRADIENT_LINEAR,
                // 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::EILL_SOLID,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($styleArray);
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ]];
        $sheet->getStyle('A3:E3')->applyFromArray($headerStyleArray);
    }
    public function headings(): array
    {
        return [
            [
                "Book Name",
                "Publisher",
                "Author",
                "Category",
                "Stock Quantity"
            ]
        ];
    }
    public function startCell(): string
    {
        return 'A3';
    }
    public function array(): array
    {
        $customArray = array();
        foreach ($this->data as $k=>$val) {
            $customArray[] = array(
                $val->title,
                $val->publisher->publisher_name,
                $val->author->author_name,
                $val->category->category_name,
                $val->stock_quantity,
            );
        }
         $data = $customArray;
        $data[] = ['Stock Alert Total Items','','','', $this->data->count()];
        return $data;
    }
}

