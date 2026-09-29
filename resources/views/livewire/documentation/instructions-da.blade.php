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
        <a href="#treatments" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Behandlinger</a>
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
            <li><strong>Internatleder</strong> &mdash; leder et internat: alt, hvad en medarbejder kan, plus at invitere og administrere internatets brugere. Internatlederen holder også internatets profil (kontaktoplysninger, adresse, beskrivelse, logo) opdateret under <strong>Indstillinger &gt; Internat</strong>; kun en administrator kan ændre internatets navn eller arter.</li>
            <li><strong>Medarbejder</strong> &mdash; står for internatets daglige arbejde: dyr, vaccinationer, behandlinger, adoptioner, fadderskaber, frivillige og anlæg.</li>
            <li><strong>Læser</strong> &mdash; kun læseadgang til internatet: kan se dyr, vaccinationer, behandlinger og anlæg og udskrive dyreark og dyrelister, men kan ikke oprette, redigere eller slette noget og ser ikke personoplysninger om adoptanter, faddere, frivillige eller medlemmer.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Arbejde med flere internater</h3>
        <p>En bruger kan høre til mere end ét internat, med forskellig rolle i hvert. Brug internatvælgeren til at skifte aktivt internat; alle lister, tællere og formularer viser derefter kun data fra det internat. Data deles aldrig mellem internater.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Anbefalet rækkefølge for opsætning</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>En administrator udfylder opslagstabellerne (regioner, dyrearter, racer, størrelser, pelstyper, vacciner, behandlinger, sygdomme, aktiviteter).</li>
            <li>Administratoren opretter internatet, udfylder dets profil (kontaktoplysninger, region, beskrivelse, logo) og vælger, hvilke dyrearter det arbejder med.</li>
            <li>Administratoren inviterer internatets leder.</li>
            <li>Lederen konfigurerer anlæg, fløje og bure og inviterer medarbejderne.</li>
            <li>Teamet begynder at registrere dyr.</li>
            <li>Hvis den offentlige portal er slået til, udgiver teamet de dyr, der er klar til adoption.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Find rundt i applikationen</h3>
        <p>Sidemenuen viser kun det, din rolle kan bruge. Ledere og personale ser menuen Dyr (ét punkt pr. dyreart, der er slået til for internatet, samt Fadderskaber, Adoptioner, Vaccinationer og Behandlinger), Frivillige, Medlemmer og Anlæg; ledere ser også Brugere. Administratorer ser i stedet Brugere og menuen Administration. Læsere ser de samme menuer som medarbejdere, undtagen Fadderskaber, Adoptioner, Frivillige og Medlemmer, og siderne viser ingen knapper til at oprette, redigere eller slette. Denne dokumentation findes altid nederst i sidemenuen.</p>
        <p>Menuen Dyr indeholder også <strong>Adoptionsansøgninger</strong> for ledere og personale, og kun ledere ser <strong>Rapporter</strong>.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Oversigt</h2>
        <p>Oversigten giver dig et billede af det aktive internat:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Tællere for dyr på internatet, ledig burkapacitet og adoptioner i år. Ledere og medarbejdere ser også afventende adoptionsansøgninger, forsinkede vaccinationer og skyldige medlemskontingenter, hver med link til sin liste.</li>
            <li>Advarsler, når noget stadig mangler, fx ingen bure eller arter uden racer.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Kræver opmærksomhed</h3>
        <p>Korte lister med dyr, hvor der skal gøres noget. Hver liste viser op til fem dyr og vises kun, når den har nogen; <strong>Se alle</strong> åbner dyrelisten med det tilsvarende filter.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Dyr med ukendt placering</strong> &mdash; dyr på internatet uden bur, og hvor længe, så de kan få en plads.</li>
            <li><strong>Åbne helbredsproblemer</strong> &mdash; dyr med en aktiv eller kronisk diagnose, den nyeste diagnose først, med diagnoserne.</li>
            <li><strong>Fadderskaber der skal fornyes</strong> &mdash; fadderskaber, hvis betalte periode sluttede inden for de sidste 30 dage eller slutter inden for de næste 30, med fadderens navn, så du kan kontakte vedkommende. Kun for internatledere og medarbejdere.</li>
            <li><strong>Dyr uden foto</strong> &mdash; uden foto vises et dyr dårligt på den offentlige portal og kan ikke deles på sociale medier.</li>
            <li><strong>Længst i internatet</strong> &mdash; de ledige dyr, der har ventet længst siden indtaget, og hvor længe: gode kandidater at fremhæve.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Seneste aktivitet</h3>
        <p>De seneste indtag (med indtagsdato og bur), adoptioner (med dato og adoptantens fornavn, skjult for læsere) og dødsfald (med dato).</p>
        <p>Ledig kapacitet tæller kun internatets egne bure: fløje for plejefamilier tælles ikke med, og det gør dyrene, der bor hos plejefamilier, heller ikke.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Oversigt for administratorer</h3>
        <p>Administratorer hører ikke til et internat, så deres oversigt viser hele platformen: tællere for internater, brugere aktive inden for de sidste 30 dage, dyr i pleje og adoptioner i år; en tabel over internaterne med deres dyr, adoptioner, seneste login og seneste dyreopdatering, de mindst brugte først og gamle logins fremhævet; <strong>Opsætning der mangler</strong> (internater uden arter, bure eller brugere, og arter uden racer); og <strong>Ikke accepterede invitationer</strong>, de inviterede brugere, der aldrig har logget ind. Den viser kun totaler, aldrig dyr eller personoplysninger.</p>
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
        <p>Søg på navn, reference, mikrochip eller interne noter, og filtrer på status, art eller placering (facilitet, fløj eller bur). Det sidste filter finder dyr med <strong>Åbne helbredsproblemer</strong>, efter kastration (<strong>Kastreret</strong>, <strong>Ikke kastreret</strong>, <strong>Kastreret, detaljer mangler</strong>) eller med manglende data (ingen alder, intet foto, ingen indtagsdato eller ingen placering), hvilket hjælper med at holde journalerne komplette. Dyr med et åbent helbredsproblem har et hjerte ved siden af navnet på listen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Udskrivning</h3>
        <p>Du kan udskrive et enkelt dyrs kort fra dets side eller udskrive dyrelisten; den udskrevne liste bruger de samme filtre, som er aktive på skærmen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Del på sociale medier</h3>
        <p>Dyr, der kan adopteres og er tilgængelige, har en deleknap øverst på deres side. Den laver en tekst med dyrets oplysninger og internatets kontaktoplysninger, klar til at kopiere, og lader dig downloade hovedbilledet for at poste på Facebook, Instagram eller WhatsApp. Når dyret er offentliggjort på den offentlige portal, indeholder teksten et link til dyret, og du kan også dele det direkte på Facebook eller WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Helbred</h3>
        <p>Registrer diagnoser på dyrets side med <strong>Ny diagnose</strong>: sygdommen, diagnosedatoen, status (<strong>Aktiv</strong>, <strong>Kronisk</strong> eller <strong>Behandlet</strong>) og behandlingsnoter. En diagnose, der markeres som Behandlet, får en dato for helbredelse (som standard i dag). Aktive og kroniske diagnoser er dyrets åbne helbredsproblemer: de vises på dyrets side, på oversigten og i dyrelistens filter. Vaccinationer og kliniske noter gemmes også for hvert dyr. Størrelser tilbydes kun for arter, der har størrelser opsat.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Kastration</h3>
        <p>Når <strong>Kastreret / Steriliseret</strong> er slået til, registrerer du <strong>Kastrationsdato</strong> og hvem der udførte den (<strong>Internatet</strong> eller <strong>Før ankomst</strong>); lad dem stå tomme, hvis det er ukendt. Når det er slået fra, vælger du <strong>Kastrationsstatus</strong> (Afventer, Planlagt med dato, eller Frarådes) og tilføjer noter. Nye dyr, der ikke er kastreret, starter som Afventer.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinationer</h2>
        <p>Hver vaccination registrerer vaccinen, datoen for vaccination, næste planlagte dato, batchnummer, dyrlæge og noter. Registrér den seneste dosis og næste dato i samme registrering: den forbliver afventende, indtil en senere dosis af den vaccine registreres. For at planlægge en vaccination udfylder du kun næste dato; når dosen registreres senere, er den afsluttet. Når vaccinen har en hyppighed (for eksempel rabies, hver 36. måned), udfyldes næste dato ud fra dosens dato og kan ændres. Siden Vaccinationer viser dem for alle internatets dyr.</p>
        <p>Hver dag modtager brugere, der har slået vaccinationsnotifikationer til for et internat, en e-mail med internatets vaccinationer, der er planlagt inden for de næste syv dage og stadig afventer. Hver vaccination meldes kun én gang. Et klokkeikon i brugerlisten viser, hvem der modtager disse e-mails.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vaccinationsplan</h3>
        <p>Internater, der vaccinerer i grupper, kan åbne <strong>Vaccinationsplan</strong> på siden Vaccinationer. For det valgte år viser den pr. vaccine, hvor mange afventende vaccinationer for dyrene på internatet der falder i hver måned; den første kolonne tæller dem, der allerede faldt før det år. Klik på et tal for at se dyrene med chip og placering, og brug <strong>Udskriv liste til dyrlægen</strong> til at tage listen med på vaccinationsdagen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Gruppevaccination</h3>
        <p><strong>Gruppevaccination</strong> registrerer den samme vaccine for flere dyr på én gang, for eksempel den dag dyrlægen vaccinerer en gruppe. Vælg vaccinen og hvilke dyr der skal vises: dem, der har vaccinen planlagt i en given måned (som standard den aktuelle måned), de forsinkede eller alle dyr på internatet af vaccinens dyreart, og filtrér efter dyreart eller placering efter behov. De viste dyr er valgt på forhånd; fravælg undtagelserne. Dato, næste planlagte dato, batchnummer, dyrlæge og noter udfyldes én gang for dem alle. I vaccinationsplanen åbner knappen <strong>Gruppevaccination</strong> ved siden af en måneds liste denne formular med de dyr allerede vist.</p>
    </section>

    <section id="treatments" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Behandlinger</h2>
        <p>Behandlinger er tilbagevendende forebyggende pleje, der ikke er en vaccine, for eksempel indvortes og udvortes ormebehandling. Hver behandling registrerer behandlingen, datoen for behandlingen, næste planlagte dato, det anvendte produkt, dyrlæge og noter. De fungerer som vaccinationer: en registrering forbliver afventende, indtil en ny omgang af samme behandling registreres, og når behandlingen har en hyppighed (for eksempel ormebehandling hver 3. måned), udfyldes næste dato ud fra behandlingsdatoen.</p>
        <p>Registrér dem på dyrets side med <strong>Ny behandling</strong>. Siden <strong>Behandlinger</strong> i menuen Dyr viser dem for alle internatets dyr, med søgning og et filter på næste planlagte dato; forsinkede datoer vises med rødt og dem inden for syv dage med gult.</p>
        <p><strong>Gruppebehandling</strong> registrerer en omgang for mange dyr på én gang: vælg behandlingen og eventuelt en dyreart eller placering; alle dyr på internatet, som den gælder for, er valgt på forhånd, så fravælg undtagelserne og udfyld dato, produkt, dyrlæge og noter én gang.</p>
        <p>Brugere med vaccinationsnotifikationer slået til får også en daglig e-mail med de behandlinger, der er planlagt inden for de næste syv dage, grupperet efter behandling og dato, så en ormebehandlingsrunde kommer som én påmindelse og ikke én pr. dyr.</p>
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
            <li><strong>Helbred</strong> &mdash; givne vaccinationer (pr. måned og pr. vaccine), forsinkede vaccinationer, diagnoser pr. sygdom, åbne sager, de kastrationer internatet har udført i perioden (med dato og udført af internatet) og andelen af steriliserede dyr på internatet.</li>
        </ul>
        <p>Hold musen over et diagram for at se værdierne, eller åbn <strong>Vis tabel</strong> under det. Knappen <strong>udskriv</strong> åbner den aktuelle fane som en rapport med internatets oplysninger &mdash; for eksempel den årlige aktivitetsrapport til generalforsamlingen &mdash;, klar til at udskrive eller gemme som PDF i browseren.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Brugere</h2>
        <p>Internatledere og administratorer inviterer nye brugere via e-mail; den inviterede person modtager et link til at vælge sin adgangskode. For hvert internat, brugeren hører til, vælger du rollen (internatleder, medarbejder eller læser), og om brugeren skal modtage vaccinationsnotifikationer (de omfatter også behandlinger).</p>
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
            <li><strong>Internater</strong> &mdash; opret, rediger og fjern internater. Ud over navn og by har et internat et kort navn, kontaktoplysninger (e-mail, telefon, websted), adresse, region, en beskrivelse og et logo &mdash; som bruges på den offentlige portal, når den er slået til. Listen <strong>Dyrearter</strong> i internatformularen styrer, hvilke dyrearter der vises i menuen Dyr for internatets leder og personale. E-mailadressen er påkrævet. Når internatet har en internatleder, kan vedkommende selv opdatere alt undtagen navn og arter under <strong>Indstillinger &gt; Internat</strong>.</li>
            <li><strong>Regioner</strong> &mdash; de regioner, internaterne hører til, bruges også som filter på den offentlige portal.</li>
            <li><strong>Dyrearter</strong>, <strong>Racer</strong>, <strong>Størrelser</strong> og <strong>Pelstyper</strong> &mdash; de muligheder, der bruges til at beskrive dyrene.</li>
            <li><strong>Vacciner</strong>, <strong>Behandlinger</strong> og <strong>Sygdomme</strong> &mdash; de muligheder, der bruges i dyrenes helbredsjournaler. Vacciner og behandlinger angiver, hvilke dyrearter de gælder for, og eventuelt en hyppighed i måneder, som bruges til at udfylde næste planlagte dato.</li>
            <li><strong>Aktiviteter</strong> &mdash; de opgaver, frivillige kan hjælpe med.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Indstillinger</h2>
        <p>Åbn Indstillinger fra brugermenuen for at se din profil, skifte adgangskode og vælge udseende (lyst, mørkt eller system) og sprog. Dit navn kan kun ændres af en administrator eller internatleder, og din e-mailadresse kan ikke ændres. Sproget gemmes på din konto og bruges også i de e-mails, du modtager. På de offentlige sider og login-siden kan alle skifte sprog i menuen øverst. Internatledere ser også en fane <strong>Internat</strong>, hvor de opdaterer det aktive internats kontaktoplysninger, adresse, region, beskrivelse og logo; e-mailadressen er påkrævet, og navn og arter kan kun ændres af en administrator.</p>
    </section>
</div>
