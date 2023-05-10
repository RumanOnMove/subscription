<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("shop_id")->nullable();
            $table->string("name")->index();
            $table->json("meta")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(["shop_id", "name"]);
            $table->foreign("shop_id")->references("id")->on("shops")->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowances');
    }
};
