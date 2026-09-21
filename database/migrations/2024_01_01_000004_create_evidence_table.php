<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')->constrained('checks')->cascadeOnDelete();
            $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('key', 128);
            $table->longText('value')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('selector', 512)->nullable();
            $table->longText('snippet')->nullable();
            $table->string('measured_value', 512)->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->index(['scan_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
