<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Product())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string("name", 200);
            $table->string("description", 200)->nullable();
            $table->string("image_url", 200)->nullable();
            $table->string("home_url", 200)->nullable();
            $table->unsignedBigInteger("gateway_id")->nullable();
            $table->string("gateway_product_id")->nullable();
            $table->string("category", 100);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("gateway_id")->references("id")->on((new PaymentGateway())->getTable())->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(DB::getDriverName() != "sqlite"){
            Schema::table((new Product())->getTable(), function (Blueprint $table) {
                $table->dropForeign((new Product())->getTable()."_gateway_id_foreign");
            });
        }

        Schema::dropIfExists((new Product())->getTable());
    }
};
