<?php

use App\Models\PlanGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\Plan;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_group_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("plan_group_id");
            $table->unsignedBigInteger("plan_id");
            $table->boolean("is_primary")->default(false);
            $table->foreign("plan_group_id")->references("id")->on((new PlanGroup())->getTable())->cascadeOnDelete();
            $table->foreign("plan_id")->references("id")->on((new Plan())->getTable())->cascadeOnDelete();
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
        Schema::dropIfExists('plan_group_plans');
    }
};
