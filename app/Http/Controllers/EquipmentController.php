<?php

namespace App\Http\Controllers;

use App\Enums\EquipmentCategory;
use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        [$filters, $query] = $this->filteredQuery($request);

        return view('equipment.index', [
            'equipment' => $query->latest()->paginate(15)->withQueryString()->fragment('inventory-results'),
            'categories' => EquipmentCategory::cases(),
            'departments' => Equipment::query()->distinct()->orderBy('department')->pluck('department'),
            'hasFilters' => collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty(),
        ]);
    }

    public function create(): View
    {
        return view('equipment.create', ['categories' => EquipmentCategory::cases()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $equipment = Equipment::create($this->validated($request));

        return redirect()->route('equipment.edit', $equipment)->with('status', 'Registro guardado correctamente.');
    }

    public function edit(Request $request, Equipment $equipment): View
    {
        $candidate = $request->string('return_to')->toString();
        $returnTo = Str::startsWith($candidate, route('equipment.index'))
            ? $candidate
            : route('equipment.index').'#inventory-results';

        return view('equipment.edit', [
            'equipment' => $equipment,
            'categories' => EquipmentCategory::cases(),
            'returnTo' => $returnTo,
        ]);
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $equipment->update($this->validated($request));

        return back()->with('status', 'Registro actualizado correctamente.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['inventory_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240']]);
        $workbook = IOFactory::load($request->file('inventory_file')->getRealPath());
        $sheet = $workbook->getSheetByName('Inventario unificado') ?? $workbook->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $required = $this->officialHeaders();
        $indexes = null;
        $errors = [];
        $records = [];

        foreach ($rows as $offset => $row) {
            $normalizedRow = collect($row)->map(fn ($value) => $this->normalizeHeader($value));

            if (collect($required)->every(fn ($header) => $normalizedRow->contains($header))) {
                $indexes = collect($required)->mapWithKeys(fn ($header) => [$header => $normalizedRow->search($header)]);

                continue;
            }

            if (collect($row)->every(fn ($value) => blank($value))) {
                continue;
            }

            if ($indexes === null) {
                continue;
            }

            $line = $offset + 1;
            $category = EquipmentCategory::fromImportValue($row[$indexes['tipo_de_activo']] ?? null);

            if ($category === null && collect($row)->filter(fn ($value) => filled($value))->count() <= 1) {
                continue;
            }

            $name = trim((string) ($row[$indexes['colaborador']] ?? ''));
            $department = trim((string) ($row[$indexes['area_o_puesto']] ?? ''));

            if ($name === '' || $department === '' || $category === null) {
                $errors[] = "Fila {$line}: revise tipo de activo, colaborador y área o puesto.";

                continue;
            }

            $businessLineValue = $row[$indexes['linea_empresarial']] ?? null;
            $statusValue = $row[$indexes['estado']] ?? null;
            $hasBusinessLine = blank($businessLineValue) ? null : $this->toBoolean($businessLineValue);

            $records[] = [
                'collaborator_name' => $name,
                'category' => $category->value,
                'movement' => $this->nullableString($row[$indexes['movimiento']] ?? null),
                'movement_date' => $this->nullableString($row[$indexes['fecha']] ?? null),
                'brand' => null,
                'department' => $department,
                'society' => $this->nullableString($row[$indexes['sociedad']] ?? null),
                'has_business_line' => $hasBusinessLine,
                'phone_number' => $this->nullableString($row[$indexes['numero_celular']] ?? null),
                'source_file' => $this->nullableString($row[$indexes['archivo_de_origen']] ?? null),
                'is_active' => filled($statusValue) ? $this->toBoolean($statusValue) : ($category !== EquipmentCategory::Phone || $hasBusinessLine === true),
            ];
        }

        if ($indexes === null) {
            throw ValidationException::withMessages(['inventory_file' => 'No se encontró el formato oficial. Descargue la plantilla para verificar las 10 columnas requeridas.']);
        }

        if ($errors !== []) {
            throw ValidationException::withMessages(['inventory_file' => implode(' ', array_slice($errors, 0, 8))]);
        }

        if ($records === []) {
            throw ValidationException::withMessages(['inventory_file' => 'El archivo no contiene registros para importar.']);
        }

        $timestamp = now();
        $records = collect($records)->map(function (array $record) use ($timestamp): array {
            $identity = collect($record)->only(['collaborator_name', 'category', 'movement', 'movement_date', 'department', 'society', 'phone_number'])->all();

            return [...$record, 'import_key' => hash('sha256', json_encode($identity, JSON_UNESCAPED_UNICODE)), 'created_at' => $timestamp, 'updated_at' => $timestamp];
        })->all();

        DB::transaction(fn () => Equipment::query()->upsert(
            $records,
            ['import_key'],
            ['collaborator_name', 'category', 'movement', 'movement_date', 'brand', 'department', 'society', 'has_business_line', 'phone_number', 'source_file', 'is_active', 'updated_at'],
        ));

        return back()->with('status', count($records).' registros importados correctamente.');
    }

    public function template(): BinaryFileResponse
    {
        $spreadsheet = $this->newSpreadsheet('Plantilla de inventario unificado');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [],
            ['Inventario de celulares, equipos y monitores'],
            ['Registros separados por tipo de activo para facilitar la consulta.'],
            [],
            ['CELULARES'],
            $this->officialHeaderLabels(),
            ['Celular', 'Listado actual', null, 'Ana Rodríguez', 'Contabilidad', 'Dolcesia Fruit', 'Sí', '8888-8888', 'Activo', 'Inventario interno'],
            [],
            ['EQUIPOS Y MONITORES'],
            $this->officialHeaderLabels(),
            ['Equipo', 'Entrega', 'Oct-26', 'Carlos Méndez', 'Ventas', 'Donatella', null, null, 'Activo', 'Inventario interno'],
            ['Monitor', 'Entrega', 'Oct-26', 'Carlos Méndez', 'Ventas', 'Donatella', null, null, 'Activo', 'Inventario interno'],
        ]);
        $this->styleUnifiedSheet($sheet, [6, 10], [2, 5, 9]);

        return $this->downloadSpreadsheet($spreadsheet, 'plantilla-inventario-donatella.xlsx');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [, $query] = $this->filteredQuery($request);
        $spreadsheet = $this->newSpreadsheet('Inventario de equipos');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([$this->officialHeaderLabels()]);
        $row = 2;

        foreach ($query->orderBy('collaborator_name')->get() as $item) {
            $sheet->fromArray([[
                $item->category->importLabel(), $item->movement, $item->movement_date, $item->collaborator_name,
                $item->department, $item->society, $item->has_business_line === null ? null : ($item->has_business_line ? 'Sí' : 'No'),
                $item->phone_number, $item->is_active ? 'Activo' : 'Inactivo', $item->source_file,
            ]], null, "A{$row}");
            $row++;
        }
        $sheet->setAutoFilter('A1:J'.max(1, $row - 1));
        $sheet->freezePane('A2');

        return $this->downloadSpreadsheet($spreadsheet, 'inventario-donatella-'.now()->format('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        [, $query] = $this->filteredQuery($request);
        $items = $query->orderBy('category')->orderBy('collaborator_name')->get();

        return Pdf::loadView('equipment.report', ['items' => $items, 'generatedAt' => now()])
            ->setPaper('a4', 'landscape')->download('inventario-donatella-'.now()->format('Y-m-d').'.pdf');
    }

    private function filteredQuery(Request $request): array
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'category' => ['nullable', Rule::enum(EquipmentCategory::class)], 'department' => ['nullable', 'string', 'max:120'],
        ]);
        $query = Equipment::query();
        $search = trim($filters['q'] ?? '');

        if ($search !== '') {
            $query->where(fn ($equipmentQuery) => $equipmentQuery->where('collaborator_name', 'like', "%{$search}%")->orWhere('brand', 'like', "%{$search}%"));
        }
        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        } elseif (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }
        if (filled($filters['category'] ?? null)) {
            $query->where('category', $filters['category']);
        }
        if (filled($filters['department'] ?? null)) {
            $query->where('department', $filters['department']);
        }

        return [$filters, $query];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'collaborator_name' => ['required', 'string', 'max:150'], 'category' => ['required', Rule::enum(EquipmentCategory::class)],
            'movement' => ['nullable', 'string', 'max:50'], 'movement_date' => ['nullable', 'string', 'max:30'],
            'brand' => ['nullable', 'string', 'max:100'], 'department' => ['required', 'string', 'max:120'],
            'society' => ['nullable', 'string', 'max:120'], 'has_business_line' => ['nullable', 'boolean'],
            'phone_number' => ['nullable', 'string', 'max:40'], 'source_file' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_business_line'] = $request->filled('has_business_line') ? $request->boolean('has_business_line') : null;

        return $validated;
    }

    private function normalizeHeader(mixed $header): string
    {
        return Str::of((string) $header)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString();
    }

    private function toBoolean(mixed $value): bool
    {
        return in_array(Str::lower(Str::ascii(trim((string) $value))), ['1', 'si', 'true', 'activo', 'activa', 'yes'], true);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function officialHeaders(): array
    {
        return ['tipo_de_activo', 'movimiento', 'fecha', 'colaborador', 'area_o_puesto', 'sociedad', 'linea_empresarial', 'numero_celular', 'estado', 'archivo_de_origen'];
    }

    private function officialHeaderLabels(): array
    {
        return ['Tipo de activo', 'Movimiento', 'Fecha', 'Colaborador', 'Área o puesto', 'Sociedad', 'Línea empresarial', 'Número celular', 'Estado', 'Archivo de origen'];
    }

    private function newSpreadsheet(string $title): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()->setCreator('Grupo Donatella')->setTitle($title);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4D6B4D');
        $sheet->getStyle('A1:J500')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    private function styleUnifiedSheet($sheet, array $headerRows, array $titleRows): void
    {
        foreach ($headerRows as $row) {
            $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle("A{$row}:J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4D6B4D');
        }
        foreach ($titleRows as $row) {
            $sheet->mergeCells("A{$row}:J{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        }
    }

    private function downloadSpreadsheet(Spreadsheet $spreadsheet, string $filename): BinaryFileResponse
    {
        $path = tempnam(sys_get_temp_dir(), 'donatella_inventory_');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return response()->download($path, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend(true);
    }
}
