<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('site_visits')) {
            Schema::create('site_visits', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->string('session_id', 100)->nullable()->index();
                $table->string('device_type', 20)->default('desktop');
                $table->string('page_url', 500)->nullable();
                $table->date('visited_date')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
