<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugAndDatePublished extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            $table->string("slug")->nullable();
            $table->timestamp("date_published")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            //
        });
    }
}
