<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UsersAddRememberMeToken extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('remember_token')->default('');
        });
    }
}
