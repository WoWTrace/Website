<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table): void {
            $table->id();
            $table->string('product', 32)->unique();
            $table->string('name', 255);
            $table->string('badgeText', 255);
            $table->string('badgeType', 255);
            $table->string('lastVersion', 30)->nullable();
            $table->string('lastBuildConfig', 32)->nullable();
            $table->timestamp('detected')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
}
