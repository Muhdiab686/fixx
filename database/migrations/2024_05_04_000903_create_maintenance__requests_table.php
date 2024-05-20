<?php

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
        Schema::create('maintenance__requests', function (Blueprint $table) {
            $table->id();
            $table->string("free_day");
            $table->string("number");
            $table->string("QR_code");
            $table->string("video")->nullable;
            $table->text("notes")->default("null");
            $table->string("Request_details")->default("null");
            $table->string("Request_state")->default("null");
            $table->string("consumable_parts")->default("null");
            $table->string("repairs")->default("null");
            $table->string("warranty_state")->default("null");
            $table->string("salary")->default("null");
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete;
            $table->foreignId('worker_id')->constrained('_worker')->cascadeOnDelete;
            $table->foreignId('electrical_part_id')->constrained('electrical_parts')->cascadeOnDelete;
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
        Schema::dropIfExists('maintenance__requests');
    }
};
