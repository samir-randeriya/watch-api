<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name')->nullable();
            $table->string('type')->nullable();
            $table->string('year')->nullable();
            $table->string('item_name')->nullable();
            $table->string('description')->nullable();
            $table->string('watch_pic1')->nullable();
            $table->string('watch_pic2')->nullable();
            $table->string('watch_pic3')->nullable();
            $table->string('watch_pic4')->nullable();
            $table->string('watch_pic5')->nullable();
            $table->string('watch_pic6')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('price')->nullable();
            $table->string('user_id')->nullable();
            $table->string('negotiation')->nullable();
            $table->string('accessories')->nullable();
            $table->string('condition')->nullable();
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
        Schema::dropIfExists('product');
    }
}
