<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_deactivate_categories(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'Telefonía'])
            ->assertSessionHasNoErrors();

        $category = Category::where('name', 'Telefonía')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Telefonía móvil',
                'is_active' => '0',
            ])->assertSessionHasNoErrors();

        $category->refresh();

        $this->assertSame('Telefonía móvil', $category->name);
        $this->assertFalse($category->is_active);
    }

    public function test_regular_users_can_not_manage_categories(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_inactive_categories_are_not_available_for_new_tickets(): void
    {
        $category = Category::where('name', 'Hardware')->firstOrFail();
        $category->update(['is_active' => false]);

        $this->actingAs(User::factory()->create())->post(route('tickets.store'), [
            'subject' => 'Equipo dañado',
            'category' => 'Hardware',
            'description' => 'El equipo no enciende.',
            'requires_anydesk' => '0',
        ])->assertSessionHasErrors('category');
    }
}
