<?php

namespace App\Http\Controllers;

use App\Models\Bts;
use App\Models\Domain;
use Carbon\Carbon;
use App\Models\Motor;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
{
    $query = Reminder::query();

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

    return view('reminders.index', compact('reminders','todayReminders'));
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
        $motors_expiring = Motor::with('karyawan')->whereDate('tanggal_pajak', '<=', Carbon::now()->addDays(30))->get();
        $expired_status = [];
        if($motors_expiring->isNotEmpty()){
            foreach($motors_expiring as $motor){
                $days_left = round(Carbon::now('Asia/Jakarta')->startOfDay()->diffInDays($motor->tanggal_pajak, false));

                if($days_left > 0){
                    $expired_status[$motor->id] = 'soon';
                } elseif($days_left == 0){
                    $expired_status[$motor->id] = 'today';
                } else {
                   $expired_status[$motor->id] = 'passed';
                }
            }
        }

        $bts = Bts::all();
        $domain = Domain::all();

        return response()->json([
            'motor' => $motor,
            'bts' => $bts,
            'domain' => $domain,
        ]);
    }
}