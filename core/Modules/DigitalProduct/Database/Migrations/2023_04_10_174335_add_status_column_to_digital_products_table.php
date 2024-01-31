<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('digital_products', function (Blueprint $table) {
            $table->tinyInteger('status_id')->default(2)->after('image_id')->comment('1=active, 2=inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('digital_products', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
