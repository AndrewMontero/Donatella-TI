<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px; }
        body { color: #352d27; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        h1 { margin: 0; color: #2d551b; font-size: 20px; }
        .report-header { width: 100%; margin-bottom: 4px; border-collapse: collapse; }
        .report-header td { padding: 0; vertical-align: middle; }
        .report-logo-cell { width: 155px; text-align: right; }
        .report-logo { width: 140px; height: auto; }
        .subtitle { margin: 4px 0 14px; color: #7f7465; font-size: 10px; }
        .summary { width: 100%; margin-bottom: 16px; border-spacing: 8px 0; }
        .summary td { width: 25%; padding: 10px 12px; border: 1px solid #eadcc9; border-radius: 6px; background: #fff8ed; }
        .summary-label { color: #7f7465; font-size: 8px; text-transform: uppercase; }
        .summary-value { margin-top: 3px; font-size: 17px; font-weight: bold; }
        table.details { width: 100%; border-collapse: collapse; }
        .details th { padding: 7px; background: #4d6b4d; color: #fff; text-align: left; font-size: 8px; }
        .details td { padding: 6px 7px; border-bottom: 1px solid #eadcc9; vertical-align: top; }
        .details tr:nth-child(even) td { background: #fffaf3; }
        .subject { font-weight: bold; }
        .description { margin-top: 2px; color: #62584c; font-size: 8px; }
        .status { white-space: nowrap; font-weight: bold; }
        .footer { margin-top: 12px; color: #7f7465; font-size: 8px; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/donatella-fruit-logo.png');
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <table class="report-header">
        <tr>
            <td>
                <h1>Reporte mensual de tickets</h1>
                <div class="subtitle">
                    Grupo Donatella · {{ $monthLabel }} ·
                    {{ $selectedUser ? 'Colaborador: '.$selectedUser->name : 'Todos los colaboradores' }}
                </div>
            </td>
            @if ($logoData)
                <td class="report-logo-cell">
                    <img class="report-logo" src="{{ $logoData }}" alt="Donatella Fruit">
                </td>
            @endif
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><div class="summary-label">Total</div><div class="summary-value">{{ $tickets->count() }}</div></td>
            <td><div class="summary-label">Pendientes</div><div class="summary-value">{{ $counts['pending'] }}</div></td>
            <td><div class="summary-label">En proceso</div><div class="summary-value">{{ $counts['inProgress'] }}</div></td>
            <td><div class="summary-label">Hechos</div><div class="summary-value">{{ $counts['done'] }}</div></td>
        </tr>
    </table>

    <table class="details">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Colaborador</th>
                <th>Ticket</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>AnyDesk</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $ticket->user->name }}<br><span class="description">{{ $ticket->user->email }}</span></td>
                    <td><div class="subject">{{ $ticket->subject }}</div><div class="description">{{ str($ticket->description)->limit(170) }}</div></td>
                    <td>{{ $ticket->category }}</td>
                    <td class="status">{{ $ticket->status->label() }}</td>
                    <td>{{ $ticket->requires_anydesk ? ($ticket->anydesk_address ?: 'Sí') : 'No' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="padding: 24px; text-align: center; color: #7f7465;">No se registraron tickets para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generado el {{ $generatedAt->format('d/m/Y H:i') }} · Reporte interno de la Mesa de Ayuda TI.</div>
</body>
</html>
