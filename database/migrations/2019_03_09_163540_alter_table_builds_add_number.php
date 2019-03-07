<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBuildsAddNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('builds', function (Blueprint $table) {
            $table->integer('number')->after('project_id');
            $table->unique(['project_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('builds', function (Blueprint $table) {
            $table->dropForeign('builds_project_id_foreign');
            $table->dropUnique(['project_id', 'number']);
            $table->foreign('project_id')->references('id')->on('projects')->onUpdate('cascade');
            $table->dropColumn('number');
        });
    }
}
