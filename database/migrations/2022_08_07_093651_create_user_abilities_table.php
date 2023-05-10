<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_abilities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("user_id");
            $table->morphs("model");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("user_id")->references("id")->on((new User())->getTable())->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_abilities');
    }
};
