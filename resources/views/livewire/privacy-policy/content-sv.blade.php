<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Integritetspolicy</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Senast uppdaterad: 25 september 2026</p>
        <p>{{ config('app.name') }} är en plattform som samlar flera djurhem. Vi tar skyddet av dina personuppgifter på allvar och behandlar dem i enlighet med dataskyddsförordningen (GDPR) och lagen (2018:218) med kompletterande bestämmelser till EU:s dataskyddsförordning (dataskyddslagen). Denna policy förklarar vilka uppgifter vi behandlar, varför, hur länge och vilka rättigheter du har.</p>
    </header>

    <section id="responsible">
        <h2>1. Vem som ansvarar för dina uppgifter</h2>
        <p>Varje djurhem som använder plattformen är <strong>personuppgiftsansvarig</strong> för de uppgifter det samlar in i sin verksamhet (till exempel uppgifter om adoptanter, faddrar och volontärer). {{ config('app.name') }} tillhandahåller den tekniska plattformen och agerar <strong>personuppgiftsbiträde</strong> åt dessa djurhem, och behandlar uppgifterna endast enligt deras instruktioner.</p>
        <p>{{ config('app.name') }} är personuppgiftsansvarig för uppgifter om plattformens användarkonton och om besökare på den offentliga sidan.</p>
    </section>

    <section id="data">
        <h2>2. Vilka uppgifter vi behandlar</h2>

        <h3>Besökare på den offentliga sidan</h3>
        <p>Du kan titta på djuren som söker hem utan att skapa ett konto. Vi behandlar endast de tekniska uppgifter som behövs för att webbplatsen ska fungera: IP-adress, typ av webbläsare och enhet samt en sessionscookie. Om du skickar en adoptionsansökan behandlar vi även de uppgifter som beskrivs nedan.</p>

        <h3>Plattformens användare (djurhemmens team)</h3>
        <ul>
            <li>Namn och e-postadress;</li>
            <li>Lösenord (lagras endast som hash, aldrig i läsbar text);</li>
            <li>Det eller de djurhem du tillhör och din roll (administratör, föreståndare eller personal);</li>
            <li>Datum för senaste inloggning och sessionsuppgifter (IP-adress och webbläsare).</li>
        </ul>

        <h3>Adoptionssökande</h3>
        <ul>
            <li>Namn, e-post, telefon, postnummer och ort;</li>
            <li>Djuret du ansöker om, dina svar om ditt hem (boendeform, trädgård, barn och andra djur) och din motivering;</li>
            <li>Datumet då du lämnade ditt samtycke och IP-adressen som ansökan skickades från, som sparas endast för att skydda formuläret mot missbruk.</li>
        </ul>
        <p>Ansökan skickas endast till det djurhem som tar hand om djuret, som använder den för att bedöma adoptionen. Om den godkänns blir dina kontaktuppgifter en del av adoptionsregistret.</p>

        <h3>Adoptanter</h3>
        <ul>
            <li>Namn, e-post, telefon, adress, postnummer och ort;</li>
            <li>Det adopterade djuret, adoptionsdatum och eventuellt returdatum;</li>
            <li>Adoptionsavgift och anteckningar som djurhemmet har gjort.</li>
        </ul>

        <h3>Faddrar</h3>
        <ul>
            <li>Namn, e-post, telefon, adress, postnummer och ort;</li>
            <li>Fadderdjuret och betalningshistorik (datum, perioder och belopp);</li>
            <li>Kommunikationsönskemål (nyheter om djuret och/eller nyhetsbrev).</li>
        </ul>

        <h3>Volontärer</h3>
        <ul>
            <li>Namn, kön, födelsedatum och foto;</li>
            <li>Nummer på identitetshandling och skatteregistreringsnummer;</li>
            <li>Kontaktuppgifter, adress, yrke och färdmedel;</li>
            <li>Tillgänglighet, start- och slutdatum för samarbetet samt bedömningar av närvaro och insats;</li>
            <li>Önskemål om nyhetsbrev.</li>
        </ul>

        <p>Den offentliga sidan visar endast information om djuren (foton, egenskaper och beskrivning) och djurhemmets kontaktuppgifter. Den publicerar <strong>aldrig</strong> uppgifter om adoptanter, faddrar, volontärer eller användare.</p>
    </section>

    <section id="purposes">
        <h2>3. Varför vi använder uppgifterna och med vilken rättslig grund</h2>
        <ul>
            <li><strong>Hantera adoptioner, fadderskap och volontärarbete</strong> &mdash; fullgörande av avtalet med dig eller åtgärder före avtalet på din begäran (art. 6.1 b GDPR);</li>
            <li><strong>Bedöma adoptionsansökningar</strong> &mdash; åtgärder före avtalet på din begäran (art. 6.1 b GDPR), med det samtycke du lämnar i formuläret; IP-adressen sparas för att skydda formuläret mot missbruk &mdash; berättigat intresse (art. 6.1 f GDPR);</li>
            <li><strong>Följa upp djurens välbefinnande efter adoptionen</strong> &mdash; djurhemmets berättigade intresse av djurskydd (art. 6.1 f);</li>
            <li><strong>Fullgöra rättsliga förpliktelser</strong>, såsom skatteregler samt registrering och märkning av sällskapsdjur (art. 6.1 c);</li>
            <li><strong>Skicka nyhetsbrev och nyheter om ett fadderdjur</strong> &mdash; ditt samtycke, som du när som helst kan återkalla (art. 6.1 a);</li>
            <li><strong>Hålla plattformen säker och i drift</strong>, inklusive åtkomstkontroll och tekniska loggar &mdash; berättigat intresse (art. 6.1 f).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookies</h2>
        <p>Vi använder endast cookies och lokal lagring som är <strong>strikt nödvändiga</strong> för att webbplatsen ska fungera eller för en funktion du uttryckligen har begärt, och enligt 9 kap. 28 § lagen (2022:482) om elektronisk kommunikation krävs därför inte ditt samtycke. Vi använder inga reklam-, analys- eller tredjepartscookies, och alla typsnitt och resurser levereras från våra egna servrar.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Namn</th>
                        <th class="px-4 py-3">Typ</th>
                        <th class="px-4 py-3">Syfte</th>
                        <th class="px-4 py-3">Varaktighet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Håller din session aktiv medan du använder webbplatsen.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minuters inaktivitet</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Skyddar formulär mot förfalskade förfrågningar från andra webbplatser.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minuters inaktivitet</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Håller dig inloggad, endast om du kryssar i &rdquo;Kom ihåg mig&rdquo; när du loggar in.</td>
                        <td class="px-4 py-3">400 dagar eller tills du loggar ut</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Lokal lagring</td>
                        <td class="px-4 py-3">Sparar ditt val av ljust eller mörkt tema, endast om du väljer ett i inställningarna.</td>
                        <td class="px-4 py-3">Tills du rensar den i webbläsaren</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Du kan ta bort eller blockera cookies i webbläsarens inställningar; om du blockerar sessionscookies kan du inte logga in i djurhemmens område.</p>
    </section>

    <section id="sharing">
        <h2>5. Vem vi delar uppgifter med</h2>
        <p>Vi säljer inte dina uppgifter och lämnar inte ut dem i kommersiellt syfte. Varje djurhems uppgifter är endast åtkomliga för det djurhemmets team och för plattformens administratörer. De kan även behandlas av tjänsteleverantörer som hjälper oss att driva plattformen (drift och utskick av e-post), alltid med lämpliga avtalsmässiga skyddsåtgärder, eller lämnas ut till myndigheter när lagen kräver det.</p>
    </section>

    <section id="retention">
        <h2>6. Hur länge vi sparar uppgifterna</h2>
        <ul>
            <li><strong>Användarkonton:</strong> så länge kontot är aktivt;</li>
            <li><strong>Adoptionsansökningar:</strong> raderas automatiskt 6 månader efter den senaste ändringen;</li>
            <li><strong>Adoptioner och fadderskap:</strong> så länge det behövs för att följa upp djuret och fullgöra tillämpliga rättsliga förpliktelser;</li>
            <li><strong>Volontärer:</strong> under samarbetet och därefter endast under den tid som lagen kräver;</li>
            <li><strong>Sessioner:</strong> upphör automatiskt efter en tids inaktivitet.</li>
        </ul>
        <p>Borttagna poster kan sparas under en begränsad tid, utanför normal åtkomst, för revisions- och återställningsändamål.</p>
    </section>

    <section id="rights">
        <h2>7. Dina rättigheter</h2>
        <p>Du kan när som helst begära <strong>tillgång</strong> till dina uppgifter, <strong>rättelse</strong> eller <strong>radering</strong> av dem, <strong>begränsning</strong> av behandlingen eller <strong>dataportabilitet</strong>, <strong>invända</strong> mot behandling som grundas på berättigat intresse och <strong>återkalla ett lämnat samtycke</strong>, utan att det påverkar behandling som skett dessförinnan.</p>
        <p>För att utöva dessa rättigheter kontaktar du direkt det djurhem du har haft kontakt med (dess kontaktuppgifter finns på varje djurs sida)@if ($contactEmail), eller skriver till oss på <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Vi svarar senast inom en månad.</p>
        <p>Du har också rätt att lämna in klagomål till Integritetsskyddsmyndigheten (IMY) på <a href="https://www.imy.se" target="_blank" rel="noopener">www.imy.se</a>.</p>
    </section>

    <section id="security">
        <h2>8. Säkerhet</h2>
        <p>Lösenord lagras som hash, åtkomst sker endast via inbjudan och varje användare kan bara se uppgifterna för det djurhem hen tillhör. Vi vidtar tekniska och organisatoriska åtgärder för att skydda uppgifterna mot obehörig åtkomst, förlust eller ändring.</p>
    </section>

    <section id="changes">
        <h2>9. Ändringar i denna policy</h2>
        <p>Vi kan uppdatera denna policy för att återspegla ändringar i plattformen eller i lagen. Datumet för den senaste uppdateringen visas alltid högst upp på denna sida.</p>
    </section>
</div>
