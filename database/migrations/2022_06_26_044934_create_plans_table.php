<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Enums\IntervalUnit;
use MoveOn\Subscription\Enums\PricingScheme;
use MoveOn\Subscription\Enums\UsageType;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Plan;
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
        Schema::create((new Plan())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId("gateway_id")->references("id")->on((new PaymentGateway())->getTable());
            $table->foreignId("product_id")->references("id")->on((new Product())->getTable());
            $table->string("gateway_plan_id")->nullable();
            $table->string("currency", 10);
            $table->string("name", 200);
            $table->string("description", 200)->nullable();
            $table->decimal("unit_amount", 7, 2)->nullable();
            $table->string("quantity_source", 50);
            $table->integer("default_quantity")->default(0);
            $table->string("usage_type",100)->default(UsageType::LICENCED());
            $table->string("interval_unit")->default(IntervalUnit::MONTH());
            $table->integer("interval_count");
            $table->string("pricing_scheme", 100)->default(PricingScheme::FIXED());
            $table->integer("trial_period_days");
            $table->decimal("system_usage_charge", 7, 2);
            $table->string("is_active")->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = (new Plan())->getTable();
        if(DB::getDriverName() != "sqlite"){
            Schema::table($tableName, function (Blueprint $table) use($tableName){
                $table->dropForeign("{$tableName}_gateway_id_foreign");
                $table->dropForeign("{$tableName}_product_id_foreign");
            });
        }

        Schema::dropIfExists($tableName);
    }
};
