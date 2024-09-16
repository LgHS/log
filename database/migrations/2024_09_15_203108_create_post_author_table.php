<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_author', function (Blueprint $table) {
            $table->uuid('post_id');
            $table->uuid('user_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->primary(['post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_author');
    }
};
