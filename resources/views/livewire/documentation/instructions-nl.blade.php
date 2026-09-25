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
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Leden</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Locaties</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Gebruikers</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Openbaar portaal</a>
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
            <li><strong>Kijker</strong> &mdash; alleen-lezen toegang tot het asiel: kan dieren, vaccinaties en locaties bekijken en dierenfiches en -lijsten afdrukken, maar kan niets aanmaken, bewerken of verwijderen, en ziet geen persoonsgegevens van adoptanten, sponsors, vrijwilligers of leden.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Werken met meerdere asielen</h3>
        <p>Een gebruiker kan bij meer dan één asiel horen, met in elk asiel een andere rol. Gebruik de asielkiezer om het actieve asiel te wisselen; elke lijst, telling en elk formulier toont dan alleen de gegevens van dat asiel. Gegevens worden nooit tussen asielen gedeeld.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Aanbevolen volgorde van inrichting</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Een beheerder vult de referentietabellen in (regio's, diersoorten, rassen, maten, vachttypes, vaccins, ziektes, activiteiten).</li>
            <li>De beheerder maakt het asiel aan, vult het profiel aan (contactgegevens, regio, beschrijving, logo) en kiest met welke diersoorten het werkt.</li>
            <li>De beheerder nodigt de asielbeheerder uit.</li>
            <li>De asielbeheerder stelt de locaties, vleugels en hokken in en nodigt de medewerkers uit.</li>
            <li>Het team begint met het registreren van dieren.</li>
            <li>Als het openbare portaal is ingeschakeld, publiceert het team de dieren die klaar zijn voor adoptie.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Je weg vinden</h3>
        <p>De zijbalk toont alleen wat jouw rol mag gebruiken. Managers en medewerkers zien het menu Dieren (één item per diersoort die voor het asiel is ingeschakeld, plus Sponsorschappen, Adopties en Vaccinaties), Vrijwilligers, Leden en Locaties; managers zien daarnaast Gebruikers. Beheerders zien in plaats daarvan Gebruikers en het menu Beheer. Kijkers zien dezelfde menu's als medewerkers, behalve Sponsorschappen, Adopties, Vrijwilligers en Leden, en de pagina's tonen hun geen knoppen om iets aan te maken, te bewerken of te verwijderen. Deze documentatie staat altijd onderaan de zijbalk.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dashboard</h2>
        <p>Het dashboard geeft u een overzicht van het actieve asiel:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Tellers voor dieren in het asiel, beschikbare hokcapaciteit en adopties dit jaar. Beheerders en medewerkers zien ook openstaande adoptieaanvragen, achterstallige vaccinaties en achterstallige contributies, elk met een link naar de lijst.</li>
            <li>De meest recente opnames, adopties, sponsorschappen en overlijdens.</li>
            <li>Dieren zonder bekende locatie, zodat ze aan een hok kunnen worden toegewezen.</li>
            <li>Waarschuwingen wanneer er nog iets ontbreekt, zoals geen hokken ingesteld of diersoorten zonder rassen.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dieren</h2>
        <p>Het menu Dieren toont de dieren van het asiel per diersoort. Elk dossier bevat identificatie (referentie, naam, chip), uiterlijke beschrijving (ras, kleuren, vachttype, formaat, geslacht, gecastreerd), datums (geboorte, opname, vertrek, overlijden), foto's, een openbare beschrijving, interne notities en klinische notities. Alleen de diersoorten die de beheerder voor jouw asiel heeft ingeschakeld verschijnen daar.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>De status van een dier wordt automatisch bepaald, dus u stelt die nooit handmatig in:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Overleden</strong> &mdash; er is een overlijdensdatum ingevuld.</li>
            <li><strong>Geadopteerd</strong> &mdash; het dier heeft een adoptie zonder terugbrengdatum.</li>
            <li><strong>Beschikbaar</strong> / <strong>Niet beschikbaar</strong> &mdash; in alle andere gevallen, afhankelijk van of het dier als adopteerbaar is gemarkeerd.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Opties en locatie</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Beschikbaar voor adoptie</strong> &mdash; het dier kan geadopteerd worden; dit bepaalt of de status beschikbaar of niet beschikbaar is.</li>
            <li><strong>Beschikbaar voor sponsoring</strong> &mdash; het dier kan sponsors krijgen. De actie om te sponsoren wordt alleen aangeboden bij dieren met deze optie, en blijft ook na adoptie beschikbaar.</li>
            <li><strong>Hok</strong> &mdash; de lijst met hokken is gegroepeerd per locatie en vleugel en toont hoeveel plaatsen er in elk hok vrij zijn, met een groene, gele of rode markering naarmate het voller wordt.</li>
        </ul>
        <p>Als het openbare portaal is ingeschakeld, verschijnen er twee extra opties: <strong>Publiceren op het openbare portaal</strong> en <strong>Uitgelicht</strong>. Zie <a href="#public-portal" class="underline underline-offset-2">Openbaar portaal</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zoeken en filteren</h3>
        <p>Zoek op naam, referentie, chip of interne notities en filter op status, diersoort of locatie (locatie, vleugel of hok). Het filter <em>ontbrekende gegevens</em> vindt dieren zonder leeftijd, zonder foto, zonder opnamedatum of zonder locatie, zodat de dossiers compleet blijven.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Afdrukken</h3>
        <p>U kunt het dossier van één dier afdrukken vanaf de pagina van dat dier, of de dierenlijst afdrukken; de afgedrukte lijst gebruikt dezelfde filters die op het scherm actief zijn.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Delen op sociale media</h3>
        <p>Adopteerbare en beschikbare dieren hebben bovenaan hun pagina een deelknop. Die maakt een tekst met de gegevens van het dier en de contactgegevens van het asiel, klaar om te kopiëren, en laat u de hoofdfoto downloaden om te posten op Facebook, Instagram of WhatsApp. Als het dier op het openbare portaal staat, bevat de tekst een link naar het dier en kunt u het ook rechtstreeks delen op Facebook of WhatsApp.</p>
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
        <p>De pagina Adopties (onder Dieren in de zijbalk) toont alle adopties van het asiel; zoek op naam, telefoon, e-mail of notities van de adoptant, of op naam of referentie van het dier.</p>
        <p>Als een geadopteerd dier terugkomt in het asiel, vul dan de <strong>terugbrengdatum</strong> in bij de adoptie: het dier wordt weer beschikbaar en de adoptie blijft in de geschiedenis bewaard.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Sponsorschappen</h2>
        <p>Sponsors dragen bij aan de verzorging van een dier zonder het te adopteren. Een sponsorschap bewaart de contactgegevens van de sponsor en of die nieuws over het dier of de nieuwsbrief wil ontvangen.</p>
        <p>Sponsorschappen kunnen alleen worden aangemaakt voor dieren die als <strong>Beschikbaar voor sponsoring</strong> zijn gemarkeerd. De pagina Sponsorschappen (onder Dieren in de zijbalk) toont ze allemaal, met dezelfde zoekfunctie als adopties: naam, telefoon, e-mail of notities van de sponsor, of naam of referentie van het dier.</p>
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
        <p>De vrijwilligerslijst is doorzoekbaar op naam, telefoon, e-mail, fiscaal nummer of notities, en te filteren op voorkeursdiersoort, beschikbare dag en activiteit &mdash; handig om te zien wie op een bepaalde dag kan helpen.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Leden</h2>
        <p>Houd de leden van uw vereniging en hun contributie bij. Elk lid heeft een lidnummer, persoons- en contactgegevens, een datum van toetreding, een status en zijn contributie, en kan worden gekoppeld aan zijn vrijwilligersrecord als het om dezelfde persoon gaat.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Contributies</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Inschrijfgeld</strong> &mdash; wordt één keer betaald bij toetreding. Het kan 0 zijn; dan is er niets verschuldigd.</li>
            <li><strong>Contributie</strong> &mdash; het terugkerende bedrag: Maandelijks, Per kwartaal, Halfjaarlijks of Jaarlijks.</li>
        </ul>
        <p>Managers stellen de standaardwaarden van het asiel in met de knop <strong>Contributies</strong> in de ledenlijst. Nieuwe leden krijgen deze waarden, die daarna per lid kunnen worden aangepast.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Lidnummers</h3>
        <p>Laat het nummer leeg en het volgende wordt automatisch toegekend, of vul er een in om uw bestaande nummering te behouden. Een nummer kan maar één keer per asiel worden gebruikt, en nummers van verwijderde leden worden nooit hergebruikt.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Betalingen</h3>
        <p>Registreer het inschrijfgeld of een contributie op de pagina van het lid. Een contributie wordt vooraf ingevuld met de volgende te betalen periode (vanaf de dag na de laatst betaalde periode, of vanaf de datum van toetreding) en de contributie van het lid. Elke betaling registreert ook de betaaldatum, het bedrag, de methode (Contant, Bankoverschrijving, Mobiele betaling of Overig) en notities.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Contributie achterstallig</h3>
        <p>Een actief lid wordt gemarkeerd als <strong>Contributie achterstallig</strong> wanneer het inschrijfgeld niet betaald is of geen contributie vandaag dekt; de markering verdwijnt zodra de betaling is geregistreerd. Zet <strong>Alleen achterstallige contributie</strong> aan in de lijst om te zien wie een herinnering nodig heeft.</p>
        <p>De status (Actief, Geschorst of Oud-lid) verandert nooit automatisch: pas hem aan in het formulier van het lid, volgens de regels van uw vereniging. De lijst toont standaard actieve leden; gebruik het filter Status om de andere te zien.</p>
        <p>Managers en medewerkers kunnen leden toevoegen en bewerken en betalingen registreren; alleen managers kunnen leden verwijderen of de standaardwaarden wijzigen. Kijkers hebben geen toegang tot leden.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Locaties</h2>
        <p>Een asiel is in drie niveaus ingedeeld: <strong>locaties</strong> (fysieke vestigingen, met een adres) bevatten <strong>vleugels</strong>, en vleugels bevatten <strong>hokken</strong>. Elk hok heeft een code en een capaciteit.</p>
        <p>De totale capaciteit van de hokken bepaalt hoeveel dieren het asiel kan huisvesten, en dieren worden aan hokken toegewezen. Stel minstens één hok in voordat u dieren registreert, zodat ze een locatie kunnen krijgen.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Gebruikers</h2>
        <p>Asielbeheerders en beheerders nodigen nieuwe gebruikers uit per e-mail; de uitgenodigde persoon ontvangt een link om een wachtwoord in te stellen. Voor elk asiel waar de gebruiker bij hoort, kiest u de rol (asielbeheerder, medewerker of kijker) en of de gebruiker vaccinatiemeldingen ontvangt.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Een asielbeheerder kan alleen gebruikers toevoegen aan de asielen die hij of zij beheert.</li>
            <li>Een beheerder kan gebruikers toevoegen aan elk asiel en kan andere beheerders aanmaken.</li>
        </ul>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Openbaar portaal</h2>
        <p>Een installatie kan optioneel een openbare website naast de backoffice hebben. Die wordt ingeschakeld door wie de server beheert; staat hij uit, dan stuurt de startpagina bezoekers naar de inlogpagina en zijn de opties hieronder verborgen.</p>
        <p>Staat hij aan, dan kan iedereen (zonder in te loggen):</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>De dieren van alle asielen bekijken die klaar zijn voor adoptie, gefilterd op diersoort, geslacht, maat, ras en regio.</li>
            <li>De kaart van een dier openen om de foto's, de openbare beschrijving en het asiel waar het verblijft te zien.</li>
            <li>De lijst met partnerasielen bekijken, elk met een eigen pagina met contactgegevens, beschrijving, logo en dieren.</li>
            <li>De link van één dier openen die vanuit de backoffice is gedeeld: die opent direct de fiche van dat dier, en linkvoorbeelden op sociale netwerken tonen de naam, foto en beschrijving.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Wat openbaar wordt getoond</h3>
        <p>Een dier verschijnt alleen op het portaal als aan <strong>al</strong> deze voorwaarden is voldaan:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publiceren op het openbare portaal</strong> staat aan (standaard uit, zodat er niets per ongeluk wordt gepubliceerd).</li>
            <li>Het dier is beschikbaar voor adoptie (geadopteerde of overleden dieren verdwijnen automatisch).</li>
            <li>Het asiel is niet verwijderd.</li>
        </ul>
        <p>Dieren die als <strong>Uitgelicht</strong> zijn gemarkeerd worden eerst getoond, met een badge. Alleen naam, referentie, foto's, openbare beschrijving en beschrijvende gegevens (diersoort, ras, maat, geslacht, leeftijd, vachttype, gecastreerd) worden getoond &mdash; interne notities, klinische notities, chip en hok worden nooit gepubliceerd.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Tips voor goede advertenties</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Voeg minstens één goede foto en een vriendelijke openbare beschrijving toe &mdash; dat zien adoptanten als eerste.</li>
            <li>Houd het asielprofiel (contactgegevens, beschrijving, logo) actueel: het staat op de openbare pagina van het asiel, in zoekresultaten en in linkvoorbeelden.</li>
            <li>De openbare pagina's zijn voorbereid op zoekmachines (Google en andere) en er wordt automatisch een sitemap gemaakt; de backoffice wordt nooit geïndexeerd.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Beheer</h2>
        <p>Alleen beheerders zien dit menu. Hier worden de asielen en de referentietabellen onderhouden die door alle asielen worden gedeeld:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Asielen</strong> &mdash; asielen aanmaken, bewerken en verwijderen. Naast naam en plaats heeft een asiel een korte naam, contactgegevens (e-mail, telefoon, website), adres, regio, een beschrijving en een logo &mdash; gebruikt op het openbare portaal als dat is ingeschakeld. De lijst <strong>Diersoorten</strong> in het asielformulier bepaalt welke diersoorten in het menu Dieren verschijnen voor de manager en medewerkers van dat asiel.</li>
            <li><strong>Regio's</strong> &mdash; de regio's waartoe de asielen behoren, ook gebruikt als filter op het openbare portaal.</li>
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
