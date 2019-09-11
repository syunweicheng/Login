<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMembersTable.
 */
class CreateMembersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('members', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('password');
			$table->string('mobile')->comment('mobile')->unique();
			$table->string('email')->comment('電子信箱')->unique();
			
			$table->unsignedTinyInteger('status')->default(0)->comment('狀態');
			
			$table->rememberToken();
			$table->timestamps();
			
			$table->index('email');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('members');
	}
}
