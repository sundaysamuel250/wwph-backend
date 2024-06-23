<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWwphJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wwph_jobs', function (Blueprint $table) {
            $table->id();
            $table->text("title");
            $table->unsignedBigInteger("company_id");
            $table->unsignedBigInteger("work_type");
            $table->unsignedBigInteger("job_type");
            $table->string("job_role");
            $table->double("salary", 20, 2)->default();
            $table->text("salary_narration")->nullable();
            $table->text("education")->nullable();
            $table->text("location");
            $table->longText("description");
            $table->longText("requirements");
            $table->integer("experience")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wwph_jobs');
    }
}
