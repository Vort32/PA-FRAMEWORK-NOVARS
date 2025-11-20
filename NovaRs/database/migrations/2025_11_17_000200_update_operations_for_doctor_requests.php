<?php

use App\Enums\OperationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (! Schema::hasColumn('operations', 'requested_doctor_id')) {
                $table->foreignId('requested_doctor_id')
                    ->nullable()
                    ->after('doctor_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        DB::statement('ALTER TABLE operations MODIFY doctor_id BIGINT UNSIGNED NULL');

        $enum = "'".implode("','", OperationStatus::values())."'";
        DB::statement("ALTER TABLE operations MODIFY status ENUM($enum) NOT NULL DEFAULT '".OperationStatus::Scheduled->value."'");
    }

    public function down(): void
    {
        $original = ['scheduled', 'ongoing', 'completed', 'postponed', 'cancelled'];
        $enum = "'".implode("','", $original)."'";

        DB::statement("ALTER TABLE operations MODIFY status ENUM($enum) NOT NULL DEFAULT 'scheduled'");
        DB::statement('ALTER TABLE operations MODIFY doctor_id BIGINT UNSIGNED NOT NULL');

        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'requested_doctor_id')) {
                $table->dropForeign(['requested_doctor_id']);
                $table->dropColumn('requested_doctor_id');
            }
        });
    }
};
