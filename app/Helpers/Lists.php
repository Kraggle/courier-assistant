<?php

namespace App\Helpers;

class Lists {
	/**
	 * Get a collection of the expense types
	 * 
	 * @return \Illuminate\Support\Collection|string
	 */
	public static function expenseTypes(string $type = null) {
		$c = collect([
			'work' => 'Other allowable business expenses',
			'vehicle' => 'Car, van and travel expenses',
			'maintenance' => 'Repairs and maintenance',
			'office' => 'Telephone, fax, stationery and other office costs',
			'interest' => 'Interest and bank and credit card etc. financial charges',
			'professional' => 'Accountancy, legal and other professional fees'
		]);
		if ($type) return $c[$type];
		return $c;
	}

	/**
	 * Get a collection of the route types
	 * 
	 * @return \Illuminate\Support\Collection|string
	 */
	public static function routeTypes(string $type = null) {
		$c = collect([
			'md' => 'Standard Route',
			'lg' => 'Large Route',
			'mfn-md' => 'Standard Collection',
			'mfn-lg' => 'Large Collection',
			'same-md' => 'Standard Collection with Sameday',
			'same-lg' => 'Large Collection with Sameday',
			'poc' => 'On Site Manager',
		]);
		if ($type) return $c[$type];
		return $c;
	}

	/**
	 * Get a collection of the expense types
	 * 
	 * @return \Illuminate\Support\Collection|string
	 */
	public static function rateTypes(string $type = null) {
		$c = collect([
			'fuel' => 'Invoice fuel rate (per mile)',
			'md' => 'Standard route (per day)',
			'lg' => 'Large route (per day)',
			'mfn-md' => 'Standard collection (per day)',
			'mfn-lg' => 'Large collection (per day)',
			'same-md' => 'Standard collection with sameday (per day)',
			'same-lg' => 'Large collection with sameday (per day)',
			'poc' => 'On site manager (per day)',
		]);
		if ($type) return $c[$type];
		return $c;
	}

	/**
	 * Get a collection of days of the week
	 * 
	 * @return \Illuminate\Support\Collection|string
	 */
	public static function weekDays(int $day = null) {
		$c = collect([
			0 => 'Sunday',
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
		]);
		if ($day) return $c[$day];
		return $c;
	}
}
