{{-- Selector d'idioma català / castellà --}}
@php
    $current = app()->getLocale();
@endphp

<div class="language-switcher" style="display:inline-flex; align-items:center; gap:6px;">
    <span style="font-size:.75rem; color:#000;">[{{ $current }}]</span>

    <a href="{{ route('locale.switch', 'ca') }}"
       title="Català"
       style="text-decoration:none; padding:4px 8px; border-radius:8px; font-size:.85rem; font-weight:600;
              {{ $current === 'ca' ? 'background:#2563eb;color:#fff;' : 'color:#6b7280;' }}">
        CA
    </a>
    <span style="color:#d1d5db;">|</span>
    <a href="{{ route('locale.switch', 'es') }}"
       title="Castellano"
       style="text-decoration:none; padding:4px 8px; border-radius:8px; font-size:.85rem; font-weight:600;
              {{ $current === 'es' ? 'background:#2563eb;color:#fff;' : 'color:#6b7280;' }}">
        ES
    </a>
</div>