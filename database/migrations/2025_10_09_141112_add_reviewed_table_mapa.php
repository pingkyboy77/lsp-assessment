<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mapa', function (Blueprint $table) {
            // Review columns (Admin)
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('submitted_at');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_notes')->nullable()->after('reviewed_at');

            // Foreign keys
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('mapa', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['signed_by']);

            $table->dropColumn(['reviewed_by', 'reviewed_at', 'review_notes']);
        });
    }
};
