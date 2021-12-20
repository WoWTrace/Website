<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileTableEncrypted extends Migration
{
    public function up(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->boolean('encrypted')->default(false)->after('verified');
        });
    }

    public function down(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->dropColumn('encrypted');
        });
    }
}
