<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileVersionPrimaryKey extends Migration
{
    public function up(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropForeign('listfile_version_id_foreign');
            $table->dropPrimary(['id', 'contentHash']);
            $table->primary(['id', 'contentHash', 'buildId']);

            $table->foreign('id')
                ->references('id')
                ->on('listfile')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropForeign('listfile_version_id_foreign');
            $table->dropPrimary(['id', 'contentHash', 'buildId']);
            $table->primary(['id', 'contentHash']);

            $table->foreign('id')
                ->references('id')
                ->on('listfile')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
