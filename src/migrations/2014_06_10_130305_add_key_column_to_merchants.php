<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeyColumnToMerchants extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('ff-qiwi-gate')->table('merchants', function (Blueprint $table) {
			$table->string('key')->after('callback_url');
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
			$table->dropColumn('key');
		});
	}

}
