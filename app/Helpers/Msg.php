<?php

namespace App\Helpers;

class Msg {
	/**
	 * Create a message for missing headers while parsing a CSV.
	 * 
	 * @param array $headers
	 * @return string
	 */
	public static function bulkHeaders(array $headers): string {
		$keys = K::readableJoin(array_map(fn ($v) => "`$v`", $headers));
		return __('The headers have to be :headers, you are missing at least one!', ['headers' => $keys]);
	}

	/**
	 * Create a message for headers while uploading a CSV.
	 * 
	 * @param array $headers
	 * @param array $optional
	 * @return string
	 */
	public static function bulkHelper(array $headers, array $optional = []): string {
		$keys = K::readableJoin(array_map(fn ($v) => "`$v`", $headers));
		if ($optional)
			$opt = K::readableJoin(array_map(fn ($v) => "`$v`", $optional));
		return __('You need to have at least the columns :headers', ['headers' => $keys]) . ($optional ? __(', you can also optionally have :optional.', ['optional' => $opt]) : '.');
	}

	/**
	 * Invalid file message.
	 * 
	 * @return string
	 */
	public static function invalidFile(): string {
		return __('You have to upload a valid file!');
	}

	/**
	 * Successfully added your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function added(string $type): string {
		return __('Successfully added your :type!', ['type' => $type]);
	}

	/**
	 * Successfully deleted your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function deleted(string $type): string {
		return __('Successfully deleted your :type!', ['type' => $type]);
	}

	/**
	 * Successfully edited your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function edited(string $type): string {
		return __('Successfully updated your :type!', ['type' => $type]);
	}

	/**
	 * Are you sure you want to delete... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function sureDelete(string $type): string {
		return __('Are you sure you want to delete this :type?', ['type' => $type]);
	}

	/**
	 * Add... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function add(string $type): string {
		return __('Add :type', ['type' => $type]);
	}

	/**
	 * Edit... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function edit(string $type): string {
		return __('Edit :type', ['type' => $type]);
	}

	/**
	 * Delete... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function delete(string $type): string {
		return __('Delete :type', ['type' => $type]);
	}

	/**
	 * You don't appear to have added any... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function noResults(string $type): string {
		return __("You don't appear to have added any :types yet!", ['types' => $type]);
	}

	/**
	 * Export all of your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function exportQuestion(string $type): string {
		return __("Do you want to export all of your :types as CSV?", ['types' => $type]);
	}

	/**
	 * Export... title.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function exportTitle(string $type): string {
		return __("Export :types", ['types' => $type]);
	}

	/**
	 * Bulk... title.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function bulkTitle(string $type): string {
		return __("Bulk add :types", ['types' => $type]);
	}
}
