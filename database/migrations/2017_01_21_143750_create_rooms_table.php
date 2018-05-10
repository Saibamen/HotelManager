<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 5);
            $table->smallInteger('floor');
            $table->smallInteger('capacity')->unsigned();
            $table->double('price', 15, 2);
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->unique(['number', 'floor']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
