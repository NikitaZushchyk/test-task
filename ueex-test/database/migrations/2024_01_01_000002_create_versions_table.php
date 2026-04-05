<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('versionable_id');
            $table->string('versionable_type');
            $table->unsignedInteger('version');
            $table->json('data');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['versionable_id', 'versionable_type'], 'versions_versionable_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
