<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requirement_templates', function (Blueprint $table) {
            $table
                ->enum('requirement_type', ['all_required', 'choose_one', 'choose_min'])
                ->default('all_required')
                ->after('description');
            $table->integer('min_required')->nullable()->after('requirement_type');
        });

        Schema::create('certification_scheme_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_scheme_id')->constrained()->onDelete('cascade');
            $table->foreignId('requirement_template_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['certification_scheme_id', 'requirement_template_id'], 'cert_req_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirement_templates', function (Blueprint $table) {
            $table->dropColumn('requirement_type');
            $table->dropColumn('min_required');
        });
    }
};
