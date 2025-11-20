<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['operation_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_equipments');
    }
};
