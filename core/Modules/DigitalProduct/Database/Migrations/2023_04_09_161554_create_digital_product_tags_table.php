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
        Schema::create('digital_product_tags', function (Blueprint $table) {
            $table->id();
            $table->string("tag_name");
            $table->unsignedBigInteger("product_id");
            $table->string("type")->nullable();
            $table->foreign("product_id")->references("id")->on("digital_products")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digital_product_tags');
    }
};
