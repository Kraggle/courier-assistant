<?php

namespace App\Helpers;

class ColorHelper {
	public static function hexToHsl($hex) {
		$rgb = self::hexToRgb($hex);
		$rgb = array_map(function ($part) {
			return $part / 255;
		}, $rgb);

		$max = max($rgb);
		$min = min($rgb);

		$l = ($max + $min) / 2;

		if ($max == $min) {
			$h = $s = 0;
		} else {
			$diff = $max - $min;
			$s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

			switch ($max) {
				case $rgb[0]:
					$h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
					break;
				case $rgb[1]:
					$h = ($rgb[2] - $rgb[0]) / $diff + 2;
					break;
				case $rgb[2]:
					$h = ($rgb[0] - $rgb[1]) / $diff + 4;
					break;
			}

			$h /= 6;
		}

		return array($h, $s, $l);
	}

	public static function hexToRGB($hex) {
		$hex = str_replace('#', '', $hex);
		$hex = array($hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]);
		return array_map(function ($part) {
			return hexdec($part);
		}, $hex);
	}

	public static function toString($array) {
		return implode(', ', $array);
	}

	public static function hslToHex($hsl) {
		list($h, $s, $l) = $hsl;
		if ($s == 0) $s = 0.000001;

		$q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
		$p = 2 * $l - $q;

		$r = self::hue2rgb($p, $q, $h + 1 / 3);
		$g = self::hue2rgb($p, $q, $h);
		$b = self::hue2rgb($p, $q, $h - 1 / 3);

		return self::rgb2hex($r) . self::rgb2hex($g) . self::rgb2hex($b);
	}

	public static function hue2rgb($p, $q, $t) {
		if ($t < 0) $t += 1;
		if ($t > 1) $t -= 1;
		if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
		if ($t < 1 / 2) return $q;
		if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;

		return $p;
	}

	public static function rgb2hex($rgb) {
		return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
	}

	public static function stringToColor(string $string) {
		$hash = 0;
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++) {
			$hash = ord(substr($string, $i, 1)) + (($hash << 5) - $hash);
		}
		$color = '';
		for ($i = 0; $i < 3; $i++) {
			$value = ($hash >> ($i * 8)) & 0xFF;
			$color .= substr(('00' . dechex($value)), -2, 2);
		}

		return $color;
	}

	public static function set($hex, $saturation = 0.5, $lightness = 0.65) {
		$hsl = self::hexToHsl($hex);
		if ($saturation != null)
			$hsl[1] = $saturation;
		if ($lightness != null)
			$hsl[2] = $lightness;
		return self::hslToHex($hsl);
	}
}
