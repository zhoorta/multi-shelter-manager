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
        <a href="#treatments" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Treatments</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptions</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Sponsorships</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Volunteers</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Members</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Facilities</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Reports</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Users</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Public Portal</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Settings</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Getting Started</h2>
        <p>On the very first run, the application asks you to create the initial administrator account. From then on, new accounts are only created by invitation.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Roles</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Admin</strong> &mdash; manages the whole platform: shelters, the shared lookup tables and user accounts. Admins do not manage pets or facilities.</li>
            <li><strong>Manager</strong> &mdash; runs a shelter: everything a staff member can do, plus inviting and managing that shelter's users. Managers also keep the shelter's profile (contacts, address, description, logo) up to date under <strong>Settings &gt; Shelter</strong>; only an admin can change the shelter's name or species.</li>
            <li><strong>Staff</strong> &mdash; handles the shelter's daily work: pets, vaccinations, treatments, adoptions, sponsorships, volunteers and facilities.</li>
            <li><strong>Viewer</strong> &mdash; read-only access to the shelter: can see pets, vaccinations, treatments and facilities and print pet sheets and lists, but cannot create, edit or delete anything, and does not see the personal data of adopters, sponsors, volunteers or members.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Working with several shelters</h3>
        <p>A user can belong to more than one shelter, with a different role in each. Use the shelter switcher to change the active shelter; every list, count and form then shows only that shelter's data. Data is never shared between shelters.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Recommended setup order</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>An admin fills in the lookup tables (regions, species, breeds, sizes, fur types, vaccines, treatments, sicknesses, activities).</li>
            <li>The admin creates the shelter, completes its profile (contacts, region, description, logo) and chooses which species it works with.</li>
            <li>The admin invites the shelter's manager.</li>
            <li>The manager configures the facilities, wings and cages, and invites the staff.</li>
            <li>The team starts registering pets.</li>
            <li>If the public portal is enabled, the team publishes the pets that are ready for adoption.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Finding your way around</h3>
        <p>The sidebar only shows what your role can use. Managers and staff see the Pets menu (one entry per species enabled for the active shelter, plus Sponsorships, Adoptions, Vaccinations and Treatments), Volunteers, Members and Facilities; managers also see Users. Admins see Users and the Administration menu instead. Viewers see the same menus as staff, except Sponsorships, Adoptions, Volunteers and Members, and pages have no create, edit or delete buttons for them. This documentation is always available at the bottom of the sidebar.</p>
        <p>The Pets menu also includes <strong>Adoption Applications</strong> for managers and staff, and only managers see <strong>Reports</strong>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Modules</h3>
        <p>A shelter that does not use every part of the app can switch modules off: <strong>Members</strong>, <strong>Volunteers</strong>, <strong>Sponsorships</strong>, <strong>Adoption Applications</strong>, <strong>Reports</strong>, and <strong>Vaccinations and Treatments</strong>. Managers do it under <strong>Settings &gt; Shelter</strong> and admins in the shelter's edit form, in the <strong>Modules</strong> section. A module that is switched off disappears from the sidebar, the dashboard and the pet pages, and its pages no longer open. With Adoption Applications off, the public pet page no longer shows the &ldquo;I want to adopt&rdquo; button. Nothing is deleted: switching a module back on restores all its data. Pets, adoptions, diagnoses and facilities are always on.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dashboard</h2>
        <p>The dashboard gives you an overview of the active shelter:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Counters for pets in the shelter, available cage capacity and adoptions this year. Managers and staff also see pending adoption applications, overdue vaccinations and overdue member fees, each linking to its list.</li>
            <li>Warnings when something is still missing, such as no cages defined or species without breeds.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Needs attention</h3>
        <p>Short lists of animals that need something done. Each shows up to five animals and only appears when it has any; <strong>View all</strong> opens the pets list with the matching filter.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Pets with Unknown Location</strong> &mdash; animals in the shelter with no cage, and for how long, so they can be placed.</li>
            <li><strong>Open health issues</strong> &mdash; animals with an active or chronic diagnosis, the most recent diagnosis first, with the diagnoses.</li>
            <li><strong>Sponsorships to Renew</strong> &mdash; sponsorships whose paid period ended in the last 30 days or ends in the next 30, with the sponsor&rsquo;s name, so you can contact them. Managers and staff only.</li>
            <li><strong>Pets without a Photo</strong> &mdash; without a photo a pet can&rsquo;t be shown well on the public portal or shared on social media.</li>
            <li><strong>Longest in the Shelter</strong> &mdash; the available animals that have waited longest since check-in, with how long: good candidates to promote.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Recent activity</h3>
        <p>The latest intakes (with check-in date and cage), adoptions (with the date and the adopter&rsquo;s first name, hidden from viewers) and deaths (with the date).</p>
        <p>Available capacity only counts the shelter's own cages: foster family wings are left out, and so are the animals living with foster families.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Admin dashboard</h3>
        <p>Admins do not belong to a shelter, so their dashboard shows the whole platform instead: counters for shelters, users active in the last 30 days, animals in care and adoptions this year; a table of the shelters with their animals, adoptions, last login and last pet update, the least recently used first and old logins highlighted; <strong>Setup to complete</strong> (shelters with no species, cages or users, and species without breeds); and <strong>Invitations not accepted</strong>, the invited users who never logged in. It shows totals only, never animals or people&rsquo;s details.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pets</h2>
        <p>The Pets menu lists the shelter's animals by species; only the species the admin has enabled for your shelter appear there. Each pet record includes identification (reference, name, microchip), physical description (breed, colours, fur type, size, gender, neutered), dates (birth, check-in, check-out, death), photos, a public description, internal notes and clinical notes.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>A pet's status is worked out automatically, so you never set it by hand:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Deceased</strong> &mdash; a date of death is filled in.</li>
            <li><strong>Adopted</strong> &mdash; the pet has an adoption with no return date.</li>
            <li><strong>Available</strong> / <strong>Not available</strong> &mdash; otherwise, depending on whether the pet is marked as adoptable.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Options and location</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Is Adoptable</strong> &mdash; the pet can be adopted; it drives the Available / Not available status.</li>
            <li><strong>Is Sponsorable</strong> &mdash; the pet can receive sponsorships. The Sponsor action is only offered for pets with this option on, and it stays available even after the pet is adopted.</li>
            <li><strong>Cage</strong> &mdash; the cage list is grouped by facility and wing and shows how many places are free in each cage, with a green, yellow or red marker as it fills up.</li>
        </ul>
        <p>When the public portal is enabled, two more options appear: <strong>Publish to Portal</strong> and <strong>Is Featured</strong>. See <a href="#public-portal" class="underline underline-offset-2">Public Portal</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Searching and filtering</h3>
        <p>Search by name, reference, microchip or internal notes, and filter by status, species or location (facility, wing or cage). The last filter finds pets with <strong>Open health issues</strong>, by sterilisation (<strong>Neutered</strong>, <strong>Not neutered</strong>, <strong>Neutered, details missing</strong>) or with missing data (no age, no photo, no check-in date or no location), which helps keep records complete. Pets with an open health issue show a heart next to their name in the list.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Printing</h3>
        <p>You can print a single pet's sheet from its page, or print the pet list; the printed list uses the same filters that are active on screen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Sharing on social media</h3>
        <p>Pets that are adoptable and available have a share button at the top of their page. It prepares a text with the pet's details and your shelter's contacts, ready to copy, and lets you download the main photo to post on Facebook, Instagram or WhatsApp. When the pet is published on the public portal, the text includes a link to the pet, and you can also share it straight to Facebook or WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Health</h3>
        <p>Record diagnoses on the pet&rsquo;s page with <strong>New Diagnosis</strong>: the sickness, the diagnosis date, the status (<strong>Active</strong>, <strong>Chronic</strong> or <strong>Treated</strong>) and treatment notes. A diagnosis marked as Treated gets a resolution date (today by default). Active and chronic diagnoses are the pet&rsquo;s open health issues: they show on the pet&rsquo;s page, on the dashboard and in the pets list filter. Vaccinations and clinical notes are also kept on each pet. Sizes are only offered for species that have sizes configured.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Sterilisation</h3>
        <p>With <strong>Is Neutered</strong> on, record the <strong>Neutering Date</strong> and who did it (<strong>The shelter</strong> or <strong>Before arrival</strong>); leave them empty when unknown. With it off, choose the <strong>Neutering Status</strong> (Pending, Scheduled with its date, or Not recommended) and add notes. New animals that are not neutered start as Pending.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinations</h2>
        <p>Each vaccination records the vaccine, the date it was given, the next due date, lot number, veterinarian and notes. Record the last dose and the next date on the same entry: it stays pending until a later dose of that vaccine is recorded. To plan a vaccination, enter only the next date; recording the dose later completes it. When the vaccine has a frequency (for example rabies, every 36 months), the next date is filled in from the dose date and can still be changed. The Vaccinations page lists them across all the shelter's pets.</p>
        <p>Every day, users who have vaccination notifications turned on for a shelter receive an email listing that shelter's vaccinations due in the next seven days that are still pending. Each vaccination is only notified once. A bell icon in the users list shows who receives these emails.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vaccination plan</h3>
        <p>Shelters that vaccinate in groups can open <strong>Vaccination Plan</strong> on the Vaccinations page. For the chosen year it shows, per vaccine, how many pending vaccinations of the animals in the shelter fall due in each month; the first column counts those already due before that year. Click a number to list the animals, with their microchip and location, and use <strong>Print list for the vet</strong> to take the list to the vaccination day.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Group vaccination</h3>
        <p><strong>Group Vaccination</strong> records the same vaccine for several animals at once, for example on the day the vet vaccinates a group. Choose the vaccine and which animals to list: those due in a given month (the current month by default), the overdue ones, or all the animals in the shelter of that vaccine's species, and filter by species or location if needed. The listed animals start ticked; untick the exceptions. The date, next due date, lot number, veterinarian and notes are entered once for all of them. In the vaccination plan, the <strong>Group Vaccination</strong> button next to a month's list opens this form with those animals already listed.</p>
    </section>

    <section id="treatments" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Treatments</h2>
        <p>Treatments are recurring preventive care that is not a vaccine, such as internal and external deworming. Each treatment records the treatment, the date it was given, the next due date, the product used, the veterinarian and notes. They work like vaccinations: an entry stays pending until a later round of the same treatment is recorded, and when the treatment has a frequency (for example deworming every 3 months), the next date is filled in from the date given.</p>
        <p>Record one on the pet's page with <strong>New Treatment</strong>. The <strong>Treatments</strong> page, in the Pets menu, lists them for all the shelter's pets, with a search and a filter by next due date; overdue dates are shown in red and those due within a week in amber.</p>
        <p><strong>Group Treatment</strong> records a round for many animals at once: choose the treatment and, optionally, a species or location; every animal in the shelter it applies to starts ticked, so untick the exceptions and enter the date, product, veterinarian and notes once.</p>
        <p>Users with vaccination notifications turned on also get a daily email with the treatments due in the next seven days, grouped by treatment and date, so a deworming round comes as one reminder rather than one per animal.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptions</h2>
        <p>An adoption records the adopter's contact details, the adoption date, the fee, notes and the application status (Pending, Approved or Rejected). Start one from the pet's page.</p>
        <p>The Adoptions page (under Pets in the sidebar) lists every adoption of the shelter; search by adopter name, phone, email, notes, or by the pet's name or reference.</p>
        <p>If an adopted pet comes back to the shelter, fill in the <strong>return date</strong> on the adoption: the pet becomes available again and the adoption stays in its history.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Adoption applications</h3>
        <p>When the public portal is on, visitors can send an adoption application from a pet's card with the <strong>I want to adopt</strong> button. The form asks for their contacts, the type of home, whether there is a garden, children or other animals, and why they want to adopt.</p>
        <p>Applications appear in <strong>Adoption Applications</strong> under Pets, pending ones first, and the sidebar shows how many are pending. For each one you can:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Approve</strong> &mdash; opens the adoption form already filled in with the applicant's details; saving it registers the adoption and marks the application as approved.</li>
            <li><strong>Reject</strong> &mdash; marks it as rejected.</li>
            <li><strong>Delete</strong> &mdash; removes it.</li>
        </ul>
        <p>When a pet has been adopted or is no longer available, its pending applications are flagged and can be rejected all at once. Users with <strong>Adoption Application Notifications</strong> turned on receive an email for each new application; the applicant receives no email, so contact them yourself. Applications are deleted automatically six months after their last change, as stated in the privacy policy.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Sponsorships</h2>
        <p>Sponsors support a pet's care without adopting it. A sponsorship stores the sponsor's contact details and whether they want to receive news about the pet or the newsletter.</p>
        <p>Sponsorships can only be created for pets marked as <strong>Is Sponsorable</strong>. The Sponsorships page (under Pets in the sidebar) lists them all, with the same search as adoptions: sponsor name, phone, email, notes, or the pet's name or reference.</p>
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
        <p>The volunteers list can be searched by name, phone, email, tax number or notes, and filtered by preferred species, day of availability and activity &mdash; handy to find who can help on a given day.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Members</h2>
        <p>Keep a record of your association's members and their fees. Each member has a member number, personal and contact details, a join date, a status and their fees, and can be linked to their volunteer record when they are the same person.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Fees</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Joining Fee</strong> &mdash; paid once, when joining. It can be 0, in which case nothing is owed.</li>
            <li><strong>Membership Fee</strong> &mdash; the recurring fee: Monthly, Quarterly, Semiannual or Yearly.</li>
        </ul>
        <p>Managers set the shelter's default values with the <strong>Fees</strong> button on the members list. New members start with these values, which can then be changed for each member.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Member numbers</h3>
        <p>Leave the number empty and the next one is assigned automatically, or type one to keep the numbering you already use. A number can only be used once per shelter, and the numbers of deleted members are never reused.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Payments</h3>
        <p>Record the joining fee or a membership fee on the member's page. A membership fee comes pre-filled with the next period to pay (from the day after the last paid period, or from the join date) and the member's fee. Each payment also records the payment date, the amount, the method (Cash, Bank transfer, Mobile payment or Other) and notes.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Fees overdue</h3>
        <p>An active member is marked <strong>Fees overdue</strong> when the joining fee is unpaid or no membership fee covers today; the mark disappears as soon as the payment is recorded. Turn on <strong>Fees overdue only</strong> on the list to see who needs a reminder.</p>
        <p>The status (Active, Suspended or Former member) never changes automatically: change it in the member's form, following your association's rules. The list shows active members by default; use the Status filter to see the others.</p>
        <p>Managers and staff can add and edit members and record payments; only managers can delete members or change the default fees. Viewers have no access to members.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Facilities</h2>
        <p>A shelter is organized in three levels: <strong>facilities</strong> (physical sites, with an address) contain <strong>wings</strong>, and wings contain <strong>cages</strong>. Each cage has a code and a capacity.</p>
        <p>The total capacity of the cages determines how many pets the shelter can house, and cages are where pets are assigned. Configure at least one cage before registering pets so they can be given a location.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Foster families</h3>
        <p>If your shelter places animals with foster families, create a wing for them (for example in a facility called "Foster families") and turn on <strong>Foster families wing</strong> in the wing's form. In that wing each cage is one family: use the family's name as the cage name and its capacity as the number of animals it can take.</p>
        <p>Optionally choose a <strong>Contact (volunteer)</strong> for each family; the volunteer record holds their phone and address. To place an animal with a family, choose the family's cage on the pet's form, as with any other cage.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>The pet's page shows <strong>Foster family</strong> with the family's name and, for managers and staff, the contact's name and phone. Viewers see the family's name but not the contact.</li>
            <li>The volunteer's page lists the animals currently with their family.</li>
            <li>Foster families do not count towards the shelter's capacity on the dashboard or in the Occupancy report, which counts the animals in foster families separately.</li>
            <li>On the public portal the pet shows an <strong>In a foster family</strong> badge; the family is never shown publicly.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Reports</h2>
        <p>Only managers see Reports. Choose the period at the top (the last 12 months, a year, all time or your own dates); periods of two years or more are shown per year instead of per month. The reports are split into four tabs:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Animals</strong> &mdash; intakes, adoptions, returns and deaths; the number of animals in the shelter over time; intakes and adoptions by species; adoptions by age and the median number of days until adoption; and the available animals that have been waiting longest.</li>
            <li><strong>Finances</strong> &mdash; income by source (sponsorships, membership fees, joining fees and adoption fees), counted by payment date; active sponsorships over time and their monthly value; active, new and overdue members, with the membership fees expected and collected; member payments by method; and the sponsorships whose paid period ends in the next 30 days.</li>
            <li><strong>Occupancy</strong> &mdash; today's occupancy, capacity, and the animals in cages, with no known location and in foster families; occupancy over time and by wing. Capacity is only a guideline, so there are no over-capacity warnings, and past months are compared with today's capacity.</li>
            <li><strong>Health</strong> &mdash; vaccinations given (per month and by vaccine), overdue vaccinations, diagnoses by sickness, open cases, the sterilisations performed by the shelter in the period (those with a date and done by the shelter) and the share of sterilised animals in the shelter.</li>
        </ul>
        <p>Hover over a chart to see its values, or open <strong>Show table</strong> below it. The <strong>print</strong> button opens the current tab as a report with the shelter's details &mdash; for example the yearly activity report for the general assembly &mdash; ready to print or save as PDF from the browser.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Users</h2>
        <p>Managers and admins invite new users by email; the invited person receives a link to set their password. For each shelter membership you choose the role (manager, staff or viewer) and whether the user receives vaccination notifications (which also cover treatments).</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>A manager can only add users to the shelters they manage.</li>
            <li>An admin can add users to any shelter and can create other admins.</li>
        </ul>
        <p>Each membership can also have <strong>Adoption Application Notifications</strong> turned on: those users receive an email for each new adoption application.</p>
        <p>Until an invited person logs in for the first time, the Users list shows a <strong>Resend invitation</strong> button on their row. It emails a new link and cancels the previous one; the link expires after 48 hours.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Public Portal</h2>
        <p>An installation can optionally run a public website next to the backoffice. It is switched on by whoever runs the server; when it is off, the home page simply sends visitors to the login page and the options below are hidden.</p>
        <p>When it is on, anyone (no login needed) can:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Browse the pets of every shelter that are ready for adoption, filtered by species, gender, size, breed and region.</li>
            <li>Open a pet's card to see its photos, public description and the shelter that houses it.</li>
            <li>See the list of partner shelters, each with its own page showing its contacts, description, logo and pets.</li>
            <li>Open a link to a single pet shared from the backoffice: it opens that pet's card directly, and link previews on social networks show its name, photo and description.</li>
        </ul>
        <p>From a pet's card, visitors can also send an adoption application with the <strong>I want to adopt</strong> button (see <a href="#adoptions" class="underline underline-offset-2">Adoptions</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">What is shown publicly</h3>
        <p>A pet only appears on the portal when <strong>all</strong> of these are true:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publish to Portal</strong> is on (it is off by default, so nothing is published by accident).</li>
            <li>The pet is adoptable and its status is Available (so adopted or deceased pets disappear automatically).</li>
            <li>Its shelter has not been removed.</li>
        </ul>
        <p>Pets marked <strong>Is Featured</strong> are shown first, with a Featured badge. Only the name, reference, photos, public description and descriptive details (species, breed, size, gender, age, fur type, neutered) are shown &mdash; internal notes, clinical notes, microchip and cage are never published.</p>
        <p>Pets living with a foster family show an <strong>In a foster family</strong> badge; the family's name and contacts are never published.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Tips for good listings</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Add at least one good photo and a friendly public description &mdash; they are what adopters see first.</li>
            <li>Keep the shelter profile (contacts, description, logo) up to date: it is shown on the shelter's public page and in search engine results and link previews.</li>
            <li>The public pages are prepared for search engines (Google and others) and a sitemap is generated automatically; the backoffice is never indexed.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Only admins see this menu. It maintains the shelters and the lookup tables shared by every shelter:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Shelters</strong> &mdash; create, edit and remove shelters. Besides the name and city, a shelter has a short name, contacts (email, phone, website), address, region, a description and a logo &mdash; used on the public portal when enabled. The <strong>Species</strong> checklist on the shelter form controls which species appear in the Pets menu for that shelter's manager and staff. The email is required. Once the shelter has a manager, they can update everything except the name and species themselves in <strong>Settings &gt; Shelter</strong>.</li>
            <li><strong>Regions</strong> &mdash; the districts shelters belong to, also used as a filter on the public portal.</li>
            <li><strong>Species</strong>, <strong>Breeds</strong>, <strong>Sizes</strong> and <strong>Fur Types</strong> &mdash; the options used to describe pets.</li>
            <li><strong>Vaccines</strong>, <strong>Treatments</strong> and <strong>Sicknesses</strong> &mdash; the options used in pet health records. Vaccines and treatments have the species they apply to and an optional frequency in months, used to fill in the next due date.</li>
            <li><strong>Activities</strong> &mdash; the tasks volunteers can help with.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Settings</h2>
        <p>From the user menu, open Settings to view your profile, change your password and choose the appearance (light, dark or system) and your language. Your name can only be changed by an admin or manager, and your email cannot be changed. The language is saved to your account and is also used for the e-mails you receive. On the public pages and the login page, anyone can switch language in the menu at the top. Managers also see a <strong>Shelter</strong> tab to update the active shelter's contacts, address, region, description and logo; the email is required, and the name and species can only be changed by an admin.</p>
    </section>
</div>
