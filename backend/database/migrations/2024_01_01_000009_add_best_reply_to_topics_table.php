<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->unsignedBigInteger('best_reply_id')->nullable()->comment('最佳回复ID');
            $table->timestamp('best_reply_set_at')->nullable()->comment('设置最佳回复时间');

            $table->foreign('best_reply_id')->references('id')->on('replies')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropForeign(['best_reply_id']);
            $table->dropColumn('best_reply_id');
            $table->dropColumn('best_reply_set_at');
        });
    }
};
