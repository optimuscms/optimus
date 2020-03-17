<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediablesTable extends Migration
{
    public function up()
    {
        Schema::create('mediables', function (Blueprint $table) {
            $table->unsignedInteger('media_id')->index();
            $table->unsignedInteger('mediable_id')->index();
            $table->string('mediable_type');
            $table->string('group');

            $table->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mediables');
    }
}