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
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopcje</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopcje wirtualne</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Wolontariusze</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Członkowie</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Obiekty</a>
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
            <li><strong>Kierownik</strong> &mdash; prowadzi schronisko: może wszystko to, co pracownik, a dodatkowo zaprasza użytkowników tego schroniska i nimi zarządza.</li>
            <li><strong>Pracownik</strong> &mdash; zajmuje się codzienną pracą schroniska: zwierzętami, szczepieniami, adopcjami, adopcjami wirtualnymi, wolontariuszami i obiektami.</li>
            <li><strong>Podgląd</strong> &mdash; dostęp tylko do odczytu: może przeglądać zwierzęta, szczepienia i obiekty oraz drukować karty i listy zwierząt, ale nie może niczego tworzyć, edytować ani usuwać i nie widzi danych osobowych adoptujących, opiekunów wirtualnych, wolontariuszy ani członków.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Praca z kilkoma schroniskami</h3>
        <p>Użytkownik może należeć do więcej niż jednego schroniska, z inną rolą w każdym z nich. Aktywne schronisko zmienisz przełącznikiem schronisk; każda lista, licznik i formularz pokazuje wtedy tylko dane tego schroniska. Dane nigdy nie są współdzielone między schroniskami.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zalecana kolejność konfiguracji</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Administrator uzupełnia tabele słownikowe (regiony, gatunki, rasy, rozmiary, rodzaje sierści, szczepionki, choroby, zajęcia).</li>
            <li>Administrator tworzy schronisko, uzupełnia jego profil (kontakt, region, opis, logo) i wybiera gatunki, z którymi pracuje.</li>
            <li>Administrator zaprasza kierownika schroniska.</li>
            <li>Kierownik konfiguruje obiekty, skrzydła i kojce oraz zaprasza pracowników.</li>
            <li>Zespół zaczyna rejestrować zwierzęta.</li>
            <li>Jeśli portal publiczny jest włączony, zespół publikuje zwierzęta gotowe do adopcji.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Poruszanie się po aplikacji</h3>
        <p>Panel boczny pokazuje tylko to, z czego może korzystać Twoja rola. Kierownicy i pracownicy widzą menu Zwierzęta (po jednej pozycji dla każdego gatunku włączonego w schronisku oraz Adopcje wirtualne, Adopcje i Szczepienia), Wolontariusze, Członkowie i Obiekty; kierownicy widzą też Użytkownicy. Administratorzy widzą zamiast tego Użytkownicy i menu Administracja. Użytkownicy z rolą podglądu widzą te same menu co pracownicy, z wyjątkiem Adopcji wirtualnych, Adopcji, Wolontariuszy i Członków, a na stronach nie mają przycisków tworzenia, edycji ani usuwania. Ta dokumentacja jest zawsze dostępna na dole panelu bocznego.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pulpit</h2>
        <p>Pulpit daje przegląd aktywnego schroniska:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Liczniki zwierząt w schronisku, wolnych miejsc w kojcach i adopcji w tym roku. Kierownicy i pracownicy widzą też oczekujące wnioski adopcyjne, zaległe szczepienia i zaległe składki członkowskie, każdy z linkiem do listy.</li>
            <li>Najnowsze przyjęcia, adopcje, adopcje wirtualne i zgony.</li>
            <li>Zwierzęta bez znanej lokalizacji, aby można je było przypisać do kojca.</li>
            <li>Ostrzeżenia, gdy czegoś jeszcze brakuje, np. nie zdefiniowano kojców lub gatunki nie mają ras.</li>
        </ul>
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
        <p>Szukaj po imieniu, numerze, mikroczipie lub notatkach wewnętrznych i filtruj według statusu, gatunku lub lokalizacji (obiekt, skrzydło lub kojec). Filtr <em>brakujące dane</em> wyszukuje zwierzęta bez wieku, bez zdjęcia, bez daty przyjęcia lub bez lokalizacji, co pomaga utrzymać kompletne karty.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Drukowanie</h3>
        <p>Możesz wydrukować kartę pojedynczego zwierzęcia z jego strony albo listę zwierząt; wydrukowana lista używa tych samych filtrów, które są aktywne na ekranie.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Udostępnianie w mediach społecznościowych</h3>
        <p>Zwierzęta, które można adoptować i są dostępne, mają u góry swojej strony przycisk udostępniania. Przygotowuje on tekst z danymi zwierzęcia i kontaktami schroniska, gotowy do skopiowania, i pozwala pobrać główne zdjęcie, aby opublikować je na Facebooku, Instagramie lub WhatsAppie. Gdy zwierzę jest opublikowane na portalu publicznym, tekst zawiera link do zwierzęcia, a ponadto możesz udostępnić je bezpośrednio na Facebooku lub WhatsAppie.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Zdrowie</h3>
        <p>Rejestruj przy każdym zwierzęciu choroby (z datą diagnozy, statusem i notatkami o leczeniu), szczepienia i notatki kliniczne. Wielkości są dostępne tylko dla gatunków, dla których je skonfigurowano.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Szczepienia</h2>
        <p>Każde szczepienie zapisuje szczepionkę, datę podania lub planowaną datę, numer serii, lekarza weterynarii i notatki. Strona Szczepienia wyświetla je dla wszystkich zwierząt schroniska.</p>
        <p>Codziennie użytkownicy, którzy mają włączone powiadomienia o szczepieniach dla danego schroniska, otrzymują e-mail z listą szczepień tego schroniska zaplanowanych na najbliższe siedem dni, które nie zostały jeszcze podane. O każdym szczepieniu powiadamia się tylko raz. Ikona dzwonka na liście użytkowników pokazuje, kto otrzymuje te wiadomości.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adopcje</h2>
        <p>Adopcja zapisuje dane kontaktowe adoptującego, datę adopcji, opłatę, notatki i status wniosku (Oczekujący, Zatwierdzony lub Odrzucony). Rozpocznij ją ze strony zwierzęcia.</p>
        <p>Strona Adopcje (w menu Zwierzęta w panelu bocznym) zawiera wszystkie adopcje schroniska; szukaj po imieniu i nazwisku, telefonie, e-mailu lub notatkach adoptującego albo po imieniu lub numerze referencyjnym zwierzęcia.</p>
        <p>Jeśli adoptowane zwierzę wraca do schroniska, wpisz w adopcji <strong>datę zwrotu</strong>: zwierzę znów staje się dostępne, a adopcja pozostaje w jego historii.</p>
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
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Użytkownicy</h2>
        <p>Kierownicy i administratorzy zapraszają nowych użytkowników e-mailem; zaproszona osoba otrzymuje link do ustawienia hasła. Dla każdego schroniska, do którego należy użytkownik, wybierasz rolę (kierownik, pracownik lub podgląd) oraz to, czy otrzymuje powiadomienia o szczepieniach.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Kierownik może dodawać użytkowników tylko do schronisk, którymi zarządza.</li>
            <li>Administrator może dodawać użytkowników do dowolnego schroniska i tworzyć innych administratorów.</li>
        </ul>
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
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Co jest widoczne publicznie</h3>
        <p>Zwierzę pojawia się w portalu tylko wtedy, gdy spełnione są <strong>wszystkie</strong> te warunki:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Opcja <strong>Opublikuj w portalu publicznym</strong> jest włączona (domyślnie wyłączona, aby nic nie zostało opublikowane przez pomyłkę).</li>
            <li>Zwierzę jest dostępne do adopcji (zwierzęta adoptowane lub zmarłe znikają automatycznie).</li>
            <li>Jego schronisko nie zostało usunięte.</li>
        </ul>
        <p>Zwierzęta oznaczone jako <strong>Wyróżniony</strong> są pokazywane jako pierwsze, z odpowiednią plakietką. Pokazywane są tylko imię, numer referencyjny, zdjęcia, publiczny opis i dane opisowe (gatunek, rasa, rozmiar, płeć, wiek, rodzaj sierści, sterylizacja) &mdash; notatki wewnętrzne, notatki kliniczne, chip i kojec nigdy nie są publikowane.</p>
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
            <li><strong>Schroniska</strong> &mdash; tworzenie, edycja i usuwanie schronisk. Oprócz nazwy i miejscowości schronisko ma nazwę skróconą, dane kontaktowe (e-mail, telefon, strona www), adres, region, opis i logo &mdash; używane w portalu publicznym, gdy jest włączony. Lista <strong>Gatunki</strong> w formularzu schroniska określa, które gatunki pojawiają się w menu Zwierzęta dla kierownika i pracowników tego schroniska.</li>
            <li><strong>Regiony</strong> &mdash; regiony, do których należą schroniska, używane też jako filtr w portalu publicznym.</li>
            <li><strong>Gatunki</strong>, <strong>Rasy</strong>, <strong>Wielkości</strong> i <strong>Rodzaje sierści</strong> &mdash; opcje używane do opisu zwierząt.</li>
            <li><strong>Szczepionki</strong> i <strong>Choroby</strong> &mdash; opcje używane w dokumentacji zdrowotnej zwierząt.</li>
            <li><strong>Zajęcia</strong> &mdash; zadania, w których mogą pomagać wolontariusze.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Ustawienia</h2>
        <p>Z menu użytkownika otwórz Ustawienia, aby zobaczyć swój profil, zmienić hasło i wybrać wygląd (jasny, ciemny lub systemowy). Imię może zmienić tylko administrator lub kierownik, a adresu e-mail nie można zmienić.</p>
    </section>
</div>
