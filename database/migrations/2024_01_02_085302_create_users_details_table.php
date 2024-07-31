<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_details', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('type')->nullable();
            $table->string('user_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('contact_number')->nullable();
            $table->string('company_number')->nullable();
            $table->string('address')->nullable();
            $table->string('password')->nullable();
            $table->string('profile_photo')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('users_details');
    }
}
