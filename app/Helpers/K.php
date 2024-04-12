<?php

namespace App\Helpers;

use DateTime;
use App\Models\User;
use NumberFormatter;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Vite;

class K {
    /**
     * Change an elements name e.g. `name[value]` to
     * dot notation e.g. `name.value`.
     *
     * @param string $var
     * @return string
     */
    public static function toDot($var) {
        return preg_replace(['/\[/', '/\]/'], ['.', ''], $var);
    }

    public static function isTrue($value) {
        return in_array($value, ['on', 'true', true, 1, '1', 'TRUE'], true);
    }

    public static function oneTrue($array) {
        foreach ($array as $value)
            if (self::isTrue($value)) return true;
        return false;
    }

    public static function checked($value) {
        return self::isTrue($value) ? ' checked' : '';
    }

    public static function selected($value, $other) {
        return $value == $other ? ' selected' : '';
    }

    public static function log(...$values) {
        // ini_set('error_log', public_path('error_log'));

        foreach ($values as $value) {
            if (in_array(gettype($value), ['array', 'object', 'boolean', 'NULL']))
                error_log(json_encode($value, JSON_PRETTY_PRINT));
            else
                error_log($value);
        }
    }

    public static function print(...$values) {
        foreach ($values as $value) {
            if (in_array(gettype($value), ['array', 'object', 'boolean', 'NULL']))
                error_log(json_encode($value, JSON_PRETTY_PRINT));
            else
                error_log($value);
        }
    }

    public static function makeId($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return "_$randstring";
    }

    public static function truncate($string, $length = 75) {
        return strlen($string) > $length ? trim(substr($string, 0, $length)) . "..." : $string;
    }

    public static function makeClass($classes) {
        try {
            return implode(' ', $classes);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $classes ?? '';
    }

    public static function generateToken() {
        //Generate a random string.
        $token = openssl_random_pseudo_bytes(16);

        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);

        return $token;
    }

    public static function timestamp($modify = '') {
        $date = new DateTime();
        if ($modify) $date->modify($modify);
        return $date;
    }

    public static function expired($stamp) {
        return new DateTime() > $stamp;
    }

    public static function valid($stamp) {
        return !self::expired($stamp);
    }

    public static function getPageCookie($page) {
        $cookie = $_COOKIE[$page] ?? (object) [];
        if (is_string($cookie)) $cookie = json_decode($cookie);
        return $cookie;
    }

    /**
     * @param array $arrays
     * @return array Merged array
     */
    public static function merge(...$arrays) {
        $target = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => &$value) {
                if (is_array($value) && isset($target[$key]) && is_array($target[$key])) {
                    if (self::isAssoc($value)) {
                        $target[$key] = self::merge($target[$key], $value);
                    } else {
                        $target[$key] = array_merge($target[$key], $value);
                    }
                } else {
                    $target[$key] = $value;
                }
            }
        }

        return $target;
    }

    public static function isAssoc($array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function asset($path, $file) {
        $url = asset($path) . $file;
        $path = public_path($path) . preg_replace("/\?.*$/", '', $file);
        if (is_file($path)) {
            $stamp = filemtime($path);
            $mark = strpos($url, '?') === false ? '?' : '&';
            return "$url{$mark}v=$stamp";
        } else {
            return $url;
        }
    }

    public static function stripParentheses($expression) {
        if (Str::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        return $expression;
    }

    /**
     * Get the svg version of the Font Awesome Icon
     * 
     * @param string $icon    The icon name
     * @param array  $options Additional options for the getter  
     */
    public static function icon($icon, $options = []) {
        $options = array_merge([
            'icon' => $icon,
            'type' => 'regular' // can also have 'color', 'class' & 'id'
        ], $options);

        $ret = file_get_contents('https://fa.kgl.app?' . http_build_query($options));
        return substr($ret, 0, 1) === '<' ? $ret : '';
    }

    /**
     * Get the svg version of the Font Awesome Icon
     * 
     * @param array $icon    The icon name
     * @param assoc $options Shared options for the getter
     */
    public static function icons($icons, $options = []) {
        if (!is_array($icons) || !count($icons)) return [];

        foreach ($icons as &$icon) {
            $icon = array_merge($icon, $options);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fa.kgl.app');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['array' => $icons]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        if (!$res) return [];
        return json_decode($res);
    }

    /**
     * Converts a multidimensional array to an object.
     * 
     * @param array   $array The array to convert.
     * @return object The converted array.
     */
    public static function toObject($array) {
        if (in_array(gettype($array), ['object', 'array']))
            return json_decode(json_encode($array), false);
        return $array;
    }
    /**
     * Converts an object to a multidimensional array.
     * 
     * @param object $object The object to convert.
     * @return array The converted object.
     */
    public static function toArray($object) {
        if (in_array(gettype($object), ['object', 'array']))
            return json_decode(json_encode($object), true);
        return $object;
    }

    public static function convertAttrs(array &$attrs) {
        foreach ($attrs as $attr => $value) {
            if (is_array($value))
                $attrs[$attr] = json_encode($value);
        }
    }

    public static function textSnippet($string, $length = 20) {
        if (strlen($string) <= $length) return $string;
        $mid = 3;
        $sI = floor(($length - $mid) / 2);
        $eI = $length - $mid - $sI;
        $sS = substr($string, 0, $sI);
        $eS = substr($string, -$eI, $eI);
        return $sS . str_repeat('*', $mid) . $eS;
    }

    public static function isActive($check, $against, $word = 'active') {
        return $check == $against ? " $word" : '';
    }

    public static function getIcons() {
        $icons = file_get_contents(asset('js/data/icons.json'));
        $icons = json_decode($icons);

        foreach ($icons as &$icon) {
            $icon = ['icon' => $icon];
        }

        $icons = self::icons($icons, ['type' => 'duotone']);

        return $icons;
    }

    public static function selectConvert($options) {
        $r_val = null;
        if (gettype($options) == 'string') {
            $r_val = (object) [];
            $each = explode("\n", $options);
            foreach ($each as $v) {
                $part = explode(":", $v);
                $r_val->{$part[0]} = $part[1];
            }
        } else {
            $r_val = [];
            foreach ($options as $k => $v) {
                $r_val[] = "$k:$v";
            }
            $r_val = implode("\n", $r_val);
        }
        return $r_val;
    }

    /**
     * Get and decode a json file to object
     * 
     * @param string $path
     * @return array|object
     */
    public static function getJson(string $path) {
        try {
            return json_decode(file_get_contents(Vite::asset($path)));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return [];
    }


    /**
     * Get file contents
     * 
     * @param string $path
     * @return string
     */
    public static function getContents(string $path) {
        try {
            return file_get_contents($path);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return '';
    }

    public static function flattenArray(array $arr, object &$to) {
        foreach ($arr as $key => $value) {
            if (is_array($value) && self::isAssoc($value)) {
                self::flattenArray($value, $to);
            } else $to->$key = $value;
        }
    }

    public static function hashObject($object) {
        return hash('sha1', json_encode($object));
    }

    /**
     * Get the authenticated user
     * 
     * @return \App\Models\User
     */
    public static function user(): User {
        $id = auth()->user()->id ?? 0;
        return User::findOrNew($id);
    }

    /**
     * Pluralizes a word if quantity is not one.
     *
     * @param string $singular Singular form of word
     * @param string $plural Plural form of word
     * @param int $qty Number of items
     * 
     * @return string Pluralized word if quantity is not one, otherwise singular
     */
    public static function pluralize($singular, $plural, $qty) {
        $word = $qty == 1 ? $singular : $plural;
        return str_replace('%', $qty, $word);
    }

    /**
     * Format a time string to a human readable format.
     * 
     * @param string $time
     * @param string $format defaults to 'g:i A' e.g. '5:00 AM'
     * 
     * @return string
     */
    public static function formatTime($time, $format = 'g:i A') {
        if (!$time) return;
        return date($format, strtotime($time));
    }

    public static function formatCurrency($value) {
        if ($value < 0.9999) return $value . 'p';
        $fmt = new NumberFormatter('en_UK', NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($value, 'GBP');
    }

    /**
     * Parse a date string into a Carbon object.
     * 
     * @param string|Carbon $date
     * @return Illuminate\Support\Carbon;
     */
    public static function date($date = '') {
        $date = $date instanceof Carbon ? $date : Carbon::create($date);
        return $date->locale('en');
    }

    /**
     * Get a now date without the time
     * @return Illuminate\Support\Carbon;
     */
    public static function now() {
        return self::date(now()->format('Y-m-d'));
    }

    /**
     * Parse a date string or a Carbon object into a formatted string.
     * 
     * @param string|Carbon $date
     * @return Illuminate\Support\Carbon;
     */
    public static function dateString($date = '', $format = 'Y-m-d') {
        $date = $date instanceof Carbon ? $date : Carbon::create($date);
        return $date->format($format);
    }

    public static function displayDate($date, $format = 'D, M jS') {
        return self::date($date)->format($format);
    }

    /**
     * Get hourly rate from given hours and minutes.
     * 
     * @param int $hours
     * @param int $minutes
     * @param int|double $rate
     * 
     * @return double
     */
    public static function getHourly($rate, $hours, $minutes) {
        $time = $hours + ($minutes ? ($minutes / 60) : 0);
        return $time ? $rate / $time : $rate;
    }

    public static function getPayDay($date, $weeks = 2, $day = 'Thursday') {
        if (is_numeric($day)) $day = Lists::weekDays($day);

        $date = self::date($date);
        $dN = array_search(ucfirst($day), Carbon::getDays());

        while ($date->dayOfWeek != $dN) {
            if ($date->dayOfWeek > $dN)
                $date->subDays(1);
            if ($date->dayOfWeek < $dN)
                $date->addDays(1);
        }

        return $date->addWeeks($weeks);
    }
    /**
     * Join a string with a natural language conjunction at the end. 
     * 
     * @param array $list
     * @param string|null $conjunction default to 'and'
     * @return string
     */
    public static function readableJoin(array $list, string $conjunction = null): string {
        if (!$conjunction) $conjunction = __('and');
        $last = array_pop($list);
        if ($list)
            return implode(', ', $list) . " $conjunction $last";
        return $last ?? '';
    }

    /**
     * Convert seconds to human readable format.
     * 
     * @param int $input
     * @return string
     */
    public static function secondsToHuman($input) {
        $secInMin = 60;
        $secInHr = $secInMin * 60;
        $secInDay = $secInHr * 24;

        $days = floor($input / $secInDay);

        $hrSecs = $input % $secInDay;
        $hrs = floor($hrSecs / $secInHr);

        $minSecs = $hrSecs % $secInHr;
        $mins = floor($minSecs / $secInMin);

        $join = [];
        if ($days > 0)
            $join[] = self::pluralize(__('% day'), __('% days'), $days);
        if ($hrs > 0)
            $join[] = self::pluralize(__('% hour'), __('% hours'), $hrs);
        if ($mins > 0)
            $join[] = self::pluralize(__('% minute'), __('% minutes'), $mins);

        return self::readableJoin($join);
    }

    /**
     * Cast assoc array of items to passed types.
     * 
     * @param array $items
     * @param array $types
     * @return array
     */
    public static function castArray(array $items, array $types): array {
        $r = [];
        foreach ($items as $k => $v) {
            if (isset($types[$k]))
                $r[$k] = self::cast($v, $types[$k]);
        }
        return $r;
    }

    protected static $castTypes = [
        'array',
        'bool',
        'boolean',
        'date',
        'datetime',
        'decimal',
        'double',
        'float',
        'int',
        'integer',
        'json',
        'object',
        'string',
        'timestamp',
    ];

    /**
     * Cast a value to a given type.
     * 
     * @param mixed $value
     * @param string|array $type
     * @return mixed
     */
    public static function cast($value, string|array $type) {
        $isNullable = false;
        $hasDefault = false;
        $default = null;
        $format = null;

        $aType = $type;
        if (gettype($aType) == 'string')
            $aType = array_map(fn ($item) => Str::of($item)->trim(), explode(',', $aType));

        foreach ($aType as $v) {
            $v = explode(':', $v, 2);

            // get the type and format if exists
            if (in_array($v[0], self::$castTypes)) {
                $type = $v[0];
                if (count($v) > 1)
                    $format = $v[1];
                continue;
            }

            // see if nullable
            if (in_array($v[0], ['null', 'nullable'])) {
                $isNullable = true;
                continue;
            }

            // see if has default and get it
            if ($v[0] == 'default') {
                $hasDefault = true;
                $default = $v[1];
                continue;
            }
        }

        // self::log([
        //     'type' => $type,
        //     'isNullable' => $isNullable,
        //     'hasDefault' => $hasDefault,
        //     'default' => $default,
        //     'format' => $format
        // ]);

        if ($hasDefault && !$value)
            return $default;

        if ($isNullable && !$value)
            return null;

        switch ($type) {
            case 'array':
            case 'json':
            case 'object':
                // have to make this if needed
                return $value;
            case 'bool':
            case 'boolean':
                return self::isTrue($value) ? 1 : 0;
            case 'date':
            case 'datetime':
            case 'timestamp':
                $value = self::date(str_replace("'", '', $value));
                if ($format)
                    return $value->format($format);
                return $value;
            case 'decimal':
            case 'double':
            case 'float':
                return (float) $value;
            case 'int':
            case 'integer':
                return (int) $value;
            case 'string':
                return (string) $value;
            default:
                return $value;
        }
    }

    /**
     * Get first day of the week.
     * 
     * @param string|carbon|null $date
     * @return Carbon
     */
    public static function firstDayOfWeek($date = '') {
        $date = self::date($date);
        while ($date->format('D') != 'Sun')
            $date->subDays(1);
        return $date;
    }

    /**
     * Get last day of the week.
     * 
     * @param string|carbon|null $date
     * @return Carbon
     */
    public static function lastDayOfWeek($date = '') {
        $date = self::date($date);
        while ($date->format('D') != 'Sat')
            $date->addDays(1);
        return $date;
    }

    public static function type($value) {
        return gettype($value);
    }

    public static function class($value) {
        return get_class($value);
    }
}
