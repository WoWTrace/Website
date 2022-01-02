<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterListFileSuggestionAddSuggestionKey extends Migration
{
    public function up(): void
    {
        Schema::table('listfile_suggestion', function (Blueprint $table): void {
            $table->string('suggestionKey')->index()->first();
        });
    }

    public function down(): void
    {
        Schema::table('listfile_suggestion', function (Blueprint $table): void {
            $table->dropColumn('suggestionKey');
        });
    }
}
