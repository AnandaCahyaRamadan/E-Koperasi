@php
    $alignClass = $align ?? 'start';
    $justifyClass = match($alignClass) {
        'center' => 'justify-content-center',
        'end' => 'justify-content-end',
        default => 'justify-content-between',
    };
    $activeField = $this->sortField ?? ($sortField ?? '');
    $activeDirection = $this->sortDirection ?? ($sortDirection ?? 'asc');
@endphp
<th wire:click="sortBy('{{ $field }}')" class="sortable text-{{ $alignClass }}" style="cursor: pointer;">
    <div class="d-flex align-items-center {{ $justifyClass }} gap-1">
        <span>{{ $label }}</span>
        @if($activeField === $field)
            <i class="bi bi-arrow-{{ $activeDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
        @else
            <i class="bi bi-arrow-down-up text-muted opacity-25" style="font-size: 0.7rem;"></i>
        @endif
    </div>
</th>
