<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\PlanTier;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create((new PlanTier())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId("plan_id")->references("id")->on((new Plan())->getTable());
            $table->unsignedInteger("start");
            $table->unsignedInteger("end")->nullable();
            $table->decimal("unit_amount", 7, 2);
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
        $tableName = (new PlanTier())->getTable();

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table($tableName, function (Blueprint $table) use($tableName){
                $table->dropForeign("{$tableName}_plan_id_foreign");
            });
        }

        Schema::dropIfExists($tableName);
    }
};
