<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoveOn\Subscription\Enums\DiscountDuration;
use MoveOn\Subscription\Enums\FeeType;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\PaymentGateway;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Discount())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string("name", 200);
            $table->unsignedBigInteger("gateway_id")->index();
            $table->string("coupon_code", 50)->unique();
            $table->string("currency", 10)->default("USD")->nullable();
            $table->string("duration", 30)->default(DiscountDuration::ONCE());
            $table->integer("duration_in_months")->nullable();
            $table->string("amount_type", 30)->default(FeeType::FIXED());
            $table->string("gateway_coupon_id")->nullable();
            $table->decimal("amount", 8, 2);
            $table->decimal("maximum_discount_amount", 8, 2)->nullable();
            $table->boolean("is_active")->default(false);
            $table->timestamp("expired_at")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("gateway_id")
                  ->references("id")
                  ->on((new PaymentGateway())->getTable())
                  ->restrictOnDelete()
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Discount())->getTable());
    }
};
