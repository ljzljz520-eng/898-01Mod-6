<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('badge_id')->comment('徽章ID');
            $table->text('source_proof')->comment('获得来源证明，如"累计回答10个问题"');
            $table->timestamp('awarded_at')->useCurrent()->comment('获得时间');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
            $table->unique(['user_id', 'badge_id']);
            $table->index('user_id');
            $table->index('badge_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
