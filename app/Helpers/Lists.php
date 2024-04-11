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
			'work' => __('Cost of equipment used at work'),
			'vehicle' => __('Vehicle and travel expenses'),
			'maintenance' => __('Repairs and maintenance for vehicles'),
			'office' => __('Office costs (phones and stationery)'),
			'interest' => __('Interest on loans'),
			'charges' => __('Bank, credit card and other financial charges'),
			'professional' => __('Accountancy, legal and other professional costs')
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
			'md' => __('Standard Van'),
			'lg' => __('Large Van'),
			'mfn-md' => __('Standard Van Collection'),
			'mfn-lg' => __('Large Van Collection'),
			'poc' => __('On Site Manager'),
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
			'fuel' => __('Invoice fuel rate (per mile)'),
			'md' => __('Standard van (per day)'),
			'lg' => __('Large van (per day)'),
			'mfn-md' => __('Standard van collection (per day)'),
			'mfn-lg' => __('Large van collection (per day)'),
			'poc' => __('On site manager (per day)'),
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
			0 => __('Sunday'),
			1 => __('Monday'),
			2 => __('Tuesday'),
			3 => __('Wednesday'),
			4 => __('Thursday'),
			5 => __('Friday'),
			6 => __('Saturday'),
		]);
		if ($day) return $c[$day];
		return $c;
	}
}
