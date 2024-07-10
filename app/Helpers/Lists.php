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
			'work' => 'Cost of equipment used at work',
			'vehicle' => 'Vehicle and travel expenses',
			'maintenance' => 'Repairs and maintenance for vehicles',
			'office' => 'Office costs (phones and stationery)',
			'interest' => 'Interest on loans',
			'charges' => 'Bank, credit card and other financial charges',
			'professional' => 'Accountancy, legal and other professional costs'
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
			'md' => 'Standard Van',
			'lg' => 'Large Van',
			'mfn-md' => 'Standard Van Collection',
			'mfn-lg' => 'Large Van Collection',
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
			'md' => 'Standard van (per day)',
			'lg' => 'Large van (per day)',
			'mfn-md' => 'Standard van collection (per day)',
			'mfn-lg' => 'Large van collection (per day)',
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
