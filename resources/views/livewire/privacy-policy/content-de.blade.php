<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Datenschutzerklärung</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Zuletzt aktualisiert: 23. September 2026</p>
        <p>{{ config('app.name') }} ist eine Plattform, die mehrere Tierheime zusammenbringt. Wir nehmen den Schutz Ihrer personenbezogenen Daten ernst und verarbeiten sie gemäß der Datenschutz-Grundverordnung (DSGVO) und dem geltenden portugiesischen Recht. Diese Erklärung beschreibt, welche Daten wir verarbeiten, warum, wie lange und welche Rechte Sie haben.</p>
    </header>

    <section id="responsible">
        <h2>1. Wer für Ihre Daten verantwortlich ist</h2>
        <p>Jedes Tierheim, das die Plattform nutzt, ist <strong>Verantwortlicher</strong> für die Daten, die es im Rahmen seiner Tätigkeit erhebt (zum Beispiel Daten von Adoptierenden, Paten und Ehrenamtlichen). {{ config('app.name') }} stellt die technische Plattform bereit und handelt für diese Tierheime als <strong>Auftragsverarbeiter</strong>, der die Daten ausschließlich nach deren Weisung verarbeitet.</p>
        <p>{{ config('app.name') }} ist Verantwortlicher für die Daten der Benutzerkonten der Plattform und der Besucher der öffentlichen Seite.</p>
    </section>

    <section id="data">
        <h2>2. Welche Daten wir verarbeiten</h2>

        <h3>Besucher der öffentlichen Seite</h3>
        <p>Sie können die Tiere, die ein Zuhause suchen, ansehen, ohne ein Konto anzulegen oder ein Formular auszufüllen. Wir verarbeiten nur die technischen Daten, die für den Betrieb der Website erforderlich sind: IP-Adresse, Browser- und Gerätetyp sowie ein Sitzungscookie.</p>

        <h3>Benutzer der Plattform (Tierheimteams)</h3>
        <ul>
            <li>Name und E-Mail-Adresse;</li>
            <li>Passwort (nur als Hash gespeichert, niemals im Klartext);</li>
            <li>Das bzw. die Tierheime, denen Sie angehören, und Ihre Rolle (Administrator, Tierheimleitung oder Mitarbeiter);</li>
            <li>Datum der letzten Anmeldung und Sitzungsdaten (IP-Adresse und Browser).</li>
        </ul>

        <h3>Adoptierende</h3>
        <ul>
            <li>Name, E-Mail, Telefon, Adresse, Postleitzahl und Ort;</li>
            <li>Das adoptierte Tier, das Vermittlungsdatum und ggf. das Rückgabedatum;</li>
            <li>Schutzgebühr und vom Tierheim erfasste Notizen.</li>
        </ul>

        <h3>Paten</h3>
        <ul>
            <li>Name, E-Mail, Telefon, Adresse, Postleitzahl und Ort;</li>
            <li>Das Patentier und der Zahlungsverlauf (Daten, Zeiträume und Beträge);</li>
            <li>Kommunikationswünsche (Neuigkeiten über das Tier und/oder Newsletter).</li>
        </ul>

        <h3>Ehrenamtliche</h3>
        <ul>
            <li>Name, Geschlecht, Geburtsdatum und Foto;</li>
            <li>Ausweisnummer und Steuer-Identifikationsnummer (NIF);</li>
            <li>Kontaktdaten, Adresse, Beruf und Verkehrsmittel;</li>
            <li>Verfügbarkeit, Beginn und Ende der Mitarbeit sowie Bewertungen von Anwesenheit und Leistung;</li>
            <li>Newsletter-Wunsch.</li>
        </ul>

        <p>Die öffentliche Seite zeigt nur Informationen über die Tiere (Fotos, Merkmale und Beschreibung) und die Kontaktdaten des Tierheims. Sie veröffentlicht <strong>niemals</strong> Daten von Adoptierenden, Paten, Ehrenamtlichen oder Benutzern.</p>
    </section>

    <section id="purposes">
        <h2>3. Wofür wir die Daten verwenden und auf welcher Rechtsgrundlage</h2>
        <ul>
            <li><strong>Verwaltung von Vermittlungen, Patenschaften und Ehrenamt</strong> &mdash; Erfüllung der Vereinbarung mit Ihnen oder vorvertragliche Maßnahmen auf Ihre Anfrage (Art. 6 Abs. 1 lit. b DSGVO);</li>
            <li><strong>Nachbetreuung des Wohlergehens der Tiere nach der Adoption</strong> &mdash; berechtigtes Interesse des Tierheims am Tierschutz (Art. 6 Abs. 1 lit. f);</li>
            <li><strong>Erfüllung rechtlicher Pflichten</strong>, etwa steuerlicher Vorschriften und der Registrierung und Kennzeichnung von Heimtieren (Art. 6 Abs. 1 lit. c);</li>
            <li><strong>Versand von Newslettern und Neuigkeiten über ein Patentier</strong> &mdash; Ihre Einwilligung, die Sie jederzeit widerrufen können (Art. 6 Abs. 1 lit. a);</li>
            <li><strong>Sicherheit und Betrieb der Plattform</strong>, einschließlich Zugangskontrolle und technischer Protokolle &mdash; berechtigtes Interesse (Art. 6 Abs. 1 lit. f).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookies</h2>
        <p>Wir verwenden nur Cookies und lokalen Speicher, die für den Betrieb der Website oder für eine von Ihnen ausdrücklich angeforderte Funktion <strong>unbedingt erforderlich</strong> sind; nach portugiesischem Recht (Lei n.º 41/2004) ist daher keine Einwilligung erforderlich. Wir verwenden keine Werbe-, Analyse- oder Drittanbieter-Cookies, und alle Schriftarten und Ressourcen werden von unseren eigenen Servern ausgeliefert.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Art</th>
                        <th class="px-4 py-3">Zweck</th>
                        <th class="px-4 py-3">Dauer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Hält Ihre Sitzung aufrecht, während Sie die Website nutzen.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} Minuten Inaktivität</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Schützt Formulare vor gefälschten Anfragen von anderen Websites.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} Minuten Inaktivität</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Hält Sie angemeldet, nur wenn Sie bei der Anmeldung &bdquo;Angemeldet bleiben&ldquo; auswählen.</td>
                        <td class="px-4 py-3">400 Tage oder bis zur Abmeldung</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Lokaler Speicher</td>
                        <td class="px-4 py-3">Speichert Ihre Wahl zwischen hellem und dunklem Design, nur wenn Sie in den Einstellungen eines auswählen.</td>
                        <td class="px-4 py-3">Bis Sie ihn im Browser löschen</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Sie können Cookies in den Einstellungen Ihres Browsers löschen oder blockieren; wenn Sie die Sitzungscookies blockieren, können Sie sich nicht im Tierheimbereich anmelden.</p>
    </section>

    <section id="sharing">
        <h2>5. An wen wir Daten weitergeben</h2>
        <p>Wir verkaufen Ihre Daten nicht und geben sie nicht zu kommerziellen Zwecken weiter. Die Daten jedes Tierheims sind nur für das Team dieses Tierheims und die Administratoren der Plattform zugänglich. Sie können außerdem von Dienstleistern verarbeitet werden, die uns beim Betrieb der Plattform unterstützen (Hosting und E-Mail-Versand), stets unter angemessenen vertraglichen Garantien, oder Behörden offengelegt werden, wenn das Gesetz dies verlangt.</p>
    </section>

    <section id="retention">
        <h2>6. Wie lange wir die Daten speichern</h2>
        <ul>
            <li><strong>Benutzerkonten:</strong> solange das Konto aktiv ist;</li>
            <li><strong>Vermittlungen und Patenschaften:</strong> so lange, wie es für die Nachbetreuung des Tieres und die Erfüllung geltender rechtlicher Pflichten erforderlich ist;</li>
            <li><strong>Ehrenamtliche:</strong> während der Mitarbeit und danach nur für den gesetzlich vorgeschriebenen Zeitraum;</li>
            <li><strong>Sitzungen:</strong> laufen nach einer Zeit der Inaktivität automatisch ab.</li>
        </ul>
        <p>Gelöschte Einträge können für einen begrenzten Zeitraum außerhalb des normalen Zugriffs zu Prüf- und Wiederherstellungszwecken aufbewahrt werden.</p>
    </section>

    <section id="rights">
        <h2>7. Ihre Rechte</h2>
        <p>Sie können jederzeit <strong>Auskunft</strong> über Ihre Daten, deren <strong>Berichtigung</strong> oder <strong>Löschung</strong>, die <strong>Einschränkung</strong> der Verarbeitung oder die <strong>Übertragbarkeit</strong> der Daten verlangen, der auf berechtigtem Interesse beruhenden Verarbeitung <strong>widersprechen</strong> und <strong>jede erteilte Einwilligung widerrufen</strong>, ohne dass die zuvor erfolgte Verarbeitung davon berührt wird.</p>
        <p>Um diese Rechte auszuüben, wenden Sie sich direkt an das Tierheim, mit dem Sie zu tun hatten (seine Kontaktdaten stehen auf der Seite jedes Tieres)@if ($contactEmail), oder schreiben Sie uns an <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Wir antworten spätestens innerhalb eines Monats.</p>
        <p>Sie haben außerdem das Recht, bei der portugiesischen Datenschutzbehörde (CNPD) unter <a href="https://www.cnpd.pt" target="_blank" rel="noopener">www.cnpd.pt</a> Beschwerde einzulegen.</p>
    </section>

    <section id="security">
        <h2>8. Sicherheit</h2>
        <p>Passwörter werden als Hash gespeichert, der Zugang erfolgt nur auf Einladung, und jeder Benutzer kann nur die Daten des Tierheims sehen, dem er angehört. Wir setzen technische und organisatorische Maßnahmen ein, um die Daten vor unbefugtem Zugriff, Verlust oder Veränderung zu schützen.</p>
    </section>

    <section id="changes">
        <h2>9. Änderungen dieser Erklärung</h2>
        <p>Wir können diese Erklärung aktualisieren, um Änderungen der Plattform oder der Rechtslage widerzuspiegeln. Das Datum der letzten Aktualisierung steht immer oben auf dieser Seite.</p>
    </section>
</div>
