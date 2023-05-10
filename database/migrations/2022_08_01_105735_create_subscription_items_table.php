<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\SubscriptionItem;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new SubscriptionItem())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId("subscription_id");
            $table->foreignId("plan_id")->nullable()->references("id")->on((new Plan())->getTable())->restrictOnDelete();
            $table->string("gateway_subscription_item_id");
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
        $tableName = (new SubscriptionItem())->getTable();
        if(DB::getDriverName() != "sqlite"){
            Schema::table($tableName, function (Blueprint $table) use($tableName){
                $table->dropForeign($tableName."_plan_id_foreign");
            });
        }
        Schema::dropIfExists($tableName);
    }
};
