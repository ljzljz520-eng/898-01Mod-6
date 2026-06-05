<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->boolean('is_charity')->default(false)->comment('是否公益活动');
            $table->boolean('is_ad')->default(false)->comment('是否广告帖');
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('is_charity');
            $table->dropColumn('is_ad');
        });
    }
};
