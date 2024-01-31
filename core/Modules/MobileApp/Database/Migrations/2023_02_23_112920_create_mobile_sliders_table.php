<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileSlidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_sliders', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->tinyText("description");

            $table->unsignedBigInteger("category")->nullable();
            $table->unsignedBigInteger("campaign")->nullable();

            $table->unsignedBigInteger("image_id");
            $table->string("button_text");
            $table->tinyText("url");
            $table->string("type")->nullable();
            $table->timestamps();
            
            $table->foreign("category")->references("id")->on("categories");
            $table->foreign("campaign")->references("id")->on("campaigns");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_sliders');
    }
}
