<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_no')->nullable()->after('password');
            $table->text('address')->nullable()->after('password');
            $table->string('role')->default('1')->after('password');
            $table->string('language')->nullable()->after('password');
            $table->string('status')->default('Active')->after('password');
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
            $table->dropColumn('phone_no');
            $table->dropColumn('address');
            $table->dropColumn('role');
            $table->dropColumn('medical_document');
            $table->dropColumn('language');
            $table->dropColumn('status');
        });
    }
}
