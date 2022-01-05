<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileAddPathDiscovery extends Migration
{
    public function up(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->timestamp('pathDiscovery')->nullable()->index()->after('verified');
        });
    }

    public function down(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->dropColumn('pathDiscovery');
        });
    }
}
