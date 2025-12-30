<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bts;
use App\Models\Motor;
use App\Models\Domain;
use App\Models\Telepon;
use App\Models\Reminder;
use Illuminate\Http\Request;
use App\Models\JenisPembayaran;

class ReminderController extends Controller
{
    public function index(Request $request)
{
    $query = Reminder::query();
    $telps = Telepon::query()->get();

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('tentang_reminder', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('status_pelaksanaan', 'like', "%{$search}%")
              ->orWhere('tanggal_reminder', 'like', "%{$search}%");
        });
    }

    $reminders = $query->get();

    $todayReminders = Reminder::where('status', 'aktif')->where('tanggal_reminder', '<=', Carbon::today()->format('Y-m-d'))->get();

    return view('reminders.index', compact('reminders','todayReminders','telps'));
}




    public function store(Request $request)
    {
        $request->validate([
            'tentang_reminder' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal_reminder' => 'required|date',
            'status' => 'required|in:aktif,tidak-aktif',
            'status_pelaksanaan' => 'required|in:sudah,belum',
        ]);

        Reminder::create($request->all());

        return redirect()->route('reminders.index')->with('success', 'Reminder berhasil ditambahkan');
    }




    public function update(Request $request, $id)
    {
        $request->validate([
            'tentang_reminder' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal_reminder' => 'required|date',
            'status' => 'required|in:aktif,tidak-aktif',
            'status_pelaksanaan' => 'required|in:sudah,belum',
        ]);

        $reminders = Reminder::find($id);
        $reminders->update($request->all());

        return redirect()->route('reminders.index')->with('success', 'Reminder berhasil diperbarui');
    }


    public function destroy($id)
    {
        $reminder = Reminder::find($id);
        $reminder->delete();

        return redirect()->route('reminders.index')->with('success', 'Reminder berhasil dihapus');
    }

    public function getReminderTask()
    {
        $now = Carbon::now('Asia/Jakarta')->startOfDay();

        $motors = Motor::with('karyawan')
            ->whereDate('tanggal_pajak', '<=', now()->addDays(30))
            ->get()
            ->map(function ($motor) use ($now) {
                $days_left = $now->diffInDays($motor->tanggal_pajak, false);

                $motor->expired_status = $days_left > 0
                    ? 'soon'
                    : ($days_left === 0 ? 'today' : 'passed');

                return $motor;
            });

        $bts = Bts::whereDate('jatuh_tempo', '<=', now()->addDays(30))
            ->where('status', '!=', 'Tidak Aktif')
            ->get()
            ->map(function ($bts) use ($now) {
                $days_left = $now->diffInDays($bts->jatuh_tempo, false);

                $bts->expired_status = $days_left > 0
                    ? 'soon'
                    : ($days_left === 0 ? 'today' : 'passed');

                return $bts;
            });

        $domains = Domain::whereDate('tgl_expired', '<=', now()->addDays(30))
            ->where('status_berlangganan', '!=', 'Tidak Aktif')
            ->get()
            ->map(function ($domain) use ($now) {
                $days_left = $now->diffInDays($domain->tgl_expired, false);

                $domain->expired_status = $days_left > 0
                    ? 'soon'
                    : ($days_left === 0 ? 'today' : 'passed');

                return $domain;
            });

        $jenispembayarans = JenisPembayaran::with('telepon')
            ->whereDate('tanggal_jatuh_tempo', '<=', now()->addDays(30))
            ->where('status', '!=', 'tidak-aktif')
            ->get()
            ->map(function ($jenispembayaran) use ($now) {
                $days_left = $now->diffInDays($jenispembayaran->tanggal_jatuh_tempo, false);

                $jenispembayaran->expired_status = $days_left > 0
                    ? 'soon'
                    : ($days_left === 0 ? 'today' : 'passed');

                return $jenispembayaran;
            });

        $reminders = Reminder::with('telepon')
            ->where('status', 'aktif')
            ->where('status_pelaksanaan', 'belum')
            ->whereDate('tanggal_reminder', '<=', now()->addDays(30))
            ->get()
            ->map(function ($reminder) use ($now) {
                $days_left = $now->diffInDays($reminder->tanggal_reminder, false);

                $reminder->expired_status = $days_left > 0
                    ? 'soon'
                    : ($days_left === 0 ? 'today' : 'passed');

                return $reminder;
            });

        return response()->json([
            'motor'  => $motors,
            'bts'    => $bts,
            'domain' => $domains,
            'jenispembayaran' => $jenispembayarans,
            'reminder' => $reminders,
        ]);
    }
}