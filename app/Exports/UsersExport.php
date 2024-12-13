<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        return User::with('role')->get()->map(function ($user) {
            return [
                'name'=> $user->name,
                'email'=> $user->email,
                'role'=> $user->role->roleTitle,
                ];
    });
    }

    public function headings(): array {
         return [ 
            'nom', 
            'email', 
            'role', 
        ];
         }

         public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
         {
            return [
                1=> ['font'=>['bold'=>true]],
            ];
         }
         public function registerEvents(): array
         {
            return [
                AfterSheet::class=>function(AfterSheet $event) {
                    $sheet = $event->sheet->getDelegate();
                    $sheet->getStyle('A1:C1')->applyFromArray([
                         'font' => [ 'bold' => true, ], 
                         'fill' => [ 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                          'startColor' => [ 'rgb' => 'FFFF', 
                        ], 
                    ], 
                ]);
                    $sheet->setAutoFilter('A1:C1');          
                 },
            ];
        }
}
