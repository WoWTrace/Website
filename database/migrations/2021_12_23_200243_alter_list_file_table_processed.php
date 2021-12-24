<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileTableProcessed extends Migration
{
    public function up(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->boolean('processed')->default(false)->after('encrypted');
        });
    }

    public function down(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->dropColumn('processed');
        });
    }
}
