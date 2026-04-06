<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Supplier::get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'notelp',
            
            'Created At',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->user->name,
            $supplier->user->email,
            $supplier->notelp,
            $supplier->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
