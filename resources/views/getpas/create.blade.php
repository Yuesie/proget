<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Formulir Pengajuan Getpas Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('getpas.store') }}" method="POST">
                    @csrf
                    
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Informasi Umum</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nomor Getpas (Otomatis)</label>
                        <p class="mt-1 text-lg font-bold text-indigo-600">
                            {{ $nextNomorGetpas ?? 'Akan di-generate saat disimpan' }}
                        </p>
                    </div>

                    {{-- KODE YANG SUDAH DIREVISI --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Departemen Pengaju</label>
                        <input type="text" 
                               value="{{ Auth::user()->department ?? 'PERTAMINA PATRA NIAGA' }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                               readonly>
                    </div>
                    {{-- AKHIR REVISI --}}

                    <div class="mb-4">
                        <label for="perihal" class="block text-sm font-medium text-gray-700">Perihal (Tujuan Masuk/Keluar)</label>
                        <input type="text" name="perihal" id="perihal" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    
                    <div class="mb-6">
                        <label for="jenis_pengendalian" class="block text-sm font-medium text-gray-700">Jenis Pengendalian</label>
                        <select name="jenis_pengendalian" id="jenis_pengendalian" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Pilih Jenis</option>
                            <option value="Material Masuk & Keluar Kembali">Material Masuk & Keluar Kembali</option>
                            <option value="Material Masuk & Tidak Keluar Kembali">Material Masuk & Tidak Keluar Kembali</option>
                            <option value="Material Keluar & Tidak Masuk Kembali">Material Keluar & Tidak Masuk Kembali</option>
                        </select>
                    </div>

                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Daftar Barang / Material</h3>
                    
                    <div id="items-container">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="flex space-x-4 mb-4 items-end border-b pb-4">
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700">Nama Barang / Alat Kerja</label>
                                    <input type="text" name="items[{{ $i }}][nama_barang]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="w-1/6">
                                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                    <input type="text" name="items[{{ $i }}][satuan]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="w-1/6">
                                    <label class="block text-sm font-medium text-gray-700">Kuantitas</label>
                                    <input type="number" name="items[{{ $i }}][kuantitas]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <input type="text" name="items[{{ $i }}][keterangan]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="mt-6 pt-4 border-t">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Ajukan Getpas
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>