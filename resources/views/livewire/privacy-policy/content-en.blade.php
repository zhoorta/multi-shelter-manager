<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Privacy Policy</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Last updated: 23 September 2026</p>
        <p>{{ config('app.name') }} is a platform that brings several animal shelters together. We take the protection of your personal data seriously and process it in accordance with the General Data Protection Regulation (GDPR) and the national data protection laws of the European Union member states. This policy explains what data we process, why, for how long, and what your rights are.</p>
    </header>

    <section id="responsible">
        <h2>1. Who is responsible for your data</h2>
        <p>Each shelter using the platform is the <strong>data controller</strong> for the data it collects in the course of its activity (for example, data about adopters, sponsors and volunteers). {{ config('app.name') }} provides the technology platform and acts as a <strong>data processor</strong> for those shelters, processing the data only on their instructions.</p>
        <p>{{ config('app.name') }} is the controller for data relating to platform user accounts and to visitors of the public page.</p>
    </section>

    <section id="data">
        <h2>2. What data we process</h2>

        <h3>Visitors of the public page</h3>
        <p>You can browse the animals up for adoption without creating an account or filling in any form. We only process the technical data needed for the site to work: IP address, browser and device type, and a session cookie.</p>

        <h3>Platform users (shelter teams)</h3>
        <ul>
            <li>Name and e-mail address;</li>
            <li>Password (stored only in hashed form, never in readable text);</li>
            <li>The shelter(s) you belong to and your role (administrator, manager or staff);</li>
            <li>Date of last login and session data (IP address and browser).</li>
        </ul>

        <h3>Adopters</h3>
        <ul>
            <li>Name, e-mail, phone, address, postal code and city;</li>
            <li>The adopted animal, adoption date and any return date;</li>
            <li>Adoption fee and notes recorded by the shelter.</li>
        </ul>

        <h3>Sponsors</h3>
        <ul>
            <li>Name, e-mail, phone, address, postal code and city;</li>
            <li>The sponsored animal and payment history (dates, periods and amounts);</li>
            <li>Communication preferences (news about the animal and/or newsletter).</li>
        </ul>

        <h3>Volunteers</h3>
        <ul>
            <li>Name, gender, date of birth and photo;</li>
            <li>ID document number and tax identification number;</li>
            <li>Contact details, address, occupation and means of transport;</li>
            <li>Availability, collaboration start and end dates, and attendance and performance evaluations;</li>
            <li>Newsletter preference.</li>
        </ul>

        <p>The public page only shows information about the animals (photos, characteristics and description) and the shelter's contact details. It <strong>never</strong> publishes data about adopters, sponsors, volunteers or users.</p>
    </section>

    <section id="purposes">
        <h2>3. Why we use the data and on what legal basis</h2>
        <ul>
            <li><strong>Managing adoptions, sponsorships and volunteering</strong> &mdash; performance of the agreement with you or pre-contractual steps at your request (Art. 6(1)(b) GDPR);</li>
            <li><strong>Following up on the animals' welfare after adoption</strong> &mdash; the shelter's legitimate interest in animal protection (Art. 6(1)(f));</li>
            <li><strong>Complying with legal obligations</strong>, such as tax rules and the registration and identification of companion animals (Art. 6(1)(c));</li>
            <li><strong>Sending newsletters and news about a sponsored animal</strong> &mdash; your consent, which you may withdraw at any time (Art. 6(1)(a));</li>
            <li><strong>Keeping the platform secure and running</strong>, including access control and technical logs &mdash; legitimate interest (Art. 6(1)(f)).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookies</h2>
        <p>We only use cookies and local storage that are <strong>strictly necessary</strong> for the site to work or for a feature you have explicitly requested, so under the EU ePrivacy Directive (Directive 2002/58/EC, Art. 5(3)) and the national laws that implement it, your consent is not required. We do not use advertising, analytics or third-party cookies, and all fonts and assets are served from our own servers.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Purpose</th>
                        <th class="px-4 py-3">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Keeps your session while you browse the site.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutes of inactivity</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Protects forms against forged requests from other sites.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutes of inactivity</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Keeps you signed in, only if you tick &ldquo;Remember me&rdquo; when logging in.</td>
                        <td class="px-4 py-3">400 days or until you log out</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Local storage</td>
                        <td class="px-4 py-3">Stores your light or dark theme preference, only if you choose one in the settings.</td>
                        <td class="px-4 py-3">Until you clear it in your browser</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>You can delete or block cookies in your browser settings; if you block the session cookies, you will not be able to log in to the shelter area.</p>
    </section>

    <section id="sharing">
        <h2>5. Who we share data with</h2>
        <p>We do not sell or hand over your data for commercial purposes. Each shelter's data is only accessible to that shelter's team and to the platform administrators. It may also be processed by service providers that help us run the platform (hosting and e-mail delivery), always under appropriate contractual safeguards, or disclosed to authorities where required by law.</p>
    </section>

    <section id="retention">
        <h2>6. How long we keep the data</h2>
        <ul>
            <li><strong>User accounts:</strong> while the account is active;</li>
            <li><strong>Adoptions and sponsorships:</strong> for as long as needed to follow up on the animal and to meet applicable legal obligations;</li>
            <li><strong>Volunteers:</strong> during the collaboration and, afterwards, only for the period required by law;</li>
            <li><strong>Sessions:</strong> expire automatically after a period of inactivity.</li>
        </ul>
        <p>Deleted records may be kept for a limited period, outside normal access, for audit and error-recovery purposes.</p>
    </section>

    <section id="rights">
        <h2>7. Your rights</h2>
        <p>You may at any time request <strong>access</strong> to, <strong>rectification</strong> or <strong>erasure</strong> of your data, <strong>restriction</strong> of processing or data <strong>portability</strong>, <strong>object</strong> to processing based on legitimate interest, and <strong>withdraw any consent</strong> you have given, without affecting processing carried out before then.</p>
        <p>To exercise these rights, contact the shelter you dealt with directly (its contact details are shown on each animal's page)@if ($contactEmail), or write to us at <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. We will reply within one month at the latest.</p>
        <p>You also have the right to lodge a complaint with the data protection authority of the EU member state where you live or work, or where the alleged infringement took place (Art. 77 GDPR). The European Data Protection Board lists every national authority at <a href="https://www.edpb.europa.eu/about-edpb/about-edpb/members_en" target="_blank" rel="noopener">www.edpb.europa.eu</a>.</p>
    </section>

    <section id="security">
        <h2>8. Security</h2>
        <p>Passwords are stored hashed, access is by invitation only, and each user can only see the data of the shelter they belong to. We apply technical and organisational measures to protect data against unauthorised access, loss or alteration.</p>
    </section>

    <section id="changes">
        <h2>9. Changes to this policy</h2>
        <p>We may update this policy to reflect changes to the platform or the law. The date of the last update is always shown at the top of this page.</p>
    </section>
</div>
