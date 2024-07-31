<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('contact_number')->nullable();
            $table->string('company_number')->nullable();
            $table->string('company_address')->nullable();
            $table->string('password')->nullable();
            $table->string('profile_photo')->nullable();
            $table->softDeletes(); // Adds the deleted_at column for soft deletes
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
        Schema::dropIfExists('companies');
    }
}
