<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operation_requests', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('patient_id')->constrained('users')->nullOnDelete();
            $table->string('referral_letter_path')->nullable()->after('symptoms_description');
            $table->string('referral_letter_original_name')->nullable()->after('referral_letter_path');
            $table->text('doctor_notes')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('operation_requests', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn([
                'doctor_id',
                'referral_letter_path',
                'referral_letter_original_name',
                'doctor_notes',
            ]);
        });
    }
};
