{{-- Public portal "public-pet-details" modal; the host component uses App\Livewire\Concerns\ShowsPublicPets. Pass showShelterLink => false on the shelter's own page. The content is the public-pet-profile partial, also used by the public pet page. --}}
<flux:modal name="public-pet-details" :closable="false" class="w-full max-w-2xl !p-0 backdrop:bg-stone-950/50! backdrop:backdrop-blur-sm">
    @if ($pet = $this->selectedPet)
        <div wire:key="public-pet-details-{{ $pet->id }}" class="relative overflow-hidden rounded-xl">
            <flux:modal.close>
                <button type="button" aria-label="{{ __('Close') }}" class="absolute top-3 right-3 z-20 flex size-10 items-center justify-center rounded-full bg-white/95 text-stone-700 shadow-lg ring-1 ring-black/5 transition hover:scale-110 hover:bg-white hover:text-orange-500 focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-300">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </flux:modal.close>
            @include('livewire.partials.public-pet-profile', ['pet' => $pet, 'showShelterLink' => $showShelterLink ?? true, 'pageUrl' => $pet->publicPageUrl()])
        </div>
    @endif
</flux:modal>
