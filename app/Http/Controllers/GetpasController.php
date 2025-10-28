<?php

namespace App\Http\Controllers;
use App\Models\Getpas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Approval;
use Illuminate\Support\Facades\DB; 
use Barryvdh\DomPDF\Facade\Pdf;

class GetpasController extends Controller
{
    public function create()
{
    // --- SKEMA BARU: [NOMOR_URUT]/GPN/PPN/BLN/TAHUN ---
    
    // 1. Definisikan array untuk konversi bulan ke Romawi
    $romans = array(
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 
        6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 
        11 => 'XI', 12 => 'XII'
    );
    
    // Konstanta yang perlu Anda tentukan
    // GPN = Gate Pass Number
    // PPN = Kode Perusahaan/Lokasi (Bisa diganti dengan kode stasiun/terminal Anda, misal: TBBM)
    $prefixCode = 'GP-ITBJM'; 
    
    $currentMonth = now()->format('n');
    $currentYear = now()->format('Y');
    $romanMonth = $romans[$currentMonth] ?? $currentMonth;

    // 2. Cari Getpas terakhir yang dibuat pada TAHUN ini
    // Kita mencari nomor urut tertinggi. Kita asumsikan nomor urut selalu ada di depan.
    $lastGetpas = Getpas::whereYear('created_at', $currentYear)
                        ->latest()
                        ->first();

    $nextNumber = 1; // Default
    if ($lastGetpas) {
        // Asumsi formatnya adalah [NOMOR_URUT]/GPN/PPN/BLN/TAHUN
        // Kita ambil angka di bagian PALING DEPAN
        $parts = explode('/', $lastGetpas->nomor_getpas);
        
        // Ambil angka pertama dari string (nomor urut terakhir)
        if (isset($parts[0])) {
            $lastSequence = (int)$parts[0];
            $nextNumber = $lastSequence + 1; 
        }
    }
    
    // 3. Format nomor urut menjadi MINIMAL 6 digit (contoh: 000001)
    // Berdasarkan gambar, nomor urutnya terlihat memiliki 7 digit (1084284)
    $formattedSequence = str_pad($nextNumber, 7, '0', STR_PAD_LEFT); 

    // 4. Konstruksi Nomor Getpas Lengkap: NOMOR_URUT/GPN/PPN/BLN/TAHUN
    // Contoh: 0000001/GPN/PPN/X/2025
    $nextNomorGetpas = "{$formattedSequence}/{$prefixCode}/{$romanMonth}/{$currentYear}";

    // 5. Kirim nomor ke view
    return view('getpas.create', compact('nextNomorGetpas'));
}

    // app/Http/Controllers/GetpasController.php

// ... (fungsi create() tetap sama) ...

public function store(Request $request)
{
    // --- 1. VALIDASI BARU SESUAI NAMA INPUT DI FORM ---
    $validated = $request->validate([
        'perihal' => 'required|string|max:255',
        'jenis_pengendalian' => 'required|string', // Ini sesuai dengan select option Anda
        // Validasi untuk array barang (items)
        'items.*.nama_barang' => 'nullable|string|max:255',
        'items.*.satuan' => 'nullable|string|max:50',
        'items.*.kuantitas' => 'nullable|integer|min:0',
        'items.*.keterangan' => 'nullable|string|max:255',
    ]);

    // 2. LOGIKA PENOMORAN GETPAS SAAT INI (diambil dari logika create)
    $prefixCode = 'GPN/PPN'; 
    $currentYear = now()->format('Y');
    $currentMonth = now()->format('n');
    $romans = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII');
    $romanMonth = $romans[$currentMonth] ?? $currentMonth;

    $lastGetpas = Getpas::whereYear('created_at', $currentYear)->latest()->first();
    $nextNumber = 1;
    if ($lastGetpas) {
        $parts = explode('/', $lastGetpas->nomor_getpas);
        if (isset($parts[0])) {
            $lastSequence = (int)$parts[0];
            $nextNumber = $lastSequence + 1;
        }
    }

    $formattedSequence = str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    $finalNomorGetpas = "{$formattedSequence}/{$prefixCode}/{$romanMonth}/{$currentYear}";
    // ------------------------------------------------------------
    
    // 3. OLAH DATA BARANG (Hanya simpan item yang nama_barangnya terisi)
    $filteredItems = collect($request->input('items'))->filter(function ($item) {
        return !empty($item['nama_barang']);
    })->values()->all(); // Pastikan urutannya rapi

    if (empty($filteredItems)) {
        return back()->withInput()->with('error', 'Anda wajib mengisi minimal 1 item barang yang akan dibawa/dimasukkan.');
    }


    // 4. PEMBUATAN RECORD GETPAS
    $getpas = Getpas::create([
        'user_id' => Auth::id(),
        'nomor_getpas' => $finalNomorGetpas, 
        
        // Data dari validasi
        'perihal' => $validated['perihal'],
        'jenis_pengendalian' => $validated['jenis_pengendalian'], 
        
        // Data yang BELUM ADA di form, tapi ada di tabel (Hapus jika tidak ada di tabel)
        // Saya akan menggunakan department user sebagai placeholder untuk 'fungsi'
        'fungsi' => Auth::user()->department ?? 'PERTAMINA PATRA NIAGA',
        // 'pekerjaan' => 'Pekerjaan Default Jika Tidak Ada Input', 
        
        // Data barang yang sudah difilter
        'data_barang' => json_encode($filteredItems), 
    ]);

    // 5. LANJUTKAN KE PROSES APPROVAL
    return $this->submitForApproval($getpas);
}

    public function submitForApproval(Getpas $getpas)
    {
        // 1. Definisikan peran yang wajib menyetujui
        $requiredRoles = ['SECURITY', 'HSSE', 'TEKNIK'];
        $approvalData = [];
        $allApproverIds = collect(); // Koleksi untuk menyimpan semua ID Approver untuk notifikasi

        foreach ($requiredRoles as $role) {
            // 2. Cari SEMUA User yang memiliki peran tersebut
            $approverIds = DB::table('role_user')
                                ->where('role', $role)
                                ->pluck('user_id');

            if ($approverIds->isEmpty()) {
                return back()->with('error', "Tidak ada Approver terdaftar untuk peran {$role}. Proses gagal.");
            }
            
            // Tambahkan ID ke koleksi untuk notifikasi nanti
            $allApproverIds = $allApproverIds->merge($approverIds);

            // 3. Membuat SATU entri Approval per PERAN (menuju Pool Approver)
            $approvalData[] = [
                'getpas_id' => $getpas->id,
                'role' => $role,
                'status' => 'POOL_PENDING', // Status: Menunggu persetujuan dari pool
                'approved_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        Approval::insert($approvalData);
        $getpas->update(['status' => 'PENDING']);
        
        // TODO: Implementasi Notifikasi (Email/WhatsApp) ke SEMUA user di $allApproverIds
        
        return redirect()->route('dashboard')->with('success', 'Getpas diajukan. Menunggu persetujuan tim.');
    }

    public function printFinal(Getpas $getpas)
    {
        // 1. Otorisasi: Hanya User yang mengajukan atau Admin yang bisa mencetak
        if (Auth::id() !== $getpas->user_id && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Anda tidak berhak mencetak dokumen ini.');
        }

        // 2. Cek Status: Hanya boleh dicetak jika statusnya APPROVED_FINAL
        if ($getpas->status !== 'APPROVED_FINAL') {
            return back()->with('error', 'Dokumen belum selesai disetujui.');
        }

        // 3. Load View PDF dan Buat PDF
        $pdf = Pdf::loadView('getpas.pdf_template', compact('getpas'));

        // 4. Return PDF untuk diunduh (atau ditampilkan di browser)
        return $pdf->download('Getpas-' . $getpas->nomor_getpas . '.pdf'); 
    }
}