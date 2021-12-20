<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileTableFixHashLength extends Migration
{
    public function up(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->string('contentHash', 32)->change();
            $table->string('rootCdnHash', 32)->change();
        });
    }

    public function down(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->string('contentHash', 16)->change();
            $table->string('rootCdnHash', 16)->change();
        });
    }
}
