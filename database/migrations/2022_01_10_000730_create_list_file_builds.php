<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListFileBuilds extends Migration
{
    public function up(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropForeign('listfile_version_id_foreign');
            $table->dropPrimary(['id', 'contentHash', 'buildId']);
            $table->renameColumn('buildId', 'firstBuildId');
            $table->primary(['id', 'contentHash']);

            $table->foreign('id')
                ->references('id')
                ->on('listfile')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('listfile_builds', function (Blueprint $table): void {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('buildId');

            $table->primary(['id', 'buildId']);

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
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropForeign('listfile_version_id_foreign');
            $table->dropPrimary(['id', 'contentHash']);
            $table->renameColumn('firstBuildId', 'buildId');
            $table->primary(['id', 'contentHash', 'buildId']);

            $table->foreign('id')
                ->references('id')
                ->on('listfile')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::dropIfExists('listfile_builds');
    }
}
