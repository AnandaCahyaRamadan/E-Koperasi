@php
    $hasTotal = method_exists($paginator, 'total');
    $shouldShow = $hasTotal ? $paginator->total() > 0 : $paginator->hasPages();
@endphp
@if ($shouldShow)
    <nav class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 pt-3 pb-1 w-100" aria-label="Navigasi Halaman">
        <div class="text-muted text-center text-md-start" style="font-size: 0.8rem;">
            @if ($hasTotal)
                Menampilkan <span class="fw-semibold text-dark">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> data
            @else
                Halaman <span class="fw-semibold text-dark">{{ $paginator->currentPage() }}</span>
            @endif
        </div>

        <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap justify-content-center align-items-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border px-2" style="border-radius: 6px; font-size: 0.78rem; height: 30px; display: inline-flex; align-items: center; background-color: #f9fafb; color: #9ca3af; border-color: #e5e7eb;">
                        <i class="bi bi-chevron-left me-1"></i>Prev
                    </span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" 
                            wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                            wire:loading.attr="disabled" 
                            class="page-link border px-2 shadow-none" 
                            style="border-radius: 6px; font-size: 0.78rem; height: 30px; display: inline-flex; align-items: center; color: #374151; font-weight: 500; background-color: #ffffff; border-color: #e5e7eb;">
                        <i class="bi bi-chevron-left me-1"></i>Prev
                    </button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border-0 px-1 text-muted bg-transparent" style="height: 30px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link fw-semibold" 
                                      style="border-radius: 6px; min-width: 30px; height: 30px; padding: 0 8px; display: inline-flex; align-items: center; justify-content: center; background-color: #16834A !important; border-color: #16834A !important; color: #ffffff !important; font-size: 0.8rem;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" 
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                        class="page-link border text-dark fw-medium shadow-none" 
                                        style="border-radius: 6px; min-width: 30px; height: 30px; padding: 0 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; background-color: #ffffff; border-color: #e5e7eb; color: #374151;">
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
                            class="page-link border px-2 shadow-none" 
                            style="border-radius: 6px; font-size: 0.78rem; height: 30px; display: inline-flex; align-items: center; color: #374151; font-weight: 500; background-color: #ffffff; border-color: #e5e7eb;">
                        Next<i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border px-2" style="border-radius: 6px; font-size: 0.78rem; height: 30px; display: inline-flex; align-items: center; background-color: #f9fafb; color: #9ca3af; border-color: #e5e7eb;">
                        Next<i class="bi bi-chevron-right ms-1"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

