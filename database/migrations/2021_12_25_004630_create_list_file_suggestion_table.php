<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListFileSuggestionTable extends Migration
{
    public function up(): void
    {
        Schema::create('listfile_suggestion', function (Blueprint $table): void {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('userId')->nullable();
            $table->string('path', 255);
            $table->string('type', 20)->nullable();
            $table->boolean('accepted')->nullable()->index();
            $table->unsignedBigInteger('reviewerUserId')->nullable();
            $table->timestamp('reviewedAt')->nullable();
            $table->timestamps();

            $table->primary(['id', 'userId', 'path']);

            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('reviewerUserId')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listfile_suggestion');
    }
}
