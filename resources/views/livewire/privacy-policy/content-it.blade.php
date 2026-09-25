<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Informativa sulla Privacy</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Ultimo aggiornamento: 25 settembre 2026</p>
        <p>{{ config('app.name') }} è una piattaforma che riunisce diversi rifugi per animali. Prendiamo sul serio la protezione dei tuoi dati personali e li trattiamo in conformità al Regolamento Generale sulla Protezione dei Dati (GDPR) e al Codice in materia di protezione dei dati personali (D.Lgs. 196/2003, come modificato dal D.Lgs. 101/2018). Questa informativa spiega quali dati trattiamo, perché, per quanto tempo e quali sono i tuoi diritti.</p>
    </header>

    <section id="responsible">
        <h2>1. Chi è responsabile dei tuoi dati</h2>
        <p>Ogni rifugio che utilizza la piattaforma è <strong>titolare del trattamento</strong> dei dati che raccoglie nello svolgimento della propria attività (ad esempio i dati di adottanti, sostenitori e volontari). {{ config('app.name') }} fornisce la piattaforma tecnologica e agisce come <strong>responsabile del trattamento</strong> per conto di tali rifugi, trattando i dati esclusivamente secondo le loro istruzioni.</p>
        <p>{{ config('app.name') }} è titolare del trattamento dei dati relativi agli account utente della piattaforma e ai visitatori della pagina pubblica.</p>
    </section>

    <section id="data">
        <h2>2. Quali dati trattiamo</h2>

        <h3>Visitatori della pagina pubblica</h3>
        <p>Puoi consultare gli animali in adozione senza creare un account. Trattiamo solo i dati tecnici necessari al funzionamento del sito: indirizzo IP, tipo di browser e dispositivo, e un cookie di sessione. Se invii una richiesta di adozione, trattiamo anche i dati descritti di seguito.</p>

        <h3>Utenti della piattaforma (team dei rifugi)</h3>
        <ul>
            <li>Nome e indirizzo email;</li>
            <li>Password (conservata solo in forma di hash, mai in chiaro);</li>
            <li>Il rifugio o i rifugi a cui appartieni e il tuo ruolo (amministratore, responsabile o operatore);</li>
            <li>Preferenze di notifica via e-mail (vaccinazioni in scadenza e nuove richieste di adozione);</li>
            <li>Data dell'ultimo accesso e dati di sessione (indirizzo IP e browser).</li>
        </ul>

        <h3>Richiedenti di adozione</h3>
        <ul>
            <li>Nome, e-mail, telefono, codice postale e città;</li>
            <li>L'animale per cui fai richiesta, le tue risposte sulla tua casa (tipo di abitazione, giardino, bambini e altri animali) e la tua motivazione;</li>
            <li>La data in cui hai dato il consenso e l'indirizzo IP da cui è stata inviata la richiesta, conservato solo per proteggere il modulo da abusi.</li>
        </ul>
        <p>La richiesta viene inviata solo al rifugio che si prende cura di quell'animale, che la utilizza per valutare l'adozione. Se viene approvata, i tuoi dati di contatto entrano a far parte della scheda di adozione.</p>

        <h3>Adottanti</h3>
        <ul>
            <li>Nome, email, telefono, indirizzo, CAP e città;</li>
            <li>L'animale adottato, la data di adozione ed eventuale data di restituzione;</li>
            <li>Contributo di adozione e note registrate dal rifugio.</li>
        </ul>

        <h3>Sostenitori (adozioni a distanza)</h3>
        <ul>
            <li>Nome, email, telefono, indirizzo, CAP e città;</li>
            <li>L'animale sostenuto e lo storico dei pagamenti (date, periodi e importi);</li>
            <li>Preferenze di comunicazione (aggiornamenti sull'animale e/o newsletter).</li>
        </ul>

        <h3>Volontari</h3>
        <ul>
            <li>Nome, sesso, data di nascita e foto;</li>
            <li>Numero del documento d'identità e codice fiscale;</li>
            <li>Contatti, indirizzo, professione e mezzo di trasporto;</li>
            <li>Disponibilità, date di inizio e fine della collaborazione e valutazioni di presenza e prestazioni;</li>
            <li>Se accolgono animali come famiglia affidataria e quali animali sono affidati alle loro cure;</li>
            <li>Preferenza per la newsletter.</li>
        </ul>

        <h3>Soci</h3>
        <ul>
            <li>Nome, codice fiscale, e-mail, telefono, indirizzo, codice postale e città;</li>
            <li>Numero di socio, data di iscrizione, stato (attivo, sospeso o uscito) ed eventuale collegamento alla scheda di volontario;</li>
            <li>Quota di iscrizione e quote associative, storico dei pagamenti (date, periodi coperti, importi e metodo di pagamento) e note registrate dal rifugio.</li>
        </ul>

        <p>La pagina pubblica mostra solo informazioni sugli animali (foto, caratteristiche e descrizione) e i contatti del rifugio. <strong>Non</strong> pubblica <strong>mai</strong> dati di adottanti, sostenitori, volontari o utenti. Gli animali che vivono presso una famiglia affidataria sono solo indicati come tali, senza alcun dato sulla famiglia. Le stesse informazioni sugli animali sono pubblicate nel feed delle novità (RSS) di ciascun rifugio e possono essere condivise dal rifugio sui social network.</p>
    </section>

    <section id="purposes">
        <h2>3. Per quali finalità usiamo i dati e su quale base giuridica</h2>
        <ul>
            <li><strong>Gestire adozioni, adozioni a distanza e volontariato</strong> &mdash; esecuzione dell'accordo con te o misure precontrattuali adottate su tua richiesta (art. 6, par. 1, lett. b) GDPR);</li>
            <li><strong>Gestire i soci e le loro quote</strong> &mdash; esecuzione dell'accordo con te o misure precontrattuali adottate su tua richiesta (art. 6, par. 1, lett. b) GDPR);</li>
            <li><strong>Valutare le richieste di adozione</strong> &mdash; misure precontrattuali adottate su tua richiesta (art. 6, par. 1, lett. b) GDPR), con il consenso che dai nel modulo; l'indirizzo IP è conservato per proteggere il modulo da abusi &mdash; legittimo interesse (art. 6, par. 1, lett. f));</li>
            <li><strong>Seguire il benessere degli animali dopo l'adozione</strong> &mdash; legittimo interesse del rifugio alla tutela degli animali (art. 6, par. 1, lett. f));</li>
            <li><strong>Adempiere agli obblighi di legge</strong>, come le norme fiscali e la registrazione e identificazione degli animali da compagnia (art. 6, par. 1, lett. c));</li>
            <li><strong>Inviare newsletter e aggiornamenti su un animale sostenuto</strong> &mdash; il tuo consenso, che puoi revocare in qualsiasi momento (art. 6, par. 1, lett. a));</li>
            <li><strong>Mantenere la piattaforma sicura e funzionante</strong>, compresi il controllo degli accessi e i log tecnici &mdash; legittimo interesse (art. 6, par. 1, lett. f)).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookie</h2>
        <p>Utilizziamo solo cookie e archiviazione locale <strong>strettamente necessari</strong> al funzionamento del sito o a una funzione da te espressamente richiesta; ai sensi dell'art. 122 del Codice in materia di protezione dei dati personali non è quindi richiesto il tuo consenso. Non utilizziamo cookie pubblicitari, analitici o di terze parti, e tutti i font e le risorse sono serviti dai nostri server.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Finalità</th>
                        <th class="px-4 py-3">Durata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Mantiene la tua sessione mentre navighi nel sito.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minuti di inattività</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Protegge i moduli da richieste contraffatte provenienti da altri siti.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minuti di inattività</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Ti mantiene connesso, solo se selezioni &ldquo;Ricordami&rdquo; al momento dell'accesso.</td>
                        <td class="px-4 py-3">400 giorni o fino alla disconnessione</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Archiviazione locale</td>
                        <td class="px-4 py-3">Memorizza la tua preferenza per il tema chiaro o scuro, solo se ne scegli uno nelle impostazioni.</td>
                        <td class="px-4 py-3">Finché non la cancelli nel browser</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Puoi eliminare o bloccare i cookie nelle impostazioni del browser; se blocchi i cookie di sessione, non potrai accedere all'area dei rifugi.</p>
    </section>

    <section id="sharing">
        <h2>5. Con chi condividiamo i dati</h2>
        <p>Non vendiamo né cediamo i tuoi dati a fini commerciali. I dati di ogni rifugio sono accessibili solo al team di quel rifugio e agli amministratori della piattaforma. Possono inoltre essere trattati da fornitori di servizi che ci aiutano a gestire la piattaforma (hosting e invio email), sempre con adeguate garanzie contrattuali, oppure comunicati alle autorità quando richiesto dalla legge.</p>
    </section>

    <section id="retention">
        <h2>6. Per quanto tempo conserviamo i dati</h2>
        <ul>
            <li><strong>Account utente:</strong> finché l'account è attivo;</li>
            <li><strong>Richieste di adozione:</strong> cancellate automaticamente 6 mesi dopo l'ultima modifica;</li>
            <li><strong>Adozioni e adozioni a distanza:</strong> per il tempo necessario a seguire l'animale e ad adempiere agli obblighi di legge applicabili;</li>
            <li><strong>Soci:</strong> per tutta la durata dell'iscrizione e, successivamente, solo per il periodo previsto dalla legge (ad esempio per le registrazioni delle quote);</li>
            <li><strong>Volontari:</strong> durante la collaborazione e, successivamente, solo per il periodo previsto dalla legge;</li>
            <li><strong>Sessioni:</strong> scadono automaticamente dopo un periodo di inattività.</li>
        </ul>
        <p>I record eliminati possono essere conservati per un periodo limitato, al di fuori dell'accesso ordinario, per finalità di audit e di recupero in caso di errore.</p>
    </section>

    <section id="rights">
        <h2>7. I tuoi diritti</h2>
        <p>Puoi in qualsiasi momento richiedere l'<strong>accesso</strong> ai tuoi dati, la loro <strong>rettifica</strong> o <strong>cancellazione</strong>, la <strong>limitazione</strong> del trattamento o la <strong>portabilità</strong> dei dati, <strong>opporti</strong> al trattamento basato sul legittimo interesse e <strong>revocare qualsiasi consenso</strong> prestato, senza pregiudicare il trattamento effettuato in precedenza.</p>
        <p>Per esercitare questi diritti, contatta direttamente il rifugio con cui hai avuto a che fare (i suoi contatti sono indicati nella pagina di ogni animale)@if ($contactEmail), oppure scrivici all'indirizzo <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Ti risponderemo entro un mese al massimo.</p>
        <p>Hai inoltre il diritto di proporre reclamo al Garante per la protezione dei dati personali su <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener">www.garanteprivacy.it</a>.</p>
    </section>

    <section id="security">
        <h2>8. Sicurezza</h2>
        <p>Le password sono conservate in forma di hash, l'accesso avviene solo su invito e ogni utente può vedere solo i dati del rifugio a cui appartiene. Adottiamo misure tecniche e organizzative per proteggere i dati da accessi non autorizzati, perdita o alterazione.</p>
    </section>

    <section id="changes">
        <h2>9. Modifiche a questa informativa</h2>
        <p>Possiamo aggiornare questa informativa per riflettere modifiche alla piattaforma o alla legge. La data dell'ultimo aggiornamento è sempre indicata in cima a questa pagina.</p>
    </section>
</div>
