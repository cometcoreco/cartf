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
            $table->unsignedBigInteger('product_type')
                ->index()
                ->default(2)
                ->after('description')
                ->comment('1=normal, 2=digital');
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
            $table->dropColumn('product_type');
        });
    }
};
