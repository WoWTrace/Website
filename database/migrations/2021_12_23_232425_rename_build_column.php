<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBuildColumn extends Migration
{
    public function up(): void
    {
        Schema::table('build', function (Blueprint $table): void {
            $table->renameColumn('build', 'clientBuild');
        });
    }

    public function down(): void
    {
        Schema::table('build', function (Blueprint $table): void {
            $table->renameColumn('clientBuild', 'build');
        });
    }
}
