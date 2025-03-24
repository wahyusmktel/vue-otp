<?php

namespace App\Http\Controllers;

use App\Models\DataTanah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as FileStorage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataTanahExport;
use Barryvdh\DomPDF\Facade\Pdf;

class DataTanahController extends Controller
{
    public function index(Request $request)
    {
        $query = DataTanah::query();

        // Filter berdasarkan keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_lokasi', 'like', "%$search%")
                ->orWhere('status_tanah', 'like', "%$search%");
        }

        $dataTanah = $query->latest()->get();

        return inertia('Admin/DataTanah/Index', [
            'dataTanah' => $dataTanah,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lokasi' => 'required',
            'luas' => 'required|numeric',
            'status_tanah' => 'required',
            'keterangan' => 'nullable',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('dokumen')) {
            $data['dokumen'] = $request->file('dokumen')->store('dokumen-tanah', 'public');
        }

        DataTanah::create($data);
        return back()->with('toast', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, DataTanah $dataTanah)
    {
        $data = $request->validate([
            'nama_lokasi' => 'required',
            'luas' => 'required|numeric',
            'status_tanah' => 'required',
            'keterangan' => 'nullable',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Hapus 'dokumen' dari $data kalau tidak ada file baru diunggah
        if (!$request->hasFile('dokumen')) {
            unset($data['dokumen']);
        }

        // Kalau ada file baru
        if ($request->hasFile('dokumen')) {
            // Hapus dokumen lama
            if ($dataTanah->dokumen && FileStorage::disk('public')->exists($dataTanah->dokumen)) {
                FileStorage::disk('public')->delete($dataTanah->dokumen);
            }

            // Simpan file baru
            $data['dokumen'] = $request->file('dokumen')->store('dokumen-tanah', 'public');
        }

        $dataTanah->update($data);

        return back()->with('toast', 'Data berhasil diperbarui!');
    }

    public function destroy(DataTanah $dataTanah)
    {
        $dataTanah->delete();
        return back()->with('toast', 'Data berhasil dihapus!');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new DataTanahExport($request->search), 'data_tanah.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $dataTanah = DataTanah::query()
            ->when($request->search, function ($q, $search) {
                $q->where('nama_lokasi', 'like', "%$search%")
                ->orWhere('status_tanah', 'like', "%$search%");
            })->get();

        $pdf = Pdf::loadView('exports.data_tanah_pdf', ['dataTanah' => $dataTanah]);

        return $pdf->download('data_tanah.pdf');
    }
}
