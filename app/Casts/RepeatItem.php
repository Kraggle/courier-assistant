<?php

namespace App\Casts;

use App\Helpers\K;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class RepeatItem implements CastsAttributes {
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed {
        return (object) K::castArray(json_decode($value, true), [
            'describe' => 'string',
            'cost' => ['default:0', 'float'],
            'type' => ['default:work', 'string']
        ]);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed {
        return json_encode($value);
    }
}
