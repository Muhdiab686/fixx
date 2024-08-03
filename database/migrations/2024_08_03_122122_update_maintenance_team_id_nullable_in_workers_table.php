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
        Schema::table('_worker', function (Blueprint $table) {
            $table->foreignId('maintenance_team_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('_worker', function (Blueprint $table) {
            $table->foreignId('maintenance_team_id')->nullable(false)->change();

        });
    }
};
