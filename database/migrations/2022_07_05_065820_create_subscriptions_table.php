<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\Subscription;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Subscription())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs("owner");
            $table->unsignedBigInteger("plan_id");
            $table->unsignedBigInteger("gateway_id");
            $table->unsignedBigInteger("discount_id")->nullable();
            $table->string("gateway_subscription_id");
            $table->string("status", 50);
            $table->text("cancel_reason")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("plan_id")->references("id")->on((new Plan())->getTable());
            $table->foreign("gateway_id")->references("id")->on((new PaymentGateway())->getTable());
            $table->foreign("discount_id")->references("id")->on((new Discount())->getTable());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = (new Subscription())->getTable();
        Schema::table($tableName, function (Blueprint $table) use($tableName){
            $table->dropForeign($tableName."_plan_id_foreign");
            $table->dropForeign($tableName."_gateway_id_foreign");
            $table->dropForeign($tableName."_discount_id_foreign");
        });
        Schema::dropIfExists((new Subscription())->getTable());
    }
};
