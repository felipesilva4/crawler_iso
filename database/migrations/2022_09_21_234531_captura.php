<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Captura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capturas', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('decimal'); // Decimal pode começar com 0,por isso string
            $table->string('number'); // Número pode começar com zero, por isso string
            $table ->string('currency');
            $table->string('location', 600)->nullable();
            $table->string('icon',600)->nullable();
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
        Schema::dropIfExists('capturas');
    }
}
