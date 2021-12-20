<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductTableEncrypted extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table): void {
            $table->boolean('encrypted')->after('badgeType');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table): void {
            $table->dropColumn('name');
        });
    }
}
