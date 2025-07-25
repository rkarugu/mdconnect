<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
*/
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id')->nullable()->after('password');

        // Optional: Add foreign key constraint
        $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
    });
}
};