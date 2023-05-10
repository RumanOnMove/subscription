<?php

use App\Models\PlanGroup;
use App\Models\User;
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
        Schema::create('user_trial_plan_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("user_id");
            $table->foreignId("plan_group_id");
            $table->foreignId("plan_id");
            $table->foreign("user_id")
                  ->references("id")
                  ->on((new User())->getTable())
                  ->cascadeOnDelete();
            $table->foreign("plan_group_id")
                  ->references("id")
                  ->on((new PlanGroup())->getTable())
                  ->cascadeOnDelete();
            $table->foreign("plan_id")
                  ->references("id")
                  ->on((new Plan())->getTable())
                  ->cascadeOnDelete();
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
        Schema::dropIfExists('user_trial_plan_groups');
    }
};
