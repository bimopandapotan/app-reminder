<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\TransaksiDomain;
use Carbon\Carbon;

class DomainController extends Controller
{
    public function index(Request $request)
{
    $tanggalFilter = $request->input('tanggal_filter');
    $search = $request->input('search');
    
    $domains = Domain::when($tanggalFilter, function ($query) use ($tanggalFilter) {
        $query->whereYear('tgl_expired', $tanggalFilter);
    })
    ->when($search, function ($query) use ($search) {
        $query->where('nama_domain', 'like', "%{$search}%")
              ->orWhere('status_berlangganan', 'like', "%{$search}%");
    })
    ->orderBy('tgl_expired', 'asc')
    ->get();

    $transaksi_domain = TransaksiDomain::all();

    $domains_expired = Domain::where('tgl_expired', '<=', now())->get();

    return view('domain_hosting.tb_domain', compact('domains', 'domains_expired', 'tanggalFilter','transaksi_domain'));
}


    public function store(Request $request)
    {   
        $request->validate([
            'nama_domain' => 'required|unique:domain,nama_domain',
            'tgl_expired' => 'required|date',
            'telepon' => 'required|string|max:15',
            'nama_perusahaan' => 'required',
            'nominal' => 'required|numeric', 
            'status_berlangganan' => 'required|in:Aktif,Tidak Aktif',  
        ]);

        Domain::create($request->all());
        return redirect()->route('domain.index')->with('success', 'Domain berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_domain' => 'required|unique:domain,nama_domain,' . $id,
            'tgl_expired' => 'required|date',
            'telepon' => 'required|string|max:15',
            'nama_perusahaan' => 'required',
            'nominal' => 'required|numeric',
            'status_berlangganan' => 'required|in:Aktif,Tidak Aktif',  
        ]);

        $domain = Domain::find($id);
        $domain->update($request->all());

        return redirect()->route('domain.index')->with('success', 'Domain berhasil diperbarui.');
    }
}
