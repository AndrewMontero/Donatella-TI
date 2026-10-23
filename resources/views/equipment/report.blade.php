<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8"><style>
@page{margin:28px}body{font-family:DejaVu Sans,sans-serif;color:#352d27;font-size:10px}h1{color:#4d6b4d;margin:0 0 4px;font-size:20px}.meta{color:#7f7465;margin-bottom:18px}table{width:100%;border-collapse:collapse}th{background:#4d6b4d;color:#fff;text-align:left;padding:8px}td{border-bottom:1px solid #eadcc9;padding:7px 8px}tr:nth-child(even) td{background:#fff8ed}.status{font-weight:bold}.footer{margin-top:14px;color:#7f7465;font-size:9px}
</style></head><body>
<h1>Inventario de equipos</h1>
<div class="meta">Grupo Donatella · Generado el {{ $generatedAt->format('d/m/Y H:i') }} · {{ $items->count() }} registros</div>
<table><thead><tr><th>Tipo</th><th>Movimiento</th><th>Fecha</th><th>Colaborador</th><th>Área o puesto</th><th>Sociedad</th><th>Línea</th><th>Número</th><th>Estado</th></tr></thead><tbody>
@forelse ($items as $item)
<tr><td>{{ $item->category->importLabel() }}</td><td>{{ $item->movement }}</td><td>{{ $item->movement_date }}</td><td>{{ $item->collaborator_name }}</td><td>{{ $item->department }}</td><td>{{ $item->society }}</td><td>{{ $item->has_business_line === null ? '' : ($item->has_business_line ? 'Sí' : 'No') }}</td><td>{{ $item->phone_number }}</td><td class="status">{{ $item->is_active ? 'Activo' : 'Inactivo' }}</td></tr>
@empty <tr><td colspan="9">No hay registros para mostrar.</td></tr> @endforelse
</tbody></table><div class="footer">Reporte interno de inventario de TI.</div>
</body></html>
