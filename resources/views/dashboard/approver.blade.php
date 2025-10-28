<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inbox Persetujuan (Tugas Anda)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Tugas Menunggu Tindakan Anda (Role: {{ $userRoles->implode(', ') }})</h3>
                
                @if($pendingTasks->isEmpty())
                    <p class="text-gray-600">Tidak ada Getpas yang menunggu persetujuan di pool Anda. Selamat!</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Getpas</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran Anda</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diajukan Oleh</th>
                                <th class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingTasks as $task)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $task->getpas->nomor_getpas }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $task->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $task->getpas->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <a href="{{ route('getpas.show', $task->getpas) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Tinjau</a>

                                        <form action="{{ route('approval.action', $task) }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" onclick="return confirm('Anda yakin menyetujui Getpas ini?')" class="text-green-600 hover:text-green-900">Approve</button>
                                        </form>

                                        <form action="{{ route('approval.action', $task) }}" method="POST" class="inline-block ml-3">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="comment" value="Alasan penolakan (TODO: Ganti dengan input dari Modal)">
                                            <button type="submit" onclick="return confirm('Anda yakin menolak Getpas ini?')" class="text-red-600 hover:text-red-900">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>