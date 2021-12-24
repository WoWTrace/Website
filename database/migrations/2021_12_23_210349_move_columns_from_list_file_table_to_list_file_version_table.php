<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveColumnsFromListFileTableToListFileVersionTable extends Migration
{
    public function up(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->dropColumn('rootCdnHash');
            $table->dropColumn('contentHash');
            $table->dropColumn('encrypted');
            $table->dropColumn('processed');

        });
    }

    public function down(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->char('contentHash', 32)->nullable()->unique()->after('lookup');
            $table->char('rootCdn', 32)->nullable()->after('rootCdnHash');
            $table->boolean('encrypted')->default(false)->after('verified');
            $table->boolean('processed')->default(false)->after('encrypted');
        });
    }
}
