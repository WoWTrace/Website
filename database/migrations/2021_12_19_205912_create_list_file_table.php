<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListFileTable extends Migration
{
    /** @var string */
    private const TABLE = 'listfile';

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table): void {
            $table->id()->unsigned();
            $table->string('path', 255)->nullable()->unique();
            $table->string('type', 20)->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->char('lookup', 16)->nullable()->unique();
            $table->char('content_hash', 16)->nullable()->unique();
            $table->char('root_cdn', 16)->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        // Because Laravel doesn't support full text search migration
        DB::statement(sprintf('ALTER TABLE `%s` ADD FULLTEXT INDEX `listfile_path_fulltext` (`path`);', self::TABLE));
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
}
