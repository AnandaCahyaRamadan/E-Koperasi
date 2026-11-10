<!-- jQuery (Offline) -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

<!-- Toastr JS (Offline) -->
<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

<!-- SweetAlert2 JS (Offline) -->
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

<!-- Flatpickr Datepicker JS (Offline) -->
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendor/flatpickr/id.js') }}"></script>

<!-- DataTables BS5 JS (Offline) -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap5.min.js') }}"></script>

<script>
    // 1. Configure Toastr Defaults
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "800",
            "timeOut": "3500",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    }

    // 2. SweetAlert2 Toast Mixin
    window.SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Helper functions for easy window-level calling
    window._shownToastKeys = window._shownToastKeys || new Set();
    window.showToast = function(type, message, title = '') {
        if (!message) return;
        const key = `${type}:${title}:${message}`;
        if (window._shownToastKeys.has(key)) return;
        window._shownToastKeys.add(key);
        setTimeout(() => window._shownToastKeys.delete(key), 1500);

        if (typeof toastr !== 'undefined' && toastr[type]) {
            toastr[type](message, title);
        } else if (typeof SwalToast !== 'undefined') {
            window.SwalToast.fire({
                icon: type,
                title: title ? `<strong>${title}</strong><br>${message}` : message
            });
        }
    };

    window.showAlert = function(type, title, text) {
        Swal.fire({
            icon: type,
            title: title,
            text: text,
            confirmButtonColor: '#16834A',
            confirmButtonText: 'OK'
        });
    };

    window.showConfirm = function(options, onConfirmed) {
        Swal.fire({
            title: options.title || 'Apakah Anda Yakin?',
            text: options.text || '',
            icon: options.icon || 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16834A',
            cancelButtonColor: '#6c757d',
            confirmButtonText: options.confirmButtonText || 'Ya, Lanjutkan',
            cancelButtonText: options.cancelButtonText || 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirmed === 'function') {
                onConfirmed();
            }
        });
    };

    // 3. Flatpickr Auto-Initializer with Livewire Model Support
    function initFlatpickrInstances() {
        if (typeof flatpickr === 'undefined') return;

        // Standard single datepickers
        document.querySelectorAll('.datepicker, [data-datepicker], input[type="date"]').forEach(function(el) {
            if (el._flatpickr) return; // avoid re-initialization

            const isAltInput = el.getAttribute('data-alt-input') !== 'false';
            const dateFormat = el.getAttribute('data-date-format') || 'Y-m-d';
            const altFormat = el.getAttribute('data-alt-format') || 'j F Y';

            flatpickr(el, {
                locale: 'id',
                dateFormat: dateFormat,
                altInput: isAltInput,
                altFormat: altFormat,
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    el.value = dateStr;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        // Range datepickers (e.g. for sales and inventory reports)
        document.querySelectorAll('.datepicker-range, [data-datepicker-range]').forEach(function(el) {
            if (el._flatpickr) return;

            flatpickr(el, {
                locale: 'id',
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                onChange: function(selectedDates, dateStr, instance) {
                    el.value = dateStr;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    }

    // 4. DataTables Auto-Initializer
    function initDataTablesInstances() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('.datatable, [data-datatable]').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        language: {
                            search: "Cari:",
                            searchPlaceholder: "Ketik kata kunci...",
                            lengthMenu: "Tampilkan _MENU_ baris",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            zeroRecords: "Data tidak ditemukan",
                            paginate: {
                                first: "Awal",
                                last: "Akhir",
                                next: '<i class="bi bi-chevron-right"></i>',
                                previous: '<i class="bi bi-chevron-left"></i>'
                            }
                        },
                        pageLength: 10,
                        responsive: true
                    });
                }
            });
        }
    }

    // 5. Run on DOM load and Livewire navigation
    document.addEventListener('DOMContentLoaded', function() {
        initFlatpickrInstances();
        initDataTablesInstances();
    });

    document.addEventListener('livewire:navigated', function() {
        initFlatpickrInstances();
        initDataTablesInstances();
    });

    document.addEventListener('livewire:initialized', function() {
        // Listen to Livewire dispatched events
        Livewire.on('swal:modal', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            Swal.fire({
                icon: payload.type || payload.icon || 'info',
                title: payload.title || '',
                text: payload.text || payload.message || '',
                confirmButtonColor: '#16834A',
                confirmButtonText: payload.confirmButtonText || 'OK'
            });
        });

        Livewire.on('swal:confirm', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            Swal.fire({
                title: payload.title || 'Konfirmasi Tindakan',
                text: payload.text || payload.message || '',
                icon: payload.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: payload.confirmColor || '#16834A',
                cancelButtonColor: '#6c757d',
                confirmButtonText: payload.confirmButtonText || 'Ya, Lanjutkan',
                cancelButtonText: payload.cancelButtonText || 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (payload.action) {
                        Livewire.dispatch(payload.action, payload.params || {});
                    }
                }
            });
        });

        Livewire.on('toast', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            const type = payload.type || payload.icon || 'success';
            const message = payload.message || payload.text || '';
            const title = payload.title || '';
            window.showToast(type, message, title);
        });

        Livewire.on('toastr', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            const type = payload.type || 'info';
            const message = payload.message || '';
            const title = payload.title || '';
            if (typeof toastr !== 'undefined' && toastr[type]) {
                toastr[type](message, title);
            }
        });

        // Native Livewire 3 Hook for wire:confirm -> SweetAlert2!
        Livewire.hook('directive.init', ({ el, directive }) => {
            if (directive.value === 'confirm') {
                let message = directive.expression || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                message = message.replaceAll('\\n', '\n');
                
                el.__livewire_confirm = (action, instead) => {
                    Swal.fire({
                        title: el.getAttribute('data-swal-title') || 'Konfirmasi Tindakan',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DC3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            action();
                        } else if (typeof instead === 'function') {
                            instead();
                        }
                    });
                };
                el.__livewire_confirm.__isSwal = true;
            }
        });
    });

    // Guard on click capture to ensure dynamic elements also use SweetAlert2
    document.addEventListener('click', function(e) {
        const el = e.target.closest('[wire\\:confirm]');
        if (el && el.__livewire_confirm && !el.__livewire_confirm.__isSwal) {
            const rawMsg = el.getAttribute('wire:confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const title = el.getAttribute('data-swal-title') || 'Konfirmasi Tindakan';
            el.__livewire_confirm = (action, instead) => {
                Swal.fire({
                    title: title,
                    text: rawMsg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DC3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        action();
                    } else if (typeof instead === 'function') {
                        instead();
                    }
                });
            };
            el.__livewire_confirm.__isSwal = true;
        }
    }, true);

    // Delegated click handler for SweetAlert2 Confirmation Dialogs (custom attributes)
    document.addEventListener('click', function(e) {
        const confirmBtn = e.target.closest('[data-swal-confirm]');
        if (confirmBtn) {
            e.preventDefault();
            e.stopPropagation();
            const title = confirmBtn.getAttribute('data-swal-title') || 'Apakah Anda Yakin?';
            const text = confirmBtn.getAttribute('data-swal-confirm') || confirmBtn.getAttribute('data-swal-text') || '';
            const confirmBtnText = confirmBtn.getAttribute('data-swal-btn') || 'Ya, Lanjutkan';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16834A',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (confirmBtn.tagName === 'A' && confirmBtn.href && !confirmBtn.href.startsWith('javascript:')) {
                        window.location.href = confirmBtn.href;
                    } else if (confirmBtn.type === 'submit' && confirmBtn.form) {
                        confirmBtn.form.submit();
                    }
                }
            });
        }
    });

    // 5. Flash message bridge from Laravel session to Toastr & SweetAlert
    @if(session('success'))
        (function() {
            const msg = "{{ addslashes(session('success')) }}";
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => window.showToast('success', msg, 'Berhasil'));
            } else {
                window.showToast('success', msg, 'Berhasil');
            }
        })();
    @endif

    @if(session('error'))
        (function() {
            const msg = "{{ addslashes(session('error')) }}";
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => window.showToast('error', msg, 'Gagal'));
            } else {
                window.showToast('error', msg, 'Gagal');
            }
        })();
    @endif

    @if(session('warning'))
        (function() {
            const msg = "{{ addslashes(session('warning')) }}";
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => window.showToast('warning', msg, 'Perhatian'));
            } else {
                window.showToast('warning', msg, 'Perhatian');
            }
        })();
    @endif

    @if(session('info'))
        (function() {
            const msg = "{{ addslashes(session('info')) }}";
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => window.showToast('info', msg, 'Informasi'));
            } else {
                window.showToast('info', msg, 'Informasi');
            }
        })();
    @endif

    @if(session('status'))
        (function() {
            const msg = "{{ addslashes(session('status')) }}";
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => window.showToast('info', msg, 'Informasi'));
            } else {
                window.showToast('info', msg, 'Informasi');
            }
        })();
    @endif

    @if($errors->any())
        (function() {
            @foreach($errors->all() as $err)
                const err_{{ $loop->index }} = "{{ addslashes($err) }}";
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => window.showToast('error', err_{{ $loop->index }}, 'Perhatian'));
                } else {
                    window.showToast('error', err_{{ $loop->index }}, 'Perhatian');
                }
            @endforeach
        })();
    @endif

    // Automatically clear persistent focus / outline after mouse clicks on buttons and links
    document.addEventListener('mouseup', function(e) {
        const el = e.target.closest('button, .btn, .cat-pill, .page-link, .btn-action-edit, .btn-action-delete, .btn-action-view, [role="button"]');
        if (el) {
            el.blur();
        }
    });
</script>
