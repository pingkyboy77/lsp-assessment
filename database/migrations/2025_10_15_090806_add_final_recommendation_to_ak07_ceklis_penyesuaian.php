<?php

// File: database/migrations/2025_01_XX_XXXXXX_add_ak07_final_recommendation_columns.php

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
        // 1. Add final recommendation columns to ak07_ceklis_penyesuaian table
        if (Schema::hasTable('ak07_ceklis_penyesuaian')) {
            Schema::table('ak07_ceklis_penyesuaian', function (Blueprint $table) {
                if (!Schema::hasColumn('ak07_ceklis_penyesuaian', 'final_recommendation')) {
                    $table->enum('final_recommendation', ['continue', 'not_continue'])
                        ->nullable()
                        ->after('status')
                        ->comment('Final recommendation from asesor: continue or not_continue');
                }

                if (!Schema::hasColumn('ak07_ceklis_penyesuaian', 'recommendation_notes')) {
                    $table->text('recommendation_notes')
                        ->nullable()
                        ->after('final_recommendation')
                        ->comment('Asesor notes for the recommendation');
                }

                if (!Schema::hasColumn('ak07_ceklis_penyesuaian', 'final_signature_path')) {
                    $table->string('final_signature_path')
                        ->nullable()
                        ->after('recommendation_notes')
                        ->comment('Path to final recommendation signature image');
                }

                if (!Schema::hasColumn('ak07_ceklis_penyesuaian', 'final_signed_at')) {
                    $table->timestamp('final_signed_at')
                        ->nullable()
                        ->after('final_signature_path')
                        ->comment('When final recommendation was signed');
                }

                if (!Schema::hasColumn('ak07_ceklis_penyesuaian', 'final_signed_by')) {
                    $table->foreignId('final_signed_by')
                        ->nullable()
                        ->constrained('users')
                        ->onDelete('set null')
                        ->after('final_signed_at')
                        ->comment('User who signed the final recommendation');
                }
            });
        }

        // 2. Add reschedule source columns to tuk_reschedule_histories table
        if (Schema::hasTable('tuk_reschedule_histories')) {
            Schema::table('tuk_reschedule_histories', function (Blueprint $table) {
                if (!Schema::hasColumn('tuk_reschedule_histories', 'reschedule_source')) {
                    $table->enum('reschedule_source', ['admin_manual', 'ak07_final_recommendation'])
                        ->default('admin_manual')
                        ->after('reschedule_reason')
                        ->comment('Source of reschedule: admin manual or AK07 recommendation');
                }

                if (!Schema::hasColumn('tuk_reschedule_histories', 'ak07_nomor')) {
                    $table->string('ak07_nomor')
                        ->nullable()
                        ->after('mapa_nomor')
                        ->comment('AK07 nomor if reschedule from AK07 recommendation');
                }
            });
        }

        // 3. Add final recommendation status column to mapas table
        if (Schema::hasTable('mapas')) {
            Schema::table('mapas', function (Blueprint $table) {
                if (!Schema::hasColumn('mapas', 'final_recommendation_status')) {
                    $table->enum('final_recommendation_status', ['pending', 'approved', 'rejected'])
                        ->nullable()
                        ->after('status')
                        ->comment('Status of final recommendation: pending, approved, or rejected');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Remove columns from ak07_ceklis_penyesuaian
        if (Schema::hasTable('ak07_ceklis_penyesuaian')) {
            Schema::table('ak07_ceklis_penyesuaian', function (Blueprint $table) {
                // Drop foreign key first
                if (Schema::hasColumn('ak07_ceklis_penyesuaian', 'final_signed_by')) {
                    $table->dropForeign(['final_signed_by']);
                }

                // Drop columns
                $columns = [
                    'final_recommendation',
                    'recommendation_notes',
                    'final_signature_path',
                    'final_signed_at',
                    'final_signed_by',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('ak07_ceklis_penyesuaian', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // 2. Remove columns from tuk_reschedule_histories
        if (Schema::hasTable('tuk_reschedule_histories')) {
            Schema::table('tuk_reschedule_histories', function (Blueprint $table) {
                $columns = ['reschedule_source', 'ak07_nomor'];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('tuk_reschedule_histories', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // 3. Remove column from mapas
        if (Schema::hasTable('mapas')) {
            Schema::table('mapas', function (Blueprint $table) {
                if (Schema::hasColumn('mapas', 'final_recommendation_status')) {
                    $table->dropColumn('final_recommendation_status');
                }
            });
        }
    }
};
