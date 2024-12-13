<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateModel implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        return collect([
            [
                'nom' => 'hermann test',
                'email' => 'hermannfomo4444@gmail.com',
                'role' => 'fullstack',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'nom',
            'email',            
            'role',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFFF', // Couleur de fond jaune
                        ],
                    ],
                ]);
                $sheet->setAutoFilter('A1:D1'); // Appliquer le filtre automatique sur la première ligne
                $sheet->freezePane('A2'); // Geler la première ligne pour qu'elle reste visible lors du défilement
            },
        ];
    }
}
