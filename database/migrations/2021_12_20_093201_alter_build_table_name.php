<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBuildTableName extends Migration
{
    public function up(): void
    {
        Schema::table('build', function (Blueprint $table): void {
            $table->string('name')->after('patch');
        });
    }

    public function down(): void
    {
        Schema::table('build', function (Blueprint $table): void {
            $table->dropColumn('name');
        });
    }
}
