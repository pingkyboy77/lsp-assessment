<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delegasi_personil_asesmen', function (Blueprint $table) {
            $table->boolean('is_rescheduled')->default(false)->after('notes');
            $table->timestamp('rescheduled_at')->nullable()->after('is_rescheduled');
            $table->unsignedBigInteger('rescheduled_by')->nullable()->after('rescheduled_at');
            $table->text('reschedule_reason')->nullable()->after('rescheduled_by');

            $table->foreign('rescheduled_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('tuk_requests', function (Blueprint $table) {
            $table->boolean('is_rescheduled')->default(false)->after('recommended_by');
            $table->timestamp('rescheduled_at')->nullable()->after('is_rescheduled');
            $table->unsignedBigInteger('rescheduled_by')->nullable()->after('rescheduled_at');
            $table->text('reschedule_reason')->nullable()->after('rescheduled_by');

            $table->foreign('rescheduled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('delegasi_personil_asesmen', function (Blueprint $table) {
            $table->dropForeign(['rescheduled_by']);
            $table->dropColumn(['is_rescheduled', 'rescheduled_at', 'rescheduled_by', 'reschedule_reason']);
        });

        Schema::table('tuk_requests', function (Blueprint $table) {
            $table->dropForeign(['rescheduled_by']);
            $table->dropColumn(['is_rescheduled', 'rescheduled_at', 'rescheduled_by', 'reschedule_reason']);
        });
    }
};
