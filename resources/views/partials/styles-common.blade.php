<!-- Critical Anti-FOUC Guard (Zero flash of unstyled content) -->
<style id="anti-fouc-style">
    [x-cloak] { display: none !important; }
    html {
        visibility: visible;
        background-color: #F4F6F8;
    }
    body {
        visibility: hidden !important;
        opacity: 0 !important;
        margin: 0;
        background-color: #F4F6F8;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }
    body.app-loaded {
        visibility: visible !important;
        opacity: 1 !important;
        transition: opacity 0.1s ease-out;
    }
    /* Anti-FOUC Sidebar & Menu Reset */
    .sidebar-menu, .sidebar-menu li, .sidebar-menu-header, .sidebar-menu-item {
        list-style: none !important;
        list-style-type: none !important;
    }
</style>
<script>
    (function() {
        var revealed = false;
        function show() {
            if (revealed || !document.body) return;
            revealed = true;
            document.body.classList.add('app-loaded');
        }
        function allStylesLoaded() {
            var links = document.head.querySelectorAll('link[rel="stylesheet"]');
            for (var i = 0; i < links.length; i++) {
                try {
                    if (!links[i].sheet) return false;
                } catch (e) {
                    // Cross-origin stylesheet fallback
                }
            }
            return links.length > 0;
        }
        function tryReveal() {
            if (document.body && allStylesLoaded()) {
                show();
            }
        }
        document.addEventListener('DOMContentLoaded', tryReveal);
        window.addEventListener('load', show);
        document.addEventListener('livewire:navigated', function() {
            if (document.body) {
                document.body.classList.add('app-loaded');
            }
        });
        var poll = setInterval(function() {
            if (revealed) {
                clearInterval(poll);
                return;
            }
            tryReveal();
        }, 30);
        // Emergency fallback if an asset hangs
        setTimeout(function() {
            clearInterval(poll);
            show();
        }, 2500);
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

