<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Enums\FeeType;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            config('subscription.subscription_table_prefix') . '_payment_gateways',
            function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 50)->unique();
                $table->string('gateway_type', 50);
                $table->tinyInteger('display_order')->default(0);
                $table->string('logo', 255)->nullable();
                $table->string('url', 255)->nullable();
                $table->decimal('fee', 6, 2)->default(0);
                $table->string('fee_type', 30)->default(FeeType::FIXED());
                $table->boolean('is_active')->default(true);
                $table->boolean('customer_visible')->default(true);
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('subscription.subscription_table_prefix') . '_payment_gateways');
    }
}
