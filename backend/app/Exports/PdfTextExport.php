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
    $lastRow = count($this->rows) + 1;

    // Estilo para o cabeçalho
    $sheet->getRowDimension(1)->setRowHeight(38.25);
    $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    $sheet->getStyle('A1:F1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFFE066'); // amarelo claro

    // Estilo para linhas pares 
    for ($row = 2; $row <= $lastRow; $row++) {
        if ($row % 2 === 0) {
            $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9'); // cinza claro
        }
    }

    // Estilo de borda para todas as células
    $borderStyle = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color'       => ['argb' => 'FF000000'],
            ],
        ],
    ];

    // Aplica bordas a todas as células
    for ($row = 1; $row <= $lastRow; $row++) {
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderStyle);
    }

    // ── Formato numérico coluna Valor (F) ───────────────────────────
    $sheet->getStyle('F2:F' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');

    return [
        'A:F' => [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ],
    ];
}
    
}