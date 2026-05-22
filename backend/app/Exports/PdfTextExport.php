<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PdfTextExport implements FromArray, WithHeadings
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    // Define os cabeçalhos das colunas para o ficheiro Excel.
    public function headings(): array
    {
        return [
            'ITEM',
            'INFORMAÇÃO',
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }
}