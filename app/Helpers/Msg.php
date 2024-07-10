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
		return "The headers have to be $keys, you are missing at least one!";
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
		if ($optional) $opt = K::readableJoin(array_map(fn ($v) => "`$v`", $optional));
		return ("You need to have at least the columns $keys") . ($optional ? ", you can also optionally have $opt." : '');
	}

	/**
	 * Invalid file message.
	 * 
	 * @return string
	 */
	public static function invalidFile(): string {
		return 'You have to upload a valid file!';
	}

	/**
	 * Successfully added your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function added(string $type): string {
		return "Successfully added your $type!";
	}

	/**
	 * Successfully deleted your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function deleted(string $type): string {
		return "Successfully deleted your $type!";
	}

	/**
	 * Successfully edited your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function edited(string $type): string {
		return "Successfully updated your $type!";
	}

	/**
	 * Are you sure you want to delete... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function sureDelete(string $type): string {
		return "Are you sure you want to delete this $type?";
	}

	/**
	 * Add... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function add(string $type): string {
		return "Add $type";
	}

	/**
	 * Edit... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function edit(string $type): string {
		return "Edit $type";
	}

	/**
	 * Delete... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function delete(string $type): string {
		return "Delete $type";
	}

	/**
	 * You don't appear to have added any... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function noResults(string $type): string {
		return "You don't appear to have added any $type yet!";
	}

	/**
	 * Export all of your... message.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function exportQuestion(string $type): string {
		return "Do you want to export all of your $type as CSV?";
	}

	/**
	 * Export... title.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function exportTitle(string $type): string {
		return "Export $type";
	}

	/**
	 * Bulk... title.
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function bulkTitle(string $type): string {
		return "Bulk add $type";
	}
}
