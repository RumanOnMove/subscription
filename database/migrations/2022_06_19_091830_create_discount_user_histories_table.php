<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\Discount;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config("subscription.subscription_table_prefix") . "_discount_usage_histories", function (Blueprint $table) {
            $table->id();
            $table->morphs("owner");
            $table->foreignId("discount_id")->references("id")->on((new Discount())->getTable());
            $table->unsignedBigInteger("subscription_id");
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
        Schema::dropIfExists(config("subscription.subscription_table_prefix") . "_discount_usage_histories");
    }
};
