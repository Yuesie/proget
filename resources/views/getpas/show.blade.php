// ... di dalam resources/views/getpas/show.blade.php

<h4 class="mt-6 text-lg font-semibold">Status Persetujuan Paralel:</h4>
<div class="border p-4 mt-2">
    @foreach($getpas->approvals as $approval)
        <div class="flex justify-between py-1 border-b">
            <span class="font-medium">{{ $approval->role }}</span>
            <span class="{{ $approval->status === 'APPROVED' ? 'text-green-600' : ($approval->status === 'REJECTED' ? 'text-red-600' : 'text-yellow-600') }}">
                {{ str_replace('_', ' ', $approval->status) }}
            </span>
            @if($approval->approver_id)
                <span class="text-sm text-gray-500">Oleh: {{ $approval->approver->name ?? 'N/A' }}</span>
            @endif
        </div>
    @endforeach
</div>