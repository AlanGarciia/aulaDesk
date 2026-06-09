<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1a1a1a; font-size: 12px; margin: 0; padding: 0; }

        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 { margin: 0; font-size: 22px; color: #0f1729; }
        .header .sub { color: #6b7280; font-size: 12px; margin-top: 4px; }
        .header .espai { color: #6b7280; font-size: 11px; margin-top: 2px; }

        .section-title {
            background: #f1f5f9;
            color: #0f1729;
            font-size: 13px;
            font-weight: bold;
            padding: 7px 10px;
            margin: 20px 0 10px;
            border-left: 4px solid #2563eb;
        }

        table.data { width: 100%; border-collapse: collapse; }
        table.data td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        table.data td.label {
            width: 35%;
            color: #6b7280;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        table.data td.value { color: #1a1a1a; }

        .tutor-card {
            border: 1px solid #e5e7eb;
            border-left: 4px solid #f5a524;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }
        .tutor-card .name { font-weight: bold; font-size: 13px; color: #0f1729; }
        .tutor-card .badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 6px;
        }
        .tutor-card .line { margin-top: 5px; color: #4b5563; font-size: 11px; }

        .chip {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            font-size: 11px;
            padding: 2px 9px;
            border-radius: 10px;
            margin-right: 4px;
        }
        .empty { color: #9ca3af; font-style: italic; }
        .footer { margin-top: 30px; color: #9ca3af; font-size: 9px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $alumne->nomFormatat($espai->format_nom) }}</h1>
        <div class="sub">{{ __('messages.idalu') }}: {{ $alumne->idalu }}</div>
        <div class="espai">{{ $espai->nom }}</div>
    </div>

    {{-- DADES DE L'ALUMNE --}}
    <div class="section-title">{{ __('messages.student_data') }}</div>
    <table class="data">
        <tr>
            <td class="label">{{ __('messages.name') }}</td>
            <td class="value">{{ $alumne->nom }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.surname1') }}</td>
            <td class="value">{{ $alumne->cognom1 ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.surname2') }}</td>
            <td class="value">{{ $alumne->cognom2 ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.birth_date') }}</td>
            <td class="value">
                @if($alumne->data_naixement)
                    {{ $alumne->data_naixement->format('d/m/Y') }}
                    ({{ __('messages.years_old', ['age' => $alumne->data_naixement->age]) }})
                @else — @endif
            </td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.dni') }}</td>
            <td class="value">{{ $alumne->dni ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.email') }}</td>
            <td class="value">{{ $alumne->correu ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.phone') }}</td>
            <td class="value">{{ $alumne->telefon ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('messages.assigned_groups') }}</td>
            <td class="value">
                @if($alumne->grups->count())
                    @foreach($alumne->grups as $g)
                        <span class="chip">{{ $g->nom }}</span>
                    @endforeach
                @else
                    <span class="empty">{{ __('messages.no_groups') }}</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- TUTORS --}}
    <div class="section-title">{{ __('messages.family_tutors') }}</div>

    @if($alumne->tutors->count())
        @foreach($alumne->tutors as $tutor)
            <div class="tutor-card">
                <span class="name">{{ $tutor->nom }} {{ $tutor->cognoms }}</span>
                <div class="line">{{ __('messages.email') }}: {{ $tutor->correu ?: '—' }}</div>
                <div class="line">{{ __('messages.phone') }}: {{ $tutor->telefon ?: '—' }}</div>
                <div class="line">{{ __('messages.dni') }}: {{ $tutor->dni ?: '—' }}</div>
            </div>
        @endforeach
    @else
        <p class="empty">{{ __('messages.no_tutors') }}</p>
    @endif

    <div class="footer">
        {{ $espai->nom }} — {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>