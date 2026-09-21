<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->string('tool', 32);
            $table->string('url', 2048);
            $table->string('normalized_url', 2048);
            $table->string('status', 32)->default('queued'); // queued, running, paused, cancelling, success, partial, failed, rate_limited, invalid_url
            $table->string('stage', 64)->default('resolving');
            $table->unsignedSmallInteger('progress')->default(0);
            $table->unsignedInteger('pages_crawled')->default(0);
            $table->unsignedInteger('pages_target')->default(50);
            $table->json('live_log')->nullable(); // capped 200 rows
            $table->string('rulepack_version', 16)->default('v1');
            $table->string('ip_address', 45)->nullable();
            $table->string('idempotency_key', 64)->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->longText('error_message')->nullable();
            $table->unsignedInteger('elapsed_ms')->nullable();
            $table->timestamps();

            $table->index(['tool', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
