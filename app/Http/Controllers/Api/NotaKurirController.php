<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class NotaKurirController extends Controller
{
    public function getNotaData($transaksiID)
{
    $transaksi = Transaksi::with([
        'pembeli',
        'alamat',
        'detailTransaksis.produk',
        'penjadwalan.pegawai' // Eager load pegawai
    ])->findOrFail($transaksiID);

    // ✅ Filter penjadwalan bertipe "pengiriman"
            $penjadwalanPengiriman = collect($transaksi->penjadwalan)
            ->where('tipe', 'pengiriman')
            ->values();
        $transaksi->penjadwalan = $penjadwalanPengiriman;

    // Tambahkan hasil penjadwalan yang sudah difilter ke dalam objek transaksi
    $transaksi->penjadwalan = $penjadwalanPengiriman;

    // ✅ Hitung total harga dari semua produk
    $total = $transaksi->detailTransaksis->sum(function ($detail) {
        return $detail->produk->harga ?? 0;
    });

    // ✅ Cek poin loyalitas pembeli
    $poinPembeli = $transaksi->pembeli->poinLoyalitas ?? 0;

    // ✅ Konversi poin ke rupiah (1 poin = Rp100)
    $nilaiPerPoin = 100;
    $potongan = $poinPembeli * $nilaiPerPoin;

    // ✅ Hitung total bayar, pastikan tidak minus
    $totalBayar = max($total - $potongan, 0);

    // ✅ Hitung reward poin (0.8 poin per 10.000 rupiah dari totalBayar)
    $poin = floor($totalBayar / 10000 * 0.8);
    $bonus = $total > 500000 ? floor($poin * 0.2) : 0;
    $totalPoin = $poin + $bonus;

    // ✅ Sisipkan informasi perhitungan ke dalam properti tambahan
    $transaksi->perhitungan = [
        'total' => $total,
        'poinPembeli' => $poinPembeli,
        'nilaiPerPoin' => $nilaiPerPoin,
        'potongan' => $potongan,
        'poinDigunakan' => $poinPembeli,
        'totalBayar' => $totalBayar,
        'poinReward' => $poin,
        'bonus' => $bonus,
        'totalPoin' => $totalPoin,
    ];

    return response()->json($transaksi);
}


    public function daftarTransaksiSiap()
    {
        $transaksis = Transaksi::whereIn('status', ['siap dikirim', 'siap diambil'])
            ->with('pembeli')
            ->get();

        return response()->json($transaksis);
    }

}
