<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Instrukcja aplikacji</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Przewodnik po głównych obszarach {{ config('app.name') }} i ich codziennym użytkowaniu.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Pierwsze kroki</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Pulpit</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Zwierzęta</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Szczepienia</a>
        <a href="#treatments" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Zabiegi</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopcje</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopcje wirtualne</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Wolontariusze</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Członkowie</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Obiekty</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Raporty</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Użytkownicy</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Portal publiczny</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administracja</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Ustawienia</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pierwsze kroki</h2>
        <p>Przy pierwszym uruchomieniu aplikacja prosi o utworzenie początkowego konta administratora. Od tej chwili nowe konta są tworzone wyłącznie na zaproszenie.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Role</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrator</strong> &mdash; zarządza całą platformą: schroniskami, wspólnymi tabelami słownikowymi i kontami użytkowników. Administratorzy nie zarządzają zwierzętami ani obiektami.</li>
            <li><strong>Kierownik</strong> &mdash; prowadzi schronisko: może wszystko to, co pracownik, a dodatkowo zaprasza użytkowników tego schroniska i nimi zarządza. Kierownik aktualizuje też profil schroniska (dane kontaktowe, adres, opis, logo) w <strong>Ustawienia &gt; Schronisko</strong>; tylko administrator może zmienić nazwę lub gatunki schroniska.</li>
            <li><strong>Pracownik</strong> &mdash; zajmuje się codzienną pracą schroniska: zwierzętami, szczepieniami, zabiegami, adopcjami, adopcjami wirtualnymi, wolontariuszami i obiektami.</li>
            <li><strong>Podgląd</strong> &mdash; dostęp tylko do odczytu: może przeglądać zwierzęta, szczepienia, zabiegi i obiekty oraz drukować karty i listy zwierząt, ale nie może niczego tworzyć, edytować ani usuwać i nie widzi danych osobowych adoptujących, opiekunów wirtualnych, wolontariuszy ani członków.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Praca z kilkoma schroniskami</h3>
        <p>Użytkownik może należeć do więcej niż jednego schroniska, z inną rolą w każdym z nich. Aktywne schronisko zmienisz przełącznikiem schronisk; każda lista, licznik i formularz pokazuje wtedy tylko dane tego schroniska. Dane nigdy nie są współdzielone między schroniskami.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zalecana kolejność konfiguracji</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Administrator uzupełnia tabele słownikowe (regiony, gatunki, rasy, rozmiary, rodzaje sierści, szczepionki, zabiegi, choroby, zajęcia).</li>
            <li>Administrator tworzy schronisko, uzupełnia jego profil (kontakt, region, opis, logo) i wybiera gatunki, z którymi pracuje.</li>
            <li>Administrator zaprasza kierownika schroniska.</li>
            <li>Kierownik konfiguruje obiekty, skrzydła i kojce oraz zaprasza pracowników.</li>
            <li>Zespół zaczyna rejestrować zwierzęta.</li>
            <li>Jeśli portal publiczny jest włączony, zespół publikuje zwierzęta gotowe do adopcji.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Poruszanie się po aplikacji</h3>
        <p>Panel boczny pokazuje tylko to, z czego może korzystać Twoja rola. Kierownicy i pracownicy widzą menu Zwierzęta (po jednej pozycji dla każdego gatunku włączonego w schronisku oraz Adopcje wirtualne, Adopcje, Szczepienia i Zabiegi), Wolontariusze, Członkowie i Obiekty; kierownicy widzą też Użytkownicy. Administratorzy widzą zamiast tego Użytkownicy i menu Administracja. Użytkownicy z rolą podglądu widzą te same menu co pracownicy, z wyjątkiem Adopcji wirtualnych, Adopcji, Wolontariuszy i Członków, a na stronach nie mają przycisków tworzenia, edycji ani usuwania. Ta dokumentacja jest zawsze dostępna na dole panelu bocznego.</p>
        <p>Menu Zwierzęta zawiera też <strong>Wnioski adopcyjne</strong> dla kierowników i personelu, a <strong>Raporty</strong> widzą tylko kierownicy.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Moduły</h3>
        <p>Schronisko, które nie korzysta ze wszystkich części aplikacji, może wyłączyć moduły: <strong>Członkowie</strong>, <strong>Wolontariusze</strong>, <strong>Adopcje wirtualne</strong>, <strong>Wnioski adopcyjne</strong>, <strong>Raporty</strong> oraz <strong>Szczepienia i zabiegi</strong>. Kierownicy robią to w <strong>Ustawienia &gt; Schronisko</strong>, a administratorzy w formularzu edycji schroniska, w sekcji <strong>Moduły</strong>. Wyłączony moduł znika z menu bocznego, panelu i kart zwierząt, a jego strony przestają się otwierać. Gdy wnioski adopcyjne są wyłączone, publiczna strona zwierzęcia nie pokazuje już przycisku &bdquo;Chcę adoptować&rdquo;. Nic nie jest usuwane: po ponownym włączeniu modułu wszystkie jego dane wracają. Zwierzęta, adopcje, diagnozy i obiekty są zawsze włączone.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pulpit</h2>
        <p>Pulpit daje przegląd aktywnego schroniska:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Liczniki zwierząt w schronisku, wolnych miejsc w kojcach i adopcji w tym roku. Kierownicy i pracownicy widzą też oczekujące wnioski adopcyjne, zaległe szczepienia i zaległe składki członkowskie, każdy z linkiem do listy.</li>
            <li>Ostrzeżenia, gdy czegoś jeszcze brakuje, np. nie zdefiniowano kojców lub gatunki nie mają ras.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Wymaga uwagi</h3>
        <p>Krótkie listy zwierząt, przy których trzeba coś zrobić. Każda pokazuje do pięciu zwierząt i pojawia się tylko wtedy, gdy coś zawiera; <strong>Zobacz wszystkie</strong> otwiera listę zwierząt z odpowiednim filtrem.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Zwierzęta o nieznanej lokalizacji</strong> &mdash; zwierzęta w schronisku bez boksu, wraz z tym, od jak dawna, aby można je było umieścić.</li>
            <li><strong>Otwarte problemy zdrowotne</strong> &mdash; zwierzęta z aktywną lub przewlekłą diagnozą, najnowsza diagnoza jako pierwsza, wraz z diagnozami.</li>
            <li><strong>Adopcje wirtualne do odnowienia</strong> &mdash; adopcje wirtualne, których opłacony okres skończył się w ostatnich 30 dniach lub kończy się w najbliższych 30, z imieniem i nazwiskiem opiekuna, aby można było się z nim skontaktować. Tylko dla kierowników i pracowników.</li>
            <li><strong>Zwierzęta bez zdjęcia</strong> &mdash; bez zdjęcia zwierzę źle wypada na portalu publicznym i nie można go udostępnić w mediach społecznościowych.</li>
            <li><strong>Najdłużej w schronisku</strong> &mdash; dostępne zwierzęta, które najdłużej czekają od przyjęcia, wraz z czasem oczekiwania: dobrzy kandydaci do promowania.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ostatnia aktywność</h3>
        <p>Ostatnie przyjęcia (z datą przyjęcia i boksem), adopcje (z datą i imieniem adoptującego, ukrytym dla użytkowników z rolą Podgląd) i zgony (z datą).</p>
        <p>Dostępna pojemność liczy tylko własne boksy schroniska: skrzydła domów tymczasowych są pomijane, podobnie jak zwierzęta mieszkające w domach tymczasowych.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Pulpit administratora</h3>
        <p>Administratorzy nie należą do żadnego schroniska, dlatego ich pulpit pokazuje całą platformę: liczniki schronisk, użytkowników aktywnych w ostatnich 30 dniach, zwierząt pod opieką i adopcji w tym roku; tabelę schronisk z ich zwierzętami, adopcjami, ostatnim logowaniem i ostatnią aktualizacją zwierząt, najrzadziej używane jako pierwsze, a dawne logowania wyróżnione; <strong>Konfiguracja do dokończenia</strong> (schroniska bez gatunków, boksów lub użytkowników oraz gatunki bez ras); oraz <strong>Niezaakceptowane zaproszenia</strong>, czyli zaproszonych użytkowników, którzy nigdy się nie zalogowali. Pokazuje tylko sumy, nigdy zwierząt ani danych osobowych.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Zwierzęta</h2>
        <p>Menu Zwierzęta wyświetla zwierzęta schroniska według gatunku. Każda karta zawiera identyfikację (numer, imię, mikroczip), opis wyglądu (rasa, kolory, rodzaj sierści, wielkość, płeć, kastracja), daty (urodzenia, przyjęcia, wyjścia, śmierci), zdjęcia, opis publiczny, notatki wewnętrzne i notatki kliniczne. Pojawiają się tam tylko gatunki, które administrator włączył dla Twojego schroniska.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>Status zwierzęcia jest ustalany automatycznie, więc nigdy nie ustawiasz go ręcznie:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Zmarły</strong> &mdash; wpisano datę śmierci.</li>
            <li><strong>Adoptowany</strong> &mdash; zwierzę ma adopcję bez daty zwrotu.</li>
            <li><strong>Do adopcji</strong> / <strong>Niedostępny</strong> &mdash; w pozostałych przypadkach, zależnie od tego, czy zwierzę jest oznaczone jako gotowe do adopcji.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Opcje i miejsce pobytu</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Do adopcji</strong> &mdash; zwierzę może zostać adoptowane; od tego zależy, czy jego status to dostępne, czy niedostępne.</li>
            <li><strong>Do adopcji wirtualnej</strong> &mdash; zwierzę może mieć wirtualnych opiekunów. Akcja adopcji wirtualnej jest dostępna tylko dla zwierząt z tą opcją i pozostaje dostępna także po adopcji zwierzęcia.</li>
            <li><strong>Kojec</strong> &mdash; lista kojców jest pogrupowana według obiektu i skrzydła i pokazuje liczbę wolnych miejsc w każdym kojcu, z zielonym, żółtym lub czerwonym znacznikiem w miarę zapełniania.</li>
        </ul>
        <p>Gdy portal publiczny jest włączony, pojawiają się dwie dodatkowe opcje: <strong>Opublikuj w portalu publicznym</strong> i <strong>Wyróżniony</strong>. Zobacz <a href="#public-portal" class="underline underline-offset-2">Portal publiczny</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Wyszukiwanie i filtrowanie</h3>
        <p>Szukaj po nazwie, numerze referencyjnym, mikroczipie lub notatkach wewnętrznych i filtruj według statusu, gatunku lub lokalizacji (obiekt, skrzydło lub boks). Ostatni filtr znajduje zwierzęta z <strong>Otwartymi problemami zdrowotnymi</strong>, według sterylizacji (<strong>Wykastrowany</strong>, <strong>Niewykastrowany</strong>, <strong>Wykastrowany, brak szczegółów</strong>) lub z brakującymi danymi (bez wieku, bez zdjęcia, bez daty przyjęcia lub bez lokalizacji), co pomaga utrzymać kompletne karty. Zwierzęta z otwartym problemem zdrowotnym mają na liście serce obok imienia.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Drukowanie</h3>
        <p>Możesz wydrukować kartę pojedynczego zwierzęcia z jego strony albo listę zwierząt; wydrukowana lista używa tych samych filtrów, które są aktywne na ekranie.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Udostępnianie w mediach społecznościowych</h3>
        <p>Zwierzęta, które można adoptować i są dostępne, mają u góry swojej strony przycisk udostępniania. Przygotowuje on tekst z danymi zwierzęcia i kontaktami schroniska, gotowy do skopiowania, i pozwala pobrać główne zdjęcie, aby opublikować je na Facebooku, Instagramie lub WhatsAppie. Gdy zwierzę jest opublikowane na portalu publicznym, tekst zawiera link do zwierzęcia, a ponadto możesz udostępnić je bezpośrednio na Facebooku lub WhatsAppie.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zdrowie</h3>
        <p>Rejestruj diagnozy na stronie zwierzęcia przyciskiem <strong>Nowa diagnoza</strong>: chorobę, datę diagnozy, status (<strong>Aktywna</strong>, <strong>Przewlekła</strong> lub <strong>Wyleczona</strong>) i notatki o leczeniu. Diagnoza oznaczona jako Wyleczona otrzymuje datę wyleczenia (domyślnie dzisiejszą). Aktywne i przewlekłe diagnozy to otwarte problemy zdrowotne zwierzęcia: widać je na jego stronie, na pulpicie i w filtrze listy zwierząt. Przy każdym zwierzęciu przechowywane są też szczepienia i notatki kliniczne. Rozmiary są dostępne tylko dla gatunków, które mają skonfigurowane rozmiary.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Sterylizacja</h3>
        <p>Gdy <strong>Wykastrowany / Wysterylizowany</strong> jest włączone, wpisz <strong>Datę sterylizacji</strong> i kto ją wykonał (<strong>Schronisko</strong> lub <strong>Przed przyjęciem</strong>); zostaw puste, jeśli nie wiadomo. Gdy jest wyłączone, wybierz <strong>Status sterylizacji</strong> (Oczekujący, Zaplanowana z datą lub Niezalecana) i dodaj notatki. Nowe zwierzęta, które nie są wysterylizowane, zaczynają ze statusem Oczekujący.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Szczepienia</h2>
        <p>Każde szczepienie zapisuje szczepionkę, datę podania, następną planowaną datę, numer serii, lekarza weterynarii i notatki. Zapisz ostatnią dawkę i następną datę w tym samym wpisie: pozostaje on oczekujący, dopóki nie zostanie zapisana późniejsza dawka tej szczepionki. Aby zaplanować szczepienie, wpisz tylko następną datę; zapisanie później dawki je zakończy. Gdy szczepionka ma częstotliwość (np. wścieklizna, co 36 miesięcy), następna data jest uzupełniana na podstawie daty dawki i można ją zmienić. Strona Szczepienia wyświetla je dla wszystkich zwierząt schroniska.</p>
        <p>Codziennie użytkownicy, którzy mają włączone powiadomienia o szczepieniach dla danego schroniska, otrzymują e-mail z listą szczepień tego schroniska zaplanowanych na najbliższe siedem dni, które wciąż oczekują. O każdym szczepieniu powiadamia się tylko raz. Ikona dzwonka na liście użytkowników pokazuje, kto otrzymuje te wiadomości.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Plan szczepień</h3>
        <p>Schroniska, które szczepią grupowo, mogą otworzyć <strong>Plan szczepień</strong> na stronie Szczepienia. Dla wybranego roku pokazuje on dla każdej szczepionki, ile oczekujących szczepień zwierząt w schronisku przypada na każdy miesiąc; pierwsza kolumna liczy te, które przypadały już przed tym rokiem. Kliknij liczbę, aby zobaczyć listę zwierząt z mikroczipem i lokalizacją, i użyj <strong>Drukuj listę dla weterynarza</strong>, aby zabrać ją w dniu szczepienia.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Szczepienie grupowe</h3>
        <p><strong>Szczepienie grupowe</strong> zapisuje tę samą szczepionkę dla wielu zwierząt naraz, na przykład w dniu, w którym weterynarz szczepi grupę. Wybierz szczepionkę i zwierzęta do wyświetlenia: te, którym szczepionka przypada w danym miesiącu (domyślnie w bieżącym), zaległe albo wszystkie zwierzęta w schronisku z gatunku tej szczepionki, i w razie potrzeby filtruj po gatunku lub lokalizacji. Wyświetlone zwierzęta są od razu zaznaczone; odznacz wyjątki. Datę, następną planowaną datę, numer serii, weterynarza i notatki wpisuje się raz dla wszystkich. W planie szczepień przycisk <strong>Szczepienie grupowe</strong> obok listy danego miesiąca otwiera ten formularz z tymi zwierzętami.</p>
    </section>

    <section id="treatments" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Zabiegi</h2>
        <p>Zabiegi to okresowa opieka profilaktyczna, która nie jest szczepieniem, na przykład odrobaczanie wewnętrzne i zewnętrzne. Każdy zabieg zapisuje zabieg, datę podania, następną planowaną datę, użyty produkt, weterynarza i notatki. Działają jak szczepienia: wpis pozostaje oczekujący, dopóki nie zostanie zapisane kolejne podanie tego samego zabiegu, a gdy zabieg ma częstotliwość (np. odrobaczanie co 3 miesiące), następna data jest uzupełniana na podstawie daty podania.</p>
        <p>Zapisuj je na stronie zwierzęcia przyciskiem <strong>Nowy zabieg</strong>. Strona <strong>Zabiegi</strong> w menu Zwierzęta wyświetla je dla wszystkich zwierząt schroniska, z wyszukiwaniem i filtrem według następnej planowanej daty; zaległe daty są oznaczone na czerwono, a te z najbliższych siedmiu dni na żółto.</p>
        <p><strong>Zabieg grupowy</strong> zapisuje jedną rundę dla wielu zwierząt naraz: wybierz zabieg i opcjonalnie gatunek lub lokalizację; wszystkie zwierzęta w schronisku, których dotyczy, są od razu zaznaczone, więc odznacz wyjątki i wpisz raz datę, produkt, weterynarza i notatki.</p>
        <p>Użytkownicy z włączonymi powiadomieniami o szczepieniach otrzymują też codzienny e-mail z zabiegami zaplanowanymi na najbliższe siedem dni, pogrupowanymi według zabiegu i daty, tak aby runda odrobaczania przychodziła jako jedno przypomnienie, a nie jedno na każde zwierzę.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adopcje</h2>
        <p>Adopcja zapisuje dane kontaktowe adoptującego, datę adopcji, opłatę, notatki i status wniosku (Oczekujący, Zatwierdzony lub Odrzucony). Rozpocznij ją ze strony zwierzęcia.</p>
        <p>Strona Adopcje (w menu Zwierzęta w panelu bocznym) zawiera wszystkie adopcje schroniska; szukaj po imieniu i nazwisku, telefonie, e-mailu lub notatkach adoptującego albo po imieniu lub numerze referencyjnym zwierzęcia.</p>
        <p>Jeśli adoptowane zwierzę wraca do schroniska, wpisz w adopcji <strong>datę zwrotu</strong>: zwierzę znów staje się dostępne, a adopcja pozostaje w jego historii.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Wnioski adopcyjne</h3>
        <p>Gdy portal publiczny jest włączony, odwiedzający mogą wysłać wniosek adopcyjny z karty zwierzęcia przyciskiem <strong>Chcę adoptować</strong>. Formularz pyta o dane kontaktowe, rodzaj mieszkania, czy jest ogród, dzieci lub inne zwierzęta oraz dlaczego chcą adoptować.</p>
        <p>Wnioski pojawiają się w <strong>Wnioski adopcyjne</strong> w menu Zwierzęta, najpierw oczekujące, a pasek boczny pokazuje, ile jest oczekujących. Przy każdym możesz:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Zatwierdź</strong> &mdash; otwiera formularz adopcji wypełniony danymi wnioskodawcy; po zapisaniu adopcja zostaje zarejestrowana, a wniosek oznaczony jako zatwierdzony.</li>
            <li><strong>Odrzuć</strong> &mdash; oznacza go jako odrzucony.</li>
            <li><strong>Usuń</strong> &mdash; usuwa go.</li>
        </ul>
        <p>Gdy zwierzę zostało już adoptowane lub nie jest już dostępne, jego oczekujące wnioski są oznaczane i można je odrzucić wszystkie naraz. Użytkownicy z włączonymi <strong>Powiadomieniami o wnioskach adopcyjnych</strong> otrzymują e-mail o każdym nowym wniosku; wnioskodawca nie otrzymuje e-maila, więc skontaktuj się z nim sam. Wnioski są usuwane automatycznie sześć miesięcy po ostatniej zmianie, zgodnie z polityką prywatności.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adopcje wirtualne</h2>
        <p>Wirtualni opiekunowie wspierają utrzymanie zwierzęcia bez jego adopcji. Adopcja wirtualna przechowuje dane kontaktowe opiekuna oraz informację, czy chce otrzymywać wiadomości o zwierzęciu lub newsletter.</p>
        <p>Adopcje wirtualne można tworzyć tylko dla zwierząt oznaczonych jako <strong>Do adopcji wirtualnej</strong>. Strona Adopcje wirtualne (w menu Zwierzęta w panelu bocznym) zawiera je wszystkie, z takim samym wyszukiwaniem jak adopcje: imię i nazwisko, telefon, e-mail lub notatki opiekuna albo imię lub numer referencyjny zwierzęcia.</p>
        <p>Każda adopcja wirtualna ma listę wpłat. Wpłata zapisuje okres, który obejmuje (daty rozpoczęcia i zakończenia), datę wpłaty i kwotę, dzięki czemu obsługiwane są zarówno wpłaty jednorazowe, jak i cykliczne.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Wolontariusze</h2>
        <p>Prowadź ewidencję osób, które pomagają Twojemu schronisku, niezależnie od kont użytkowników. Dla każdego wolontariusza możesz zapisać dane osobowe i kontaktowe, zdjęcie, daty rozpoczęcia i zakończenia, środek transportu i preferencję dotyczącą newslettera, a także:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Zajęcia, w których pomaga, i gatunki, z którymi woli pracować.</li>
            <li>Dostępność w poszczególne dni tygodnia (rano i/lub po południu, okazjonalnie, co dwa tygodnie lub co tydzień).</li>
            <li>Oceny obecności i pracy.</li>
        </ul>
        <p>Listę wolontariuszy można przeszukiwać po imieniu i nazwisku, telefonie, e-mailu, numerze podatkowym lub notatkach oraz filtrować według preferowanego gatunku, dnia dostępności i zajęcia &mdash; przydatne, by sprawdzić, kto może pomóc danego dnia.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Członkowie</h2>
        <p>Prowadź ewidencję członków stowarzyszenia i ich składek. Każdy członek ma numer członkowski, dane osobowe i kontaktowe, datę przystąpienia, status i swoje składki, a jeśli jest tą samą osobą co wolontariusz, można go powiązać z jego kartą wolontariusza.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Składki</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Wpisowe</strong> &mdash; płacone jednorazowo przy przystąpieniu. Może wynosić 0 i wtedy nic nie jest należne.</li>
            <li><strong>Składka członkowska</strong> &mdash; kwota okresowa: Miesięcznie, Kwartalnie, Półrocznie lub Rocznie.</li>
        </ul>
        <p>Kierownicy ustawiają wartości domyślne schroniska przyciskiem <strong>Składki</strong> na liście członków. Nowi członkowie otrzymują te wartości, które później można zmienić dla każdego członka.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Numery członkowskie</h3>
        <p>Pozostaw numer pusty, a kolejny zostanie nadany automatycznie, albo wpisz numer, aby zachować dotychczasową numerację. Każdy numer może być użyty w schronisku tylko raz, a numery usuniętych członków nigdy nie są używane ponownie.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Płatności</h3>
        <p>Zarejestruj wpisowe lub składkę na stronie członka. Składka jest wstępnie wypełniona kolejnym okresem do opłacenia (od dnia po ostatnim opłaconym okresie lub od daty przystąpienia) i kwotą składki członka. Każda płatność zapisuje też datę płatności, kwotę, metodę (Gotówka, Przelew bankowy, Płatność mobilna lub Inne) i notatki.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zaległe składki</h3>
        <p>Aktywny członek jest oznaczany jako <strong>Zaległe składki</strong>, gdy wpisowe nie jest opłacone lub żadna składka nie obejmuje dnia dzisiejszego; oznaczenie znika, gdy tylko płatność zostanie zarejestrowana. Włącz <strong>Tylko zaległe składki</strong> na liście, aby zobaczyć, komu wysłać przypomnienie.</p>
        <p>Status (Aktywny, Zawieszony lub Były członek) nigdy nie zmienia się automatycznie: zmień go w formularzu członka, zgodnie z zasadami stowarzyszenia. Lista domyślnie pokazuje aktywnych członków; użyj filtra Status, aby zobaczyć pozostałych.</p>
        <p>Kierownicy i pracownicy mogą dodawać i edytować członków oraz rejestrować płatności; tylko kierownicy mogą usuwać członków lub zmieniać wartości domyślne. Użytkownicy z rolą podglądu nie mają dostępu do członków.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Obiekty</h2>
        <p>Schronisko jest zorganizowane na trzech poziomach: <strong>obiekty</strong> (fizyczne lokalizacje z adresem) zawierają <strong>skrzydła</strong>, a skrzydła zawierają <strong>kojce</strong>. Każdy kojec ma kod i pojemność.</p>
        <p>Łączna pojemność kojców określa, ile zwierząt może przyjąć schronisko, a zwierzęta przypisuje się właśnie do kojców. Skonfiguruj co najmniej jeden kojec przed rejestracją zwierząt, aby można im było przypisać lokalizację.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Domy tymczasowe</h3>
        <p>Jeśli Twoje schronisko umieszcza zwierzęta w domach tymczasowych, utwórz dla nich skrzydło (na przykład w obiekcie o nazwie „Domy tymczasowe”) i włącz <strong>Skrzydło domów tymczasowych</strong> w formularzu skrzydła. W tym skrzydle każdy boks to jedna rodzina: użyj nazwiska rodziny jako nazwy boksu, a jako pojemności liczby zwierząt, które może przyjąć.</p>
        <p>Opcjonalnie wybierz <strong>Kontakt (wolontariusz)</strong> dla każdej rodziny; karta wolontariusza zawiera telefon i adres. Aby umieścić zwierzę w rodzinie, wybierz boks rodziny w formularzu zwierzęcia, jak każdy inny boks.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Strona zwierzęcia pokazuje <strong>Dom tymczasowy</strong> z nazwą rodziny, a kierownikom i personelowi także imię i telefon kontaktu. Użytkownicy z rolą <strong>Podgląd</strong> widzą nazwę rodziny, ale nie kontakt.</li>
            <li>Strona wolontariusza wymienia zwierzęta, które są obecnie w jego rodzinie.</li>
            <li>Domy tymczasowe nie wliczają się do pojemności schroniska na pulpicie ani w raporcie Obłożenie, który liczy zwierzęta w domach tymczasowych osobno.</li>
            <li>Na portalu publicznym zwierzę ma odznakę <strong>W domu tymczasowym</strong>; rodzina nigdy nie jest pokazywana publicznie.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Raporty</h2>
        <p>Raporty widzą tylko kierownicy. Wybierz okres na górze (ostatnie 12 miesięcy, rok, cały okres lub własne daty); okresy dwuletnie i dłuższe są pokazywane według lat zamiast miesięcy. Raporty są podzielone na cztery karty:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Zwierzęta</strong> &mdash; przyjęcia, adopcje, zwroty i zgony; liczba zwierząt w schronisku w czasie; przyjęcia i adopcje według gatunku; adopcje według wieku i mediana dni do adopcji; oraz dostępne zwierzęta, które czekają najdłużej.</li>
            <li><strong>Finanse</strong> &mdash; przychody według źródła (adopcje wirtualne, składki, wpisowe i opłaty adopcyjne), liczone według daty płatności; aktywne adopcje wirtualne w czasie i ich miesięczna wartość; członkowie aktywni, nowi i zalegający ze składkami, z oczekiwanymi i zebranymi składkami; płatności członków według metody; oraz adopcje wirtualne, których opłacony okres kończy się w ciągu 30 dni.</li>
            <li><strong>Obłożenie</strong> &mdash; dzisiejsze obłożenie, pojemność oraz zwierzęta w boksach, bez znanej lokalizacji i w domach tymczasowych; obłożenie w czasie i według skrzydła. Pojemność jest tylko orientacyjna, więc nie ma ostrzeżeń o przepełnieniu, a poprzednie miesiące są porównywane z obecną pojemnością.</li>
            <li><strong>Zdrowie</strong> &mdash; wykonane szczepienia (miesięcznie i według szczepionki), zaległe szczepienia, diagnozy według choroby, otwarte przypadki, sterylizacje wykonane przez schronisko w danym okresie (z datą i wykonane przez schronisko) oraz odsetek wysterylizowanych zwierząt w schronisku.</li>
        </ul>
        <p>Najedź na wykres, aby zobaczyć wartości, lub otwórz <strong>Pokaż tabelę</strong> pod nim. Przycisk <strong>drukuj</strong> otwiera bieżącą kartę jako raport z danymi schroniska &mdash; na przykład roczne sprawozdanie z działalności na walne zgromadzenie &mdash; gotowy do wydruku lub zapisania jako PDF w przeglądarce.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Użytkownicy</h2>
        <p>Kierownicy i administratorzy zapraszają nowych użytkowników e-mailem; zaproszona osoba otrzymuje link do ustawienia hasła. Dla każdego schroniska, do którego należy użytkownik, wybierasz rolę (kierownik, pracownik lub podgląd) oraz to, czy otrzymuje powiadomienia o szczepieniach (obejmują one także zabiegi).</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Kierownik może dodawać użytkowników tylko do schronisk, którymi zarządza.</li>
            <li>Administrator może dodawać użytkowników do dowolnego schroniska i tworzyć innych administratorów.</li>
        </ul>
        <p>Przy każdym członkostwie w schronisku można też włączyć <strong>Powiadomienia o wnioskach adopcyjnych</strong>: ci użytkownicy otrzymują e-mail o każdym nowym wniosku adopcyjnym.</p>
        <p>Dopóki zaproszona osoba nie zaloguje się po raz pierwszy, lista użytkowników pokazuje w jej wierszu przycisk <strong>Wyślij zaproszenie ponownie</strong>. Wysyła e-mailem nowy link i unieważnia poprzedni; link wygasa po 48 godzinach.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Portal publiczny</h2>
        <p>Instalacja może opcjonalnie udostępniać publiczną stronę obok panelu administracyjnego. Włącza ją osoba zarządzająca serwerem; gdy jest wyłączona, strona główna przekierowuje odwiedzających do logowania, a poniższe opcje są ukryte.</p>
        <p>Gdy jest włączona, każdy (bez logowania) może:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Przeglądać zwierzęta ze wszystkich schronisk gotowe do adopcji, filtrując według gatunku, płci, rozmiaru, rasy i regionu.</li>
            <li>Otworzyć kartę zwierzęcia, by zobaczyć zdjęcia, publiczny opis i schronisko, w którym przebywa.</li>
            <li>Zobaczyć listę schronisk partnerskich, każde z własną stroną z kontaktem, opisem, logo i zwierzętami.</li>
            <li>Otworzyć link do pojedynczego zwierzęcia udostępniony z panelu: otwiera on bezpośrednio kartę tego zwierzęcia, a podglądy linków w mediach społecznościowych pokazują jego imię, zdjęcie i opis.</li>
        </ul>
        <p>Z karty zwierzęcia odwiedzający mogą też wysłać wniosek adopcyjny przyciskiem <strong>Chcę adoptować</strong> (zobacz <a href="#adoptions" class="underline underline-offset-2">Adopcje</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Co jest widoczne publicznie</h3>
        <p>Zwierzę pojawia się w portalu tylko wtedy, gdy spełnione są <strong>wszystkie</strong> te warunki:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Opcja <strong>Opublikuj w portalu publicznym</strong> jest włączona (domyślnie wyłączona, aby nic nie zostało opublikowane przez pomyłkę).</li>
            <li>Zwierzę jest dostępne do adopcji (zwierzęta adoptowane lub zmarłe znikają automatycznie).</li>
            <li>Jego schronisko nie zostało usunięte.</li>
        </ul>
        <p>Zwierzęta oznaczone jako <strong>Wyróżniony</strong> są pokazywane jako pierwsze, z odpowiednią plakietką. Pokazywane są tylko imię, numer referencyjny, zdjęcia, publiczny opis i dane opisowe (gatunek, rasa, rozmiar, płeć, wiek, rodzaj sierści, sterylizacja) &mdash; notatki wewnętrzne, notatki kliniczne, chip i kojec nigdy nie są publikowane.</p>
        <p>Zwierzęta mieszkające w domu tymczasowym mają odznakę <strong>W domu tymczasowym</strong>; nazwa i dane kontaktowe rodziny nigdy nie są publikowane.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Wskazówki do dobrych ogłoszeń</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Dodaj co najmniej jedno dobre zdjęcie i przyjazny publiczny opis &mdash; to adoptujący widzą najpierw.</li>
            <li>Aktualizuj profil schroniska (kontakt, opis, logo): pojawia się na publicznej stronie schroniska, w wynikach wyszukiwarek i w podglądach linków.</li>
            <li>Strony publiczne są przygotowane dla wyszukiwarek (Google i innych), a mapa witryny (sitemap) generuje się automatycznie; panel administracyjny nigdy nie jest indeksowany.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administracja</h2>
        <p>To menu widzą tylko administratorzy. Służy do utrzymywania schronisk i tabel słownikowych wspólnych dla wszystkich schronisk:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Schroniska</strong> &mdash; tworzenie, edycja i usuwanie schronisk. Oprócz nazwy i miejscowości schronisko ma nazwę skróconą, dane kontaktowe (e-mail, telefon, strona www), adres, region, opis i logo &mdash; używane w portalu publicznym, gdy jest włączony. Lista <strong>Gatunki</strong> w formularzu schroniska określa, które gatunki pojawiają się w menu Zwierzęta dla kierownika i pracowników tego schroniska. Adres e-mail jest wymagany. Gdy schronisko ma kierownika, może on sam zaktualizować wszystko oprócz nazwy i gatunków w <strong>Ustawienia &gt; Schronisko</strong>.</li>
            <li><strong>Regiony</strong> &mdash; regiony, do których należą schroniska, używane też jako filtr w portalu publicznym.</li>
            <li><strong>Gatunki</strong>, <strong>Rasy</strong>, <strong>Wielkości</strong> i <strong>Rodzaje sierści</strong> &mdash; opcje używane do opisu zwierząt.</li>
            <li><strong>Szczepionki</strong>, <strong>Zabiegi</strong> i <strong>Choroby</strong> &mdash; opcje używane w dokumentacji zdrowotnej zwierząt. Szczepionki i zabiegi określają gatunki, których dotyczą, oraz opcjonalnie częstotliwość w miesiącach, na podstawie której uzupełniana jest następna planowana data.</li>
            <li><strong>Zajęcia</strong> &mdash; zadania, w których mogą pomagać wolontariusze.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Ustawienia</h2>
        <p>Z menu użytkownika otwórz Ustawienia, aby zobaczyć swój profil, zmienić hasło i wybrać wygląd (jasny, ciemny lub systemowy) oraz język. Imię może zmienić tylko administrator lub kierownik, a adresu e-mail nie można zmienić. Język jest zapisywany na Twoim koncie i używany również w otrzymywanych e-mailach. Na stronach publicznych i stronie logowania każdy może zmienić język w menu u góry. Kierownicy widzą też kartę <strong>Schronisko</strong>, w której aktualizują dane kontaktowe, adres, region, opis i logo aktywnego schroniska; adres e-mail jest wymagany, a nazwę i gatunki może zmienić tylko administrator.</p>
        <p>Kierownicy mogą pobrać kopię wszystkich danych swojego schroniska w <strong>Ustawienia &gt; Eksport danych</strong>: plik ZIP z jednym plikiem CSV dla każdego obszaru (zwierzęta, szczepienia, zabiegi, diagnozy, adopcje, wnioski o adopcję, sponsoring i płatności, członkowie i płatności, wolontariusze i przestrzenie). Przydaje się do własnych kopii zapasowych lub przejścia na inny system. Pliki otwierają się bezpośrednio w Excelu. Zawierają dane osobowe członków, wolontariuszy, adoptujących i sponsorów, więc przechowuj je bezpiecznie i nie udostępniaj.</p>
    </section>
</div>
