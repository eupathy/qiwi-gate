<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEmailToMerchantTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('ff-qiwi-gate')->table('merchants', function (Blueprint $table) {
			$table->string('email')->after('callback_url');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('ff-qiwi-gate')->table('merchants', function (Blueprint $table) {
			$table->dropColumn('email');
		});
	}

}
