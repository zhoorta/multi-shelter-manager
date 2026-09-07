<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Enforces the application's strict shelter-based data isolation: a model
 * using this trait is only ever queried and created within the authenticated
 * user's own shelter_id, so data can never leak or cross-contaminate between
 * shelters.
 *
 * Admins are exempt because they are globally authorized across all
 * shelters; they are identified by having no shelter_id of their own (a
 * manager or staff member is always assigned one on invitation), since
 * filtering by a null shelter_id would otherwise hide every row instead of
 * showing all of them.
 */
trait MultiShelterTrait
{
    protected static function bootMultiShelterTrait(): void
    {
        static::addGlobalScope('shelter', function (Builder $builder): void {
            $model = $builder->getModel();

            if (! static::hasShelterColumn($model)) {
                return;
            }

            if (! Auth::hasUser()) {
                return;
            }

            $user = Auth::user();

            if (! $user || $user->shelter_id === null) {
                return;
            }

            $builder->where($model->getTable().'.shelter_id', $user->shelter_id);
        });

        static::creating(function (Model $model): void {
            $user = Auth::hasUser() ? Auth::user() : null;

            if ($user && $user->shelter_id !== null && static::hasShelterColumn($model) && ! $model->shelter_id) {
                $model->shelter_id = $user->shelter_id;
            }
        });
    }

    protected static function hasShelterColumn(Model $model): bool
    {
        static $cache = [];

        return $cache[$model::class] ??= Schema::hasColumn($model->getTable(), 'shelter_id');
    }
}
