<?php

use App\Enums\OperationStaffStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->enum('status', array_column(OperationStaffStatus::cases(), 'value'))
                ->default(OperationStaffStatus::Pending->value);
            $table->timestamps();

            $table->unique(['operation_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_staff');
    }
};
