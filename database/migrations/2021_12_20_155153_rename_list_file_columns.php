<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameListFileColumns extends Migration
{
    public function up(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->renameColumn('user_id', 'userId');
            $table->renameColumn('content_hash', 'contentHash');
            $table->renameColumn('root_cdn', 'rootCdnHash');
        });
    }

    public function down(): void
    {
        Schema::table('listfile', function (Blueprint $table): void {
            $table->renameColumn('userId', 'user_id');
            $table->renameColumn('contentHash', 'content_hash');
            $table->renameColumn('rootCdnHash', 'root_cdn');
        });
    }
}
