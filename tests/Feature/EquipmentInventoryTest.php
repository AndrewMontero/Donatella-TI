<?php

namespace Tests\Feature;

use App\Enums\EquipmentCategory;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class EquipmentInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_and_technician_can_view_inventory(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get(route('equipment.index'))->assertOk();
        $this->actingAs(User::factory()->technician()->create())->get(route('equipment.index'))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('equipment.index'))->assertForbidden();
    }

    public function test_technician_can_register_and_update_inventory(): void
    {
        $technician = User::factory()->technician()->create();
        $this->actingAs($technician)->post(route('equipment.store'), ['collaborator_name' => 'Ana Rodríguez', 'category' => 'laptop', 'brand' => 'Dell', 'department' => 'Contabilidad', 'movement' => 'Entrega', 'society' => 'Dolcesia Fruit', 'is_active' => '1'])->assertSessionHasNoErrors();
        $equipment = Equipment::firstOrFail();
        $this->assertSame(EquipmentCategory::Laptop, $equipment->category);
        $this->actingAs($technician)->put(route('equipment.update', $equipment), ['collaborator_name' => 'Ana Rodríguez', 'category' => 'phone', 'brand' => 'Samsung', 'department' => 'Gerencia', 'has_business_line' => '1', 'phone_number' => '8888-8888', 'is_active' => '0'])->assertSessionHasNoErrors();
        $equipment->refresh();
        $this->assertSame(EquipmentCategory::Phone, $equipment->category);
        $this->assertFalse($equipment->is_active);
    }

    public function test_technician_can_import_and_export_inventory(): void
    {
        $technician = User::factory()->technician()->create();
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->setTitle('Inventario unificado')->fromArray([
            [],
            ['Inventario de celulares, equipos y monitores'],
            ['Registros separados por tipo de activo para facilitar la consulta.'],
            [],
            ['CELULARES'],
            ['Tipo de activo', 'Movimiento', 'Fecha', 'Colaborador', 'Área o puesto', 'Sociedad', 'Línea empresarial', 'Número celular', 'Estado', 'Archivo de origen'],
            ['Celular', 'Listado actual', null, 'Carlos Méndez', 'Ventas', 'Donatella', 'Sí', '8888-8888', 'Activo', 'Celulares.xlsx'],
            [],
            ['EQUIPOS Y MONITORES'],
            ['Tipo de activo', 'Movimiento', 'Fecha', 'Colaborador', 'Área o puesto', 'Sociedad', 'Línea empresarial', 'Número celular', 'Estado', 'Archivo de origen'],
            ['Monitor', 'Entrega', 'Oct-26', 'Ana Rodríguez', 'Contabilidad', 'Dolcesia Fruit', null, null, null, 'Equipos.xlsx'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'inventory_test_').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $this->actingAs($technician)->post(route('equipment.import'), ['inventory_file' => new UploadedFile($path, 'inventario.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true)])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('equipment', ['collaborator_name' => 'Carlos Méndez', 'category' => 'phone']);
        $this->assertDatabaseHas('equipment', ['collaborator_name' => 'Ana Rodríguez', 'category' => 'monitor']);
        $this->actingAs($technician)->get(route('equipment.export.excel'))->assertOk();
        $this->actingAs($technician)->get(route('equipment.export.pdf'))->assertOk();
        @unlink($path);
    }
}
