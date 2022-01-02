<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBuildColumns extends Migration
{
    public function up(): void
    {
        Schema::table('build', function (Blueprint $table): void {
            $table->string('productConfig', 32)->nullable()->change();
            $table->string('sizeContentHash', 32)->nullable()->change();
            $table->string('sizeCdnHash', 32)->nullable()->change();
            $table->boolean('custom')
                ->default(false)
                ->after('sizeCdnHash')
                ->comment('Builds which contains custom generated configs');
            $table->timestamp('compiledAt')
                ->nullable()
                ->after('custom')
                ->comment('Compile time of the Wow.exe');
            $table->json('processedBy')
                ->default([])
                ->after('compiledAt')
                ->comment('List of process class names which processed this build');
        });
    }

    public function down(): void
    {
        Schema::table('build', function (Blueprint $table): void {
            $table->string('productConfig', 32)->nullable(false)->change();
            $table->string('sizeContentHash', 32)->nullable(false)->change();
            $table->string('sizeCdnHash', 32)->nullable(false)->change();
            $table->dropColumn('custom');
            $table->dropColumn('compiledAt');
            $table->dropColumn('processedBy');
        });
    }
}
