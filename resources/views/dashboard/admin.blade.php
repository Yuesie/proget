{{-- resources/views/dashboard/admin.blade.php --}}

<h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
    Dashboard Administrator Sistem E-Getpas
</h3>

<p class="mt-4 text-lg text-gray-700 dark:text-gray-300">
    Selamat datang, {{ Auth::user()->name }}. Anda berada di panel manajemen sistem.
</p>

<div class="mt-6 p-5 border border-indigo-200 rounded-lg bg-indigo-50 dark:bg-gray-700/50">
    <h4 class="font-semibold text-xl text-indigo-800 dark:text-indigo-300">Akses Cepat & Statistik:</h4>
    <ul class="list-disc list-inside ml-4 mt-3 space-y-1">
        <li>Total Pengajuan Getpas (Seluruh Role): <span class="font-bold">... (Contoh: 150)</span></li>
        <li>Pengguna Aktif: <span class="font-bold">... (Contoh: 45)</span></li>
        <li>Link ke Manajemen Pengguna: <a href="/admin/users" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Kelola User & Role</a></li>
        <li>Link ke Audit Log: <a href="/admin/audit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Lihat Aktivitas Sistem</a></li>
    </ul>
</div>