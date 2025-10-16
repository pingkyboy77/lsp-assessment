<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jika tabel sudah ada, alter untuk memastikan tipe data yang benar
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                // Pastikan kolom yang tidak ada ditambahkan
                if (!Schema::hasColumn('pages', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('pages', 'is_sidebar_menu')) {
                    $table->boolean('is_sidebar_menu')->default(true);
                }
                if (!Schema::hasColumn('pages', 'parent_route')) {
                    $table->string('parent_route')->nullable();
                }
            });
        } else {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('route_name')->unique();
                $table->string('slug');
                $table->string('icon')->nullable();
                $table->text('description')->nullable();
                $table->string('group')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_sidebar_menu')->default(true);
                $table->json('allowed_roles')->nullable();
                $table->string('parent_route')->nullable();
                $table->timestamps();

                $table->index(['is_active', 'is_sidebar_menu']);
                $table->index(['group', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
