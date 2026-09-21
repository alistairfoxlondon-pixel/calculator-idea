<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete();
            $table->string('url', 2048);
            $table->string('normalized_url', 2048);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('bytes')->nullable();
            $table->unsignedInteger('total_ms')->nullable();
            $table->unsignedInteger('ttfb_ms')->nullable();
            $table->unsignedInteger('dns_ms')->nullable();
            $table->unsignedInteger('connect_ms')->nullable();
            $table->unsignedInteger('tls_ms')->nullable();
            $table->longText('headers')->nullable();
            $table->longText('html_path')->nullable(); // storage path
            $table->boolean('is_html')->default(true);
            $table->unsignedSmallInteger('depth')->default(0);
            $table->string('discovered_via', 32)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index(['scan_id', 'url'], 'pages_scan_url_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
