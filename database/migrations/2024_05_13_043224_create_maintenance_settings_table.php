<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('maintenance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->unique();
            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_settings');
    }
}
