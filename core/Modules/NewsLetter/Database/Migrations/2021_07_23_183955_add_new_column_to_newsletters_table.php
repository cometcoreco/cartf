<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNewColumnToNewslettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            if (!Schema::hasColumn('newsletters', 'token')){
                $table->string('token')->nullable();
            }

            if (!Schema::hasColumn('newsletters', 'verified')){
                $table->string('verified')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('verified');
        });
    }
}
