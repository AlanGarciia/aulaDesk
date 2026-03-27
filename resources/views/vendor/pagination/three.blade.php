@if ($paginator->hasPages())
    <nav class="pagination-nav">
        {{-- Botón Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="disabled">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">‹</a>
        @endif

        {{-- Ventana de 3 páginas --}}
        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();

            $start = $current;
            $end = min($last, $current + 2);
        @endphp

        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $current)
                <span class="active">{{ $i }}</span>
            @else
                <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
            @endif
        @endfor

        {{-- Botón Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">›</a>
        @else
            <span class="disabled">›</span>
        @endif
    </nav>
@endif
