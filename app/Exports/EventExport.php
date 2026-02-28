<?php

namespace App\Exports;

use App\Models\EventRegistration;
use Maatwebsite\Excel\Concerns\FromArray;

class EventExport implements FromArray
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function array(): array
    {
        $data = [];

        $regs = EventRegistration::where('event_id', $this->eventId)
            ->with('mahasiswa')
            ->get();

        $data[] = ['Nama', 'NIM', 'Prodi', 'Status'];

        foreach ($regs as $r) {
            $data[] = [
                $r->mahasiswa->nama_mahasiswa,
                $r->mahasiswa->nim,
                $r->mahasiswa->prodi,
                strtoupper($r->status)
            ];
        }

        return $data;
    }
}
