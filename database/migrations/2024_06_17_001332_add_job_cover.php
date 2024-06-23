<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobCover extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            $table->string("job_cover");
            $table->text("application_note")->nullable();
            $table->string("application_link")->nullable();
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
