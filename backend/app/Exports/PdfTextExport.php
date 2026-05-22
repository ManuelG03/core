<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;


class PdfTextExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function headings(): array
    {
        return [
            'Nº Identificação de Seg. Social',
            'Nome do Trabalhador',
            'Ano/Mês Ref.',
            "Nat Remun.",
            'Dias',
            'Valor',
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }


    public function styles($sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

         return [
            'A:G' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
    
}