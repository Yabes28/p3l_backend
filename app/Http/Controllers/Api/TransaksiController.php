<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembeli;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = \App\Models\Transaksi::with('pembeli')
            ->where('status', 'diproses')
            ->where('tipe_transaksi', 'kirim')
            ->orderBy('waktu_transaksi', 'desc')
            ->get()
            ->map(function ($trx) {
                return [
                    'idTransaksi' => $trx->transaksiID,
                    'namaPembeli' => $trx->pembeli->nama ?? 'Tanpa Nama',
                    'tanggalPembelian' => date('Y-m-d', strtotime($trx->waktu_transaksi)),
                    'jamPembelian' => date('H:i', strtotime($trx->waktu_transaksi)),
                    'alamat' => $trx->pembeli->alamat ?? '-',
                ];
            });

        return response()->json($transaksis);
    }



    public function store(Request $request)
    {
        $transaksi = Transaksi::create($request->all());
        return response()->json($transaksi, 201);
    }

    public function show($id)
    {
        return response()->json(Transaksi::with(['pembeli', 'penitip', 'alamat'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($request->all());
        return response()->json($transaksi);
    }

    public function destroy($id)
    {
        Transaksi::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
