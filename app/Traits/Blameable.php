<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Stamps created_by, updated_by, and deleted_by with the authenticated user's id.
 *
 * Each column is stamped only if the model's table actually has it, so this trait
 * can be shared by fully audited models and by tables that only track creation
 * (e.g. no deleted_by because the table has no soft deletes).
 */
trait Blameable
{
    protected static function bootBlameable(): void
    {
        static::creating(function (Model $model): void {
            if (! Auth::check()) {
                return;
            }

            if (static::hasBlameableColumn($model, 'created_by')) {
                $model->created_by = Auth::id();
            }

            if (static::hasBlameableColumn($model, 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function (Model $model): void {
            if (Auth::check() && static::hasBlameableColumn($model, 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function (Model $model): void {
            if (! Auth::check() || ! static::hasBlameableColumn($model, 'deleted_by')) {
                return;
            }

            // Only soft-deletable models keep a row worth stamping; a model without
            // SoftDeletes is about to be physically removed, so skip the update.
            if (! method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                return;
            }

            // SoftDeletes::runSoftDelete() issues its own query for deleted_at
            // rather than saving the model, so deleted_by needs the same treatment.
            $model->newQueryWithoutScopes()
                ->whereKey($model->getKey())
                ->update(['deleted_by' => Auth::id()]);
        });
    }

    protected static function hasBlameableColumn(Model $model, string $column): bool
    {
        static $cache = [];

        return $cache[$model::class.':'.$column] ??= Schema::hasColumn($model->getTable(), $column);
    }
}
