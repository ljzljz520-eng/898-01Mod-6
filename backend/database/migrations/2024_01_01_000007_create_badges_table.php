<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('徽章名称');
            $table->string('code', 50)->unique()->comment('徽章标识代码');
            $table->string('icon', 200)->comment('徽章图标');
            $table->text('description')->comment('徽章描述');
            $table->string('source_type', 50)->comment('来源类型: register, answers, adopts, charity, topics, points, duration');
            $table->integer('source_value')->comment('来源条件值，如回答数10个则值为10');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();

            $table->index('source_type');
            $table->index('sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
