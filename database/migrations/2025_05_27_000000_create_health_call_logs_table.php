<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('health_call_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('service')->index();
            $table->string('status', 16)->index();
            $table->float('latency')->default(0);
            $table->text('message')->nullable();
            $table->unsignedBigInteger('user')->nullable()->index();
            $table->string('ip', 64)->nullable();
            $table->timestamp('checked_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('health_call_logs');
    }
}
