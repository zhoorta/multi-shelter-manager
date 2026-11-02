<?php

declare(strict_types=1);

namespace App\Livewire\Pets\Concerns;

use App\Models\PetTreatment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

/**
 * Offers the free-text treatment names already used in a shelter as
 * suggestions, so the same product is not retyped in different spellings.
 *
 * @property-read Collection<int, string> $customNameSuggestions
 */
trait SuggestsCustomTreatmentNames
{
    /**
     * The shelter whose free-text treatment names are suggested.
     */
    abstract protected function suggestionShelterId(): ?int;

    /**
     * @return Collection<int, string>
     */
    #[Computed]
    public function customNameSuggestions(): Collection
    {
        return PetTreatment::query()
            ->whereNotNull('custom_name')
            ->whereHas('pet', fn (Builder $query) => $query->where('shelter_id', $this->suggestionShelterId()))
            ->distinct()
            ->orderBy('custom_name')
            ->pluck('custom_name');
    }
}
