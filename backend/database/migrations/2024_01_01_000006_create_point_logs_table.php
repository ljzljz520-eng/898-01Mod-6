<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->integer('points')->comment('变动积分数，正为增加，负为扣除');
            $table->string('type', 50)->comment('类型: answer, adopt, charity, ad, quarrel, other');
            $table->text('description')->comment('变动描述');
            $table->unsignedBigInteger('related_id')->nullable()->comment('关联ID，如帖子ID、回复ID');
            $table->string('related_type', 50)->nullable()->comment('关联类型: topic, reply, activity');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
