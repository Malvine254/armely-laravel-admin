<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_download_requests')) {
            return;
        }

        Schema::create('blog_download_requests', function (Blueprint $table) {
            $table->id();
            $table->string('blog_id');
            $table->string('blog_title')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('link_sent_at')->nullable();
            $table->timestamps();

            $table->index(['blog_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_download_requests');
    }
};
