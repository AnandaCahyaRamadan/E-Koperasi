<!-- Critical Anti-FOUC Guard (Zero flash of unstyled content) -->
<style>
    [x-cloak] { display: none !important; }
    html { visibility: visible; }
    body {
        visibility: hidden;
        margin: 0;
        background-color: #F4F6F8;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }
    body.app-loaded {
        visibility: visible;
    }
    /* Anti-FOUC Sidebar & Menu Reset */
    .sidebar-menu, .sidebar-menu li, .sidebar-menu-header, .sidebar-menu-item {
        list-style: none !important;
        list-style-type: none !important;
    }
</style>
<script>
    (function() {
        function show() {
            if (document.body) {
                document.body.classList.add('app-loaded');
                document.body.style.visibility = 'visible';
            }
        }
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            show();
        } else {
            document.addEventListener('DOMContentLoaded', show);
            window.addEventListener('load', show);
        }
        document.addEventListener('livewire:navigated', show);
        // Safety timeout so page is never hidden longer than 150ms
        setTimeout(show, 150);
    })();
</script>
<noscript>
    <style>body { visibility: visible !important; }</style>
</noscript>

<!-- Bootstrap 5 CSS (Offline) -->
<link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<!-- Bootstrap Icons (Offline) -->
<link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
<!-- Toastr CSS (Offline) -->
<link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet">
<!-- SweetAlert2 CSS (Offline) -->
<link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<!-- Flatpickr CSS (Offline) -->
<link href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
<!-- DataTables BS5 CSS (Offline) -->
<link href="{{ asset('vendor/datatables/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
<!-- Custom Style -->
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">

