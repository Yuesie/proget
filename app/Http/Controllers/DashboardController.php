<?php

namespace App\Http\Controllers;
use App\Models\Getpas;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Logika untuk Dashboard Admin
            $totalGetpas = Getpas::count();
            $pendingApprovals = Approval::where('status', 'PENDING')->count(); 
            // Menggunakan view 'dashboard' utama yang akan meng-include 'dashboard.admin'
            return view('dashboard', compact('totalGetpas', 'pendingApprovals'));
        }

        if ($user->role === 'approver') {
            // Logika UNTUK APPROVER POOL (Parallel Approval)
            
            // 1. Ambil semua peran yang dimiliki oleh user ini (e.g., ['SECURITY', 'HSSE'])
            // Asumsi: Anda memiliki relasi 'roles' pada model User
            $userRoles = $user->roles->pluck('role');
            
            // 2. Cari tugas approval yang statusnya POOL_PENDING 
            //    dan perannya cocok dengan salah satu peran user
            $pendingTasks = Approval::with('getpas.user')
                ->whereIn('role', $userRoles)
                ->where('status', 'POOL_PENDING')  // Mencari tugas di pool
                ->whereNull('approver_id')         // Memastikan tugas belum diklaim oleh approver lain
                ->get();

            // Menggunakan view 'dashboard' utama yang akan meng-include 'dashboard.approver'
            return view('dashboard', compact('pendingTasks', 'userRoles'));
        }
        
        // Default: Requester Dashboard
        $myGetpas = Getpas::where('user_id', $user->id)->latest()->get();
        
        // Menggunakan view 'dashboard' utama yang akan meng-include 'dashboard.requester'
        return view('dashboard', compact('myGetpas'));
    }
}