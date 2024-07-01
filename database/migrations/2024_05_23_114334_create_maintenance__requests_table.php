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
            $table->string("free_day")->nullable();
            $table->string("number");
            $table->string("QR_code")->nullable();
            $table->string("video")->nullable();
            $table->text("notes")->default("null");
            $table->string("Request_details")->default("null");
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_type')->default('person');
            $table->enum('Request_state',['Pending','Complete']);
            $table->string("consumable_parts")->default("null");
            $table->string("repairs")->default("null");
            $table->string("warranty_state")->default("null");
            $table->string("salary")->default("null");
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete;
            $table->foreignId('team_id')->constrained('maintenance_teams')->cascadeOnDelete;
            $table->foreignId('elec_id')->constrained('electrical_parts')->cascadeOnDelete;
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
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
