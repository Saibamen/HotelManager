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
            // TODO:
            // $table->addColumn('tinyInteger', 'floor', ['lenght' => 3]);
            $table->smallInteger('floor');
            // TODO:
            // $table->addColumn('tinyInteger', 'capacity', ['lenght' => 3]);
            $table->smallInteger('capacity');
            $table->double('price', 15, 2);
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
