<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Application Instructions</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">A guide to the main areas of {{ config('app.name') }} and how to use them day to day.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Getting Started</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Dashboard</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Pets</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vaccinations</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptions</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Sponsorships</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Volunteers</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Facilities</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Users</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Settings</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Getting Started</h2>
        <p>On the very first run, the application asks you to create the initial administrator account. From then on, new accounts are only created by invitation.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Roles</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Admin</strong> &mdash; manages the whole platform: shelters, the shared lookup tables and user accounts. Admins do not manage pets or facilities.</li>
            <li><strong>Manager</strong> &mdash; runs a shelter: everything a staff member can do, plus inviting and managing that shelter's users.</li>
            <li><strong>Staff</strong> &mdash; handles the shelter's daily work: pets, vaccinations, adoptions, sponsorships, volunteers and facilities.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Working with several shelters</h3>
        <p>A user can belong to more than one shelter, with a different role in each. Use the shelter switcher to change the active shelter; every list, count and form then shows only that shelter's data. Data is never shared between shelters.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Recommended setup order</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>An admin creates the shelter and fills in the lookup tables (species, breeds, sizes, fur types, vaccines, sicknesses, activities).</li>
            <li>The admin invites the shelter's manager.</li>
            <li>The manager configures the facilities, wings and cages, and invites the staff.</li>
            <li>The team starts registering pets.</li>
        </ol>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dashboard</h2>
        <p>The dashboard gives you an overview of the active shelter:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Counters for active pets, adoptions, available cage capacity and staff.</li>
            <li>The most recent intakes, adoptions, sponsorships and deaths.</li>
            <li>Pets with no known location, so they can be assigned to a cage.</li>
            <li>Warnings when something is still missing, such as no cages defined or species without breeds.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pets</h2>
        <p>The Pets menu lists the shelter's animals by species. Each pet record includes identification (reference, name, microchip), physical description (breed, colours, fur type, size, gender, neutered), dates (birth, check-in, check-out, death), photos, a public description, internal notes and clinical notes.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>A pet's status is worked out automatically, so you never set it by hand:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Deceased</strong> &mdash; a date of death is filled in.</li>
            <li><strong>Adopted</strong> &mdash; the pet has an adoption with no return date.</li>
            <li><strong>Available</strong> / <strong>Not available</strong> &mdash; otherwise, depending on whether the pet is marked as adoptable.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Searching and filtering</h3>
        <p>Search by name, reference, microchip or internal notes, and filter by status, species or location (facility, wing or cage). The <em>missing data</em> filter finds pets with no age, no photo, no check-in date or no location, which helps keep records complete.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Printing</h3>
        <p>You can print a single pet's sheet from its page, or print the pet list; the printed list uses the same filters that are active on screen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Health</h3>
        <p>Record sicknesses (with diagnosis date, status and treatment notes), vaccinations and clinical notes on each pet. Sizes are only offered for species that have sizes configured.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinations</h2>
        <p>Each vaccination records the vaccine, the date it was given or is due, lot number, veterinarian and notes. The Vaccinations page lists them across all the shelter's pets.</p>
        <p>Every day, users who have vaccination notifications turned on for a shelter receive an email listing that shelter's vaccinations due in the next seven days that have not been given yet. Each vaccination is only notified once. A bell icon in the users list shows who receives these emails.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptions</h2>
        <p>An adoption records the adopter's contact details, the adoption date, the fee, notes and the application status (Pending, Approved or Rejected). Start one from the pet's page.</p>
        <p>If an adopted pet comes back to the shelter, fill in the <strong>return date</strong> on the adoption: the pet becomes available again and the adoption stays in its history.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Sponsorships</h2>
        <p>Sponsors support a pet's care without adopting it. A sponsorship stores the sponsor's contact details and whether they want to receive news about the pet or the newsletter.</p>
        <p>Each sponsorship has a list of payments. A payment records the period it covers (start and end dates), the payment date and the amount, so one-off and recurring contributions are both supported.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Volunteers</h2>
        <p>Keep a record of the people who help your shelter, separately from user accounts. For each volunteer you can store personal and contact details, a photo, start and end dates, means of transport and newsletter preference, plus:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>The activities they help with and the species they prefer to work with.</li>
            <li>Their availability per day of the week (mornings and/or afternoons, occasionally, every two weeks or weekly).</li>
            <li>Attendance and performance evaluations.</li>
        </ul>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Facilities</h2>
        <p>A shelter is organized in three levels: <strong>facilities</strong> (physical sites, with an address) contain <strong>wings</strong>, and wings contain <strong>cages</strong>. Each cage has a code and a capacity.</p>
        <p>The total capacity of the cages determines how many pets the shelter can house, and cages are where pets are assigned. Configure at least one cage before registering pets so they can be given a location.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Users</h2>
        <p>Managers and admins invite new users by email; the invited person receives a link to set their password. For each shelter membership you choose the role (manager or staff) and whether the user receives vaccination notifications.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>A manager can only add users to the shelters they manage.</li>
            <li>An admin can add users to any shelter and can create other admins.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Only admins see this menu. It maintains the shelters and the lookup tables shared by every shelter:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Shelters</strong> &mdash; create, edit and remove shelters.</li>
            <li><strong>Species</strong>, <strong>Breeds</strong>, <strong>Sizes</strong> and <strong>Fur Types</strong> &mdash; the options used to describe pets.</li>
            <li><strong>Vaccines</strong> and <strong>Sicknesses</strong> &mdash; the options used in pet health records.</li>
            <li><strong>Activities</strong> &mdash; the tasks volunteers can help with.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Settings</h2>
        <p>From the user menu, open Settings to view your profile, change your password and choose the appearance (light, dark or system). Your name can only be changed by an admin or manager, and your email cannot be changed.</p>
    </section>
</div>
