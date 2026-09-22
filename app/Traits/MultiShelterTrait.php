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
 * user's current shelter (users.current_shelter_id), so data can never leak
 * or cross-contaminate between shelters.
 *
 * Admins are exempt because they are globally authorized across all
 * shelters (users.is_admin). A non-admin with no current_shelter_id
 * selected sees no rows at all, never every row — a user can belong to
 * several shelters via the shelter_users pivot, so the absence of a shelter
 * id no longer implies "unrestricted" the way it did under the old
 * single-shelter schema.
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

            if (! $user) {
                return;
            }

            if ($user->is_admin) {
                return;
            }

            if ($user->current_shelter_id === null) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->where($model->getTable().'.shelter_id', $user->current_shelter_id);
        });

        static::creating(function (Model $model): void {
            $user = Auth::hasUser() ? Auth::user() : null;

            if (! $user || $user->is_admin || ! static::hasShelterColumn($model)) {
                return;
            }

            if ($user->current_shelter_id !== null && ! $model->shelter_id) {
                $model->shelter_id = $user->current_shelter_id;
            }
        });
    }

    protected static function hasShelterColumn(Model $model): bool
    {
        static $cache = [];

        return $cache[$model::class] ??= Schema::hasColumn($model->getTable(), 'shelter_id');
    }
}
