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
        Schema::create('scopes', function (Blueprint $table) {
            $table->bigInteger('id', false, true)->primary();
            $table->string('scope_name', 20);
        });

        DB::table('scopes')->insert([
            ['id' => 0, 'scope_name' => 'view-orders'],
            ['id' => 1, 'scope_name' => 'prepare-dishes'],
            ['id' => 2, 'scope_name' => 'deliver-orders'],
            ['id' => 3, 'scope_name' => 'cancel-orders'],
            ['id' => 4, 'scope_name' => 'manage-users'],
            ['id' => 5, 'scope_name' => 'manage-products'],
            ['id' => 6, 'scope_name' => 'complete-orders'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scopes');
    }
};
