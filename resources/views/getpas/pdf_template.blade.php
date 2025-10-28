<!DOCTYPE html>
<html>
<head>
    <title>GATE PASS {{ $getpas->nomor_getpas }}</title>
    <style>
        /* CSS untuk meniru tata letak tabel Getpas fisik */
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 10px; }
        .signature-area td { height: 60px; vertical-align: bottom; }
        .hash-text { font-size: 7px; color: #555; word-break: break-all; }
    </style>
</head>
<body>
    <table class="signature-area">
        <tr>
            @foreach(['SECURITY', 'HSSE', 'TEKNIK'] as $role)
                @php
                    $approval = $getpas->approvals->where('role', $role)->first();
                @endphp
                <td>
                    <p>Disetujui oleh: <b>{{ $role }}</b></p>
                    @if($approval && $approval->status === 'APPROVED')
                        <p style="margin-top: 5px;">{{ $approval->approver->name ?? 'N/A' }}</p>
                        <p class="hash-text">Waktu: {{ $approval->approved_at->format('Y-m-d H:i:s') }}</p>
                        <p class="hash-text">Hash: {{ substr($approval->signature_hash, 0, 15) }}...</p>
                    @else
                        <p>(Belum disetujui)</p>
                    @endif
                </td>
            @endforeach
        </tr>
    </table>
    <p class="hash-text mt-4">Dokumen ini disahkan secara digital oleh sistem E-Getpas.</p>
</body>
</html>