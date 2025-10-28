<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Aplikasi E-Getpas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- PENGATURAN TAMPILAN BERDASARKAN ROLE --}}

                    @if (Auth::user()->role === 'requester')
                        {{-- Tampilan untuk User / Requester --}}
                        @include('dashboard.requester')
                    @elseif (Auth::user()->role === 'approver')
                        {{-- Tampilan untuk Approver (Security, HSSE, Teknik) --}}
                        @include('dashboard.approver')
                    @elseif (Auth::user()->role === 'admin')
                        {{-- Tampilan untuk Admin --}}
                        **@include('dashboard.admin')**
                    @else
                        {{ __("Role Anda tidak dikenali. Silakan hubungi Admin.") }}
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>