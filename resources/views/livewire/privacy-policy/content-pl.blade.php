<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Polityka prywatności</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Ostatnia aktualizacja: 25 września 2026</p>
        <p>{{ config('app.name') }} to platforma skupiająca wiele schronisk dla zwierząt. Poważnie traktujemy ochronę Twoich danych osobowych i przetwarzamy je zgodnie z Ogólnym rozporządzeniem o ochronie danych (RODO) oraz ustawą z dnia 10 maja 2018 r. o ochronie danych osobowych. Niniejsza polityka wyjaśnia, jakie dane przetwarzamy, dlaczego, jak długo i jakie przysługują Ci prawa.</p>
    </header>

    <section id="responsible">
        <h2>1. Kto odpowiada za Twoje dane</h2>
        <p>Każde schronisko korzystające z platformy jest <strong>administratorem danych</strong>, które zbiera w ramach swojej działalności (np. danych adoptujących, opiekunów wirtualnych i wolontariuszy). {{ config('app.name') }} udostępnia platformę technologiczną i działa na rzecz tych schronisk jako <strong>podmiot przetwarzający</strong>, przetwarzając dane wyłącznie na ich polecenie.</p>
        <p>{{ config('app.name') }} jest administratorem danych dotyczących kont użytkowników platformy oraz osób odwiedzających stronę publiczną.</p>
    </section>

    <section id="data">
        <h2>2. Jakie dane przetwarzamy</h2>

        <h3>Osoby odwiedzające stronę publiczną</h3>
        <p>Możesz przeglądać zwierzęta do adopcji bez zakładania konta. Przetwarzamy wyłącznie dane techniczne niezbędne do działania serwisu: adres IP, rodzaj przeglądarki i urządzenia oraz plik cookie sesji. Jeśli wyślesz wniosek adopcyjny, przetwarzamy również dane opisane poniżej.</p>

        <h3>Użytkownicy platformy (zespoły schronisk)</h3>
        <ul>
            <li>Imię i nazwisko oraz adres e-mail;</li>
            <li>Hasło (przechowywane wyłącznie w postaci skrótu, nigdy jawnym tekstem);</li>
            <li>Schronisko lub schroniska, do których należysz, i Twoja rola (administrator, kierownik lub pracownik);</li>
            <li>Data ostatniego logowania i dane sesji (adres IP i przeglądarka).</li>
        </ul>

        <h3>Osoby składające wniosek adopcyjny</h3>
        <ul>
            <li>Imię i nazwisko, e-mail, telefon, kod pocztowy i miejscowość;</li>
            <li>Zwierzę, którego dotyczy wniosek, Twoje odpowiedzi dotyczące domu (rodzaj mieszkania, ogród, dzieci i inne zwierzęta) oraz Twoja motywacja;</li>
            <li>Data wyrażenia zgody i adres IP, z którego wysłano wniosek, przechowywany wyłącznie w celu ochrony formularza przed nadużyciami.</li>
        </ul>
        <p>Wniosek trafia wyłącznie do schroniska, które opiekuje się danym zwierzęciem i wykorzystuje go do oceny adopcji. Jeśli zostanie zatwierdzony, Twoje dane kontaktowe stają się częścią dokumentacji adopcji.</p>

        <h3>Adoptujący</h3>
        <ul>
            <li>Imię i nazwisko, e-mail, telefon, adres, kod pocztowy i miejscowość;</li>
            <li>Adoptowane zwierzę, data adopcji i ewentualna data zwrotu;</li>
            <li>Opłata adopcyjna i notatki zapisane przez schronisko.</li>
        </ul>

        <h3>Opiekunowie wirtualni</h3>
        <ul>
            <li>Imię i nazwisko, e-mail, telefon, adres, kod pocztowy i miejscowość;</li>
            <li>Wspierane zwierzę i historia wpłat (daty, okresy i kwoty);</li>
            <li>Preferencje komunikacji (wiadomości o zwierzęciu i/lub newsletter).</li>
        </ul>

        <h3>Wolontariusze</h3>
        <ul>
            <li>Imię i nazwisko, płeć, data urodzenia i zdjęcie;</li>
            <li>Numer dokumentu tożsamości i numer identyfikacji podatkowej (NIP);</li>
            <li>Dane kontaktowe, adres, zawód i środek transportu;</li>
            <li>Dostępność, daty rozpoczęcia i zakończenia współpracy oraz oceny obecności i pracy;</li>
            <li>Preferencja dotycząca newslettera.</li>
        </ul>

        <p>Strona publiczna pokazuje wyłącznie informacje o zwierzętach (zdjęcia, cechy i opis) oraz dane kontaktowe schroniska. <strong>Nigdy</strong> nie publikuje danych adoptujących, opiekunów wirtualnych, wolontariuszy ani użytkowników.</p>
    </section>

    <section id="purposes">
        <h2>3. W jakim celu i na jakiej podstawie prawnej wykorzystujemy dane</h2>
        <ul>
            <li><strong>Obsługa adopcji, adopcji wirtualnych i wolontariatu</strong> &mdash; wykonanie umowy z Tobą lub działania przed jej zawarciem podejmowane na Twoje żądanie (art. 6 ust. 1 lit. b RODO);</li>
            <li><strong>Ocena wniosków adopcyjnych</strong> &mdash; działania przed zawarciem umowy podejmowane na Twoje żądanie (art. 6 ust. 1 lit. b RODO), na podstawie zgody wyrażonej w formularzu; adres IP jest przechowywany w celu ochrony formularza przed nadużyciami &mdash; prawnie uzasadniony interes (art. 6 ust. 1 lit. f RODO);</li>
            <li><strong>Monitorowanie dobrostanu zwierząt po adopcji</strong> &mdash; prawnie uzasadniony interes schroniska w ochronie zwierząt (art. 6 ust. 1 lit. f);</li>
            <li><strong>Wypełnianie obowiązków prawnych</strong>, takich jak przepisy podatkowe oraz rejestracja i identyfikacja zwierząt domowych (art. 6 ust. 1 lit. c);</li>
            <li><strong>Wysyłanie newslettera i wiadomości o wspieranym zwierzęciu</strong> &mdash; Twoja zgoda, którą możesz w każdej chwili wycofać (art. 6 ust. 1 lit. a);</li>
            <li><strong>Zapewnienie bezpieczeństwa i działania platformy</strong>, w tym kontrola dostępu i logi techniczne &mdash; prawnie uzasadniony interes (art. 6 ust. 1 lit. f).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Pliki cookie</h2>
        <p>Używamy wyłącznie plików cookie i pamięci lokalnej, które są <strong>niezbędne</strong> do działania serwisu lub funkcji, o którą wyraźnie poprosiłeś, dlatego zgodnie z art. 399 ustawy z dnia 12 lipca 2024 r. – Prawo komunikacji elektronicznej Twoja zgoda nie jest wymagana. Nie używamy reklamowych, analitycznych ani zewnętrznych plików cookie, a wszystkie czcionki i zasoby są dostarczane z naszych własnych serwerów.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Nazwa</th>
                        <th class="px-4 py-3">Rodzaj</th>
                        <th class="px-4 py-3">Cel</th>
                        <th class="px-4 py-3">Czas przechowywania</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Utrzymuje Twoją sesję podczas korzystania z serwisu.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} min bezczynności</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Chroni formularze przed sfałszowanymi żądaniami z innych stron.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} min bezczynności</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Utrzymuje zalogowanie, tylko jeśli podczas logowania zaznaczysz &bdquo;Zapamiętaj mnie&rdquo;.</td>
                        <td class="px-4 py-3">400 dni lub do wylogowania</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Pamięć lokalna</td>
                        <td class="px-4 py-3">Zapamiętuje wybór jasnego lub ciemnego motywu, tylko jeśli wybierzesz go w ustawieniach.</td>
                        <td class="px-4 py-3">Do usunięcia w przeglądarce</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Pliki cookie możesz usunąć lub zablokować w ustawieniach przeglądarki; jeśli zablokujesz pliki cookie sesji, nie zalogujesz się do strefy schronisk.</p>
    </section>

    <section id="sharing">
        <h2>5. Komu udostępniamy dane</h2>
        <p>Nie sprzedajemy Twoich danych ani nie przekazujemy ich w celach komercyjnych. Dane każdego schroniska są dostępne wyłącznie dla zespołu tego schroniska i administratorów platformy. Mogą być również przetwarzane przez dostawców usług, którzy pomagają nam prowadzić platformę (hosting i wysyłka e-maili), zawsze z odpowiednimi zabezpieczeniami umownymi, lub ujawniane organom, gdy wymaga tego prawo.</p>
    </section>

    <section id="retention">
        <h2>6. Jak długo przechowujemy dane</h2>
        <ul>
            <li><strong>Konta użytkowników:</strong> dopóki konto jest aktywne;</li>
            <li><strong>Wnioski adopcyjne:</strong> usuwane automatycznie 6 miesięcy po ostatniej zmianie;</li>
            <li><strong>Adopcje i adopcje wirtualne:</strong> tak długo, jak jest to potrzebne do monitorowania zwierzęcia i wypełnienia obowiązujących obowiązków prawnych;</li>
            <li><strong>Wolontariusze:</strong> w trakcie współpracy, a następnie tylko przez okres wymagany przepisami;</li>
            <li><strong>Sesje:</strong> wygasają automatycznie po okresie bezczynności.</li>
        </ul>
        <p>Usunięte rekordy mogą być przechowywane przez ograniczony czas, poza normalnym dostępem, do celów audytu i odzyskiwania po błędach.</p>
    </section>

    <section id="rights">
        <h2>7. Twoje prawa</h2>
        <p>W każdej chwili możesz zażądać <strong>dostępu</strong> do swoich danych, ich <strong>sprostowania</strong> lub <strong>usunięcia</strong>, <strong>ograniczenia</strong> przetwarzania lub <strong>przeniesienia</strong> danych, <strong>wnieść sprzeciw</strong> wobec przetwarzania opartego na prawnie uzasadnionym interesie oraz <strong>wycofać każdą udzieloną zgodę</strong>, bez wpływu na przetwarzanie dokonane wcześniej.</p>
        <p>Aby skorzystać z tych praw, skontaktuj się bezpośrednio ze schroniskiem, z którym miałeś kontakt (jego dane kontaktowe znajdują się na stronie każdego zwierzęcia)@if ($contactEmail), lub napisz do nas na adres <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Odpowiemy najpóźniej w ciągu miesiąca.</p>
        <p>Masz również prawo wnieść skargę do Prezesa Urzędu Ochrony Danych Osobowych (UODO) na stronie <a href="https://uodo.gov.pl" target="_blank" rel="noopener">uodo.gov.pl</a>.</p>
    </section>

    <section id="security">
        <h2>8. Bezpieczeństwo</h2>
        <p>Hasła są przechowywane w postaci skrótu, dostęp jest możliwy wyłącznie na zaproszenie, a każdy użytkownik widzi tylko dane schroniska, do którego należy. Stosujemy środki techniczne i organizacyjne chroniące dane przed nieuprawnionym dostępem, utratą lub zmianą.</p>
    </section>

    <section id="changes">
        <h2>9. Zmiany niniejszej polityki</h2>
        <p>Możemy aktualizować tę politykę, aby odzwierciedlić zmiany w platformie lub w przepisach. Data ostatniej aktualizacji jest zawsze podana na górze tej strony.</p>
    </section>
</div>
