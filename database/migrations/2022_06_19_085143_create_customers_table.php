<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\GatewayAssociatedCustomer;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new GatewayAssociatedCustomer())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->morphs("ownerable");
            $table->foreignId("gateway_id");
            $table->string("gateway_customer_id");
            $table->boolean("is_active")->default(true);
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
        $tableName = (new GatewayAssociatedCustomer())->getTable();
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            $table->foreignId($tableName . "_gateway_id_foreign");
        });
        Schema::dropIfExists($tableName);
    }
};
