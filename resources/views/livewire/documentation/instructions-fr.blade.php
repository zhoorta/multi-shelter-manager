<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Instructions de l'Application</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Un guide des principales sections de {{ config('app.name') }} et de leur utilisation au quotidien.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Premiers Pas</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Tableau de Bord</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Animaux</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vaccinations</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptions</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Parrainages</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Bénévoles</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Installations</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Utilisateurs</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Paramètres</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Premiers Pas</h2>
        <p>Lors du tout premier lancement, l'application vous demande de créer le compte administrateur initial. Ensuite, les nouveaux comptes ne sont créés que sur invitation.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rôles</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrateur</strong> &mdash; gère toute la plateforme : les refuges, les tables de référence partagées et les comptes utilisateurs. Les administrateurs ne gèrent ni les animaux ni les installations.</li>
            <li><strong>Gestionnaire</strong> &mdash; dirige un refuge : tout ce que peut faire un employé, plus l'invitation et la gestion des utilisateurs de ce refuge.</li>
            <li><strong>Employé</strong> &mdash; s'occupe du travail quotidien du refuge : animaux, vaccinations, adoptions, parrainages, bénévoles et installations.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Travailler avec plusieurs refuges</h3>
        <p>Un utilisateur peut appartenir à plusieurs refuges, avec un rôle différent dans chacun. Utilisez le sélecteur de refuge pour changer de refuge actif ; chaque liste, compteur et formulaire n'affiche alors que les données de ce refuge. Les données ne sont jamais partagées entre refuges.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ordre de configuration recommandé</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Un administrateur crée le refuge et remplit les tables de référence (espèces, races, tailles, types de pelage, vaccins, maladies, activités).</li>
            <li>L'administrateur invite le gestionnaire du refuge.</li>
            <li>Le gestionnaire configure les installations, ailes et cages, et invite les employés.</li>
            <li>L'équipe commence à enregistrer les animaux.</li>
        </ol>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Tableau de Bord</h2>
        <p>Le tableau de bord vous donne une vue d'ensemble du refuge actif :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Des compteurs d'animaux présents, d'adoptions, de capacité disponible dans les cages et de personnel.</li>
            <li>Les arrivées, adoptions, parrainages et décès les plus récents.</li>
            <li>Les animaux sans emplacement connu, afin de pouvoir les attribuer à une cage.</li>
            <li>Des avertissements lorsqu'il manque encore quelque chose, comme l'absence de cages ou des espèces sans races.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Animaux</h2>
        <p>Le menu Animaux liste les animaux du refuge par espèce. Chaque fiche comprend l'identification (référence, nom, puce), la description physique (race, couleurs, type de pelage, taille, sexe, stérilisation), les dates (naissance, arrivée, sortie, décès), des photos, une description publique, des notes internes et des notes cliniques.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Statut</h3>
        <p>Le statut d'un animal est calculé automatiquement, vous ne le définissez donc jamais à la main :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Décédé</strong> &mdash; une date de décès est renseignée.</li>
            <li><strong>Adopté</strong> &mdash; l'animal a une adoption sans date de retour.</li>
            <li><strong>Disponible</strong> / <strong>Indisponible</strong> &mdash; dans les autres cas, selon que l'animal est marqué comme adoptable ou non.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Recherche et filtres</h3>
        <p>Recherchez par nom, référence, puce ou notes internes, et filtrez par statut, espèce ou emplacement (installation, aile ou cage). Le filtre <em>données manquantes</em> trouve les animaux sans âge, sans photo, sans date d'arrivée ou sans emplacement, ce qui aide à garder les fiches complètes.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Impression</h3>
        <p>Vous pouvez imprimer la fiche d'un animal depuis sa page, ou imprimer la liste des animaux ; la liste imprimée utilise les mêmes filtres que ceux actifs à l'écran.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Santé</h3>
        <p>Enregistrez pour chaque animal les maladies (avec date de diagnostic, statut et notes de traitement), les vaccinations et les notes cliniques. Les tailles ne sont proposées que pour les espèces qui ont des tailles configurées.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinations</h2>
        <p>Chaque vaccination enregistre le vaccin, la date à laquelle il a été administré ou est prévu, le numéro de lot, le vétérinaire et des notes. La page Vaccinations les liste pour tous les animaux du refuge.</p>
        <p>Chaque jour, les utilisateurs qui ont activé les notifications de vaccination pour un refuge reçoivent un email listant les vaccinations de ce refuge prévues dans les sept prochains jours et pas encore administrées. Chaque vaccination n'est notifiée qu'une seule fois. Une icône de cloche dans la liste des utilisateurs indique qui reçoit ces emails.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptions</h2>
        <p>Une adoption enregistre les coordonnées de l'adoptant, la date d'adoption, les frais, des notes et le statut de la demande (En attente, Approuvée ou Refusée). Commencez-la depuis la page de l'animal.</p>
        <p>Si un animal adopté revient au refuge, renseignez la <strong>date de retour</strong> sur l'adoption : l'animal redevient disponible et l'adoption reste dans son historique.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Parrainages</h2>
        <p>Les parrains soutiennent les soins d'un animal sans l'adopter. Un parrainage conserve les coordonnées du parrain et indique s'il souhaite recevoir des nouvelles de l'animal ou la newsletter.</p>
        <p>Chaque parrainage possède une liste de paiements. Un paiement enregistre la période couverte (dates de début et de fin), la date de paiement et le montant, ce qui permet de gérer aussi bien les contributions ponctuelles que récurrentes.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Bénévoles</h2>
        <p>Gardez une trace des personnes qui aident votre refuge, indépendamment des comptes utilisateurs. Pour chaque bénévole, vous pouvez enregistrer les données personnelles et coordonnées, une photo, les dates de début et de fin, le moyen de transport et la préférence de newsletter, ainsi que :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Les activités auxquelles il participe et les espèces avec lesquelles il préfère travailler.</li>
            <li>Sa disponibilité par jour de la semaine (matins et/ou après-midi, occasionnellement, toutes les deux semaines ou chaque semaine).</li>
            <li>Des évaluations d'assiduité et de performance.</li>
        </ul>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Installations</h2>
        <p>Un refuge est organisé en trois niveaux : les <strong>installations</strong> (sites physiques, avec une adresse) contiennent des <strong>ailes</strong>, et les ailes contiennent des <strong>cages</strong>. Chaque cage a un code et une capacité.</p>
        <p>La capacité totale des cages détermine combien d'animaux le refuge peut accueillir, et c'est aux cages que les animaux sont attribués. Configurez au moins une cage avant d'enregistrer des animaux afin de pouvoir leur attribuer un emplacement.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Utilisateurs</h2>
        <p>Les gestionnaires et les administrateurs invitent de nouveaux utilisateurs par email ; la personne invitée reçoit un lien pour définir son mot de passe. Pour chaque refuge auquel elle appartient, vous choisissez le rôle (gestionnaire ou employé) et si l'utilisateur reçoit les notifications de vaccination.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Un gestionnaire ne peut ajouter des utilisateurs qu'aux refuges qu'il gère.</li>
            <li>Un administrateur peut ajouter des utilisateurs à n'importe quel refuge et peut créer d'autres administrateurs.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Seuls les administrateurs voient ce menu. Il permet de gérer les refuges et les tables de référence partagées par tous les refuges :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Refuges</strong> &mdash; créer, modifier et supprimer des refuges.</li>
            <li><strong>Espèces</strong>, <strong>Races</strong>, <strong>Tailles</strong> et <strong>Types de Pelage</strong> &mdash; les options utilisées pour décrire les animaux.</li>
            <li><strong>Vaccins</strong> et <strong>Maladies</strong> &mdash; les options utilisées dans les dossiers de santé des animaux.</li>
            <li><strong>Activités</strong> &mdash; les tâches auxquelles les bénévoles peuvent participer.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Paramètres</h2>
        <p>Depuis le menu utilisateur, ouvrez Paramètres pour consulter votre profil, changer votre mot de passe et choisir l'apparence (claire, sombre ou système). Votre nom ne peut être modifié que par un administrateur ou un gestionnaire, et votre email ne peut pas être modifié.</p>
    </section>
</div>
