<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Anleitung zur Anwendung</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Ein Leitfaden zu den wichtigsten Bereichen von {{ config('app.name') }} und ihrer Nutzung im Alltag.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Erste Schritte</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Übersicht</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Tiere</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Impfungen</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vermittlungen</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Patenschaften</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Ehrenamtliche</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Einrichtungen</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Benutzer</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Öffentliches Portal</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Verwaltung</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Einstellungen</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Erste Schritte</h2>
        <p>Beim allerersten Start fordert die Anwendung Sie auf, das erste Administratorkonto anzulegen. Danach werden neue Konten nur noch per Einladung erstellt.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rollen</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrator</strong> &mdash; verwaltet die gesamte Plattform: Tierheime, die gemeinsamen Stammdatentabellen und Benutzerkonten. Administratoren verwalten keine Tiere oder Einrichtungen.</li>
            <li><strong>Tierheimleitung</strong> &mdash; führt ein Tierheim: alles, was ein Mitarbeiter tun kann, plus das Einladen und Verwalten der Benutzer dieses Tierheims.</li>
            <li><strong>Mitarbeiter</strong> &mdash; erledigt die tägliche Arbeit des Tierheims: Tiere, Impfungen, Vermittlungen, Patenschaften, Ehrenamtliche und Einrichtungen.</li>
            <li><strong>Leser</strong> &mdash; Nur-Lese-Zugriff auf das Tierheim: kann Tiere, Impfungen, Ehrenamtliche und Einrichtungen sehen und Tierblätter und -listen drucken, aber nichts anlegen, bearbeiten oder löschen, und sieht keine personenbezogenen Daten von Adoptanten oder Paten.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Arbeiten mit mehreren Tierheimen</h3>
        <p>Ein Benutzer kann mehreren Tierheimen angehören, mit jeweils unterschiedlicher Rolle. Wechseln Sie das aktive Tierheim über die Tierheimauswahl; jede Liste, jeder Zähler und jedes Formular zeigt dann nur die Daten dieses Tierheims. Daten werden niemals zwischen Tierheimen geteilt.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Empfohlene Reihenfolge der Einrichtung</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Ein Administrator füllt die Stammdaten aus (Regionen, Tierarten, Rassen, Größen, Felltypen, Impfstoffe, Krankheiten, Tätigkeiten).</li>
            <li>Der Administrator legt das Tierheim an, vervollständigt sein Profil (Kontakt, Region, Beschreibung, Logo) und wählt die Tierarten, mit denen es arbeitet.</li>
            <li>Der Administrator lädt die Tierheimleitung ein.</li>
            <li>Die Tierheimleitung richtet Einrichtungen, Trakte und Zwinger ein und lädt die Mitarbeiter ein.</li>
            <li>Das Team beginnt, Tiere zu erfassen.</li>
            <li>Ist das öffentliche Portal aktiviert, veröffentlicht das Team die Tiere, die zur Vermittlung bereit sind.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orientierung in der Anwendung</h3>
        <p>Die Seitenleiste zeigt nur, was Ihre Rolle nutzen darf. Manager und Mitarbeitende sehen das Menü Tiere (ein Eintrag pro für das Tierheim aktivierter Tierart, dazu Patenschaften, Vermittlungen und Impfungen), Ehrenamtliche und Einrichtungen; Manager sehen zusätzlich Benutzer. Administratoren sehen stattdessen Benutzer und das Menü Verwaltung. Leser sehen dieselben Menüs wie Mitarbeitende, außer Patenschaften und Vermittlungen, und die Seiten zeigen ihnen keine Schaltflächen zum Anlegen, Bearbeiten oder Löschen. Diese Dokumentation ist immer unten in der Seitenleiste erreichbar.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Übersicht</h2>
        <p>Die Übersicht zeigt Ihnen den Stand des aktiven Tierheims:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Zähler für aktuelle Tiere, Vermittlungen, freie Zwingerkapazität und Mitarbeiter.</li>
            <li>Die neuesten Aufnahmen, Vermittlungen, Patenschaften und Todesfälle.</li>
            <li>Tiere ohne bekannten Standort, damit sie einem Zwinger zugewiesen werden können.</li>
            <li>Hinweise, wenn noch etwas fehlt, etwa wenn keine Zwinger definiert sind oder Tierarten keine Rassen haben.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Tiere</h2>
        <p>Das Menü Tiere listet die Tiere des Tierheims nach Tierart auf. Jeder Eintrag umfasst die Identifikation (Referenz, Name, Mikrochip), die äußere Beschreibung (Rasse, Farben, Felltyp, Größe, Geschlecht, Kastration), Daten (Geburt, Aufnahme, Abgang, Tod), Fotos, eine öffentliche Beschreibung, interne Notizen und klinische Notizen. Es erscheinen nur die Tierarten, die der Administrator für Ihr Tierheim aktiviert hat.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Status</h3>
        <p>Der Status eines Tieres wird automatisch ermittelt, Sie setzen ihn also nie von Hand:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Verstorben</strong> &mdash; ein Sterbedatum ist eingetragen.</li>
            <li><strong>Vermittelt</strong> &mdash; das Tier hat eine Vermittlung ohne Rückgabedatum.</li>
            <li><strong>Verfügbar</strong> / <strong>Nicht verfügbar</strong> &mdash; andernfalls, je nachdem, ob das Tier als vermittelbar markiert ist.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Optionen und Unterbringung</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Zur Vermittlung verfügbar</strong> &mdash; das Tier kann vermittelt werden; davon hängt ab, ob sein Status verfügbar oder nicht verfügbar ist.</li>
            <li><strong>Für Patenschaft verfügbar</strong> &mdash; das Tier kann Patenschaften erhalten. Die Aktion für eine Patenschaft wird nur bei Tieren mit dieser Option angeboten und bleibt auch nach der Vermittlung verfügbar.</li>
            <li><strong>Zwinger</strong> &mdash; die Zwingerliste ist nach Einrichtung und Trakt gruppiert und zeigt die freien Plätze jedes Zwingers, mit einer grünen, gelben oder roten Markierung, je voller er wird.</li>
        </ul>
        <p>Ist das öffentliche Portal aktiviert, erscheinen zwei weitere Optionen: <strong>Im öffentlichen Portal veröffentlichen</strong> und <strong>Hervorgehoben</strong>. Siehe <a href="#public-portal" class="underline underline-offset-2">Öffentliches Portal</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Suchen und Filtern</h3>
        <p>Suchen Sie nach Name, Referenz, Mikrochip oder internen Notizen und filtern Sie nach Status, Tierart oder Standort (Einrichtung, Trakt oder Zwinger). Der Filter <em>fehlende Daten</em> findet Tiere ohne Alter, ohne Foto, ohne Aufnahmedatum oder ohne Standort und hilft so, die Einträge vollständig zu halten.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Drucken</h3>
        <p>Sie können das Datenblatt eines einzelnen Tieres von seiner Seite aus drucken oder die Tierliste drucken; die gedruckte Liste verwendet dieselben Filter, die auf dem Bildschirm aktiv sind.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Gesundheit</h3>
        <p>Erfassen Sie bei jedem Tier Krankheiten (mit Diagnosedatum, Status und Behandlungsnotizen), Impfungen und klinische Notizen. Größen werden nur für Tierarten angeboten, für die Größen eingerichtet sind.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Impfungen</h2>
        <p>Jede Impfung erfasst den Impfstoff, das Datum der Verabreichung bzw. der Fälligkeit, die Chargennummer, den Tierarzt und Notizen. Die Seite Impfungen listet sie für alle Tiere des Tierheims auf.</p>
        <p>Benutzer, die Impfbenachrichtigungen für ein Tierheim aktiviert haben, erhalten täglich eine E-Mail mit den Impfungen dieses Tierheims, die in den nächsten sieben Tagen fällig und noch nicht verabreicht sind. Jede Impfung wird nur einmal gemeldet. Ein Glockensymbol in der Benutzerliste zeigt, wer diese E-Mails erhält.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vermittlungen</h2>
        <p>Eine Vermittlung erfasst die Kontaktdaten des Adoptierenden, das Vermittlungsdatum, die Schutzgebühr, Notizen und den Antragsstatus (Ausstehend, Genehmigt oder Abgelehnt). Starten Sie sie von der Seite des Tieres aus.</p>
        <p>Die Seite Vermittlungen (unter Tiere in der Seitenleiste) listet alle Vermittlungen des Tierheims; suchen Sie nach Name, Telefon, E-Mail oder Notizen der adoptierenden Person oder nach Name oder Referenz des Tieres.</p>
        <p>Kommt ein vermitteltes Tier ins Tierheim zurück, tragen Sie in der Vermittlung das <strong>Rückgabedatum</strong> ein: Das Tier ist dann wieder verfügbar, und die Vermittlung bleibt in seiner Historie erhalten.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Patenschaften</h2>
        <p>Paten unterstützen die Versorgung eines Tieres, ohne es zu adoptieren. Eine Patenschaft speichert die Kontaktdaten des Paten und ob er Neuigkeiten über das Tier oder den Newsletter erhalten möchte.</p>
        <p>Patenschaften können nur für Tiere angelegt werden, die als <strong>Für Patenschaft verfügbar</strong> markiert sind. Die Seite Patenschaften (unter Tiere in der Seitenleiste) listet alle auf, mit derselben Suche wie bei Vermittlungen: Name, Telefon, E-Mail oder Notizen der Paten oder Name bzw. Referenz des Tieres.</p>
        <p>Jede Patenschaft hat eine Liste von Zahlungen. Eine Zahlung erfasst den abgedeckten Zeitraum (Start- und Enddatum), das Zahlungsdatum und den Betrag, sodass sowohl einmalige als auch regelmäßige Beiträge möglich sind.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Ehrenamtliche</h2>
        <p>Führen Sie Buch über die Menschen, die Ihrem Tierheim helfen, unabhängig von den Benutzerkonten. Für jeden Ehrenamtlichen können Sie persönliche Daten und Kontaktdaten, ein Foto, Beginn- und Enddatum, Verkehrsmittel und Newsletter-Wunsch speichern, außerdem:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Die Tätigkeiten, bei denen er hilft, und die Tierarten, mit denen er bevorzugt arbeitet.</li>
            <li>Seine Verfügbarkeit pro Wochentag (vormittags und/oder nachmittags, gelegentlich, alle zwei Wochen oder wöchentlich).</li>
            <li>Bewertungen von Anwesenheit und Leistung.</li>
        </ul>
        <p>Die Liste der Ehrenamtlichen lässt sich nach Name, Telefon, E-Mail, Steuernummer oder Notizen durchsuchen und nach bevorzugter Tierart, Verfügbarkeitstag und Tätigkeit filtern &mdash; praktisch, um zu sehen, wer an einem bestimmten Tag helfen kann.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Einrichtungen</h2>
        <p>Ein Tierheim ist in drei Ebenen gegliedert: <strong>Einrichtungen</strong> (physische Standorte mit Adresse) enthalten <strong>Trakte</strong>, und Trakte enthalten <strong>Zwinger</strong>. Jeder Zwinger hat einen Code und eine Kapazität.</p>
        <p>Die Gesamtkapazität der Zwinger bestimmt, wie viele Tiere das Tierheim aufnehmen kann, und Tiere werden Zwingern zugewiesen. Richten Sie mindestens einen Zwinger ein, bevor Sie Tiere erfassen, damit ihnen ein Standort zugewiesen werden kann.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Benutzer</h2>
        <p>Tierheimleitungen und Administratoren laden neue Benutzer per E-Mail ein; die eingeladene Person erhält einen Link, um ihr Passwort festzulegen. Für jede Tierheimzugehörigkeit wählen Sie die Rolle (Tierheimleitung, Mitarbeiter oder Leser) und ob der Benutzer Impfbenachrichtigungen erhält.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Eine Tierheimleitung kann Benutzer nur zu den von ihr geleiteten Tierheimen hinzufügen.</li>
            <li>Ein Administrator kann Benutzer zu jedem Tierheim hinzufügen und weitere Administratoren anlegen.</li>
        </ul>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Öffentliches Portal</h2>
        <p>Eine Installation kann optional eine öffentliche Website neben dem Backoffice betreiben. Sie wird von der Person aktiviert, die den Server betreibt; ist sie deaktiviert, leitet die Startseite Besucher zur Anmeldeseite weiter und die folgenden Optionen sind ausgeblendet.</p>
        <p>Ist sie aktiviert, kann jede Person (ohne Anmeldung):</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Die vermittlungsbereiten Tiere aller Tierheime durchsuchen, gefiltert nach Tierart, Geschlecht, Größe, Rasse und Region.</li>
            <li>Die Karte eines Tieres öffnen, um Fotos, öffentliche Beschreibung und das betreuende Tierheim zu sehen.</li>
            <li>Die Liste der Partner-Tierheime ansehen, jedes mit einer eigenen Seite mit Kontakt, Beschreibung, Logo und Tieren.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Was öffentlich angezeigt wird</h3>
        <p>Ein Tier erscheint nur dann im Portal, wenn <strong>alle</strong> diese Bedingungen erfüllt sind:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Im öffentlichen Portal veröffentlichen</strong> ist aktiviert (standardmäßig aus, damit nichts versehentlich veröffentlicht wird).</li>
            <li>Das Tier ist zur Vermittlung verfügbar (vermittelte oder verstorbene Tiere verschwinden automatisch).</li>
            <li>Sein Tierheim wurde nicht entfernt.</li>
        </ul>
        <p>Als <strong>Hervorgehoben</strong> markierte Tiere werden zuerst angezeigt, mit einem entsprechenden Abzeichen. Angezeigt werden nur Name, Referenz, Fotos, öffentliche Beschreibung und beschreibende Angaben (Tierart, Rasse, Größe, Geschlecht, Alter, Felltyp, kastriert) &mdash; interne Notizen, klinische Notizen, Mikrochip und Zwinger werden nie veröffentlicht.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Tipps für gute Inserate</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Fügen Sie mindestens ein gutes Foto und eine freundliche öffentliche Beschreibung hinzu &mdash; das sehen Interessierte zuerst.</li>
            <li>Halten Sie das Tierheimprofil (Kontakt, Beschreibung, Logo) aktuell: Es erscheint auf der öffentlichen Seite des Tierheims, in Suchergebnissen und in Link-Vorschauen.</li>
            <li>Die öffentlichen Seiten sind für Suchmaschinen (Google und andere) vorbereitet und eine Sitemap wird automatisch erzeugt; das Backoffice wird nie indexiert.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Verwaltung</h2>
        <p>Nur Administratoren sehen dieses Menü. Hier werden die Tierheime und die von allen Tierheimen gemeinsam genutzten Stammdatentabellen gepflegt:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Tierheime</strong> &mdash; Tierheime anlegen, bearbeiten und entfernen. Neben Name und Ort hat ein Tierheim einen Kurznamen, Kontaktdaten (E-Mail, Telefon, Website), Adresse, Region, eine Beschreibung und ein Logo &mdash; genutzt im öffentlichen Portal, wenn es aktiviert ist. Die Liste <strong>Tierarten</strong> im Tierheimformular legt fest, welche Tierarten im Menü Tiere für Manager und Mitarbeitende dieses Tierheims erscheinen.</li>
            <li><strong>Regionen</strong> &mdash; die Regionen, zu denen die Tierheime gehören; auch als Filter im öffentlichen Portal verwendet.</li>
            <li><strong>Tierarten</strong>, <strong>Rassen</strong>, <strong>Größen</strong> und <strong>Felltypen</strong> &mdash; die Optionen zur Beschreibung der Tiere.</li>
            <li><strong>Impfstoffe</strong> und <strong>Krankheiten</strong> &mdash; die Optionen für die Gesundheitsakten der Tiere.</li>
            <li><strong>Tätigkeiten</strong> &mdash; die Aufgaben, bei denen Ehrenamtliche helfen können.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Einstellungen</h2>
        <p>Öffnen Sie über das Benutzermenü die Einstellungen, um Ihr Profil anzusehen, Ihr Passwort zu ändern und das Erscheinungsbild zu wählen (hell, dunkel oder System). Ihr Name kann nur von einem Administrator oder der Tierheimleitung geändert werden, Ihre E-Mail-Adresse kann nicht geändert werden.</p>
    </section>
</div>
