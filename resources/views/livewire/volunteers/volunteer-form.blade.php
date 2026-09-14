<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">
                {{ $volunteer ? __('Edit') : __('Create') }} &mdash; {{ __('Volunteers') }}
            </flux:heading>

            @if ($volunteer)
                <flux:subheading>{{ $volunteer->name }}</flux:subheading>
            @endif
        </div>

        <flux:button :href="$volunteer ? route('volunteers.show', $volunteer) : route('volunteers.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ $volunteer ? $volunteer->name : __('Volunteers') }}
        </flux:button>
    </div>

    <form wire:submit="saveVolunteer" autocomplete="off" class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:field>
                <flux:label>{{ __('Photo') }}</flux:label>

                @if ($volunteerImage)
                    <img
                        src="{{ $volunteerImage->temporaryUrl() }}"
                        alt="{{ __('Photo') }}"
                        class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                    >
                @elseif ($existingImagePath)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($existingImagePath) }}"
                        alt="{{ __('Photo') }}"
                        class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                    >
                @endif

                <input
                    type="file"
                    wire:model="volunteerImage"
                    accept="image/*"
                    class="block w-full text-sm text-neutral-700 file:mr-4 file:rounded-lg file:border-0 file:bg-neutral-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-neutral-700 dark:text-neutral-300 dark:file:bg-white dark:file:text-neutral-900 dark:hover:file:bg-neutral-200"
                >

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                    {{ __('PNG or JPG up to 2MB') }}
                </flux:text>

                <div wire:loading wire:target="volunteerImage">
                    <flux:text size="sm">{{ __('Uploading') }}&hellip;</flux:text>
                </div>

                <flux:error name="volunteerImage" />
            </flux:field>

            <flux:input wire:model="volunteerName" :label="__('Name')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="volunteerGender" :label="__('Gender')">
                    <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                    <flux:select.option value="male">{{ __('Male') }}</flux:select.option>
                    <flux:select.option value="female">{{ __('Female') }}</flux:select.option>
                </flux:select>

                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <flux:input
                    type="text"
                    wire:model="volunteerBirthDate"
                    :label="__('Birth Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    clearable
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="volunteerIdCard" :label="__('ID Card')" />
                <flux:input wire:model="volunteerTin" :label="__('TIN')" />
            </div>

            <flux:input wire:model="volunteerProfessionalActivity" :label="__('Professional Activity')" />
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="volunteerEmail" type="email" :label="__('Email')" />
                <flux:input wire:model="volunteerPhone" :label="__('Phone')" />
            </div>

            <flux:input wire:model="volunteerAddress" :label="__('Address')" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="volunteerPostalCode" :label="__('Postal Code')" />
                <flux:input wire:model="volunteerCity" :label="__('City')" field:class="col-span-2" />
            </div>

            <flux:select wire:model="volunteerTransportMode" :label="__('Transport Mode')">
                <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                <flux:select.option value="foot">{{ __('foot') }}</flux:select.option>
                <flux:select.option value="bycicle">{{ __('bycicle') }}</flux:select.option>
                <flux:select.option value="hitchhike">{{ __('hitchhike') }}</flux:select.option>
                <flux:select.option value="public transportation">{{ __('public transportation') }}</flux:select.option>
                <flux:select.option value="own vehicule">{{ __('own vehicule') }}</flux:select.option>
            </flux:select>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-4">
                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <flux:input
                    type="text"
                    wire:model="volunteerStartDate"
                    :label="__('Start Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    clearable
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />

                <flux:input
                    type="text"
                    wire:model="volunteerEndDate"
                    :label="__('End Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    clearable
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="volunteerAttendanceEvaluation" :label="__('Attendance Evaluation')">
                    <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                    <flux:select.option value="very low">{{ __('very low') }}</flux:select.option>
                    <flux:select.option value="low">{{ __('low') }}</flux:select.option>
                    <flux:select.option value="regular">{{ __('regular') }}</flux:select.option>
                    <flux:select.option value="high">{{ __('high') }}</flux:select.option>
                    <flux:select.option value="very high">{{ __('very high') }}</flux:select.option>
                    <flux:select.option value="excellent">{{ __('excellent') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="volunteerPerformanceEvaluation" :label="__('Performance Evaluation')">
                    <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                    <flux:select.option value="very low">{{ __('very low') }}</flux:select.option>
                    <flux:select.option value="low">{{ __('low') }}</flux:select.option>
                    <flux:select.option value="regular">{{ __('regular') }}</flux:select.option>
                    <flux:select.option value="high">{{ __('high') }}</flux:select.option>
                    <flux:select.option value="very high">{{ __('very high') }}</flux:select.option>
                    <flux:select.option value="excellent">{{ __('excellent') }}</flux:select.option>
                </flux:select>
            </div>

            <flux:label>{{ __('Activities') }}</flux:label>

            @foreach ($this->activities as $item)
                <flux:switch
                    :checked="in_array($item->id, $volunteerActivityIds, true)"
                    wire:click="toggleActivity({{ $item->id }})"
                    :label="$item->name"
                    align="left"
                />
            @endforeach
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:heading>{{ __('Availability') }}</flux:heading>

            @foreach (\App\Models\VolunteerAvailability::DAYS as $dayIndex => $day)
                <div class="grid grid-cols-1 items-end gap-4 border-b border-neutral-200 pb-4 last:border-0 last:pb-0 dark:border-neutral-700 sm:grid-cols-4">
                    <flux:label class="sm:col-span-4">{{ __('Present on '.$day) }}</flux:label>

                    <flux:checkbox wire:model="availabilities.{{ $dayIndex }}.mornings" :label="__('Morning')" />
                    <flux:checkbox wire:model="availabilities.{{ $dayIndex }}.afternoons" :label="__('Afternoon')" />

                    <flux:select wire:model="availabilities.{{ $dayIndex }}.frequency" :label="__('Frequency')" field:class="sm:col-span-2">
                        <flux:select.option value="occasionally">{{ __('occasionally') }}</flux:select.option>
                        <flux:select.option value="biweekly">{{ __('biweekly') }}</flux:select.option>
                        <flux:select.option value="weekly">{{ __('weekly') }}</flux:select.option>
                    </flux:select>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:switch wire:model="volunteerSendNewsletter" :label="__('Send Newsletter')" align="left" />
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:textarea wire:model="volunteerNotes" :label="__('Notes')" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="$volunteer ? route('volunteers.show', $volunteer) : route('volunteers.index')" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $volunteer ? __('Save') : __('Create') }}
            </flux:button>
        </div>
    </form>
</div>
