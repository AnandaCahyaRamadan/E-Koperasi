@php
    $perPageProp = $perPageModel ?? 'perPage';
    $searchProp = $searchModel ?? 'search';
    $searchPlaceholder = $placeholder ?? 'Ketik kata kunci...';
    $showSearchBox = $showSearch ?? true;
    $pageOptions = $options ?? [10, 25, 50, 100];
@endphp
<div class="row align-items-center mb-3 g-2">
    <div class="col-sm-6">
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted fw-semibold text-nowrap">Tampilkan</span>
            <select wire:model.live="{{ $perPageProp }}" class="form-select form-select-sm w-auto">
                @foreach($pageOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
            <span class="small text-muted fw-semibold text-nowrap">baris</span>
        </div>
    </div>
    @if($showSearchBox)
        <div class="col-sm-6">
            <div class="d-flex align-items-center justify-content-sm-end gap-2">
                <div class="input-group input-group-sm" style="max-width: 260px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="{{ $searchProp }}" class="form-control bg-white border-start-0" placeholder="{{ $searchPlaceholder }}">
                    @if(!empty($this->{$searchProp} ?? null))
                        <button type="button" class="btn btn-white border border-start-0 text-muted" wire:click="$set('{{ $searchProp }}', '')">
                            <i class="bi bi-x"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
