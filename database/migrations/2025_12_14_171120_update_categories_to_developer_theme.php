<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update category names to developer theme
        $categoryMappings = [
            'Technology' => 'AI',
            'Health' => 'Fullstack',
            'Science' => 'Data Science',
            'Sports' => 'Game Dev',
            'Politics' => 'Cyber',
            'Entertainment' => 'Mobile Dev',
        ];

        foreach ($categoryMappings as $oldName => $newName) {
            DB::table('categories')
                ->where('name', $oldName)
                ->update(['name' => $newName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert category names back to original
        $categoryMappings = [
            'AI' => 'Technology',
            'Fullstack' => 'Health',
            'Data Science' => 'Science',
            'Game Dev' => 'Sports',
            'Cyber' => 'Politics',
            'Mobile Dev' => 'Entertainment',
        ];

        foreach ($categoryMappings as $newName => $oldName) {
            DB::table('categories')
                ->where('name', $newName)
                ->update(['name' => $oldName]);
        }
    }
};
