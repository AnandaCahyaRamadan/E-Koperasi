{{-- Dynamic Livewire & Session Toastr Bridge --}}
@if(session()->has('success'))
    <div wire:key="toast-success-{{ microtime(true) }}" style="display:none;" x-data x-init="window.showToast('success', '{{ addslashes(session('success')) }}', 'Berhasil')"></div>
@endif

@if(session()->has('error'))
    <div wire:key="toast-error-{{ microtime(true) }}" style="display:none;" x-data x-init="window.showToast('error', '{{ addslashes(session('error')) }}', 'Gagal')"></div>
@endif

@if(session()->has('warning'))
    <div wire:key="toast-warning-{{ microtime(true) }}" style="display:none;" x-data x-init="window.showToast('warning', '{{ addslashes(session('warning')) }}', 'Perhatian')"></div>
@endif

@if(session()->has('info'))
    <div wire:key="toast-info-{{ microtime(true) }}" style="display:none;" x-data x-init="window.showToast('info', '{{ addslashes(session('info')) }}', 'Informasi')"></div>
@endif

@if(session()->has('status'))
    <div wire:key="toast-status-{{ microtime(true) }}" style="display:none;" x-data x-init="window.showToast('info', '{{ addslashes(session('status')) }}', 'Informasi')"></div>
@endif

