<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillIdToMerchantBiilsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('ff-qiwi-gate')->table('merchants_bills', function (Blueprint $table) {
			$table->string('bill_id', 100)->after('merchant_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('ff-qiwi-gate')->table('merchants_bills', function (Blueprint $table) {
			$table->dropColumn('bill_id');
		});
	}

}
