<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjadwalan;
use Carbon\Carbon;
use App\Models\Transaksi;

class PenjadwalanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaksiID' => 'required|exists:transaksis,transaksiID',
            'pegawaiID' => 'nullable|exists:pegawais,pegawaiID',
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
        ]);

        // Ambil data transaksi asli
        $transaksi = Transaksi::findOrFail($validated['transaksiID']);
        $tipe = $transaksi->tipe_transaksi === 'kirim' ? 'pengiriman' : 'pengambilan';

        // Cek apakah sudah dijadwalkan
        $sudahAda = Penjadwalan::where('transaksiID', $transaksi->transaksiID)
            ->where('tipe', $tipe)
            ->first();

        if ($sudahAda) {
            return response()->json([
                'message' => '⚠️ Transaksi ini sudah memiliki jadwal ' . $tipe
            ], 409);
        }

        // Validasi khusus pengiriman sore hari
        if (now()->isToday() && now()->hour >= 16 && $tipe === 'pengiriman') {
            return response()->json([
                'message' => '❌ Pengiriman tidak bisa dijadwalkan hari ini karena sudah lewat pukul 16:00.'
            ], 422);
        }

        // Buat penjadwalan
        $penjadwalan = Penjadwalan::create([
            'transaksiID' => $transaksi->transaksiID,
            'pegawaiID' => $validated['pegawaiID'],
            'tipe' => $tipe,
            'status' => 'diproses',
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
        ]);

        // Update status transaksi
        $transaksi->status = $tipe === 'pengiriman' ? 'siap dikirim' : 'siap diambil';
        $transaksi->save();

        return response()->json([
            'message' => "✅ Jadwal $tipe berhasil disimpan.",
            'penjadwalan' => $penjadwalan
        ]);
    }

    public function index()
    {
        $penjadwalans = \App\Models\Penjadwalan::with([
            'transaksi.pembeli',
            'transaksi.detailTransaksis.produk',
            'pegawai'
        ])
        ->orderBy('tanggal', 'desc')
        ->get();

        $result = $penjadwalans->map(function ($jadwal) {
            return [
                'penjadwalanID' => $jadwal->penjadwalanID,
                'tanggal' => $jadwal->tanggal,
                'waktu' => $jadwal->waktu,
                'tipe' => $jadwal->tipe,
                'status' => $jadwal->status,
                'namaKurir' => $jadwal->pegawai->nama ?? '-',
                'namaPembeli' => $jadwal->transaksi->pembeli->nama ?? '-',
                'alamat' => $jadwal->transaksi->pembeli->alamat ?? '-',
                'transaksiID' => $jadwal->transaksiID,
                'produk' => $jadwal->transaksi->detailTransaksis->map(function ($d) {
                    return $d->produk->namaProduk ?? 'Produk tidak diketahui';
                })

            ];
        });

        return response()->json($result);
    }

    public function updateStatus($id)
    {
        $penjadwalan = \App\Models\Penjadwalan::find($id);

        if (!$penjadwalan) {
            return response()->json(['message' => 'Penjadwalan tidak ditemukan'], 404);
        }

        if ($penjadwalan->status !== 'diproses') {
            return response()->json(['message' => 'Status tidak valid untuk ditandai kirim/ambil.'], 400);
        }

        try {
            // Cek tipe
            if ($penjadwalan->tipe === 'pengiriman') {
                $penjadwalan->status = 'berhasil dikirim';
            } elseif ($penjadwalan->tipe === 'pengambilan') {
                $penjadwalan->status = 'berhasil diambil'; // bedakan agar logis
            }

            $penjadwalan->save();

            return response()->json(['message' => 'Status berhasil ditandai', 'penjadwalan' => $penjadwalan]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function konfirmasiSelesai($id)
    {
        $penjadwalan = \App\Models\Penjadwalan::findOrFail($id);

        if ($penjadwalan->status !== 'dikirim') {
            return response()->json([
                'message' => 'Penjadwalan belum ditandai dikirim.'
            ], 400);
        }

        $penjadwalan->status = 'berhasil dikirim';
        $penjadwalan->save();

        return response()->json([
            'message' => 'Status penjadwalan berhasil dikonfirmasi sebagai berhasil dikirim.',
            'penjadwalan' => $penjadwalan
        ]);
    }

    public function konfirmasiDiterima($id)
    {
        $penjadwalan = \App\Models\Penjadwalan::with('transaksi')->findOrFail($id);

        // Validasi: hanya lanjut jika status sebelumnya sudah "berhasil dikirim" atau "berhasil diambil"
        if (!in_array($penjadwalan->status, ['berhasil dikirim', 'berhasil diambil'])) {
            return response()->json(['message' => 'Penjadwalan belum ditandai berhasil sebelumnya.'], 400);
        }

        // Ubah status penjadwalan dan transaksi menjadi "selesai"
        $penjadwalan->status = 'selesai';
        $penjadwalan->save();

        $penjadwalan->transaksi->status = 'selesai';
        $penjadwalan->transaksi->save();

        return response()->json([
            'message' => '✅ Transaksi selesai dikonfirmasi.',
            'penjadwalan' => $penjadwalan
        ]);
    }


}
