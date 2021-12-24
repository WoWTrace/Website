<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListFileVersionTable extends Migration
{
    public function up(): void
    {
        Schema::create('listfile_version', function (Blueprint $table): void {
            $table->unsignedBigInteger('id');
            $table->char('contentHash', 32);
            $table->boolean('encrypted')->default(false);
            $table->boolean('processed')->default(false);
            $table->unsignedBigInteger('buildId');
            $table->unsignedInteger('clientBuild')->index();
            $table->timestamps();

            $table->primary(['id', 'contentHash']);

            $table->foreign('id')
                ->references('id')
                ->on('listfile')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('buildId')
                ->references('id')
                ->on('build')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listfile_version');
    }
}
