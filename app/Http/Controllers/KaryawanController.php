<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusKaryawan = $request->input('select');

        $karyawan = Karyawan::when($search, function ($query) use ($search) {
            $search = strtolower($search);
            return $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('jenis_kelamin', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%")
                ->orWhere('divisi', 'like', "%{$search}%")
                ->orWhere('jabatan', 'like', "%{$search}%");

                if ($search === 'cuti') {
                    $q->orWhere('status_cuti', 1); 
                } elseif ($search === 'tidak cuti') {
                    $q->orWhere('status_cuti', 0); 
                }
            });
        })
        ->when($statusKaryawan, function ($query) use ($statusKaryawan) {
            return $query->where('status_karyawan', $statusKaryawan);
        })
        ->where('status_karyawan','aktif')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('inventory_motor.tb_karyawan', compact('karyawan', 'search', 'statusKaryawan'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nik' => 'required|numeric',
            'jenis_kelamin' => 'required',
            'tgl_masuk' => 'required|date',
            'tgl_lahir' => 'required|date',
            'tempat_lahir' => 'required',
            'no_hp' => 'required|numeric',
            'agama' => 'required',
            'divisi' => 'required',
            'jabatan' => 'required',
            'alamat' => 'required',
            'status_karyawan' => 'required',
            'status_cuti' => 'nullable|boolean',
            'foto_karyawan' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif',
            'size_baju' => 'required|in:xs,s,m,l,xl,xxl,xxxl',
            'bpjs_ketenagakerjaan' => 'required|in:Ada,Tidak Ada',
            'bpjs_kesehatan' => 'required|in:Ada,Tidak Ada',
        ]);

        $data = $request->only('nama', 'nik', 'jenis_kelamin', 'tgl_masuk','tgl_lahir','tempat_lahir','no_hp','agama','divisi','jabatan','alamat','status_karyawan','status_cuti','size_baju','bpjs_kesehatan','bpjs_ketenagakerjaan');

        if (!Storage::exists('public/karyawan')) {
            Storage::makeDirectory('public/karyawan');
        }

        if (!Storage::exists('public/ktp')) {
            Storage::makeDirectory('public/ktp');
        }

        if ($request->hasFile('foto_karyawan')) {
            $file = $request->file('foto_karyawan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/karyawan', $filename);
            $data['foto_karyawan'] = $filename;
        }

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/ktp', $filename);
            $data['foto_ktp'] = $filename;
        }

        Karyawan::create($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nik' => 'required|numeric',
            'jenis_kelamin' => 'required',
            'tgl_masuk' => 'required|date',
            'tgl_lahir' => 'required|date',
            'tempat_lahir' => 'required',
            'no_hp' => 'required|numeric',
            'agama' => 'required',
            'divisi' => 'required',
            'jabatan' => 'required',
            'alamat' => 'required',
            'status_karyawan' => 'required',
            'status_cuti' => 'nullable|boolean',
            'foto_karyawan' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'size_baju' => 'required|in:xs,s,m,l,xl,xxl,xxxl',
            'bpjs_ketenagakerjaan' => 'required|in:Ada,Tidak Ada',
            'bpjs_kesehatan' => 'required|in:Ada,Tidak Ada',
        ]);

        $karyawan = Karyawan::find($id);
        $data = $request->only('nama', 'nik', 'jenis_kelamin', 'tgl_masuk','tgl_lahir','tempat_lahir','no_hp','agama','divisi','jabatan','alamat','status_karyawan','status_cuti','size_baju','bpjs_kesehatan','bpjs_ketenagakerjaan');


        if ($request->hasFile('foto_karyawan')) {
            if ($karyawan->foto_karyawan) {
                Storage::delete('public/karyawan/' . $karyawan->foto_karyawan);
            }
    
            $file = $request->file('foto_karyawan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/karyawan', $filename);
            $data['foto_karyawan'] = $filename;
        }
    
        if ($request->hasFile('foto_ktp')) {
            if ($karyawan->foto_ktp) {
                Storage::delete('public/ktp/' . $karyawan->foto_ktp);
            }
    
            $file = $request->file('foto_ktp');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/ktp', $filename);
            $data['foto_ktp'] = $filename;
        }
    
        $karyawan->update($data);
    
        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui.');
    }


    public function destroy($id)
{
    $karyawan = Karyawan::findOrFail($id);

    if ($karyawan->foto_karyawan) {
        Storage::delete('public/karyawan/' . $karyawan->foto_karyawan);
    }

    if ($karyawan->foto_ktp) {
        Storage::delete('public/ktp/' . $karyawan->foto_ktp);
    }

    $karyawan->delete();

    return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
}


}