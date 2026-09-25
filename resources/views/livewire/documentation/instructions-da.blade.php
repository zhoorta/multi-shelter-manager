<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Vejledning til applikationen</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">En guide til de vigtigste dele af {{ config('app.name') }} og hvordan du bruger dem i hverdagen.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Kom i gang</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Oversigt</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Dyr</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vaccinationer</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptioner</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Fadderskaber</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Frivillige</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Medlemmer</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Anlæg</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Rapporter</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Brugere</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Offentlig portal</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Indstillinger</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Kom i gang</h2>
        <p>Første gang applikationen startes, beder den dig om at oprette den første administratorkonto. Derefter oprettes nye konti kun via invitation.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Roller</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrator</strong> &mdash; styrer hele platformen: internaterne, de fælles referencetabeller og brugerkontiene. Administratorer håndterer ikke dyr eller anlæg.</li>
            <li><strong>Internatleder</strong> &mdash; leder et internat: alt, hvad en medarbejder kan, plus at invitere og administrere internatets brugere.</li>
            <li><strong>Medarbejder</strong> &mdash; står for internatets daglige arbejde: dyr, vaccinationer, adoptioner, fadderskaber, frivillige og anlæg.</li>
            <li><strong>Læser</strong> &mdash; kun læseadgang til internatet: kan se dyr, vaccinationer og anlæg og udskrive dyreark og dyrelister, men kan ikke oprette, redigere eller slette noget og ser ikke personoplysninger om adoptanter, faddere, frivillige eller medlemmer.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Arbejde med flere internater</h3>
        <p>En bruger kan høre til mere end ét internat, med forskellig rolle i hvert. Brug internatvælgeren til at skifte aktivt internat; alle lister, tællere og formularer viser derefter kun data fra det internat. Data deles aldrig mellem internater.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Anbefalet rækkefølge for opsætning</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>En administrator udfylder opslagstabellerne (regioner, dyrearter, racer, størrelser, pelstyper, vacciner, sygdomme, aktiviteter).</li>
            <li>Administratoren opretter internatet, udfylder dets profil (kontaktoplysninger, region, beskrivelse, logo) og vælger, hvilke dyrearter det arbejder med.</li>
            <li>Administratoren inviterer internatets leder.</li>
            <li>Lederen konfigurerer anlæg, fløje og bure og inviterer medarbejderne.</li>
            <li>Teamet begynder at registrere dyr.</li>
            <li>Hvis den offentlige portal er slået til, udgiver teamet de dyr, der er klar til adoption.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Find rundt i applikationen</h3>
        <p>Sidemenuen viser kun det, din rolle kan bruge. Ledere og personale ser menuen Dyr (ét punkt pr. dyreart, der er slået til for internatet, samt Fadderskaber, Adoptioner og Vaccinationer), Frivillige, Medlemmer og Anlæg; ledere ser også Brugere. Administratorer ser i stedet Brugere og menuen Administration. Læsere ser de samme menuer som medarbejdere, undtagen Fadderskaber, Adoptioner, Frivillige og Medlemmer, og siderne viser ingen knapper til at oprette, redigere eller slette. Denne dokumentation findes altid nederst i sidemenuen.</p>
        <p>Menuen Dyr indeholder også <strong>Adoptionsansøgninger</strong> for ledere og personale, og kun ledere ser <strong>Rapporter</strong>.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Oversigt</h2>
        <p>Oversigten giver dig et billede af det aktive internat:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Tællere for dyr på internatet, ledig burkapacitet og adoptioner i år. Ledere og medarbejdere ser også afventende adoptionsansøgninger, forsinkede vaccinationer og skyldige medlemskontingenter, hver med link til sin liste.</li>
            <li>De seneste indtag, adoptioner, fadderskaber og dødsfald.</li>
            <li>Dyr uden kendt placering, så de kan tildeles et bur.</li>
            <li>Advarsler, når noget stadig mangler, fx ingen bure eller arter uden racer.</li>
        </ul>
        <p>Ledig kapacitet tæller kun internatets egne bure: fløje for plejefamilier tælles ikke med, og det gør dyrene, der bor hos plejefamilier, heller ikke.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Dyr</h2>
        <p>Menuen Dyr viser internatets dyr efter art. Hvert dyrekort indeholder identifikation (reference, navn, mikrochip), udseende (race, farver, pelstype, størrelse, køn, kastrering), datoer (fødsel, indtag, udskrivning, død), fotos, en offentlig beskrivelse, interne noter og kliniske noter. Kun de dyrearter, som administratoren har slået til for dit internat, vises der.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>Et dyrs status beregnes automatisk, så du indstiller den aldrig manuelt:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Død</strong> &mdash; der er udfyldt en dødsdato.</li>
            <li><strong>Adopteret</strong> &mdash; dyret har en adoption uden returdato.</li>
            <li><strong>Klar til adoption</strong> / <strong>Ikke klar</strong> &mdash; i øvrige tilfælde, afhængigt af om dyret er markeret som klar til adoption.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Indstillinger og placering</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Klar til adoption</strong> &mdash; dyret kan adopteres; det afgør, om status er tilgængelig eller ikke tilgængelig.</li>
            <li><strong>Mulig for fadderskab</strong> &mdash; dyret kan få faddere. Handlingen for fadderskab tilbydes kun for dyr med denne indstilling slået til, og den er stadig tilgængelig, efter dyret er adopteret.</li>
            <li><strong>Bur</strong> &mdash; burlisten er grupperet efter anlæg og fløj og viser, hvor mange pladser der er ledige i hvert bur, med en grøn, gul eller rød markering, efterhånden som det fyldes.</li>
        </ul>
        <p>Når den offentlige portal er slået til, vises yderligere to indstillinger: <strong>Udgiv på den offentlige portal</strong> og <strong>Fremhævet</strong>. Se <a href="#public-portal" class="underline underline-offset-2">Offentlig portal</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Søgning og filtrering</h3>
        <p>Søg efter navn, reference, mikrochip eller interne noter, og filtrer efter status, art eller placering (anlæg, fløj eller bur). Filteret <em>manglende data</em> finder dyr uden alder, foto, indtagsdato eller placering, så dyrekortene holdes komplette.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Udskrivning</h3>
        <p>Du kan udskrive et enkelt dyrs kort fra dets side eller udskrive dyrelisten; den udskrevne liste bruger de samme filtre, som er aktive på skærmen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Del på sociale medier</h3>
        <p>Dyr, der kan adopteres og er tilgængelige, har en deleknap øverst på deres side. Den laver en tekst med dyrets oplysninger og internatets kontaktoplysninger, klar til at kopiere, og lader dig downloade hovedbilledet for at poste på Facebook, Instagram eller WhatsApp. Når dyret er offentliggjort på den offentlige portal, indeholder teksten et link til dyret, og du kan også dele det direkte på Facebook eller WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Helbred</h3>
        <p>Registrer sygdomme (med diagnosedato, status og behandlingsnoter), vaccinationer og kliniske noter for hvert dyr. Størrelser tilbydes kun for arter, der har størrelser konfigureret.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinationer</h2>
        <p>Hver vaccination registrerer vaccinen, datoen for vaccination eller den planlagte dato, batchnummer, dyrlæge og noter. Siden Vaccinationer viser dem for alle internatets dyr.</p>
        <p>Hver dag modtager brugere, der har slået vaccinationsnotifikationer til for et internat, en e-mail med internatets vaccinationer, der er planlagt inden for de næste syv dage og endnu ikke er givet. Hver vaccination meldes kun én gang. Et klokkeikon i brugerlisten viser, hvem der modtager disse e-mails.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptioner</h2>
        <p>En adoption registrerer adoptantens kontaktoplysninger, adoptionsdato, gebyr, noter og ansøgningsstatus (Afventer, Godkendt eller Afvist). Start den fra dyrets side.</p>
        <p>Siden Adoptioner (under Dyr i sidemenuen) viser alle internatets adoptioner; søg på adoptantens navn, telefon, e-mail eller noter eller på dyrets navn eller reference.</p>
        <p>Hvis et adopteret dyr kommer tilbage til internatet, udfylder du <strong>returdatoen</strong> på adoptionen: dyret bliver klar til adoption igen, og adoptionen bevares i dets historik.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Adoptionsansøgninger</h3>
        <p>Når den offentlige portal er slået til, kan besøgende sende en adoptionsansøgning fra et dyrs kort med knappen <strong>Jeg vil adoptere</strong>. Formularen spørger om kontaktoplysninger, boligtype, om der er have, børn eller andre dyr, og hvorfor de vil adoptere.</p>
        <p>Ansøgningerne vises under <strong>Adoptionsansøgninger</strong> i menuen Dyr, afventende først, og sidepanelet viser, hvor mange der afventer. For hver ansøgning kan du:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Godkend</strong> &mdash; åbner adoptionsformularen udfyldt med ansøgerens oplysninger; når den gemmes, registreres adoptionen, og ansøgningen markeres som godkendt.</li>
            <li><strong>Afvis</strong> &mdash; markerer den som afvist.</li>
            <li><strong>Slet</strong> &mdash; fjerner den.</li>
        </ul>
        <p>Når et dyr allerede er adopteret eller ikke længere er tilgængeligt, markeres dets afventende ansøgninger og kan afvises på én gang. Brugere med <strong>Notifikationer om adoptionsansøgninger</strong> slået til får en e-mail for hver ny ansøgning; ansøgeren får ingen e-mail, så kontakt vedkommende selv. Ansøgninger slettes automatisk seks måneder efter seneste ændring, som angivet i privatlivspolitikken.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Fadderskaber</h2>
        <p>Faddere støtter et dyrs pleje uden at adoptere det. Et fadderskab gemmer fadderens kontaktoplysninger, og om fadderen vil modtage nyheder om dyret eller nyhedsbrevet.</p>
        <p>Fadderskaber kan kun oprettes for dyr, der er markeret som <strong>Mulig for fadderskab</strong>. Siden Fadderskaber (under Dyr i sidemenuen) viser dem alle, med samme søgning som adoptioner: fadderens navn, telefon, e-mail eller noter eller dyrets navn eller reference.</p>
        <p>Hvert fadderskab har en liste over betalinger. En betaling registrerer den periode, den dækker (start- og slutdato), betalingsdatoen og beløbet, så både engangsbidrag og faste bidrag understøttes.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Frivillige</h2>
        <p>Hold styr på de mennesker, der hjælper dit internat, adskilt fra brugerkontiene. For hver frivillig kan du gemme personlige oplysninger og kontaktoplysninger, et foto, start- og slutdato, transportmiddel og ønske om nyhedsbrev, samt:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>De aktiviteter, den frivillige hjælper med, og de arter, den frivillige helst arbejder med.</li>
            <li>Tilgængelighed pr. ugedag (formiddag og/eller eftermiddag, lejlighedsvis, hver anden uge eller hver uge).</li>
            <li>Vurderinger af fremmøde og indsats.</li>
        </ul>
        <p>Listen over frivillige kan søges på navn, telefon, e-mail, CPR-/skattenummer eller noter og filtreres efter foretrukken dyreart, ledig dag og aktivitet &mdash; praktisk til at se, hvem der kan hjælpe en bestemt dag.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Medlemmer</h2>
        <p>Hold styr på foreningens medlemmer og deres kontingent. Hvert medlem har et medlemsnummer, person- og kontaktoplysninger, en indmeldelsesdato, en status og sit kontingent og kan knyttes til sin frivilligpost, når det er samme person.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Kontingenter</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Indmeldelsesgebyr</strong> &mdash; betales én gang ved indmeldelse. Det kan være 0, og så skyldes der intet.</li>
            <li><strong>Kontingent</strong> &mdash; det tilbagevendende beløb: Månedlig, Kvartalsvis, Halvårlig eller Årlig.</li>
        </ul>
        <p>Ledere angiver internatets standardværdier med knappen <strong>Kontingenter</strong> i medlemslisten. Nye medlemmer får disse værdier, som derefter kan ændres for hvert medlem.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Medlemsnumre</h3>
        <p>Lad nummeret stå tomt, så tildeles det næste automatisk, eller skriv et nummer for at beholde den nummerering, du allerede bruger. Et nummer kan kun bruges én gang pr. internat, og slettede medlemmers numre genbruges aldrig.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Betalinger</h3>
        <p>Registrér indmeldelsesgebyret eller et kontingent på medlemmets side. Et kontingent er udfyldt på forhånd med den næste periode, der skal betales (fra dagen efter den sidst betalte periode eller fra indmeldelsesdatoen), og medlemmets kontingent. Hver betaling registrerer også betalingsdato, beløb, metode (Kontant, Bankoverførsel, Mobilbetaling eller Andet) og noter.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Kontingent i restance</h3>
        <p>Et aktivt medlem markeres med <strong>Kontingent i restance</strong>, når indmeldelsesgebyret ikke er betalt, eller intet kontingent dækker dags dato; markeringen forsvinder, så snart betalingen er registreret. Slå <strong>Kun kontingent i restance</strong> til i listen for at se, hvem der skal have en påmindelse.</p>
        <p>Status (Aktiv, Suspenderet eller Tidligere medlem) ændres aldrig automatisk: ret den i medlemmets formular efter foreningens regler. Listen viser som standard aktive medlemmer; brug filteret Status for at se de andre.</p>
        <p>Ledere og personale kan tilføje og redigere medlemmer og registrere betalinger; kun ledere kan slette medlemmer eller ændre standardværdierne. Læsere har ingen adgang til medlemmer.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Anlæg</h2>
        <p>Et internat er organiseret i tre niveauer: <strong>anlæg</strong> (fysiske steder med en adresse) indeholder <strong>fløje</strong>, og fløje indeholder <strong>bure</strong>. Hvert bur har en kode og en kapacitet.</p>
        <p>Burenes samlede kapacitet afgør, hvor mange dyr internatet kan huse, og det er til burene, dyrene tildeles. Konfigurer mindst ét bur, før du registrerer dyr, så de kan få en placering.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Plejefamilier</h3>
        <p>Hvis jeres internat anbringer dyr hos plejefamilier, så opret en fløj til dem (for eksempel i et anlæg kaldet "Plejefamilier") og slå <strong>Fløj for plejefamilier</strong> til i fløjens formular. I den fløj er hvert bur én familie: brug familiens navn som burets navn og som kapacitet det antal dyr, den kan tage imod.</p>
        <p>Vælg eventuelt en <strong>Kontakt (frivillig)</strong> for hver familie; frivilligkortet indeholder telefon og adresse. For at anbringe et dyr hos en familie vælger du familiens bur i dyrets formular, som med ethvert andet bur.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Dyrets side viser <strong>Plejefamilie</strong> med familiens navn og, for ledere og personale, kontaktens navn og telefon. Læsere ser familiens navn, men ikke kontakten.</li>
            <li>Den frivilliges side viser de dyr, der lige nu bor hos familien.</li>
            <li>Plejefamilier tæller ikke med i internatets kapacitet på oversigten eller i belægningsrapporten, som tæller dyrene i plejefamilier for sig.</li>
            <li>På den offentlige portal viser dyret mærket <strong>I plejefamilie</strong>; familien vises aldrig offentligt.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Rapporter</h2>
        <p>Kun ledere ser Rapporter. Vælg perioden øverst (seneste 12 måneder, et år, hele perioden eller egne datoer); perioder på to år eller mere vises pr. år i stedet for pr. måned. Rapporterne er delt i fire faner:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Dyr</strong> &mdash; indtag, adoptioner, returneringer og dødsfald; antallet af dyr på internatet over tid; indtag og adoptioner pr. art; adoptioner efter alder og mediantallet af dage til adoption; samt de tilgængelige dyr, der har ventet længst.</li>
            <li><strong>Økonomi</strong> &mdash; indtægter efter kilde (fadderskaber, kontingenter, indmeldelsesgebyrer og adoptionsgebyrer), talt efter betalingsdato; aktive fadderskaber over tid og deres månedlige værdi; aktive, nye og restancemedlemmer med forventede og modtagne kontingenter; medlemsbetalinger efter metode; og de fadderskaber, hvis betalte periode slutter inden for 30 dage.</li>
            <li><strong>Belægning</strong> &mdash; dagens belægning, kapacitet og dyrene i bure, uden kendt placering og i plejefamilier; belægning over tid og pr. fløj. Kapaciteten er kun vejledende, så der er ingen advarsler om overbelægning, og tidligere måneder sammenlignes med dagens kapacitet.</li>
            <li><strong>Helbred</strong> &mdash; givne vaccinationer (pr. måned og pr. vaccine), forsinkede vaccinationer, diagnoser efter sygdom, åbne sager og andelen af steriliserede dyr på internatet.</li>
        </ul>
        <p>Hold musen over et diagram for at se værdierne, eller åbn <strong>Vis tabel</strong> under det. Knappen <strong>udskriv</strong> åbner den aktuelle fane som en rapport med internatets oplysninger &mdash; for eksempel den årlige aktivitetsrapport til generalforsamlingen &mdash;, klar til at udskrive eller gemme som PDF i browseren.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Brugere</h2>
        <p>Internatledere og administratorer inviterer nye brugere via e-mail; den inviterede person modtager et link til at vælge sin adgangskode. For hvert internat, brugeren hører til, vælger du rollen (internatleder, medarbejder eller læser), og om brugeren skal modtage vaccinationsnotifikationer.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>En internatleder kan kun tilføje brugere til de internater, vedkommende leder.</li>
            <li>En administrator kan tilføje brugere til ethvert internat og kan oprette andre administratorer.</li>
        </ul>
        <p>For hvert medlemskab af et internat kan man også slå <strong>Notifikationer om adoptionsansøgninger</strong> til: de brugere får en e-mail for hver ny adoptionsansøgning.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Offentlig portal</h2>
        <p>En installation kan som tilvalg have et offentligt websted ved siden af backoffice. Det slås til af den, der driver serveren; når det er slået fra, sender forsiden besøgende til login-siden, og indstillingerne nedenfor er skjult.</p>
        <p>Når det er slået til, kan alle (uden login):</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Se dyr fra alle internater, der er klar til adoption, filtreret efter dyreart, køn, størrelse, race og region.</li>
            <li>Åbne et dyrs kort for at se billeder, den offentlige beskrivelse og det internat, hvor det bor.</li>
            <li>Se listen over partnerinternater, hver med sin egen side med kontaktoplysninger, beskrivelse, logo og dyr.</li>
            <li>Åbne et link til et enkelt dyr, der er delt fra backoffice: det åbner dyrets kort direkte, og linkforhåndsvisninger på sociale medier viser navn, billede og beskrivelse.</li>
        </ul>
        <p>Fra et dyrs kort kan besøgende også sende en adoptionsansøgning med knappen <strong>Jeg vil adoptere</strong> (se <a href="#adoptions" class="underline underline-offset-2">Adoptioner</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Hvad der vises offentligt</h3>
        <p>Et dyr vises kun på portalen, når <strong>alle</strong> disse betingelser er opfyldt:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Udgiv på den offentlige portal</strong> er slået til (slået fra som standard, så intet udgives ved en fejl).</li>
            <li>Dyret er klar til adoption (adopterede eller afdøde dyr forsvinder automatisk).</li>
            <li>Dets internat er ikke fjernet.</li>
        </ul>
        <p>Dyr markeret som <strong>Fremhævet</strong> vises først, med et mærke. Kun navn, reference, billeder, offentlig beskrivelse og beskrivende oplysninger (dyreart, race, størrelse, køn, alder, pelstype, neutraliseret) vises &mdash; interne noter, kliniske noter, chip og bur udgives aldrig.</p>
        <p>Dyr, der bor hos en plejefamilie, viser mærket <strong>I plejefamilie</strong>; familiens navn og kontaktoplysninger offentliggøres aldrig.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Tips til gode opslag</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Tilføj mindst ét godt billede og en venlig offentlig beskrivelse &mdash; det er det, adoptanter ser først.</li>
            <li>Hold internatets profil (kontaktoplysninger, beskrivelse, logo) opdateret: den vises på internatets offentlige side, i søgeresultater og i link-forhåndsvisninger.</li>
            <li>De offentlige sider er forberedt til søgemaskiner (Google og andre), og et sitemap genereres automatisk; backoffice indekseres aldrig.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Kun administratorer ser denne menu. Her vedligeholdes internaterne og de referencetabeller, som alle internater deler:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Internater</strong> &mdash; opret, rediger og fjern internater. Ud over navn og by har et internat et kort navn, kontaktoplysninger (e-mail, telefon, websted), adresse, region, en beskrivelse og et logo &mdash; som bruges på den offentlige portal, når den er slået til. Listen <strong>Dyrearter</strong> i internatformularen styrer, hvilke dyrearter der vises i menuen Dyr for internatets leder og personale.</li>
            <li><strong>Regioner</strong> &mdash; de regioner, internaterne hører til, bruges også som filter på den offentlige portal.</li>
            <li><strong>Dyrearter</strong>, <strong>Racer</strong>, <strong>Størrelser</strong> og <strong>Pelstyper</strong> &mdash; de muligheder, der bruges til at beskrive dyrene.</li>
            <li><strong>Vacciner</strong> og <strong>Sygdomme</strong> &mdash; de muligheder, der bruges i dyrenes helbredsjournaler.</li>
            <li><strong>Aktiviteter</strong> &mdash; de opgaver, frivillige kan hjælpe med.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Indstillinger</h2>
        <p>Åbn Indstillinger fra brugermenuen for at se din profil, skifte adgangskode og vælge udseende (lyst, mørkt eller system). Dit navn kan kun ændres af en administrator eller internatleder, og din e-mailadresse kan ikke ændres.</p>
    </section>
</div>
