<div class="mt-4">
    <h3 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
        Pengajuan Getpas Saya (Requester)
    </h3>
    
    <p class="mb-4 text-gray-700 dark:text-gray-300">
        Selamat datang kembali! Ini adalah area Anda untuk mengajukan dan memantau status Getpas.
    </p>

    {{-- Tombol Ajukan Getpas Baru --}}
    <p class="mt-4 mb-4">
    <a href="{{ route('getpas.create') }}" class="text-white bg-blue-600 hover:bg-blue-700 font-bold py-2 px-4 rounded">
        Ajukan Getpas Baru
    </a>
</p>

    {{-- TABEL DAFTAR GETPAS --}}
    @if ($myGetpas->isEmpty())
        <div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded" role="alert">
            Belum ada Getpas yang Anda ajukan.
        </div>
    @else
        <h4 class="text-xl font-medium mb-3 mt-8">Daftar Pengajuan (Total: {{ $myGetpas->count() }})</h4>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 shadow-lg rounded-lg">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nomor Getpas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Perihal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($myGetpas as $getpas)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $getpas->nomor_getpas }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $getpas->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $getpas->perihal }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = [
                                        'PENDING' => 'bg-yellow-100 text-yellow-800',
                                        'APPROVED_FINAL' => 'bg-green-100 text-green-800',
                                        'REJECTED' => 'bg-red-100 text-red-800',
                                        // Tambahkan status lain jika ada
                                    ][$getpas->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $getpas->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- Link ke detail Getpas --}}
                                <a href="{{ route('getpas.show', $getpas->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
