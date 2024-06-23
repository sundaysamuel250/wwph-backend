<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOthersTouser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string("avatar")->nullable();
            $table->string("company_name")->nullable();
            $table->string("industry")->nullable();
            $table->string("founded")->nullable();
            $table->string("company_size")->nullable();
            $table->string("head_office")->nullable();
            $table->string("about_company")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
