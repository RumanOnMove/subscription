<?php

use App\Models\Shop;
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
        Schema::create("shop_allowances", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("shop_id");
            $table->boolean("smart_image")->default(true);
            $table->integer("quantity");
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("shop_id")->references("id")->on((new Shop())->getTable())->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("shop_allowances");
    }
};
