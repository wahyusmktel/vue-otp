<?php

namespace App\Exports;

use App\Models\DataTanah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DataTanahExport implements FromView
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function view(): View
    {
        $query = DataTanah::query();

        if ($this->search) {
            $query->where('nama_lokasi', 'like', "%{$this->search}%")
                  ->orWhere('status_tanah', 'like', "%{$this->search}%");
        }

        return view('exports.data_tanah_excel', [
            'dataTanah' => $query->get()
        ]);
    }
}
