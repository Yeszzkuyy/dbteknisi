<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('blue');
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert data awal (dari enum yang lama)
        DB::table('project_statuses')->insert([
            ['name' => 'Open', 'color' => 'blue', 'is_default' => true, 'sort_order' => 1, 'created_at' => now()],
            ['name' => 'Progress', 'color' => 'yellow', 'is_default' => false, 'sort_order' => 2, 'created_at' => now()],
            ['name' => 'Pending', 'color' => 'orange', 'is_default' => false, 'sort_order' => 3, 'created_at' => now()],
            ['name' => 'Hold', 'color' => 'red', 'is_default' => false, 'sort_order' => 4, 'created_at' => now()],
            ['name' => 'Done', 'color' => 'green', 'is_default' => false, 'sort_order' => 5, 'created_at' => now()],
            ['name' => 'Cancel', 'color' => 'gray', 'is_default' => false, 'sort_order' => 6, 'created_at' => now()],
            ['name' => 'Warranty', 'color' => 'purple', 'is_default' => false, 'sort_order' => 7, 'created_at' => now()],
            ['name' => 'Maintenance', 'color' => 'pink', 'is_default' => false, 'sort_order' => 8, 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('project_statuses');
    }
};