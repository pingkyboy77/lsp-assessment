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
        Schema::table('apl_01_pendaftarans', function (Blueprint $table) {
            $table->unsignedBigInteger('selected_requirement_template_id')->nullable()->after('requirement_answers');
            
            // Add foreign key constraint
            $table->foreign('selected_requirement_template_id')
                  ->references('id')
                  ->on('requirement_templates')
                  ->onDelete('set null');
            
            // Add index for better performance
            $table->index('selected_requirement_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apl_01_pendaftarans', function (Blueprint $table) {
            $table->dropForeign(['selected_requirement_template_id']);
            $table->dropIndex(['selected_requirement_template_id']);
            $table->dropColumn('selected_requirement_template_id');
        });
    }
};
