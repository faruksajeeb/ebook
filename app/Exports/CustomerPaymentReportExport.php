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


class CustomerPaymentReportExport implements FromArray, WithHeadings, Responsable, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    use Exportable;
    public $data;
    public $title;
    public function __construct(object $objData,Object $customer,String $title,String $dateRange)
    {
        ini_set('max_execution_time', 30*60); // 30 min
        ini_set('memory_limit', '2048M');
        $this->customer = $customer;
        $this->data = $objData;
        $this->title =$title;
        $this->dateRange =$dateRange;
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue('A1', $this->title);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->mergeCells('A1:F2');
        $sheet->setCellValue('A3', "Customer Name");
        $sheet->setCellValue('B3', $this->customer->customer_name);
        $sheet->setCellValue('C3', "Customer Phone");
        $sheet->setCellValue('D3', $this->customer->customer_phone);
        $sheet->setCellValue('A4', "Customer Address");
        $sheet->setCellValue('B4', $this->customer->customer_address);
        $sheet->setCellValue('C4', "Date:");
        $sheet->setCellValue('D4',  $this->dateRange );
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 20,
                'color' => array('rgb' => '000000'),
            ],
            'alignment' => [
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            // 'borders' => [
            //     'outline' => [
            //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            //         'color' => ['argb' => '000000'],
            //     ],
            // ],
            'fill' => [
                // 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                // 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($styleArray);
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ]];
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyleArray);
    }
    public function headings(): array
    {
        return [
            [
                "Payment Date",
                "Customer Name",
                "Payment Method",
                "Payment Description",
                "Paid By",
                "Amount"
            ]
        ];
    }
    public function startCell(): string
    {
        return 'A5';
    }
    public function array(): array
    {
        $total = 0;
        $customArray = array();
        foreach ($this->data as $k=>$val) {
            $customArray[] = array(
                $val->payment_date,
                $val->customer->customer_name,
                $val->paymentmethod->name,
                $val->payment_description,
                $val->paid_by,
                $val->payment_amount,
            );
            $total +=  $val->payment_amount;
        }
         $data = $customArray;
        $data[] = ['Payment Total','','','','', $total];
        return $data;
    }
}

