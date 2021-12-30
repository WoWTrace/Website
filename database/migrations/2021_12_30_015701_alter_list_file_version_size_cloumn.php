<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileVersionSizeCloumn extends Migration
{
    public function up(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->unsignedInteger('fileSize')->nullable()->after('encrypted');
        });
    }

    public function down(): void
    {
        Schema::table('listfile_version', function (Blueprint $table): void {
            $table->dropColumn('fileSize');
        });
    }
}
