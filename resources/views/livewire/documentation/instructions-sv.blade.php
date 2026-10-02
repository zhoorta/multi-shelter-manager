<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Instruktioner för applikationen</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">En guide till de viktigaste delarna av {{ config('app.name') }} och hur du använder dem i vardagen.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Kom igång</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Översikt</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Djur</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vaccinationer</a>
        <a href="#treatments" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Behandlingar</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptioner</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Fadderskap</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Volontärer</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Medlemmar</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Anläggningar</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Rapporter</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Användare</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Öppen portal</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Inställningar</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Kom igång</h2>
        <p>Vid allra första starten ber applikationen dig att skapa det första administratörskontot. Därefter skapas nya konton endast via inbjudan.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Roller</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administratör</strong> &mdash; hanterar hela plattformen: djurhemmen, de gemensamma referenstabellerna och användarkontona. Administratörer hanterar inte djur eller anläggningar.</li>
            <li><strong>Föreståndare</strong> &mdash; driver ett djurhem: allt som personalen kan göra, plus att bjuda in och hantera djurhemmets användare. Föreståndaren håller också djurhemmets profil (kontaktuppgifter, adress, beskrivning, logotyp) uppdaterad under <strong>Inställningar &gt; Djurhem</strong>; bara en administratör kan ändra djurhemmets namn eller arter.</li>
            <li><strong>Personal</strong> &mdash; sköter djurhemmets dagliga arbete: djur, vaccinationer, behandlingar, adoptioner, fadderskap, volontärer och anläggningar.</li>
            <li><strong>Läsare</strong> &mdash; endast läsbehörighet till djurhemmet: kan se djur, vaccinationer, behandlingar och anläggningar och skriva ut djurblad och djurlistor, men kan inte skapa, redigera eller ta bort något och ser inte personuppgifter om adoptanter, faddrar, volontärer eller medlemmar.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Arbeta med flera djurhem</h3>
        <p>En användare kan tillhöra fler än ett djurhem, med olika roll i vart och ett. Använd djurhemsväljaren för att byta aktivt djurhem; varje lista, räknare och formulär visar då bara det djurhemmets data. Data delas aldrig mellan djurhem.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rekommenderad ordning för uppstart</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>En administratör fyller i grunddatatabellerna (regioner, djurarter, raser, storlekar, pälstyper, vacciner, behandlingar, sjukdomar, aktiviteter).</li>
            <li>Administratören skapar djurhemmet, fyller i dess profil (kontaktuppgifter, region, beskrivning, logotyp) och väljer vilka djurarter det arbetar med.</li>
            <li>Administratören bjuder in djurhemmets föreståndare.</li>
            <li>Föreståndaren konfigurerar anläggningar, flyglar och burar och bjuder in personalen.</li>
            <li>Teamet börjar registrera djur.</li>
            <li>Om den öppna portalen är aktiverad publicerar teamet de djur som är redo för adoption.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Hitta rätt i applikationen</h3>
        <p>Sidomenyn visar bara det som din roll kan använda. Chefer och personal ser menyn Djur (en post per djurart som är aktiverad för djurhemmet, samt Fadderskap, Adoptioner, Vaccinationer och Behandlingar), Volontärer, Medlemmar och Anläggningar; chefer ser även Användare. Administratörer ser i stället Användare och menyn Administration. Läsare ser samma menyer som personal, utom Fadderskap, Adoptioner, Volontärer och Medlemmar, och sidorna visar inga knappar för att skapa, redigera eller ta bort. Den här dokumentationen finns alltid längst ned i sidomenyn.</p>
        <p>Menyn Djur innehåller också <strong>Adoptionsansökningar</strong> för chefer och personal, och bara chefer ser <strong>Rapporter</strong>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Moduler</h3>
        <p>Ett djurhem som inte använder alla delar av appen kan stänga av moduler: <strong>Medlemmar</strong>, <strong>Volontärer</strong>, <strong>Fadderskap</strong>, <strong>Adoptionsansökningar</strong>, <strong>Rapporter</strong> samt <strong>Vaccinationer och behandlingar</strong>. Chefer gör det under <strong>Inställningar &gt; Djurhem</strong> och administratörer i djurhemmets redigeringsformulär, i avsnittet <strong>Moduler</strong>. En avstängd modul försvinner från sidomenyn, översikten och djursidorna, och dess sidor går inte längre att öppna. När Adoptionsansökningar är avstängda visar den publika djursidan inte längre knappen &ldquo;Jag vill adoptera&rdquo;. Inget raderas: när du slår på en modul igen finns all dess data kvar. Djur, adoptioner, diagnoser och anläggningar är alltid på.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Översikt</h2>
        <p>Översikten ger dig en bild av det aktiva djurhemmet:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Räknare för djur på hemmet, ledig burkapacitet och adoptioner i år. Ansvariga och personal ser också väntande adoptionsansökningar, försenade vaccinationer och obetalda medlemsavgifter, var och en med länk till sin lista.</li>
            <li>Varningar när något fortfarande saknas, till exempel inga burar eller arter utan raser.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Behöver åtgärdas</h3>
        <p>Korta listor med djur där något behöver göras. Varje lista visar upp till fem djur och syns bara när den har några; <strong>Visa alla</strong> öppnar djurlistan med motsvarande filter.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Djur med okänd placering</strong> &mdash; djur på hemmet utan bur, och sedan hur länge, så att de kan få en plats.</li>
            <li><strong>Öppna hälsoproblem</strong> &mdash; djur med en aktiv eller kronisk diagnos, den senaste diagnosen först, med diagnoserna.</li>
            <li><strong>Fadderskap att förnya</strong> &mdash; fadderskap vars betalda period tog slut de senaste 30 dagarna eller tar slut de kommande 30, med faddrens namn, så att du kan kontakta hen. Endast för föreståndare och personal.</li>
            <li><strong>Djur utan foto</strong> &mdash; utan foto visas ett djur dåligt på den publika portalen och kan inte delas i sociala medier.</li>
            <li><strong>Längst på hemmet</strong> &mdash; de tillgängliga djuren som har väntat längst sedan intaget, och hur länge: bra kandidater att lyfta fram.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Senaste aktivitet</h3>
        <p>De senaste intagen (med intagsdatum och bur), adoptionerna (med datum och adoptantens förnamn, dolt för läsare) och dödsfallen (med datum).</p>
        <p>Ledig kapacitet räknar bara härbärgets egna burar: flyglar för jourhem räknas inte, och inte heller djuren som bor i jourhem.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Översikt för administratörer</h3>
        <p>Administratörer hör inte till något djurhem, så deras översikt visar hela plattformen: räknare för djurhem, användare aktiva de senaste 30 dagarna, djur i vård och adoptioner i år; en tabell över djurhemmen med deras djur, adoptioner, senaste inloggning och senaste djuruppdatering, de minst använda först och gamla inloggningar markerade; <strong>Inställningar att slutföra</strong> (djurhem utan arter, burar eller användare, och arter utan raser); och <strong>Ej accepterade inbjudningar</strong>, de inbjudna användare som aldrig har loggat in. Den visar bara summor, aldrig djur eller personuppgifter.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Djur</h2>
        <p>Menyn Djur visar djurhemmets djur per art. Varje djurkort innehåller identifiering (referens, namn, mikrochip), utseende (ras, färger, pälstyp, storlek, kön, kastrering), datum (födelse, intag, utskrivning, död), foton, en offentlig beskrivning, interna anteckningar och kliniska anteckningar. Endast de djurarter som administratören har aktiverat för ditt djurhem visas där.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>Ett djurs status räknas ut automatiskt, så du ställer aldrig in den för hand:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Avliden</strong> &mdash; ett dödsdatum är ifyllt.</li>
            <li><strong>Adopterad</strong> &mdash; djuret har en adoption utan returdatum.</li>
            <li><strong>Tillgänglig</strong> / <strong>Inte tillgänglig</strong> &mdash; i övriga fall, beroende på om djuret är markerat som adopterbart.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Alternativ och placering</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Tillgänglig för adoption</strong> &mdash; djuret kan adopteras; det avgör om statusen är tillgänglig eller inte tillgänglig.</li>
            <li><strong>Tillgänglig för fadderskap</strong> &mdash; djuret kan få faddrar. Åtgärden för fadderskap erbjuds bara för djur med detta alternativ påslaget och finns kvar även efter att djuret har adopterats.</li>
            <li><strong>Bur</strong> &mdash; burlistan är grupperad per anläggning och flygel och visar hur många platser som är lediga i varje bur, med en grön, gul eller röd markering när den fylls.</li>
        </ul>
        <p>När den öppna portalen är aktiverad visas ytterligare två alternativ: <strong>Publicera på den öppna portalen</strong> och <strong>Utvald</strong>. Se <a href="#public-portal" class="underline underline-offset-2">Öppen portal</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Sökning och filtrering</h3>
        <p>Sök på namn, referens, mikrochip eller interna anteckningar och filtrera på status, art eller placering (anläggning, flygel eller bur). Det sista filtret hittar djur med <strong>Öppna hälsoproblem</strong>, efter kastrering (<strong>Kastrerad</strong>, <strong>Inte kastrerad</strong>, <strong>Kastrerad, uppgifter saknas</strong>) eller med saknade uppgifter (ingen ålder, inget foto, inget intagsdatum eller ingen placering), vilket hjälper till att hålla journalerna fullständiga. Djur med ett öppet hälsoproblem har ett hjärta bredvid namnet i listan.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Utskrift</h3>
        <p>Du kan skriva ut ett enskilt djurs kort från dess sida, eller skriva ut djurlistan; den utskrivna listan använder samma filter som är aktiva på skärmen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Dela i sociala medier</h3>
        <p>Djur som kan adopteras och är tillgängliga har en delningsknapp högst upp på sin sida. Den förbereder en text med djurets uppgifter och djurhemmets kontaktuppgifter, klar att kopiera, och låter dig ladda ner huvudfotot för att publicera på Facebook, Instagram eller WhatsApp. När djuret är publicerat på den offentliga portalen innehåller texten en länk till djuret, och du kan även dela det direkt på Facebook eller WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Hälsa</h3>
        <p>Registrera diagnoser på djurets sida med <strong>Ny diagnos</strong>: sjukdomen, diagnosdatumet, statusen (<strong>Aktiv</strong>, <strong>Kronisk</strong> eller <strong>Behandlad</strong>) och behandlingsanteckningar. En diagnos som markeras som Behandlad får ett datum för tillfrisknande (i dag som standard). Aktiva och kroniska diagnoser är djurets öppna hälsoproblem: de visas på djurets sida, i översikten och i djurlistans filter. Vaccinationer och kliniska anteckningar sparas också för varje djur. Storlekar erbjuds bara för arter som har storlekar inställda.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Kastrering</h3>
        <p>När <strong>Kastrerad / Steriliserad</strong> är på fyller du i <strong>Kastreringsdatum</strong> och vem som utförde den (<strong>Härbärget</strong> eller <strong>Före ankomst</strong>); lämna dem tomma om det är okänt. När det är av väljer du <strong>Kastreringsstatus</strong> (Väntande, Planerad med datum, eller Rekommenderas inte) och lägger till anteckningar. Nya djur som inte är kastrerade börjar som Väntande.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Utskrivet blad och anteckningar</h3>
        <p>Djurets utskrivna blad visar referens, plats, mikrochip, <strong>Födelsedatum</strong> (i stället för åldern, så att den aldrig blir fel på papper), biografin samt <strong>Kliniska anteckningar</strong> och <strong>Interna anteckningar</strong>. <strong>Interna anteckningar</strong> är privata anteckningar för hemmets användare: de visas aldrig i den publika portalen.</p>
        <p>Det sista filtret i djurlistan har också <strong>Återlämnade efter adoption</strong>: djur som kommit tillbaka och inte adopterats igen. På deras sida visar adoptionen en etikett för återlämnad.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinationer</h2>
        <p>Varje vaccination registrerar vaccinet, datum då det gavs, nästa planerade datum, batchnummer, veterinär och anteckningar. Registrera den senaste dosen och nästa datum i samma post: den förblir väntande tills en senare dos av det vaccinet registreras. För att planera en vaccination anger du bara nästa datum; när dosen registreras senare är den klar. När vaccinet har en frekvens (till exempel rabies, var 36:e månad) fylls nästa datum i utifrån dosens datum och kan ändras. Sidan Vaccinationer visar dem för alla djurhemmets djur.</p>
        <p>Varje dag får användare som har vaccinationsaviseringar påslagna för ett djurhem ett e-postmeddelande med det djurhemmets vaccinationer som är planerade inom de närmaste sju dagarna och fortfarande väntar. Varje vaccination aviseras bara en gång. En klockikon i användarlistan visar vem som får dessa e-postmeddelanden.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vaccinationsplan</h3>
        <p>Djurhem som vaccinerar i grupp kan öppna <strong>Vaccinationsplan</strong> på sidan Vaccinationer. För det valda året visar den per vaccin hur många väntande vaccinationer för djuren på djurhemmet som infaller varje månad; den första kolumnen räknar dem som infaller redan före det året. Klicka på en siffra för att lista djuren med chip och plats, och använd <strong>Skriv ut lista till veterinären</strong> för att ta med listan till vaccinationsdagen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Gruppvaccination</h3>
        <p><strong>Gruppvaccination</strong> registrerar samma vaccin för flera djur på en gång, till exempel den dag veterinären vaccinerar en grupp. Välj vaccinet och vilka djur som ska listas: de som har vaccinet planerat en viss månad (som standard innevarande månad), de försenade eller alla djur på djurhemmet av vaccinets djurart, och filtrera på djurart eller plats vid behov. De listade djuren är förvalda; avmarkera undantagen. Datum, nästa planerade datum, batchnummer, veterinär och anteckningar anges en gång för alla. I vaccinationsplanen öppnar knappen <strong>Gruppvaccination</strong> bredvid en månads lista det här formuläret med de djuren redan listade.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Hela året och export till Excel</h3>
        <p>I vaccinationsplanen listar <strong>Hela året</strong> alla djur med vaccinationer som ska ges det året. För valfri månad, eller hela året, laddar <strong>Exportera till Excel</strong> ned ett kalkylblad med namn, mikrochip, födelsedatum, Anläggning, Flygel och Bur samt, för varje vaccin, datum för senaste och nästa dos. På skärmen, utskriven och i Excel är listan sorterad efter Anläggning, Flygel och Bur, så att veterinären kan gå igenom korridorerna i ordning.</p>
    </section>

    <section id="treatments" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Behandlingar</h2>
        <p>Behandlingar är återkommande förebyggande vård som inte är vaccin, till exempel inre och yttre avmaskning. Varje behandling registrerar behandlingen, datum då den gavs, nästa planerade datum, produkten som användes, veterinär och anteckningar. De fungerar som vaccinationer: en post förblir väntande tills en ny omgång av samma behandling registreras, och när behandlingen har en frekvens (till exempel avmaskning var 3:e månad) fylls nästa datum i utifrån datumet då den gavs.</p>
        <p>Registrera dem på djurets sida med <strong>Ny behandling</strong>. Sidan <strong>Behandlingar</strong> i menyn Djur visar dem för alla djurhemmets djur, med sökning och ett filter på nästa planerade datum; försenade datum visas i rött och de inom sju dagar i gult.</p>
        <p><strong>Gruppbehandling</strong> registrerar en omgång för många djur på en gång: välj behandlingen och eventuellt en djurart eller plats; alla djur på djurhemmet som den gäller är förvalda, så avmarkera undantagen och ange datum, produkt, veterinär och anteckningar en gång.</p>
        <p>Användare med vaccinationsaviseringar påslagna får också ett dagligt e-postmeddelande med behandlingarna som infaller inom de närmaste sju dagarna, grupperade efter behandling och datum, så att en avmaskningsomgång kommer som en enda påminnelse och inte en per djur.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptioner</h2>
        <p>En adoption registrerar adoptantens kontaktuppgifter, adoptionsdatum, avgift, anteckningar och ansökningsstatus (Väntande, Godkänd eller Avslagen). Påbörja den från djurets sida.</p>
        <p>Sidan Adoptioner (under Djur i sidomenyn) visar djurhemmets alla adoptioner; sök på adoptantens namn, telefon, e-post eller anteckningar, eller på djurets namn eller referens.</p>
        <p>Om ett adopterat djur kommer tillbaka till djurhemmet fyller du i <strong>returdatumet</strong> på adoptionen: djuret blir tillgängligt igen och adoptionen finns kvar i dess historik.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Adoptionsansökningar</h3>
        <p>När den publika portalen är på kan besökare skicka en adoptionsansökan från ett djurs kort med knappen <strong>Jag vill adoptera</strong>. Formuläret frågar efter kontaktuppgifter, typ av bostad, om det finns trädgård, barn eller andra djur, och varför de vill adoptera.</p>
        <p>Ansökningarna visas under <strong>Adoptionsansökningar</strong> i menyn Djur, väntande först, och sidofältet visar hur många som väntar. För varje ansökan kan du:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Godkänn</strong> &mdash; öppnar adoptionsformuläret redan ifyllt med sökandens uppgifter; när det sparas registreras adoptionen och ansökan markeras som godkänd.</li>
            <li><strong>Avslå</strong> &mdash; markerar den som avslagen.</li>
            <li><strong>Ta bort</strong> &mdash; tar bort den.</li>
        </ul>
        <p>När ett djur redan har adopterats eller inte längre är tillgängligt markeras dess väntande ansökningar och kan avslås på en gång. Användare med <strong>Aviseringar om adoptionsansökningar</strong> påslagna får ett e-postmeddelande för varje ny ansökan; den sökande får inget e-postmeddelande, så kontakta hen själv. Ansökningar raderas automatiskt sex månader efter senaste ändring, enligt integritetspolicyn.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Överföring i djurregistret</h3>
        <p>Slå på <strong>Överförd i registret</strong> när veterinären redan har flyttat djuret till adoptantens namn i djurregistret (SIAC i Portugal); datumet sparas. Varje adoption visar om överföringen är klar eller väntar, och adoptionssidan har ett registerfilter för att hitta dem som väntar.</p>
        <p>Ett djurs sida listar alla dess adoptioner. När en adoption slutade med en återlämning och ingen har adopterat djuret igen visas en etikett för återlämnad med datum.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Fadderskap</h2>
        <p>Faddrar stöder ett djurs omvårdnad utan att adoptera det. Ett fadderskap sparar fadderns kontaktuppgifter och om hen vill få nyheter om djuret eller nyhetsbrevet.</p>
        <p>Fadderskap kan bara skapas för djur som är markerade som <strong>Tillgänglig för fadderskap</strong>. Sidan Fadderskap (under Djur i sidomenyn) visar alla, med samma sökning som för adoptioner: fadderns namn, telefon, e-post eller anteckningar, eller djurets namn eller referens.</p>
        <p>Varje fadderskap har en lista med betalningar. En betalning registrerar perioden den gäller (start- och slutdatum), betalningsdatum och belopp, så både engångsbidrag och återkommande bidrag stöds.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Volontärer</h2>
        <p>Håll koll på de personer som hjälper ditt djurhem, separat från användarkontona. För varje volontär kan du spara personuppgifter och kontaktuppgifter, ett foto, start- och slutdatum, färdmedel och önskemål om nyhetsbrev, samt:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>De aktiviteter hen hjälper till med och de arter hen helst arbetar med.</li>
            <li>Tillgänglighet per veckodag (förmiddag och/eller eftermiddag, ibland, varannan vecka eller varje vecka).</li>
            <li>Bedömningar av närvaro och insats.</li>
        </ul>
        <p>Volontärlistan kan sökas på namn, telefon, e-post, personnummer eller anteckningar och filtreras på föredragen djurart, tillgänglig dag och aktivitet &mdash; praktiskt för att se vem som kan hjälpa till en viss dag.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Medlemmar</h2>
        <p>För register över föreningens medlemmar och deras avgifter. Varje medlem har ett medlemsnummer, person- och kontaktuppgifter, ett anslutningsdatum, en status och sina avgifter, och kan kopplas till sin volontärpost när det är samma person.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Avgifter</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Inträdesavgift</strong> &mdash; betalas en gång, vid anslutning. Den kan vara 0, och då är ingenting skyldigt.</li>
            <li><strong>Medlemsavgift</strong> &mdash; den återkommande avgiften: Månadsvis, Kvartalsvis, Halvårsvis eller Årligen.</li>
        </ul>
        <p>Chefer anger djurhemmets standardvärden med knappen <strong>Avgifter</strong> i medlemslistan. Nya medlemmar får dessa värden, som sedan kan ändras för varje medlem.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Medlemsnummer</h3>
        <p>Lämna numret tomt så tilldelas nästa automatiskt, eller skriv ett nummer för att behålla den numrering du redan använder. Ett nummer kan bara användas en gång per djurhem, och borttagna medlemmars nummer återanvänds aldrig.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Betalningar</h3>
        <p>Registrera inträdesavgiften eller en medlemsavgift på medlemmens sida. En medlemsavgift är förifylld med nästa period att betala (från dagen efter den senast betalda perioden, eller från anslutningsdatumet) och medlemmens avgift. Varje betalning registrerar även betalningsdatum, belopp, metod (Kontant, Banköverföring, Mobilbetalning eller Annat) och anteckningar.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Förfallna avgifter</h3>
        <p>En aktiv medlem markeras med <strong>Förfallna avgifter</strong> när inträdesavgiften inte är betald eller ingen medlemsavgift täcker dagens datum; markeringen försvinner så snart betalningen registreras. Slå på <strong>Endast förfallna avgifter</strong> i listan för att se vem som behöver en påminnelse.</p>
        <p>Statusen (Aktiv, Avstängd eller Tidigare medlem) ändras aldrig automatiskt: ändra den i medlemmens formulär, enligt föreningens regler. Listan visar aktiva medlemmar som standard; använd filtret Status för att se de andra.</p>
        <p>Chefer och personal kan lägga till och redigera medlemmar och registrera betalningar; bara chefer kan ta bort medlemmar eller ändra standardvärdena. Läsare har ingen åtkomst till medlemmar.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Anläggningar</h2>
        <p>Ett djurhem är organiserat i tre nivåer: <strong>anläggningar</strong> (fysiska platser med en adress) innehåller <strong>flyglar</strong>, och flyglar innehåller <strong>burar</strong>. Varje bur har en kod och en kapacitet.</p>
        <p>Burarnas totala kapacitet avgör hur många djur djurhemmet kan ta emot, och det är till burar som djuren tilldelas. Konfigurera minst en bur innan du registrerar djur, så att de kan få en placering.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Jourhem</h3>
        <p>Om ert härbärge placerar djur i jourhem, skapa en flygel för dem (till exempel i en anläggning som heter "Jourhem") och slå på <strong>Flygel för jourhem</strong> i flygelns formulär. I den flygeln är varje bur ett jourhem: använd familjens namn som burens namn och som kapacitet antalet djur den kan ta emot.</p>
        <p>Välj gärna en <strong>Kontakt (volontär)</strong> för varje familj; volontärkortet innehåller telefon och adress. För att placera ett djur i en familj väljer du familjens bur i djurets formulär, som med vilken annan bur som helst.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Djurets sida visar <strong>Jourhem</strong> med familjens namn och, för chefer och personal, kontaktens namn och telefon. Läsare ser familjens namn men inte kontakten.</li>
            <li>Volontärens sida listar djuren som just nu bor hos familjen.</li>
            <li>Jourhem räknas inte in i härbärgets kapacitet på översikten eller i beläggningsrapporten, som räknar djuren i jourhem separat.</li>
            <li>På den publika portalen visar djuret märket <strong>I jourhem</strong>; familjen visas aldrig publikt.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Rapporter</h2>
        <p>Bara chefer ser Rapporter. Välj period högst upp (senaste 12 månaderna, ett år, hela perioden eller egna datum); perioder på två år eller mer visas per år i stället för per månad. Rapporterna är uppdelade i fyra flikar:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Djur</strong> &mdash; intag, adoptioner, returer och dödsfall; antalet djur på härbärget över tid; intag och adoptioner per art; adoptioner efter ålder och mediantiden i dagar till adoption; samt de tillgängliga djur som väntat längst.</li>
            <li><strong>Ekonomi</strong> &mdash; intäkter per källa (faddertjänster, medlemsavgifter, inträdesavgifter och adoptionsavgifter), räknade efter betalningsdatum; aktiva faddertjänster över tid och deras månadsvärde; aktiva, nya och efterliggande medlemmar med förväntade och inbetalda avgifter; medlemsbetalningar per metod; och de faddertjänster vars betalda period slutar inom 30 dagar.</li>
            <li><strong>Beläggning</strong> &mdash; dagens beläggning, kapacitet och djuren i burar, utan känd plats och i jourhem; beläggning över tid och per flygel. Kapaciteten är bara riktvärde, så det finns inga varningar för överbeläggning, och tidigare månader jämförs med dagens kapacitet.</li>
            <li><strong>Hälsa</strong> &mdash; givna vaccinationer (per månad och per vaccin), försenade vaccinationer, diagnoser per sjukdom, öppna fall, de kastreringar som hemmet utfört under perioden (med datum och utförda av hemmet) och andelen kastrerade djur på hemmet.</li>
        </ul>
        <p>Håll muspekaren över ett diagram för att se värdena, eller öppna <strong>Visa tabell</strong> under det. Knappen <strong>skriv ut</strong> öppnar den aktuella fliken som en rapport med härbärgets uppgifter &mdash; till exempel den årliga verksamhetsberättelsen till årsmötet &mdash;, klar att skriva ut eller spara som PDF i webbläsaren.</p>
        <p>Bredvid utskriftsknappen laddar <strong>Exportera listor</strong> ned kalkylblad för vald period: intag, adoptioner (med adoptanternas kontaktuppgifter), dödsfall och, när hälsoregister är på, givna vaccinationer. De är avsedda för de rapporter som kommuner och myndigheter begär.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Användare</h2>
        <p>Föreståndare och administratörer bjuder in nya användare via e-post; den inbjudna personen får en länk för att ange sitt lösenord. För varje djurhem som användaren tillhör väljer du roll (föreståndare, personal eller läsare) och om användaren ska få vaccinationsaviseringar (de omfattar även behandlingar).</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>En föreståndare kan bara lägga till användare i de djurhem hen förestår.</li>
            <li>En administratör kan lägga till användare i vilket djurhem som helst och kan skapa andra administratörer.</li>
        </ul>
        <p>För varje medlemskap i ett härbärge kan man också slå på <strong>Aviseringar om adoptionsansökningar</strong>: de användarna får ett e-postmeddelande för varje ny adoptionsansökan.</p>
        <p>Tills den inbjudna personen har loggat in för första gången visar användarlistan en knapp <strong>Skicka inbjudan igen</strong> på personens rad. Den skickar en ny länk via e-post och gör den tidigare ogiltig; länken går ut efter 48 timmar.</p>
        <p>Ett <strong>Personal</strong>-medlemskap kan begränsas till vissa områden: stäng av, under <strong>Får redigera</strong>, de som användaren inte ska redigera (Djur, Hälsa, Adoptioner, Fadderskap, Medlemmar). Med alla på redigerar användaren allt, som förut. En <strong>Läsare (endast läsning)</strong> kan bara titta och ser aldrig personuppgifter om adoptanter, fadderer, volontärer eller medlemmar; en <strong>Föreståndare</strong> redigerar allt.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Öppen portal</h2>
        <p>En installation kan som tillval ha en öppen webbplats bredvid backoffice. Den aktiveras av den som driver servern; när den är avstängd skickas besökare från startsidan till inloggningssidan och alternativen nedan är dolda.</p>
        <p>När den är påslagen kan vem som helst (utan inloggning):</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Bläddra bland djur från alla djurhem som är redo för adoption, filtrerat på djurart, kön, storlek, ras och region.</li>
            <li>Öppna ett djurs kort för att se foton, den offentliga beskrivningen och djurhemmet där det bor.</li>
            <li>Se listan över partnerdjurhem, vart och ett med en egen sida med kontaktuppgifter, beskrivning, logotyp och djur.</li>
            <li>Öppna en länk till ett enskilt djur som delats från backoffice: den öppnar djurets kort direkt, och länkförhandsvisningar i sociala medier visar namn, foto och beskrivning.</li>
        </ul>
        <p>Från ett djurs kort kan besökare också skicka en adoptionsansökan med knappen <strong>Jag vill adoptera</strong> (se <a href="#adoptions" class="underline underline-offset-2">Adoptioner</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vad som visas offentligt</h3>
        <p>Ett djur visas bara på portalen när <strong>alla</strong> dessa villkor är uppfyllda:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publicera på den öppna portalen</strong> är påslaget (avstängt som standard, så att inget publiceras av misstag).</li>
            <li>Djuret är tillgängligt för adoption (adopterade eller avlidna djur försvinner automatiskt).</li>
            <li>Djurhemmet har inte tagits bort.</li>
        </ul>
        <p>Djur markerade som <strong>Utvald</strong> visas först, med ett märke. Endast namn, referens, foton, offentlig beskrivning och beskrivande uppgifter (djurart, ras, storlek, kön, ålder, pälstyp, kastrerad) visas &mdash; interna anteckningar, kliniska anteckningar, chip och bur publiceras aldrig.</p>
        <p>Djur som bor i ett jourhem visar märket <strong>I jourhem</strong>; familjens namn och kontaktuppgifter publiceras aldrig.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Tips för bra annonser</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Lägg till minst ett bra foto och en vänlig offentlig beskrivning &mdash; det är det adoptanter ser först.</li>
            <li>Håll djurhemmets profil (kontaktuppgifter, beskrivning, logotyp) uppdaterad: den visas på djurhemmets offentliga sida, i sökresultat och i länkförhandsvisningar.</li>
            <li>De offentliga sidorna är förberedda för sökmotorer (Google med flera) och en webbplatskarta (sitemap) skapas automatiskt; backoffice indexeras aldrig.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Endast administratörer ser denna meny. Här underhålls djurhemmen och de referenstabeller som delas av alla djurhem:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Djurhem</strong> &mdash; skapa, redigera och ta bort djurhem. Förutom namn och ort har ett djurhem ett kortnamn, kontaktuppgifter (e-post, telefon, webbplats), adress, region, en beskrivning och en logotyp &mdash; som används på den öppna portalen när den är aktiverad. Listan <strong>Djurarter</strong> i djurhemsformuläret styr vilka djurarter som visas i menyn Djur för djurhemmets chef och personal. E-postadressen är obligatorisk. När djurhemmet har en föreståndare kan hen själv uppdatera allt utom namn och arter under <strong>Inställningar &gt; Djurhem</strong>.</li>
            <li><strong>Regioner</strong> &mdash; de regioner som djurhemmen tillhör, används även som filter på den öppna portalen.</li>
            <li><strong>Djurarter</strong>, <strong>Raser</strong>, <strong>Storlekar</strong> och <strong>Pälstyper</strong> &mdash; alternativen som används för att beskriva djuren.</li>
            <li><strong>Vacciner</strong>, <strong>Behandlingar</strong> och <strong>Sjukdomar</strong> &mdash; alternativen som används i djurens hälsojournaler. Vacciner och behandlingar anger vilka djurarter de gäller och eventuellt en frekvens i månader, som används för att fylla i nästa planerade datum.</li>
            <li><strong>Aktiviteter</strong> &mdash; uppgifterna som volontärer kan hjälpa till med.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Inställningar</h2>
        <p>Öppna Inställningar från användarmenyn för att se din profil, byta lösenord och välja utseende (ljust, mörkt eller system) och språk. Ditt namn kan bara ändras av en administratör eller föreståndare, och din e-postadress kan inte ändras. Språket sparas på ditt konto och används även för de e-postmeddelanden du får. På de offentliga sidorna och inloggningssidan kan vem som helst byta språk i menyn högst upp. Föreståndare ser också en flik <strong>Djurhem</strong> där de uppdaterar det aktiva djurhemmets kontaktuppgifter, adress, region, beskrivning och logotyp; e-postadressen är obligatorisk, och namn och arter kan bara ändras av en administratör.</p>
        <p>Föreståndare kan ladda ner en kopia av all data från sitt djurhem under <strong>Inställningar &gt; Exportera data</strong>: en ZIP-fil med en CSV-fil per område (djur, vaccinationer, behandlingar, diagnoser, adoptioner, adoptionsansökningar, fadderskap och betalningar, medlemmar och betalningar, volontärer och utrymmen). Använd den för egna säkerhetskopior eller för att byta system. Filerna öppnas direkt i Excel. De innehåller personuppgifter om medlemmar, volontärer, adoptanter och faddrar, så förvara dem säkert och dela dem inte. Varje nedladdning registreras (vem, när och från vilken IP-adress) och de tio senaste visas på samma sida.</p>
    </section>
</div>
