<?php

use App\Enums\OperationRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();
            $table->text('symptoms_description')->nullable();
            $table->date('preferred_date')->nullable();
            $table->enum('status', OperationRequestStatus::values())->default(OperationRequestStatus::Pending->value);
            $table->text('admin_notes')->nullable();
            $table->foreignId('operation_id')->nullable()->constrained('operations')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'preferred_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_requests');
    }
};
