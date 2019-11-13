<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainUserCustomerPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_user_customer_places', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('main_user');
            $table->integer('team_id')->comment('main_team');
            $table->integer('customer_id')->comment('main_customer_template');
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
        Schema::dropIfExists('main_user_customer_places');
    }
}
