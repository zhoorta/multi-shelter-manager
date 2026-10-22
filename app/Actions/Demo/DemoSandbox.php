<?php

declare(strict_types=1);

namespace App\Actions\Demo;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Throwaway shelters for the public demo (DEMO_MODE=true): each visitor gets a copy of the
 * template shelter and a manager account, both deleted again after a couple of hours.
 */
class DemoSandbox
{
    public const DOMAIN = 'demo.focinhos.invalid';

    /**
     * The shelter tables to copy, parents first. `scope` is the column that ties a row to the
     * shelter (`shelter_id`) or to an already copied parent table; `remap` lists other foreign
     * keys into copied tables; `files` maps file columns to the disk holding them.
     *
     * @var array<string, array{scope: string, parent?: string, remap?: array<string, string>, files?: array<string, string>}>
     */
    private const TABLES = [
        'facilities' => ['scope' => 'shelter_id'],
        'wings' => ['scope' => 'facility_id', 'parent' => 'facilities'],
        'volunteers' => ['scope' => 'shelter_id', 'files' => ['image_path' => 'public']],
        'cages' => ['scope' => 'wing_id', 'parent' => 'wings', 'remap' => ['volunteer_id' => 'volunteers']],
        'colonies' => ['scope' => 'shelter_id'],
        'members' => ['scope' => 'shelter_id', 'remap' => ['volunteer_id' => 'volunteers']],
        'member_payments' => ['scope' => 'member_id', 'parent' => 'members'],
        'pets' => ['scope' => 'shelter_id', 'remap' => ['cage_id' => 'cages', 'colony_id' => 'colonies']],
        'pet_images' => ['scope' => 'pet_id', 'parent' => 'pets', 'files' => ['image_path' => 'public']],
        'pet_sicknesses' => ['scope' => 'pet_id', 'parent' => 'pets'],
        'pet_vaccines' => ['scope' => 'pet_id', 'parent' => 'pets'],
        'pet_treatments' => ['scope' => 'pet_id', 'parent' => 'pets'],
        'pet_clinical_photos' => ['scope' => 'pet_id', 'parent' => 'pets', 'files' => ['image_path' => 'local', 'thumb_path' => 'local']],
        'adoptions' => ['scope' => 'pet_id', 'parent' => 'pets'],
        'adoption_applications' => ['scope' => 'pet_id', 'parent' => 'pets', 'remap' => ['adoption_id' => 'adoptions']],
        'sponsorships' => ['scope' => 'pet_id', 'parent' => 'pets'],
        'sponsorship_payments' => ['scope' => 'sponsorship_id', 'parent' => 'sponsorships'],
        'volunteer_activities' => ['scope' => 'volunteer_id', 'parent' => 'volunteers'],
        'volunteer_availabilities' => ['scope' => 'volunteer_id', 'parent' => 'volunteers'],
        'volunteer_species' => ['scope' => 'volunteer_id', 'parent' => 'volunteers'],
        'shelter_species' => ['scope' => 'shelter_id'],
    ];

    /**
     * @var list<string>
     */
    private const AUDIT_COLUMNS = ['created_by', 'updated_by', 'deleted_by', 'reviewed_by'];

    public static function enabled(): bool
    {
        return (bool) config('demo.enabled');
    }

    public static function isDemoUser(?User $user): bool
    {
        return $user !== null && str_ends_with($user->email, '@'.self::DOMAIN);
    }

    public function isFull(): bool
    {
        return User::query()->where('email', 'like', '%@'.self::DOMAIN)->count() >= config('demo.max_sandboxes');
    }

    /**
     * Copy the template shelter and give it a manager account.
     */
    public function create(): User
    {
        $template = Shelter::query()->findOrFail(config('demo.template_shelter_id'));

        return DB::transaction(function () use ($template): User {
            $shelter = new Shelter($template->only(['name', 'short_name', 'city', 'region_id', 'address', 'postal_code', 'phone', 'email', 'website', 'description', 'joining_fee', 'membership_fee', 'membership_fee_frequency', 'modules']));
            $shelter->logo_path = $this->copyFile($template->logo_path, 'public');
            $shelter->logo_size = $shelter->logo_path ? $template->logo_size : null;
            $shelter->save();

            $this->copyTables($template, $shelter);

            $user = new User;
            $user->forceFill([
                'name' => __('Demo manager'),
                'email' => Str::lower(Str::random(12)).'@'.self::DOMAIN,
                'password' => Str::random(40),
                'email_verified_at' => now(),
                'current_shelter_id' => $shelter->id,
                'last_login' => now(),
            ])->save();

            DB::table('shelter_users')->insert([
                'shelter_id' => $shelter->id,
                'user_id' => $user->id,
                'role' => 'manager',
                'vaccination_notifications' => false,
                'adoption_application_notifications' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $user;
        });
    }

    /**
     * Delete the sandboxes past their lifetime with their rows and files.
     *
     * @return int how many sandboxes were deleted
     */
    public function prune(): int
    {
        $users = User::query()
            ->where('email', 'like', '%@'.self::DOMAIN)
            ->where('created_at', '<', now()->subMinutes(config('demo.ttl_minutes')))
            ->get();

        foreach ($users as $user) {
            $shelterIds = DB::table('shelter_users')->where('user_id', $user->id)->pluck('shelter_id')->all();

            foreach ($shelterIds as $shelterId) {
                $this->purgeShelter((int) $shelterId);
            }

            DB::table('shelter_users')->where('user_id', $user->id)->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('data_exports')->where('user_id', $user->id)->delete();
            $user->forceDelete();
        }

        return $users->count();
    }

    private function copyTables(Shelter $template, Shelter $shelter): void
    {
        /** @var array<string, array<int, int>> $ids */
        $ids = [];

        foreach (self::TABLES as $table => $config) {
            $rows = DB::table($table)
                ->when($config['scope'] === 'shelter_id', fn ($query) => $query->where('shelter_id', $template->id))
                ->when(isset($config['parent']), fn ($query) => $query->whereIn($config['scope'], array_keys($ids[$config['parent']] ?? [])))
                ->get();

            foreach ($rows as $row) {
                if (property_exists($row, 'deleted_at') && $row->deleted_at !== null) {
                    continue;
                }

                $copy = (array) $row;
                $oldId = $copy['id'];
                unset($copy['id']);

                foreach (self::AUDIT_COLUMNS as $column) {
                    if (array_key_exists($column, $copy)) {
                        $copy[$column] = null;
                    }
                }

                if ($config['scope'] === 'shelter_id') {
                    $copy['shelter_id'] = $shelter->id;
                } else {
                    $copy[$config['scope']] = $ids[$config['parent']][$copy[$config['scope']]];
                }

                foreach ($config['remap'] ?? [] as $column => $parentTable) {
                    $copy[$column] = $copy[$column] === null ? null : ($ids[$parentTable][$copy[$column]] ?? null);
                }

                foreach ($config['files'] ?? [] as $column => $disk) {
                    $copy[$column] = $this->copyFile($copy[$column], $disk);
                }

                $ids[$table][$oldId] = (int) DB::table($table)->insertGetId($copy);
            }
        }
    }

    private function purgeShelter(int $shelterId): void
    {
        /** @var array<string, list<int>> $ids */
        $ids = [];

        foreach (self::TABLES as $table => $config) {
            $ids[$table] = DB::table($table)
                ->when($config['scope'] === 'shelter_id', fn ($query) => $query->where('shelter_id', $shelterId))
                ->when(isset($config['parent']), fn ($query) => $query->whereIn($config['scope'], $ids[$config['parent']]))
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        }

        foreach (array_reverse(self::TABLES) as $table => $config) {
            foreach ($config['files'] ?? [] as $column => $disk) {
                DB::table($table)->whereIn('id', $ids[$table])->whereNotNull($column)->pluck($column)
                    ->each(fn (string $path) => Storage::disk($disk)->delete($path));
            }

            DB::table($table)->whereIn('id', $ids[$table])->delete();
        }

        $shelter = Shelter::query()->withTrashed()->find($shelterId);

        if ($shelter !== null) {
            if ($shelter->logo_path !== null) {
                Storage::disk('public')->delete($shelter->logo_path);
            }

            DB::table('shelters')->where('id', $shelterId)->delete();
        }
    }

    /**
     * Copy a stored file under a new name, so deleting it in one shelter never touches another.
     */
    private function copyFile(?string $path, string $disk): ?string
    {
        if ($path === null || ! Storage::disk($disk)->exists($path)) {
            return null;
        }

        $copy = trim(dirname($path), '.\\/').'/'.Str::random(40).(($extension = pathinfo($path, PATHINFO_EXTENSION)) !== '' ? '.'.$extension : '');
        $copy = ltrim($copy, '/');

        Storage::disk($disk)->copy($path, $copy);

        return $copy;
    }
}
