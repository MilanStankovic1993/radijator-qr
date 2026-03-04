<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QrLabelsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Collection $rows;

    /**
     * ['db_key' => 'Header label', ...]
     */
    protected array $columns;

    public function __construct(Collection $rows, array $columns)
    {
        $this->rows = $rows;
        $this->columns = $columns;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return array_values($this->columns);
    }

    public function map($row): array
    {
        $out = [];

        foreach (array_keys($this->columns) as $key) {
            $value = data_get($row, $key);

            // malo lepši prikaz za datume
            if ($value instanceof \Carbon\CarbonInterface) {
                $value = $value->format('d.m.Y H:i');
            }

            $out[] = $value;
        }

        return $out;
    }
}