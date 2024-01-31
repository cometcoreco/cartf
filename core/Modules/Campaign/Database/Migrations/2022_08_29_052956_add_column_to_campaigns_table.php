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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns' ,'admin_id')){
                $table->unsignedBigInteger("admin_id")->nullable();
            }

            if (!Schema::hasColumn('campaigns', 'type'))
            {
                $table->string("type")->nullable();
            }

            if (!Schema::hasColumn('campaigns', 'admin_id'))
            {
                $table->foreign("admin_id")->references("id")->on("admins");
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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn("admin_id");
            $table->dropColumn("type");
        });
    }
};
