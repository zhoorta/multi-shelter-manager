<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Privatlivspolitik</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Senest opdateret: 23. september 2026</p>
        <p>{{ config('app.name') }} er en platform, der samler flere dyreinternater. Vi tager beskyttelsen af dine personoplysninger alvorligt og behandler dem i overensstemmelse med databeskyttelsesforordningen (GDPR) og databeskyttelsesloven (lov nr. 502 af 23. maj 2018). Denne politik forklarer, hvilke oplysninger vi behandler, hvorfor, hvor længe, og hvilke rettigheder du har.</p>
    </header>

    <section id="responsible">
        <h2>1. Hvem der er ansvarlig for dine oplysninger</h2>
        <p>Hvert internat, der bruger platformen, er <strong>dataansvarlig</strong> for de oplysninger, det indsamler som led i sin virksomhed (fx oplysninger om adoptanter, faddere og frivillige). {{ config('app.name') }} stiller den tekniske platform til rådighed og fungerer som <strong>databehandler</strong> for disse internater og behandler kun oplysningerne efter deres instruks.</p>
        <p>{{ config('app.name') }} er dataansvarlig for oplysninger om platformens brugerkonti og om besøgende på den offentlige side.</p>
    </section>

    <section id="data">
        <h2>2. Hvilke oplysninger vi behandler</h2>

        <h3>Besøgende på den offentlige side</h3>
        <p>Du kan se de dyr, der søger et hjem, uden at oprette en konto eller udfylde en formular. Vi behandler kun de tekniske oplysninger, der er nødvendige for, at siden fungerer: IP-adresse, browser- og enhedstype samt en sessionscookie.</p>

        <h3>Platformens brugere (internaternes teams)</h3>
        <ul>
            <li>Navn og e-mailadresse;</li>
            <li>Adgangskode (gemmes kun som hash, aldrig i læsbar tekst);</li>
            <li>Det eller de internater, du hører til, og din rolle (administrator, internatleder eller medarbejder);</li>
            <li>Dato for seneste login og sessionsoplysninger (IP-adresse og browser).</li>
        </ul>

        <h3>Adoptanter</h3>
        <ul>
            <li>Navn, e-mail, telefon, adresse, postnummer og by;</li>
            <li>Det adopterede dyr, adoptionsdato og eventuel returdato;</li>
            <li>Adoptionsgebyr og noter registreret af internatet.</li>
        </ul>

        <h3>Faddere</h3>
        <ul>
            <li>Navn, e-mail, telefon, adresse, postnummer og by;</li>
            <li>Fadderdyret og betalingshistorik (datoer, perioder og beløb);</li>
            <li>Kommunikationsønsker (nyheder om dyret og/eller nyhedsbrev).</li>
        </ul>

        <h3>Frivillige</h3>
        <ul>
            <li>Navn, køn, fødselsdato og foto;</li>
            <li>Nummer på identitetsdokument og skatteidentifikationsnummer;</li>
            <li>Kontaktoplysninger, adresse, erhverv og transportmiddel;</li>
            <li>Tilgængelighed, start- og slutdato for samarbejdet samt vurderinger af fremmøde og indsats;</li>
            <li>Ønske om nyhedsbrev.</li>
        </ul>

        <p>Den offentlige side viser kun oplysninger om dyrene (fotos, kendetegn og beskrivelse) og internatets kontaktoplysninger. Den offentliggør <strong>aldrig</strong> oplysninger om adoptanter, faddere, frivillige eller brugere.</p>
    </section>

    <section id="purposes">
        <h2>3. Hvorfor vi bruger oplysningerne og med hvilket retsgrundlag</h2>
        <ul>
            <li><strong>Håndtering af adoptioner, fadderskaber og frivilligt arbejde</strong> &mdash; opfyldelse af aftalen med dig eller foranstaltninger forud for aftalen på din anmodning (art. 6, stk. 1, litra b, GDPR);</li>
            <li><strong>Opfølgning på dyrenes trivsel efter adoption</strong> &mdash; internatets legitime interesse i dyrebeskyttelse (art. 6, stk. 1, litra f);</li>
            <li><strong>Overholdelse af retlige forpligtelser</strong>, fx skatteregler og registrering og mærkning af selskabsdyr (art. 6, stk. 1, litra c);</li>
            <li><strong>Udsendelse af nyhedsbreve og nyheder om et fadderdyr</strong> &mdash; dit samtykke, som du til enhver tid kan trække tilbage (art. 6, stk. 1, litra a);</li>
            <li><strong>Sikker og stabil drift af platformen</strong>, herunder adgangskontrol og tekniske logfiler &mdash; legitim interesse (art. 6, stk. 1, litra f).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookies</h2>
        <p>Vi bruger kun cookies og lokal lagring, der er <strong>strengt nødvendige</strong> for, at siden fungerer, eller for en funktion, du udtrykkeligt har bedt om; efter cookiebekendtgørelsen (bekendtgørelse nr. 1148 af 9. december 2011) kræves dit samtykke derfor ikke. Vi bruger ingen reklame-, statistik- eller tredjepartscookies, og alle skrifttyper og ressourcer leveres fra vores egne servere.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Navn</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Formål</th>
                        <th class="px-4 py-3">Varighed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Holder din session aktiv, mens du bruger siden.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutters inaktivitet</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Beskytter formularer mod forfalskede anmodninger fra andre sider.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutters inaktivitet</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Holder dig logget ind, kun hvis du sætter flueben i &bdquo;Husk mig&ldquo;, når du logger ind.</td>
                        <td class="px-4 py-3">400 dage eller indtil du logger ud</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Lokal lagring</td>
                        <td class="px-4 py-3">Gemmer dit valg af lyst eller mørkt tema, kun hvis du vælger et i indstillingerne.</td>
                        <td class="px-4 py-3">Indtil du rydder det i din browser</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Du kan slette eller blokere cookies i din browsers indstillinger; hvis du blokerer sessionscookies, kan du ikke logge ind i internaternes område.</p>
    </section>

    <section id="sharing">
        <h2>5. Hvem vi deler oplysninger med</h2>
        <p>Vi sælger ikke dine oplysninger og videregiver dem ikke til kommercielle formål. Hvert internats oplysninger er kun tilgængelige for internatets team og for platformens administratorer. De kan også behandles af leverandører, der hjælper os med at drive platformen (hosting og afsendelse af e-mails), altid med passende kontraktlige garantier, eller videregives til myndigheder, når loven kræver det.</p>
    </section>

    <section id="retention">
        <h2>6. Hvor længe vi opbevarer oplysningerne</h2>
        <ul>
            <li><strong>Brugerkonti:</strong> så længe kontoen er aktiv;</li>
            <li><strong>Adoptioner og fadderskaber:</strong> så længe det er nødvendigt for at følge op på dyret og overholde gældende retlige forpligtelser;</li>
            <li><strong>Frivillige:</strong> under samarbejdet og derefter kun i den periode, loven kræver;</li>
            <li><strong>Sessioner:</strong> udløber automatisk efter en periode med inaktivitet.</li>
        </ul>
        <p>Slettede poster kan opbevares i en begrænset periode uden for normal adgang med henblik på revision og gendannelse efter fejl.</p>
    </section>

    <section id="rights">
        <h2>7. Dine rettigheder</h2>
        <p>Du kan til enhver tid anmode om <strong>indsigt</strong> i dine oplysninger, <strong>berigtigelse</strong> eller <strong>sletning</strong> af dem, <strong>begrænsning</strong> af behandlingen eller <strong>dataportabilitet</strong>, gøre <strong>indsigelse</strong> mod behandling baseret på legitim interesse og <strong>trække ethvert samtykke tilbage</strong> uden at påvirke den behandling, der er sket forinden.</p>
        <p>For at udøve disse rettigheder skal du kontakte det internat, du har været i kontakt med, direkte (dets kontaktoplysninger står på hvert dyrs side)@if ($contactEmail), eller skrive til os på <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Vi svarer senest inden for en måned.</p>
        <p>Du har også ret til at klage til Datatilsynet på <a href="https://www.datatilsynet.dk" target="_blank" rel="noopener">www.datatilsynet.dk</a>.</p>
    </section>

    <section id="security">
        <h2>8. Sikkerhed</h2>
        <p>Adgangskoder gemmes som hash, adgang sker kun via invitation, og hver bruger kan kun se oplysningerne for det internat, vedkommende hører til. Vi anvender tekniske og organisatoriske foranstaltninger for at beskytte oplysningerne mod uautoriseret adgang, tab eller ændring.</p>
    </section>

    <section id="changes">
        <h2>9. Ændringer i denne politik</h2>
        <p>Vi kan opdatere denne politik for at afspejle ændringer i platformen eller lovgivningen. Datoen for seneste opdatering står altid øverst på denne side.</p>
    </section>
</div>
