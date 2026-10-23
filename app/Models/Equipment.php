<?php

namespace App\Models;

use App\Enums\EquipmentCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $collaborator_name
 * @property EquipmentCategory $category
 * @property string $brand
 * @property string $department
 * @property bool $is_active
 */
class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'collaborator_name',
        'category',
        'brand',
        'department',
        'movement',
        'movement_date',
        'society',
        'has_business_line',
        'phone_number',
        'source_file',
        'import_key',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'has_business_line' => 'boolean',
            'category' => EquipmentCategory::class,
        ];
    }
}
