<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApprovalController extends Controller
{
   public function handleAction(Request $request, Approval $approval)
    {
        $user = Auth::user();

        if ($user->role === 'approver') {
            // 1. Ambil semua peran yang dimiliki user ini (misal: SECURITY, HSSE)
            $userRoles = DB::table('role_user')
                            ->where('user_id', $user->id)
                            ->pluck('role');

            if ($userRoles->isEmpty()) {
                return view('dashboard.error', ['message' => 'Anda belum terdaftar dalam pool Approver manapun.']);
            }
            
            // 2. Ambil semua tugas Approval yang statusnya POOL_PENDING DAN memiliki role yang dimiliki user
            $pendingTasks = Approval::where('status', 'POOL_PENDING')
                                    ->whereIn('role', $userRoles)
                                    ->with('getpas')
                                    ->get();

            return view('dashboard.approver', compact('pendingTasks', 'userRoles'));
        }
        
        // 1. Otorisasi Pool: Pastikan user yang login memiliki peran yang sesuai (SECURITY/HSSE/TEKNIK)
        $isAuthorized = DB::table('role_user')
                            ->where('user_id', $user->id)
                            ->where('role', $approval->role)
                            ->exists();

        if (!$isAuthorized) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk peran ini.');
        }
        
        // 2. CEK: Apakah tugas ini sudah diselesaikan oleh orang lain di pool?
        if ($approval->status !== 'POOL_PENDING') {
            return back()->with('error', 'Tugas ini sudah diselesaikan oleh rekan Anda.');
        }

        $action = $request->input('action'); 
        
        if ($action === 'approve') {
            $approval->update([
                'status' => 'APPROVED', // Status berubah dari POOL_PENDING menjadi APPROVED
                'approver_id' => $user->id, // TUGAS DIKLAIM: ID APPROVER diisi oleh user yang pertama Approve
                'approved_at' => now(),
                // TODO: Simpan data Tanda Tangan Digital/Hash e-Signature di sini
            ]);
            
            $this->checkFinalStatus($approval->getpas); 
            $message = 'Getpas berhasil disetujui atas nama ' . $approval->role . '.';
        
        if ($action === 'approve') {
        // --- START PENAMBAHAN LOGIKA TANDA TANGAN SEDERHANA ---
        
        // 1. Generate String unik untuk Hash (Data Getpas + User ID + Waktu)
        $signatureString = $approval->getpas->nomor_getpas . 
                           $user->id . 
                           $approval->role . 
                           now()->toDateTimeString(); 
                           
        // 2. Hash String tersebut (Ini adalah e-Signature digital sederhana)
        $signatureHash = hash('sha256', $signatureString);

        $approval->update([
            'status' => 'APPROVED', 
            'approver_id' => $user->id,
            'approved_at' => now(),
            'signature_hash' => $signatureHash, // SIMPAN HASH TANDA TANGAN
        ]);
        
        // --- END PENAMBAHAN LOGIKA TANDA TANGAN SEDERHANA ---
        
        $this->checkFinalStatus($approval->getpas); 
        // ... (lanjutan kode)
    }
            
        } elseif ($action === 'reject') {
            $request->validate(['comment' => 'required|min:10']);
            
            $approval->update([
                'status' => 'REJECTED',
                'approver_id' => $user->id, // Tetap catat siapa yang me-reject
                'comment' => $request->input('comment'),
                'approved_at' => now(), // Waktu aksi
            ]);
            
            // Jika ada satu saja yang reject, seluruh Getpas ditolak!
            $approval->getpas->update(['status' => 'REJECTED']);
            // TODO: Kirim notifikasi REJECT ke Requester
            $message = 'Getpas berhasil ditolak dan dikembalikan ke requester.';
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    
    
    /**
     * Mengecek apakah semua approval sudah selesai (Final Approved)
     */
    private function checkFinalStatus(Getpas $getpas)
    {
        // Cek apakah ada yang masih POOL_PENDING atau REJECTED
        $pendingCount = $getpas->approvals()->where('status', 'POOL_PENDING')->count();
        $rejectedCount = $getpas->approvals()->where('status', 'REJECTED')->count();
        
        if ($rejectedCount > 0) {
             // Jika ada yang reject, status Getpas keseluruhan sudah dihandle di handleAction
             return; 
        }
        
        if ($pendingCount === 0) {
            // TIDAK ADA yang pending (POOL_PENDING), dan TIDAK ADA yang reject = SEMUA APPROVED
            $getpas->update(['status' => 'APPROVED_FINAL']);
            
            // TODO: Logika Tanda Tangan Final (Sistem membubuhkan semua tanda tangan ke PDF)
            // TODO: Kirim Notifikasi Final ke Requester
        }
    }
}
