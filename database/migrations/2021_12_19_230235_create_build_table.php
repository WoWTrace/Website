<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildTable extends Migration
{
    public function up(): void
    {
        Schema::create('build', function (Blueprint $table): void {
            $table->id();
            $table->char('buildConfig', 32)->index()->unique();
            $table->char('cdnConfig', 32)->index();
            $table->char('patchConfig', 32)->nullable();
            $table->char('productConfig', 32);
            $table->string('product', 32);
            $table->string('expansion', 4);
            $table->string('major', 4);
            $table->string('minor', 4);
            $table->unsignedInteger('build');
            $table->string('patch', 14)->virtualAs('CONCAT(expansion, ".", major, ".", minor)');
            $table->char('encodingContentHash', 32);
            $table->char('encodingCdnHash', 32);
            $table->char('rootContentHash', 32);
            $table->char('rootCdnHash', 32)->index();
            $table->char('installContentHash', 32);
            $table->char('installCdnHash', 32);
            $table->char('downloadContentHash', 32);
            $table->char('downloadCdnHash', 32);
            $table->char('sizeContentHash', 32);
            $table->char('sizeCdnHash', 32);
            $table->timestamps();

            $table->foreign('product')
                ->references('product')
                ->on('product')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build');
    }
}
