<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete();
            $table->string('check_id', 64);
            $table->string('tool', 32);
            $table->string('category', 64);
            $table->string('severity', 16); // critical|high|medium|low|info
            $table->string('status', 16); // pass|fail|warn|unknown|not_applicable
            $table->unsignedSmallInteger('weight')->default(1);
            $table->string('message', 1024);
            $table->string('fix', 1024)->nullable();
            $table->string('effort', 16)->nullable(); // quick_win|moderate|structural
            $table->string('impact', 16)->nullable();
            $table->string('docs_url', 512)->nullable();
            $table->string('provenance', 32)->default('Measured by us');
            $table->unsignedSmallInteger('evidence_count')->default(0);
            $table->timestamps();

            $table->index(['scan_id', 'tool']);
            $table->index('check_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
