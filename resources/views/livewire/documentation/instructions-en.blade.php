<div class="flex flex-col gap-6 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Application Instructions</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">A quick guide to the main areas of {{ config('app.name') }}.</p>
    </div>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dashboard</h2>
        <p>The dashboard gives you an overview of your shelter: active pets, available capacity, staff, and the most recent intakes, adoptions, and sponsorships.</p>
    </section>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pets</h2>
        <p>Register and track pets by species. Each pet can have vaccinations, adoptions, and sponsorships recorded against it, and can be assigned to a cage once your facilities are configured.</p>
    </section>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptions &amp; Sponsorships</h2>
        <p>Adoptions record when a pet leaves the shelter permanently. Sponsorships let supporters contribute recurring or one-off payments toward a pet's care without adopting it.</p>
    </section>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Volunteers</h2>
        <p>Keep a record of the people who help your shelter, independently from your staff accounts.</p>
    </section>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Facilities</h2>
        <p>A shelter is organized into facilities, which contain wings, which contain cages. Cage capacity determines how many pets your shelter can currently house.</p>
    </section>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Users</h2>
        <p>Managers and admins can invite new users by email. A manager can only invite staff or managers into their own shelter; an admin can invite into any shelter.</p>
    </section>

    <section class="flex flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Admins maintain the global lookup tables shared by every shelter: species, breeds, sizes, fur types, vaccines, sicknesses, and shelters themselves.</p>
    </section>
</div>
