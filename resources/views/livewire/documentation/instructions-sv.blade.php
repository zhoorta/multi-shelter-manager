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
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Medlemmar</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Anläggningar</a>
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
            <li><strong>Föreståndare</strong> &mdash; driver ett djurhem: allt som personalen kan göra, plus att bjuda in och hantera djurhemmets användare.</li>
            <li><strong>Personal</strong> &mdash; sköter djurhemmets dagliga arbete: djur, vaccinationer, adoptioner, fadderskap, volontärer och anläggningar.</li>
            <li><strong>Läsare</strong> &mdash; endast läsbehörighet till djurhemmet: kan se djur, vaccinationer och anläggningar och skriva ut djurblad och djurlistor, men kan inte skapa, redigera eller ta bort något och ser inte personuppgifter om adoptanter, faddrar, volontärer eller medlemmar.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Arbeta med flera djurhem</h3>
        <p>En användare kan tillhöra fler än ett djurhem, med olika roll i vart och ett. Använd djurhemsväljaren för att byta aktivt djurhem; varje lista, räknare och formulär visar då bara det djurhemmets data. Data delas aldrig mellan djurhem.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rekommenderad ordning för uppstart</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>En administratör fyller i grunddatatabellerna (regioner, djurarter, raser, storlekar, pälstyper, vacciner, sjukdomar, aktiviteter).</li>
            <li>Administratören skapar djurhemmet, fyller i dess profil (kontaktuppgifter, region, beskrivning, logotyp) och väljer vilka djurarter det arbetar med.</li>
            <li>Administratören bjuder in djurhemmets föreståndare.</li>
            <li>Föreståndaren konfigurerar anläggningar, flyglar och burar och bjuder in personalen.</li>
            <li>Teamet börjar registrera djur.</li>
            <li>Om den öppna portalen är aktiverad publicerar teamet de djur som är redo för adoption.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Hitta rätt i applikationen</h3>
        <p>Sidomenyn visar bara det som din roll kan använda. Chefer och personal ser menyn Djur (en post per djurart som är aktiverad för djurhemmet, samt Fadderskap, Adoptioner och Vaccinationer), Volontärer, Medlemmar och Anläggningar; chefer ser även Användare. Administratörer ser i stället Användare och menyn Administration. Läsare ser samma menyer som personal, utom Fadderskap, Adoptioner, Volontärer och Medlemmar, och sidorna visar inga knappar för att skapa, redigera eller ta bort. Den här dokumentationen finns alltid längst ned i sidomenyn.</p>
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
        <p>Sök på namn, referens, mikrochip eller interna anteckningar och filtrera på status, art eller placering (anläggning, flygel eller bur). Filtret <em>saknade uppgifter</em> hittar djur utan ålder, foto, intagsdatum eller placering, vilket hjälper dig att hålla djurkorten kompletta.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Utskrift</h3>
        <p>Du kan skriva ut ett enskilt djurs kort från dess sida, eller skriva ut djurlistan; den utskrivna listan använder samma filter som är aktiva på skärmen.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Dela i sociala medier</h3>
        <p>Djur som kan adopteras och är tillgängliga har en delningsknapp högst upp på sin sida. Den förbereder en text med djurets uppgifter och djurhemmets kontaktuppgifter, klar att kopiera, och låter dig ladda ner huvudfotot för att publicera på Facebook, Instagram eller WhatsApp. När djuret är publicerat på den offentliga portalen innehåller texten en länk till djuret, och du kan även dela det direkt på Facebook eller WhatsApp.</p>
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
        <p>Sidan Adoptioner (under Djur i sidomenyn) visar djurhemmets alla adoptioner; sök på adoptantens namn, telefon, e-post eller anteckningar, eller på djurets namn eller referens.</p>
        <p>Om ett adopterat djur kommer tillbaka till djurhemmet fyller du i <strong>returdatumet</strong> på adoptionen: djuret blir tillgängligt igen och adoptionen finns kvar i dess historik.</p>
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
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Användare</h2>
        <p>Föreståndare och administratörer bjuder in nya användare via e-post; den inbjudna personen får en länk för att ange sitt lösenord. För varje djurhem som användaren tillhör väljer du roll (föreståndare, personal eller läsare) och om användaren ska få vaccinationsaviseringar.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>En föreståndare kan bara lägga till användare i de djurhem hen förestår.</li>
            <li>En administratör kan lägga till användare i vilket djurhem som helst och kan skapa andra administratörer.</li>
        </ul>
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
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vad som visas offentligt</h3>
        <p>Ett djur visas bara på portalen när <strong>alla</strong> dessa villkor är uppfyllda:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publicera på den öppna portalen</strong> är påslaget (avstängt som standard, så att inget publiceras av misstag).</li>
            <li>Djuret är tillgängligt för adoption (adopterade eller avlidna djur försvinner automatiskt).</li>
            <li>Djurhemmet har inte tagits bort.</li>
        </ul>
        <p>Djur markerade som <strong>Utvald</strong> visas först, med ett märke. Endast namn, referens, foton, offentlig beskrivning och beskrivande uppgifter (djurart, ras, storlek, kön, ålder, pälstyp, kastrerad) visas &mdash; interna anteckningar, kliniska anteckningar, chip och bur publiceras aldrig.</p>
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
            <li><strong>Djurhem</strong> &mdash; skapa, redigera och ta bort djurhem. Förutom namn och ort har ett djurhem ett kortnamn, kontaktuppgifter (e-post, telefon, webbplats), adress, region, en beskrivning och en logotyp &mdash; som används på den öppna portalen när den är aktiverad. Listan <strong>Djurarter</strong> i djurhemsformuläret styr vilka djurarter som visas i menyn Djur för djurhemmets chef och personal.</li>
            <li><strong>Regioner</strong> &mdash; de regioner som djurhemmen tillhör, används även som filter på den öppna portalen.</li>
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
