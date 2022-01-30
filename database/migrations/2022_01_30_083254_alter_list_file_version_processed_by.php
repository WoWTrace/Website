<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileVersionProcessedBy extends Migration
{
    public function up(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropColumn('processed');
            $table->json('processedBy')
                ->after('fileSize')
                ->comment('List of process class names which processed this build');
        });
    }

    public function down(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropColumn('processedBy');
            $table->boolean('processed')
                ->default(false)
                ->after('fileSize');
        });
    }
}
