<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Handleiding van de applicatie</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Een gids voor de belangrijkste onderdelen van {{ config('app.name') }} en hoe u ze dagelijks gebruikt.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Aan de slag</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Dashboard</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Dieren</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vaccinaties</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopties</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Sponsorschappen</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vrijwilligers</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Locaties</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Gebruikers</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Beheer</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Instellingen</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Aan de slag</h2>
        <p>Bij de allereerste start vraagt de applicatie u om het eerste beheerdersaccount aan te maken. Daarna worden nieuwe accounts alleen nog via een uitnodiging aangemaakt.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rollen</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Beheerder</strong> &mdash; beheert het hele platform: de asielen, de gedeelde referentietabellen en de gebruikersaccounts. Beheerders beheren geen dieren of locaties.</li>
            <li><strong>Asielbeheerder</strong> &mdash; leidt een asiel: alles wat een medewerker kan, plus het uitnodigen en beheren van de gebruikers van dat asiel.</li>
            <li><strong>Medewerker</strong> &mdash; verzorgt het dagelijkse werk van het asiel: dieren, vaccinaties, adopties, sponsorschappen, vrijwilligers en locaties.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Werken met meerdere asielen</h3>
        <p>Een gebruiker kan bij meer dan één asiel horen, met in elk asiel een andere rol. Gebruik de asielkiezer om het actieve asiel te wisselen; elke lijst, telling en elk formulier toont dan alleen de gegevens van dat asiel. Gegevens worden nooit tussen asielen gedeeld.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Aanbevolen volgorde van inrichting</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Een beheerder maakt het asiel aan en vult de referentietabellen in (diersoorten, rassen, formaten, vachttypes, vaccins, ziektes, activiteiten).</li>
            <li>De beheerder nodigt de asielbeheerder uit.</li>
            <li>De asielbeheerder stelt de locaties, vleugels en hokken in en nodigt de medewerkers uit.</li>
            <li>Het team begint met het registreren van dieren.</li>
        </ol>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dashboard</h2>
        <p>Het dashboard geeft u een overzicht van het actieve asiel:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Tellers voor aanwezige dieren, adopties, beschikbare hokcapaciteit en medewerkers.</li>
            <li>De meest recente opnames, adopties, sponsorschappen en overlijdens.</li>
            <li>Dieren zonder bekende locatie, zodat ze aan een hok kunnen worden toegewezen.</li>
            <li>Waarschuwingen wanneer er nog iets ontbreekt, zoals geen hokken ingesteld of diersoorten zonder rassen.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dieren</h2>
        <p>Het menu Dieren toont de dieren van het asiel per diersoort. Elk dossier bevat identificatie (referentie, naam, chip), uiterlijke beschrijving (ras, kleuren, vachttype, formaat, geslacht, gecastreerd), datums (geboorte, opname, vertrek, overlijden), foto's, een openbare beschrijving, interne notities en klinische notities.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>De status van een dier wordt automatisch bepaald, dus u stelt die nooit handmatig in:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Overleden</strong> &mdash; er is een overlijdensdatum ingevuld.</li>
            <li><strong>Geadopteerd</strong> &mdash; het dier heeft een adoptie zonder terugbrengdatum.</li>
            <li><strong>Beschikbaar</strong> / <strong>Niet beschikbaar</strong> &mdash; in alle andere gevallen, afhankelijk van of het dier als adopteerbaar is gemarkeerd.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zoeken en filteren</h3>
        <p>Zoek op naam, referentie, chip of interne notities en filter op status, diersoort of locatie (locatie, vleugel of hok). Het filter <em>ontbrekende gegevens</em> vindt dieren zonder leeftijd, zonder foto, zonder opnamedatum of zonder locatie, zodat de dossiers compleet blijven.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Afdrukken</h3>
        <p>U kunt het dossier van één dier afdrukken vanaf de pagina van dat dier, of de dierenlijst afdrukken; de afgedrukte lijst gebruikt dezelfde filters die op het scherm actief zijn.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Gezondheid</h3>
        <p>Registreer bij elk dier ziektes (met diagnosedatum, status en behandelnotities), vaccinaties en klinische notities. Formaten worden alleen aangeboden voor diersoorten waarvoor formaten zijn ingesteld.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinaties</h2>
        <p>Elke vaccinatie legt het vaccin vast, de datum waarop het is toegediend of gepland staat, het batchnummer, de dierenarts en notities. De pagina Vaccinaties toont ze voor alle dieren van het asiel.</p>
        <p>Gebruikers die vaccinatiemeldingen voor een asiel hebben ingeschakeld, ontvangen elke dag een e-mail met de vaccinaties van dat asiel die in de komende zeven dagen gepland staan en nog niet zijn toegediend. Elke vaccinatie wordt maar één keer gemeld. Een belpictogram in de gebruikerslijst laat zien wie deze e-mails ontvangt.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adopties</h2>
        <p>Een adoptie legt de contactgegevens van de adoptant vast, de adoptiedatum, de bijdrage, notities en de status van de aanvraag (In behandeling, Goedgekeurd of Afgewezen). Start een adoptie vanaf de pagina van het dier.</p>
        <p>Als een geadopteerd dier terugkomt in het asiel, vul dan de <strong>terugbrengdatum</strong> in bij de adoptie: het dier wordt weer beschikbaar en de adoptie blijft in de geschiedenis bewaard.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Sponsorschappen</h2>
        <p>Sponsors dragen bij aan de verzorging van een dier zonder het te adopteren. Een sponsorschap bewaart de contactgegevens van de sponsor en of die nieuws over het dier of de nieuwsbrief wil ontvangen.</p>
        <p>Elk sponsorschap heeft een lijst met betalingen. Een betaling legt de periode vast die ze dekt (begin- en einddatum), de betaaldatum en het bedrag, zodat zowel eenmalige als terugkerende bijdragen mogelijk zijn.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vrijwilligers</h2>
        <p>Houd bij wie uw asiel helpt, los van de gebruikersaccounts. Voor elke vrijwilliger kunt u persoonlijke en contactgegevens, een foto, begin- en einddatum, vervoermiddel en nieuwsbriefvoorkeur bewaren, plus:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>De activiteiten waarbij hij of zij helpt en de diersoorten waarmee hij of zij het liefst werkt.</li>
            <li>De beschikbaarheid per dag van de week (ochtend en/of middag, af en toe, om de twee weken of wekelijks).</li>
            <li>Beoordelingen van aanwezigheid en prestaties.</li>
        </ul>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Locaties</h2>
        <p>Een asiel is in drie niveaus ingedeeld: <strong>locaties</strong> (fysieke vestigingen, met een adres) bevatten <strong>vleugels</strong>, en vleugels bevatten <strong>hokken</strong>. Elk hok heeft een code en een capaciteit.</p>
        <p>De totale capaciteit van de hokken bepaalt hoeveel dieren het asiel kan huisvesten, en dieren worden aan hokken toegewezen. Stel minstens één hok in voordat u dieren registreert, zodat ze een locatie kunnen krijgen.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Gebruikers</h2>
        <p>Asielbeheerders en beheerders nodigen nieuwe gebruikers uit per e-mail; de uitgenodigde persoon ontvangt een link om een wachtwoord in te stellen. Voor elk asiel waar de gebruiker bij hoort, kiest u de rol (asielbeheerder of medewerker) en of de gebruiker vaccinatiemeldingen ontvangt.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Een asielbeheerder kan alleen gebruikers toevoegen aan de asielen die hij of zij beheert.</li>
            <li>Een beheerder kan gebruikers toevoegen aan elk asiel en kan andere beheerders aanmaken.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Beheer</h2>
        <p>Alleen beheerders zien dit menu. Hier worden de asielen en de referentietabellen onderhouden die door alle asielen worden gedeeld:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Asielen</strong> &mdash; asielen aanmaken, bewerken en verwijderen.</li>
            <li><strong>Diersoorten</strong>, <strong>Rassen</strong>, <strong>Formaten</strong> en <strong>Vachttypes</strong> &mdash; de opties om dieren te beschrijven.</li>
            <li><strong>Vaccins</strong> en <strong>Ziektes</strong> &mdash; de opties in de gezondheidsdossiers van de dieren.</li>
            <li><strong>Activiteiten</strong> &mdash; de taken waarbij vrijwilligers kunnen helpen.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Instellingen</h2>
        <p>Open via het gebruikersmenu de Instellingen om uw profiel te bekijken, uw wachtwoord te wijzigen en de weergave te kiezen (licht, donker of systeem). Uw naam kan alleen door een beheerder of asielbeheerder worden gewijzigd, en uw e-mailadres kan niet worden gewijzigd.</p>
    </section>
</div>
