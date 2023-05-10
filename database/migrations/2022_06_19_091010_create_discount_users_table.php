<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\PaymentGateway;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config("subscription.subscription_table_prefix") . "_discount_users", function (Blueprint $table) {
            $table->id();
            $table->foreignId("discount_id")->references("id")->on((new Discount())->getTable());
            $table->morphs("owner");
            $table->unsignedBigInteger("gateway_id")->nullable()->references("id")->on((new PaymentGateway())->getTable());
            $table->unsignedBigInteger("plan_id")->nullable();
            $table->boolean("is_active")->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(["discount_id", "gateway_id", "plan_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config("subscription.subscription_table_prefix") . "_discount_users");
    }
};
