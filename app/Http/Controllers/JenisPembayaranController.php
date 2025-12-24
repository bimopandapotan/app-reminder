<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Telepon;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Models\JenisPembayaran;

class JenisPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $tanggalFilter = $request->input('tanggal_filter');
        $search = $request->input('search');

        $query = JenisPembayaran::query();
        $telps = Telepon::query()->get();

        if ($tanggalFilter) {
            $query->whereYear('tanggal_jatuh_tempo', $tanggalFilter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis_pembayaran', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        $pembayarans_expired = JenisPembayaran::where('tanggal_jatuh_tempo', '<=', Carbon::now()->addDays(7))->orderBy('tanggal_jatuh_tempo')->get();

        return view('pembayaran_bulanan.tb_jenispembayaran', compact('pembayarans', 'pembayarans_expired', 'tanggalFilter', 'telps'));
}


    public function store(Request $request)
    {
        $request->validate([
            'jenis_pembayaran' => 'required|string|max:255',
            'status' => 'required|in:aktif,tidak-aktif',
            'telepon_id' => 'required|exists:telepons,id',
        ]);

        JenisPembayaran::create($request->all());

        return redirect()->route('jenis_pembayaran.index')->with('success', 'Jenis Pembayaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_pembayaran' => 'required|string|max:255',
            'status' => 'required|in:aktif,tidak-aktif',
            'telepon_id' => 'required|exists:telepons,id',
        ]);

        $pembayaran = JenisPembayaran::find($id);
        $pembayaran->update($request->all());

        return redirect()->route('jenis_pembayaran.index')->with('success', 'Jenis Pembayaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pembayaran = JenisPembayaran::find($id);
        $pembayaran->delete();

        return redirect()->route('jenis_pembayaran.index')->with('success', 'Jenis Pembayaran berhasil dihapus');
    }
}