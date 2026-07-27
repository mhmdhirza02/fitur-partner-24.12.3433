<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PostgresBoolean implements CastsAttributes
{
    /**
     * Cast the given value from the database into a PHP boolean.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Prepare the given value for storage in the database.
     * For PostgreSQL, returns string literal ('true'/'false') to prevent integer 1/0 conversion
     * when PDO::ATTR_EMULATE_PREPARES is enabled. For MySQL/SQLite, returns standard PHP boolean.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($model->getConnection()->getDriverName() === 'pgsql') {
            return $bool ? 'true' : 'false';
        }

        return $bool;
    }
}
