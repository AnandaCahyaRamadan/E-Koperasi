@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-3 w-100 border-top mt-3" aria-label="Navigasi Halaman">
        <div class="small text-muted text-center text-md-start">
            Menampilkan <span class="fw-bold text-dark">{{ $paginator->firstItem() ?? 0 }}</span> sampai <span class="fw-bold text-dark">{{ $paginator->lastItem() ?? 0 }}</span> dari total <span class="fw-bold text-dark">{{ $paginator->total() }}</span> data
        </div>

        <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap justify-content-center align-items-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link rounded-pill px-3 border" style="font-size: 0.8rem; background-color: #f8fafc; color: #94a3b8;">
                        <i class="bi bi-chevron-left me-1"></i> Prev
                    </span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" 
                            wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                            wire:loading.attr="disabled" 
                            class="page-link rounded-pill px-3 border shadow-none" 
                            style="font-size: 0.8rem; color: #1e293b; font-weight: 600;">
                        <i class="bi bi-chevron-left me-1"></i> Prev
                    </button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link rounded-circle border-0 px-1 text-muted" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link rounded-circle fw-bold shadow-sm" 
                                      style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background-color: #16834A !important; border-color: #16834A !important; color: #ffffff !important; font-size: 0.85rem;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" 
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                        class="page-link rounded-circle border text-dark fw-semibold" 
                                        style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; background-color: #ffffff;">
                                    {{ $page }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button type="button" 
                            wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                            wire:loading.attr="disabled" 
                            class="page-link rounded-pill px-3 border shadow-none" 
                            style="font-size: 0.8rem; color: #1e293b; font-weight: 600;">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link rounded-pill px-3 border" style="font-size: 0.8rem; background-color: #f8fafc; color: #94a3b8;">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

