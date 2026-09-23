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
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptioner</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Fadderskap</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Volontärer</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Anläggningar</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Användare</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Inställningar</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Kom igång</h2>
        <p>Vid allra första starten ber applikationen dig att skapa det första administratörskontot. Därefter skapas nya konton endast via inbjudan.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Roller</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administratör</strong> &mdash; hanterar hela plattformen: djurhemmen, de gemensamma referenstabellerna och användarkontona. Administratörer hanterar inte djur eller anläggningar.</li>
            <li><strong>Föreståndare</strong> &mdash; driver ett djurhem: allt som personalen kan göra, plus att bjuda in och hantera djurhemmets användare.</li>
            <li><strong>Personal</strong> &mdash; sköter djurhemmets dagliga arbete: djur, vaccinationer, adoptioner, fadderskap, volontärer och anläggningar.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Arbeta med flera djurhem</h3>
        <p>En användare kan tillhöra fler än ett djurhem, med olika roll i vart och ett. Använd djurhemsväljaren för att byta aktivt djurhem; varje lista, räknare och formulär visar då bara det djurhemmets data. Data delas aldrig mellan djurhem.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rekommenderad ordning för uppstart</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>En administratör skapar djurhemmet och fyller i referenstabellerna (arter, raser, storlekar, pälstyper, vacciner, sjukdomar, aktiviteter).</li>
            <li>Administratören bjuder in djurhemmets föreståndare.</li>
            <li>Föreståndaren konfigurerar anläggningar, flyglar och burar och bjuder in personalen.</li>
            <li>Teamet börjar registrera djur.</li>
        </ol>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Översikt</h2>
        <p>Översikten ger dig en bild av det aktiva djurhemmet:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Räknare för aktuella djur, adoptioner, ledig burkapacitet och personal.</li>
            <li>De senaste intagen, adoptionerna, fadderskapen och dödsfallen.</li>
            <li>Djur utan känd placering, så att de kan tilldelas en bur.</li>
            <li>Varningar när något fortfarande saknas, till exempel inga burar eller arter utan raser.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Djur</h2>
        <p>Menyn Djur visar djurhemmets djur per art. Varje djurkort innehåller identifiering (referens, namn, mikrochip), utseende (ras, färger, pälstyp, storlek, kön, kastrering), datum (födelse, intag, utskrivning, död), foton, en offentlig beskrivning, interna anteckningar och kliniska anteckningar.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>Ett djurs status räknas ut automatiskt, så du ställer aldrig in den för hand:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Avliden</strong> &mdash; ett dödsdatum är ifyllt.</li>
            <li><strong>Adopterad</strong> &mdash; djuret har en adoption utan returdatum.</li>
            <li><strong>Tillgänglig</strong> / <strong>Inte tillgänglig</strong> &mdash; i övriga fall, beroende på om djuret är markerat som adopterbart.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Sökning och filtrering</h3>
        <p>Sök på namn, referens, mikrochip eller interna anteckningar och filtrera på status, art eller placering (anläggning, flygel eller bur). Filtret <em>saknade uppgifter</em> hittar djur utan ålder, foto, intagsdatum eller placering, vilket hjälper dig att hålla djurkorten kompletta.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Utskrift</h3>
        <p>Du kan skriva ut ett enskilt djurs kort från dess sida, eller skriva ut djurlistan; den utskrivna listan använder samma filter som är aktiva på skärmen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Hälsa</h3>
        <p>Registrera sjukdomar (med diagnosdatum, status och behandlingsanteckningar), vaccinationer och kliniska anteckningar för varje djur. Storlekar erbjuds bara för arter som har storlekar konfigurerade.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinationer</h2>
        <p>Varje vaccination registrerar vaccinet, datum då det gavs eller är planerat, batchnummer, veterinär och anteckningar. Sidan Vaccinationer visar dem för alla djurhemmets djur.</p>
        <p>Varje dag får användare som har vaccinationsaviseringar påslagna för ett djurhem ett e-postmeddelande med det djurhemmets vaccinationer som är planerade inom de närmaste sju dagarna och ännu inte har getts. Varje vaccination aviseras bara en gång. En klockikon i användarlistan visar vem som får dessa e-postmeddelanden.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptioner</h2>
        <p>En adoption registrerar adoptantens kontaktuppgifter, adoptionsdatum, avgift, anteckningar och ansökningsstatus (Väntande, Godkänd eller Avslagen). Påbörja den från djurets sida.</p>
        <p>Om ett adopterat djur kommer tillbaka till djurhemmet fyller du i <strong>returdatumet</strong> på adoptionen: djuret blir tillgängligt igen och adoptionen finns kvar i dess historik.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Fadderskap</h2>
        <p>Faddrar stöder ett djurs omvårdnad utan att adoptera det. Ett fadderskap sparar fadderns kontaktuppgifter och om hen vill få nyheter om djuret eller nyhetsbrevet.</p>
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
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Anläggningar</h2>
        <p>Ett djurhem är organiserat i tre nivåer: <strong>anläggningar</strong> (fysiska platser med en adress) innehåller <strong>flyglar</strong>, och flyglar innehåller <strong>burar</strong>. Varje bur har en kod och en kapacitet.</p>
        <p>Burarnas totala kapacitet avgör hur många djur djurhemmet kan ta emot, och det är till burar som djuren tilldelas. Konfigurera minst en bur innan du registrerar djur, så att de kan få en placering.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Användare</h2>
        <p>Föreståndare och administratörer bjuder in nya användare via e-post; den inbjudna personen får en länk för att ange sitt lösenord. För varje djurhem som användaren tillhör väljer du roll (föreståndare eller personal) och om användaren ska få vaccinationsaviseringar.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>En föreståndare kan bara lägga till användare i de djurhem hen förestår.</li>
            <li>En administratör kan lägga till användare i vilket djurhem som helst och kan skapa andra administratörer.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Endast administratörer ser denna meny. Här underhålls djurhemmen och de referenstabeller som delas av alla djurhem:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Djurhem</strong> &mdash; skapa, redigera och ta bort djurhem.</li>
            <li><strong>Djurarter</strong>, <strong>Raser</strong>, <strong>Storlekar</strong> och <strong>Pälstyper</strong> &mdash; alternativen som används för att beskriva djuren.</li>
            <li><strong>Vacciner</strong> och <strong>Sjukdomar</strong> &mdash; alternativen som används i djurens hälsojournaler.</li>
            <li><strong>Aktiviteter</strong> &mdash; uppgifterna som volontärer kan hjälpa till med.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Inställningar</h2>
        <p>Öppna Inställningar från användarmenyn för att se din profil, byta lösenord och välja utseende (ljust, mörkt eller system). Ditt namn kan bara ändras av en administratör eller föreståndare, och din e-postadress kan inte ändras.</p>
    </section>
</div>
