<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Istruzioni dell'Applicazione</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Una guida alle principali sezioni di {{ config('app.name') }} e al loro utilizzo quotidiano.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Primi Passi</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Pannello di Controllo</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Animali</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vaccinazioni</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adozioni</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adozioni a Distanza</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Volontari</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Soci</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Strutture</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Utenti</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Portale Pubblico</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Amministrazione</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Impostazioni</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Primi Passi</h2>
        <p>Al primissimo avvio, l'applicazione chiede di creare l'account amministratore iniziale. Da quel momento, i nuovi account vengono creati solo su invito.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ruoli</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Amministratore</strong> &mdash; gestisce l'intera piattaforma: i rifugi, le tabelle di riferimento condivise e gli account utente. Gli amministratori non gestiscono animali né strutture.</li>
            <li><strong>Responsabile</strong> &mdash; dirige un rifugio: tutto ciò che può fare un operatore, più l'invito e la gestione degli utenti di quel rifugio.</li>
            <li><strong>Operatore</strong> &mdash; si occupa del lavoro quotidiano del rifugio: animali, vaccinazioni, adozioni, adozioni a distanza, volontari e strutture.</li>
            <li><strong>Consultazione</strong> &mdash; accesso in sola lettura al rifugio: può vedere animali, vaccinazioni e strutture e stampare schede ed elenchi degli animali, ma non può creare, modificare né eliminare nulla e non vede i dati personali di adottanti, sostenitori, volontari e soci.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Lavorare con più rifugi</h3>
        <p>Un utente può appartenere a più di un rifugio, con un ruolo diverso in ciascuno. Usa il selettore del rifugio per cambiare il rifugio attivo; ogni elenco, conteggio e modulo mostra quindi solo i dati di quel rifugio. I dati non vengono mai condivisi tra rifugi.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ordine di configurazione consigliato</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Un amministratore compila le tabelle di riferimento (regioni, specie, razze, taglie, tipi di pelo, vaccini, malattie, attività).</li>
            <li>L'amministratore crea il rifugio, completa il suo profilo (contatti, regione, descrizione, logo) e sceglie le specie con cui lavora.</li>
            <li>L'amministratore invita il responsabile del rifugio.</li>
            <li>Il responsabile configura strutture, ali e gabbie e invita gli operatori.</li>
            <li>Il team inizia a registrare gli animali.</li>
            <li>Se il portale pubblico è attivo, il team pubblica gli animali pronti per l'adozione.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orientarsi nell'applicazione</h3>
        <p>La barra laterale mostra solo ciò che il tuo ruolo può usare. Responsabili e staff vedono il menu Animali (una voce per ogni specie attivata per il rifugio, più Adozioni a Distanza, Adozioni e Vaccinazioni), Volontari, Soci e Strutture; i responsabili vedono anche Utenti. Gli amministratori vedono invece Utenti e il menu Amministrazione. Gli utenti in consultazione vedono gli stessi menu dello staff, tranne Adozioni a Distanza, Adozioni, Volontari e Soci, e le pagine non mostrano loro pulsanti per creare, modificare o eliminare. Questa documentazione è sempre disponibile in fondo alla barra laterale.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Pannello di Controllo</h2>
        <p>Il pannello di controllo offre una panoramica del rifugio attivo:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Contatori di animali nel rifugio, capienza disponibile nelle gabbie e adozioni dell'anno. Responsabili e personale vedono anche le richieste di adozione in attesa, le vaccinazioni scadute e le quote associative arretrate, ciascuno con un link alla relativa lista.</li>
            <li>Gli ingressi, le adozioni, le adozioni a distanza e i decessi più recenti.</li>
            <li>Gli animali senza una posizione nota, così da poterli assegnare a una gabbia.</li>
            <li>Avvisi quando manca ancora qualcosa, ad esempio nessuna gabbia definita o specie senza razze.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Animali</h2>
        <p>Il menu Animali elenca gli animali del rifugio per specie. Ogni scheda include l'identificazione (riferimento, nome, microchip), la descrizione fisica (razza, colori, tipo di mantello, taglia, sesso, sterilizzazione), le date (nascita, ingresso, uscita, decesso), foto, una descrizione pubblica, note interne e note cliniche. Compaiono solo le specie che l'amministratore ha attivato per il tuo rifugio.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Stato</h3>
        <p>Lo stato di un animale viene calcolato automaticamente, quindi non va mai impostato a mano:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Deceduto</strong> &mdash; è stata inserita una data di decesso.</li>
            <li><strong>Adottato</strong> &mdash; l'animale ha un'adozione senza data di restituzione.</li>
            <li><strong>Adottabile</strong> / <strong>Non adottabile</strong> &mdash; negli altri casi, a seconda che l'animale sia contrassegnato come adottabile.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Opzioni e collocazione</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Adottabile</strong> &mdash; l'animale può essere adottato; determina se il suo stato è disponibile o non disponibile.</li>
            <li><strong>Disponibile per Adozione a Distanza</strong> &mdash; l'animale può ricevere adozioni a distanza. L'azione è offerta solo per gli animali con questa opzione attiva e resta disponibile anche dopo l'adozione dell'animale.</li>
            <li><strong>Gabbia</strong> &mdash; l'elenco delle gabbie è raggruppato per struttura e ala e mostra quanti posti liberi ha ogni gabbia, con un indicatore verde, giallo o rosso man mano che si riempie.</li>
        </ul>
        <p>Quando il portale pubblico è attivo, compaiono altre due opzioni: <strong>Pubblica sul Portale Pubblico</strong> e <strong>In Evidenza</strong>. Vedi <a href="#public-portal" class="underline underline-offset-2">Portale Pubblico</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ricerca e filtri</h3>
        <p>Cerca per nome, riferimento, microchip o note interne e filtra per stato, specie o posizione (struttura, ala o gabbia). Il filtro <em>dati mancanti</em> trova gli animali senza età, senza foto, senza data di ingresso o senza posizione, aiutando a mantenere le schede complete.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Stampa</h3>
        <p>Puoi stampare la scheda di un singolo animale dalla sua pagina, oppure stampare l'elenco degli animali; l'elenco stampato usa gli stessi filtri attivi sullo schermo.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Condividere sui social</h3>
        <p>Gli animali adottabili e disponibili hanno in alto nella loro pagina un pulsante di condivisione. Prepara un testo con i dati dell'animale e i contatti del rifugio, pronto da copiare, e permette di scaricare la foto principale per pubblicarla su Facebook, Instagram o WhatsApp. Quando l'animale è pubblicato sul portale pubblico, il testo include un link all'animale e puoi anche condividerlo direttamente su Facebook o WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Salute</h3>
        <p>Registra per ogni animale le malattie (con data di diagnosi, stato e note sul trattamento), le vaccinazioni e le note cliniche. Le taglie sono proposte solo per le specie che hanno taglie configurate.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinazioni</h2>
        <p>Ogni vaccinazione registra il vaccino, la data di somministrazione o la data prevista, il numero di lotto, il veterinario e le note. La pagina Vaccinazioni le elenca per tutti gli animali del rifugio.</p>
        <p>Ogni giorno, gli utenti che hanno attivato le notifiche di vaccinazione per un rifugio ricevono un'email con le vaccinazioni di quel rifugio previste nei prossimi sette giorni e non ancora somministrate. Ogni vaccinazione viene notificata una sola volta. Un'icona a forma di campanella nell'elenco utenti indica chi riceve queste email.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adozioni</h2>
        <p>Un'adozione registra i contatti dell'adottante, la data di adozione, il contributo, le note e lo stato della richiesta (In attesa, Approvata o Respinta). Avviala dalla pagina dell'animale.</p>
        <p>La pagina Adozioni (sotto Animali nella barra laterale) elenca tutte le adozioni del rifugio; cerca per nome, telefono, email o note dell'adottante, oppure per nome o riferimento dell'animale.</p>
        <p>Se un animale adottato torna al rifugio, compila la <strong>data di restituzione</strong> nell'adozione: l'animale torna disponibile e l'adozione resta nel suo storico.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adozioni a Distanza</h2>
        <p>I sostenitori contribuiscono alla cura di un animale senza adottarlo. Un'adozione a distanza conserva i contatti del sostenitore e indica se desidera ricevere aggiornamenti sull'animale o la newsletter.</p>
        <p>Le adozioni a distanza si possono creare solo per animali contrassegnati come <strong>Disponibile per Adozione a Distanza</strong>. La pagina Adozioni a Distanza (sotto Animali nella barra laterale) le elenca tutte, con la stessa ricerca delle adozioni: nome, telefono, email o note del sostenitore, oppure nome o riferimento dell'animale.</p>
        <p>Ogni adozione a distanza ha un elenco di pagamenti. Un pagamento registra il periodo coperto (date di inizio e fine), la data di pagamento e l'importo, così sono supportati sia i contributi una tantum sia quelli periodici.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Volontari</h2>
        <p>Tieni traccia delle persone che aiutano il tuo rifugio, separatamente dagli account utente. Per ogni volontario puoi salvare dati personali e contatti, una foto, date di inizio e fine, mezzo di trasporto e preferenza per la newsletter, oltre a:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Le attività in cui aiuta e le specie con cui preferisce lavorare.</li>
            <li>La disponibilità per giorno della settimana (mattina e/o pomeriggio, occasionalmente, ogni due settimane o settimanalmente).</li>
            <li>Le valutazioni di presenza e prestazioni.</li>
        </ul>
        <p>L'elenco dei volontari si può cercare per nome, telefono, email, codice fiscale o note, e filtrare per specie preferita, giorno di disponibilità e attività &mdash; utile per sapere chi può aiutare in un certo giorno.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Soci</h2>
        <p>Tieni il registro dei soci dell'associazione e delle loro quote. Ogni socio ha un numero di socio, dati personali e di contatto, una data di adesione, uno stato e le sue quote, e può essere collegato alla sua scheda di volontario quando si tratta della stessa persona.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Quote</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Quota d'ingresso</strong> &mdash; si paga una sola volta, all'adesione. Può essere 0, e in quel caso non è dovuto nulla.</li>
            <li><strong>Quota associativa</strong> &mdash; l'importo periodico: Mensile, Trimestrale, Semestrale o Annuale.</li>
        </ul>
        <p>I responsabili impostano i valori predefiniti del rifugio con il pulsante <strong>Quote</strong> dell'elenco dei soci. I nuovi soci partono da questi valori, che poi si possono modificare per ogni socio.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Numeri di socio</h3>
        <p>Lascia il numero vuoto e viene assegnato automaticamente il successivo, oppure scrivine uno per mantenere la numerazione che usi già. Ogni numero può essere usato una sola volta per rifugio, e i numeri dei soci eliminati non vengono mai riutilizzati.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Pagamenti</h3>
        <p>Registra la quota d'ingresso o una quota associativa nella pagina del socio. La quota associativa è precompilata con il prossimo periodo da pagare (dal giorno dopo l'ultimo periodo pagato, o dalla data di adesione) e con la quota del socio. Ogni pagamento registra anche la data di pagamento, l'importo, il metodo (Contanti, Bonifico bancario, Pagamento mobile o Altro) e le note.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Quote arretrate</h3>
        <p>Un socio attivo viene segnato con <strong>Quote arretrate</strong> quando la quota d'ingresso non è pagata o nessuna quota copre la data di oggi; il segno scompare appena il pagamento viene registrato. Attiva <strong>Solo quote arretrate</strong> nell'elenco per vedere a chi mandare un promemoria.</p>
        <p>Lo stato (Attivo, Sospeso o Ex socio) non cambia mai automaticamente: modificalo nel modulo del socio, secondo le regole dell'associazione. L'elenco mostra i soci attivi per impostazione predefinita; usa il filtro Stato per vedere gli altri.</p>
        <p>Responsabili e staff possono aggiungere e modificare soci e registrare pagamenti; solo i responsabili possono eliminare soci o cambiare i valori predefiniti. Gli utenti in consultazione non hanno accesso ai soci.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Strutture</h2>
        <p>Un rifugio è organizzato su tre livelli: le <strong>strutture</strong> (sedi fisiche, con un indirizzo) contengono le <strong>ali</strong>, e le ali contengono le <strong>gabbie</strong>. Ogni gabbia ha un codice e una capienza.</p>
        <p>La capienza totale delle gabbie determina quanti animali può ospitare il rifugio, ed è alle gabbie che vengono assegnati gli animali. Configura almeno una gabbia prima di registrare gli animali, così da poter assegnare loro una posizione.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Utenti</h2>
        <p>Responsabili e amministratori invitano i nuovi utenti via email; la persona invitata riceve un link per impostare la propria password. Per ogni rifugio di appartenenza scegli il ruolo (responsabile, operatore o consultazione) e se l'utente riceve le notifiche di vaccinazione.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Un responsabile può aggiungere utenti solo ai rifugi che gestisce.</li>
            <li>Un amministratore può aggiungere utenti a qualsiasi rifugio e può creare altri amministratori.</li>
        </ul>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Portale Pubblico</h2>
        <p>Un'installazione può, facoltativamente, offrire un sito pubblico accanto al backoffice. Lo attiva chi gestisce il server; quando è disattivato, la home page manda i visitatori alla pagina di accesso e le opzioni seguenti sono nascoste.</p>
        <p>Quando è attivo, chiunque (senza accesso) può:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Sfogliare gli animali di tutti i rifugi pronti per l'adozione, filtrando per specie, sesso, taglia, razza e regione.</li>
            <li>Aprire la scheda di un animale per vedere foto, descrizione pubblica e il rifugio che lo ospita.</li>
            <li>Vedere l'elenco dei rifugi partner, ciascuno con una propria pagina con contatti, descrizione, logo e animali.</li>
            <li>Aprire il link di un singolo animale condiviso dal backoffice: apre direttamente la scheda di quell'animale, e le anteprime dei link sui social mostrano nome, foto e descrizione.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Cosa viene mostrato pubblicamente</h3>
        <p>Un animale compare sul portale solo quando sono vere <strong>tutte</strong> queste condizioni:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Pubblica sul Portale Pubblico</strong> è attivo (è disattivato di default, così nulla viene pubblicato per errore).</li>
            <li>L'animale è adottabile e disponibile (gli animali adottati o deceduti scompaiono automaticamente).</li>
            <li>Il suo rifugio non è stato rimosso.</li>
        </ul>
        <p>Gli animali contrassegnati come <strong>In Evidenza</strong> vengono mostrati per primi, con un apposito badge. Vengono mostrati solo nome, riferimento, foto, descrizione pubblica e dati descrittivi (specie, razza, taglia, sesso, età, tipo di pelo, sterilizzazione) &mdash; note interne, note cliniche, microchip e gabbia non vengono mai pubblicati.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Consigli per buoni annunci</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Aggiungi almeno una bella foto e una descrizione pubblica accogliente &mdash; è ciò che gli adottanti vedono per primo.</li>
            <li>Tieni aggiornato il profilo del rifugio (contatti, descrizione, logo): compare nella pagina pubblica del rifugio, nei risultati dei motori di ricerca e nelle anteprime dei link.</li>
            <li>Le pagine pubbliche sono predisposte per i motori di ricerca (Google e altri) e viene generata automaticamente una sitemap; il backoffice non viene mai indicizzato.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Amministrazione</h2>
        <p>Solo gli amministratori vedono questo menu. Serve a gestire i rifugi e le tabelle di riferimento condivise da tutti i rifugi:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Rifugi</strong> &mdash; creare, modificare e rimuovere rifugi. Oltre al nome e alla città, un rifugio ha un nome breve, contatti (email, telefono, sito web), indirizzo, regione, una descrizione e un logo &mdash; usati sul portale pubblico quando è attivo. L'elenco <strong>Specie</strong> nel modulo del rifugio stabilisce quali specie compaiono nel menu Animali per il responsabile e lo staff di quel rifugio.</li>
            <li><strong>Regioni</strong> &mdash; le regioni a cui appartengono i rifugi, usate anche come filtro sul portale pubblico.</li>
            <li><strong>Specie</strong>, <strong>Razze</strong>, <strong>Taglie</strong> e <strong>Tipi di Mantello</strong> &mdash; le opzioni usate per descrivere gli animali.</li>
            <li><strong>Vaccini</strong> e <strong>Malattie</strong> &mdash; le opzioni usate nelle cartelle sanitarie degli animali.</li>
            <li><strong>Attività</strong> &mdash; i compiti in cui i volontari possono aiutare.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Impostazioni</h2>
        <p>Dal menu utente, apri Impostazioni per vedere il tuo profilo, cambiare la password e scegliere l'aspetto (chiaro, scuro o di sistema). Il tuo nome può essere modificato solo da un amministratore o da un responsabile, e la tua email non può essere modificata.</p>
    </section>
</div>
