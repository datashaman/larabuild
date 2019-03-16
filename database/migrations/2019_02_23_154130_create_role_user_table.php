<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleUserTable extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('role');
            $table->string('team_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'role', 'team_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}
