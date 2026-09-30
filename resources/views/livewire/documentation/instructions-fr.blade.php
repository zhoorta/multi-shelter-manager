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
        <a href="#treatments" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Traitements</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoptions</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Parrainages</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Bénévoles</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Membres</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Installations</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Rapports</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Utilisateurs</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Portail Public</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administration</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Paramètres</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Premiers Pas</h2>
        <p>Lors du tout premier lancement, l'application vous demande de créer le compte administrateur initial. Ensuite, les nouveaux comptes ne sont créés que sur invitation.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Rôles</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrateur</strong> &mdash; gère toute la plateforme : les refuges, les tables de référence partagées et les comptes utilisateurs. Les administrateurs ne gèrent ni les animaux ni les installations.</li>
            <li><strong>Gestionnaire</strong> &mdash; dirige un refuge : tout ce que peut faire un employé, plus l'invitation et la gestion des utilisateurs de ce refuge. Le gestionnaire tient aussi à jour le profil du refuge (coordonnées, adresse, description, logo) dans <strong>Paramètres &gt; Refuge</strong> ; seul un administrateur peut modifier le nom ou les espèces du refuge.</li>
            <li><strong>Employé</strong> &mdash; s'occupe du travail quotidien du refuge : animaux, vaccinations, traitements, adoptions, parrainages, bénévoles et installations.</li>
            <li><strong>Consultation</strong> &mdash; accès en lecture seule au refuge : peut voir les animaux, les vaccinations, les traitements et les installations et imprimer les fiches et listes d'animaux, mais ne peut rien créer, modifier ni supprimer, et ne voit pas les données personnelles des adoptants, des parrains, des bénévoles ni des membres.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Travailler avec plusieurs refuges</h3>
        <p>Un utilisateur peut appartenir à plusieurs refuges, avec un rôle différent dans chacun. Utilisez le sélecteur de refuge pour changer de refuge actif ; chaque liste, compteur et formulaire n'affiche alors que les données de ce refuge. Les données ne sont jamais partagées entre refuges.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ordre de configuration recommandé</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Un administrateur remplit les tables de référence (régions, espèces, races, tailles, types de pelage, vaccins, traitements, maladies, activités).</li>
            <li>L'administrateur crée le refuge, complète son profil (coordonnées, région, description, logo) et choisit les espèces qu'il accueille.</li>
            <li>L'administrateur invite le gestionnaire du refuge.</li>
            <li>Le gestionnaire configure les installations, ailes et cages, et invite les employés.</li>
            <li>L'équipe commence à enregistrer les animaux.</li>
            <li>Si le portail public est activé, l'équipe publie les animaux prêts à être adoptés.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">S'orienter dans l'application</h3>
        <p>La barre latérale n'affiche que ce que votre rôle peut utiliser. Les gestionnaires et le personnel voient le menu Animaux (une entrée par espèce activée pour le refuge, plus Parrainages, Adoptions, Vaccinations et Traitements), Bénévoles, Membres et Installations ; les gestionnaires voient aussi Utilisateurs. Les administrateurs voient à la place Utilisateurs et le menu Administration. Les utilisateurs en consultation voient les mêmes menus que les employés, sauf Parrainages, Adoptions, Bénévoles et Membres, et les pages ne leur affichent aucun bouton de création, de modification ou de suppression. Cette documentation est toujours disponible en bas de la barre latérale.</p>
        <p>Le menu Animaux comprend aussi les <strong>Demandes d'Adoption</strong> pour les gestionnaires et le personnel, et seuls les gestionnaires voient les <strong>Rapports</strong>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Modules</h3>
        <p>Un refuge qui n'utilise pas toutes les parties de l'application peut désactiver des modules : <strong>Membres</strong>, <strong>Bénévoles</strong>, <strong>Parrainages</strong>, <strong>Demandes d'adoption</strong>, <strong>Rapports</strong> et <strong>Vaccins et traitements</strong>. Les gestionnaires le font dans <strong>Paramètres &gt; Refuge</strong> et les administrateurs dans le formulaire de modification du refuge, section <strong>Modules</strong>. Un module désactivé disparaît du menu latéral, du tableau de bord et des fiches des animaux, et ses pages ne s'ouvrent plus. Si les demandes d'adoption sont désactivées, la page publique de l'animal n'affiche plus le bouton &laquo; Je veux adopter &raquo;. Rien n'est supprimé : réactiver un module restaure toutes ses données. Les animaux, les adoptions, les diagnostics et les installations sont toujours actifs.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Tableau de Bord</h2>
        <p>Le tableau de bord vous donne une vue d'ensemble du refuge actif :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Des compteurs d'animaux au refuge, de capacité disponible dans les cages et d'adoptions de l'année. Les responsables et le personnel voient aussi les candidatures d'adoption en attente, les vaccins en retard et les cotisations en retard, chacun avec un lien vers sa liste.</li>
            <li>Des avertissements lorsqu'il manque encore quelque chose, comme l'absence de cages ou des espèces sans races.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">À traiter</h3>
        <p>De courtes listes d&rsquo;animaux qui demandent une action. Chacune montre jusqu&rsquo;à cinq animaux et n&rsquo;apparaît que si elle en contient ; <strong>Tout voir</strong> ouvre la liste des animaux avec le filtre correspondant.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Animaux à l&rsquo;Emplacement Inconnu</strong> &mdash; les animaux du refuge sans cage, et depuis combien de temps, pour pouvoir les placer.</li>
            <li><strong>Problèmes de santé en cours</strong> &mdash; les animaux avec un diagnostic actif ou chronique, le diagnostic le plus récent en premier, avec les diagnostics.</li>
            <li><strong>Parrainages à Renouveler</strong> &mdash; les parrainages dont la période payée a pris fin dans les 30 derniers jours ou se termine dans les 30 prochains, avec le nom du parrain, pour pouvoir le contacter. Réservé aux gestionnaires et aux employés.</li>
            <li><strong>Animaux sans Photo</strong> &mdash; sans photo, un animal est mal présenté sur le portail public et ne peut pas être partagé sur les réseaux sociaux.</li>
            <li><strong>Depuis le Plus Longtemps au Refuge</strong> &mdash; les animaux disponibles qui attendent depuis le plus longtemps depuis leur arrivée, avec la durée : de bons candidats à mettre en avant.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Activité récente</h3>
        <p>Les dernières arrivées (avec la date d&rsquo;arrivée et la cage), adoptions (avec la date et le prénom de l&rsquo;adoptant, masqué pour les utilisateurs en consultation) et décès (avec la date).</p>
        <p>La capacité disponible ne compte que les box du refuge lui-même : les ailes de familles d'accueil en sont exclues, tout comme les animaux qui vivent en famille d'accueil.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Tableau de bord de l&rsquo;administrateur</h3>
        <p>Les administrateurs n&rsquo;appartiennent à aucun refuge ; leur tableau de bord montre donc toute la plateforme : des compteurs de refuges, d&rsquo;utilisateurs actifs ces 30 derniers jours, d&rsquo;animaux pris en charge et d&rsquo;adoptions de l&rsquo;année ; un tableau des refuges avec leurs animaux, adoptions, dernière connexion et dernière mise à jour des animaux, les moins utilisés en premier et les connexions anciennes mises en évidence ; <strong>Configuration à terminer</strong> (refuges sans espèces, cages ou utilisateurs, et espèces sans races) ; et <strong>Invitations non acceptées</strong>, les utilisateurs invités qui ne se sont jamais connectés. Il n&rsquo;affiche que des totaux, jamais d&rsquo;animaux ni de données personnelles.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Animaux</h2>
        <p>Le menu Animaux liste les animaux du refuge par espèce. Chaque fiche comprend l'identification (référence, nom, puce), la description physique (race, couleurs, type de pelage, taille, sexe, stérilisation), les dates (naissance, arrivée, sortie, décès), des photos, une description publique, des notes internes et des notes cliniques. Seules les espèces que l'administrateur a activées pour votre refuge y apparaissent.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Statut</h3>
        <p>Le statut d'un animal est calculé automatiquement, vous ne le définissez donc jamais à la main :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Décédé</strong> &mdash; une date de décès est renseignée.</li>
            <li><strong>Adopté</strong> &mdash; l'animal a une adoption sans date de retour.</li>
            <li><strong>Disponible</strong> / <strong>Indisponible</strong> &mdash; dans les autres cas, selon que l'animal est marqué comme adoptable ou non.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Options et emplacement</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Disponible à l'Adoption</strong> &mdash; l'animal peut être adopté ; cela détermine si son statut est disponible ou non disponible.</li>
            <li><strong>Disponible au Parrainage</strong> &mdash; l'animal peut recevoir des parrainages. L'action de parrainer n'est proposée que pour les animaux ayant cette option, et reste disponible même après l'adoption de l'animal.</li>
            <li><strong>Cage</strong> &mdash; la liste des cages est regroupée par installation et par aile et indique le nombre de places libres dans chaque cage, avec un repère vert, jaune ou rouge à mesure qu'elle se remplit.</li>
        </ul>
        <p>Lorsque le portail public est activé, deux options supplémentaires apparaissent : <strong>Publier sur le Portail Public</strong> et <strong>Mis en Avant</strong>. Voir <a href="#public-portal" class="underline underline-offset-2">Portail Public</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Recherche et filtres</h3>
        <p>Recherchez par nom, référence, puce ou notes internes, et filtrez par statut, espèce ou emplacement (installation, aile ou cage). Le dernier filtre trouve les animaux avec des <strong>Problèmes de santé en cours</strong>, selon la stérilisation (<strong>Stérilisé</strong>, <strong>Non stérilisé</strong>, <strong>Stérilisé, détails manquants</strong>) ou avec des données manquantes (sans âge, sans photo, sans date d&rsquo;arrivée ou sans emplacement), ce qui aide à garder les fiches complètes. Les animaux avec un problème de santé en cours ont un cœur à côté de leur nom dans la liste.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Impression</h3>
        <p>Vous pouvez imprimer la fiche d'un animal depuis sa page, ou imprimer la liste des animaux ; la liste imprimée utilise les mêmes filtres que ceux actifs à l'écran.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Partager sur les réseaux sociaux</h3>
        <p>Les animaux adoptables et disponibles ont un bouton de partage en haut de leur page. Il prépare un texte avec les informations de l'animal et les coordonnées du refuge, prêt à copier, et permet de télécharger la photo principale pour publier sur Facebook, Instagram ou WhatsApp. Quand l'animal est publié sur le portail public, le texte inclut un lien vers l'animal, et vous pouvez aussi le partager directement sur Facebook ou WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Santé</h3>
        <p>Enregistrez les diagnostics sur la page de l&rsquo;animal avec <strong>Nouveau diagnostic</strong> : la maladie, la date du diagnostic, le statut (<strong>Active</strong>, <strong>Chronique</strong> ou <strong>Traitée</strong>) et des notes de traitement. Un diagnostic marqué Traitée reçoit une date de guérison (aujourd&rsquo;hui par défaut). Les diagnostics actifs et chroniques sont les problèmes de santé en cours de l&rsquo;animal : ils apparaissent sur sa page, sur le tableau de bord et dans le filtre de la liste des animaux. Les vaccinations et les notes cliniques sont aussi conservées pour chaque animal. Les tailles ne sont proposées que pour les espèces qui en ont.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Stérilisation</h3>
        <p>Avec <strong>Stérilisé / Castré</strong> activé, indiquez la <strong>Date de stérilisation</strong> et qui l&rsquo;a faite (<strong>Le refuge</strong> ou <strong>Avant l&rsquo;arrivée</strong>) ; laissez-les vides si vous ne savez pas. Désactivé, choisissez le <strong>Statut de la stérilisation</strong> (En attente, Prévue avec sa date, ou Déconseillée) et ajoutez des notes. Les nouveaux animaux non stérilisés commencent En attente.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vaccinations</h2>
        <p>Chaque vaccination enregistre le vaccin, la date d'administration, la prochaine date prévue, le numéro de lot, le vétérinaire et des notes. Enregistrez la dernière dose et la prochaine date dans la même fiche : elle reste en attente jusqu'à l'enregistrement d'une dose ultérieure de ce vaccin. Pour planifier une vaccination, renseignez seulement la prochaine date ; enregistrer ensuite la dose la complète. Lorsque le vaccin a une fréquence (par exemple la rage, tous les 36 mois), la prochaine date est remplie à partir de la date de la dose et reste modifiable. La page Vaccinations les liste pour tous les animaux du refuge.</p>
        <p>Chaque jour, les utilisateurs qui ont activé les notifications de vaccination pour un refuge reçoivent un email listant les vaccinations de ce refuge prévues dans les sept prochains jours et toujours en attente. Chaque vaccination n'est notifiée qu'une seule fois. Une icône de cloche dans la liste des utilisateurs indique qui reçoit ces emails.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Plan de vaccination</h3>
        <p>Les refuges qui vaccinent par groupes peuvent ouvrir le <strong>Plan de Vaccination</strong> depuis la page Vaccinations. Pour l'année choisie, il montre, par vaccin, combien de vaccinations en attente des animaux du refuge sont prévues chaque mois ; la première colonne compte celles déjà prévues avant cette année. Cliquez sur un nombre pour afficher la liste des animaux, avec leur puce et leur emplacement, et utilisez <strong>Imprimer la liste pour le vétérinaire</strong> pour l'emporter le jour de la vaccination.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vaccination groupée</h3>
        <p>La <strong>Vaccination Groupée</strong> enregistre le même vaccin pour plusieurs animaux à la fois, par exemple le jour où le vétérinaire vaccine un groupe. Choisissez le vaccin et les animaux à lister : ceux dont le vaccin est prévu un mois donné (par défaut, le mois en cours), ceux en retard, ou tous les animaux du refuge de l'espèce de ce vaccin, et filtrez par espèce ou emplacement si besoin. Les animaux listés sont cochés au départ ; décochez les exceptions. La date, la prochaine date prévue, le numéro de lot, le vétérinaire et les notes sont saisis une seule fois pour tous. Dans le plan de vaccination, le bouton <strong>Vaccination Groupée</strong> à côté de la liste d'un mois ouvre ce formulaire avec ces animaux déjà listés.</p>
    </section>

    <section id="treatments" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Traitements</h2>
        <p>Les traitements sont des soins préventifs périodiques qui ne sont pas des vaccins, comme le déparasitage interne et externe. Chaque traitement enregistre le traitement, la date d'administration, la prochaine date prévue, le produit utilisé, le vétérinaire et des notes. Ils fonctionnent comme les vaccinations : une fiche reste en attente jusqu'à l'enregistrement d'une nouvelle prise du même traitement et, lorsque le traitement a une fréquence (par exemple le déparasitage tous les 3 mois), la prochaine date est remplie à partir de la date d'administration.</p>
        <p>Enregistrez-les sur la page de l'animal avec <strong>Nouveau Traitement</strong>. La page <strong>Traitements</strong>, dans le menu Animaux, les liste pour tous les animaux du refuge, avec une recherche et un filtre par prochaine date prévue ; les dates dépassées apparaissent en rouge et celles des sept prochains jours en orange.</p>
        <p>Le <strong>Traitement Groupé</strong> enregistre une tournée pour de nombreux animaux à la fois : choisissez le traitement et, si vous le souhaitez, une espèce ou un emplacement ; tous les animaux du refuge concernés sont cochés au départ, décochez donc les exceptions et saisissez une seule fois la date, le produit, le vétérinaire et les notes.</p>
        <p>Les utilisateurs qui ont activé les notifications de vaccination reçoivent aussi un email quotidien avec les traitements prévus dans les sept prochains jours, regroupés par traitement et par date, pour qu'une tournée de déparasitage arrive en un seul rappel et non un par animal.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoptions</h2>
        <p>Une adoption enregistre les coordonnées de l'adoptant, la date d'adoption, les frais, des notes et le statut de la demande (En attente, Approuvée ou Refusée). Commencez-la depuis la page de l'animal.</p>
        <p>La page Adoptions (sous Animaux dans la barre latérale) liste toutes les adoptions du refuge ; recherchez par nom, téléphone, email ou notes de l'adoptant, ou par le nom ou la référence de l'animal.</p>
        <p>Si un animal adopté revient au refuge, renseignez la <strong>date de retour</strong> sur l'adoption : l'animal redevient disponible et l'adoption reste dans son historique.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Demandes d'adoption</h3>
        <p>Lorsque le portail public est activé, les visiteurs peuvent envoyer une demande d'adoption depuis la fiche d'un animal avec le bouton <strong>Je veux adopter</strong>. Le formulaire demande leurs coordonnées, le type de logement, la présence d'un jardin, d'enfants ou d'autres animaux, et pourquoi ils veulent adopter.</p>
        <p>Les demandes apparaissent dans <strong>Demandes d'Adoption</strong>, dans le menu Animaux, les demandes en attente d'abord, et la barre latérale indique combien sont en attente. Pour chacune, vous pouvez :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Approuver</strong> &mdash; ouvre le formulaire d'adoption déjà rempli avec les données du demandeur ; en l'enregistrant, l'adoption est créée et la demande est marquée comme approuvée.</li>
            <li><strong>Refuser</strong> &mdash; la marque comme refusée.</li>
            <li><strong>Supprimer</strong> &mdash; la supprime.</li>
        </ul>
        <p>Lorsqu'un animal a été adopté ou n'est plus disponible, ses demandes en attente sont signalées et peuvent être refusées en une seule fois. Les utilisateurs ayant activé les <strong>Notifications de Demandes d'Adoption</strong> reçoivent un email pour chaque nouvelle demande ; le demandeur ne reçoit aucun email, contactez-le donc vous-même. Les demandes sont supprimées automatiquement six mois après leur dernière modification, comme l'indique la politique de confidentialité.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Parrainages</h2>
        <p>Les parrains soutiennent les soins d'un animal sans l'adopter. Un parrainage conserve les coordonnées du parrain et indique s'il souhaite recevoir des nouvelles de l'animal ou la newsletter.</p>
        <p>Les parrainages ne peuvent être créés que pour les animaux marqués <strong>Disponible au Parrainage</strong>. La page Parrainages (sous Animaux dans la barre latérale) les liste tous, avec la même recherche que les adoptions : nom, téléphone, email ou notes du parrain, ou le nom ou la référence de l'animal.</p>
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
        <p>La liste des bénévoles peut être recherchée par nom, téléphone, email, numéro fiscal ou notes, et filtrée par espèce préférée, jour de disponibilité et activité &mdash; pratique pour savoir qui peut aider un jour donné.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Membres</h2>
        <p>Tenez le registre des membres de l'association et de leurs cotisations. Chaque membre a un numéro de membre, des coordonnées personnelles, une date d'adhésion, un statut et ses cotisations, et peut être relié à sa fiche de bénévole lorsqu'il s'agit de la même personne.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Cotisations</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Droit d'entrée</strong> &mdash; payé une seule fois, à l'adhésion. Il peut être de 0, et dans ce cas rien n'est dû.</li>
            <li><strong>Cotisation</strong> &mdash; le montant périodique : Mensuelle, Trimestrielle, Semestrielle ou Annuelle.</li>
        </ul>
        <p>Les gestionnaires définissent les valeurs par défaut du refuge avec le bouton <strong>Cotisations</strong> de la liste des membres. Les nouveaux membres reçoivent ces valeurs, qui peuvent ensuite être modifiées pour chaque membre.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Numéros de membre</h3>
        <p>Laissez le numéro vide pour attribuer automatiquement le suivant, ou saisissez-en un pour garder la numérotation que vous utilisez déjà. Un numéro ne peut être utilisé qu'une fois par refuge, et les numéros des membres supprimés ne sont jamais réutilisés.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Paiements</h3>
        <p>Enregistrez le droit d'entrée ou une cotisation sur la page du membre. Une cotisation est préremplie avec la prochaine période à payer (à partir du lendemain de la dernière période payée, ou de la date d'adhésion) et avec la cotisation du membre. Chaque paiement enregistre aussi la date de paiement, le montant, le mode (Espèces, Virement bancaire, Paiement mobile ou Autre) et des notes.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Cotisations en retard</h3>
        <p>Un membre actif est marqué <strong>Cotisations en retard</strong> quand le droit d'entrée n'est pas payé ou qu'aucune cotisation ne couvre la date du jour ; la marque disparaît dès que le paiement est enregistré. Activez <strong>Cotisations en retard uniquement</strong> dans la liste pour voir à qui envoyer un rappel.</p>
        <p>Le statut (Actif, Suspendu ou Ancien membre) ne change jamais automatiquement : modifiez-le dans le formulaire du membre, selon les règles de l'association. La liste affiche les membres actifs par défaut ; utilisez le filtre Statut pour voir les autres.</p>
        <p>Les gestionnaires et le personnel peuvent ajouter et modifier des membres et enregistrer des paiements ; seuls les gestionnaires peuvent supprimer des membres ou modifier les valeurs par défaut. Les utilisateurs en consultation n'ont pas accès aux membres.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Installations</h2>
        <p>Un refuge est organisé en trois niveaux : les <strong>installations</strong> (sites physiques, avec une adresse) contiennent des <strong>ailes</strong>, et les ailes contiennent des <strong>cages</strong>. Chaque cage a un code et une capacité.</p>
        <p>La capacité totale des cages détermine combien d'animaux le refuge peut accueillir, et c'est aux cages que les animaux sont attribués. Configurez au moins une cage avant d'enregistrer des animaux afin de pouvoir leur attribuer un emplacement.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Familles d'accueil</h3>
        <p>Si votre refuge place des animaux en familles d'accueil, créez une aile pour elles (par exemple dans une installation appelée « Familles d'accueil ») et activez <strong>Aile des familles d'accueil</strong> dans le formulaire de l'aile. Dans cette aile, chaque box est une famille : utilisez le nom de la famille comme nom du box et, comme capacité, le nombre d'animaux qu'elle peut accueillir.</p>
        <p>Vous pouvez choisir un <strong>Contact (bénévole)</strong> pour chaque famille ; la fiche du bénévole contient son téléphone et son adresse. Pour placer un animal dans une famille, choisissez le box de la famille dans le formulaire de l'animal, comme pour tout autre box.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>La page de l'animal affiche <strong>Famille d'accueil</strong> avec le nom de la famille et, pour les gestionnaires et le personnel, le nom et le téléphone du contact. Les utilisateurs en <strong>Consultation</strong> voient le nom de la famille mais pas le contact.</li>
            <li>La page du bénévole liste les animaux actuellement dans sa famille.</li>
            <li>Les familles d'accueil ne comptent pas dans la capacité du refuge sur le tableau de bord ni dans le rapport d'Occupation, qui compte à part les animaux en familles d'accueil.</li>
            <li>Sur le portail public, l'animal affiche le badge <strong>En famille d'accueil</strong> ; la famille n'est jamais affichée publiquement.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Rapports</h2>
        <p>Seuls les gestionnaires voient les Rapports. Choisissez la période en haut (12 derniers mois, une année, depuis toujours ou vos propres dates) ; les périodes de deux ans ou plus sont affichées par année au lieu de par mois. Les rapports sont répartis en quatre onglets :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Animaux</strong> &mdash; entrées, adoptions, retours et décès ; le nombre d'animaux au refuge dans le temps ; entrées et adoptions par espèce ; adoptions par âge et nombre médian de jours avant l'adoption ; et les animaux disponibles qui attendent depuis le plus longtemps.</li>
            <li><strong>Finances</strong> &mdash; recettes par origine (parrainages, cotisations, droits d'adhésion et frais d'adoption), comptées par date de paiement ; parrainages actifs dans le temps et leur valeur mensuelle ; membres actifs, nouveaux et en retard de cotisation, avec les cotisations attendues et perçues ; paiements des membres par moyen ; et les parrainages dont la période payée se termine dans les 30 prochains jours.</li>
            <li><strong>Occupation</strong> &mdash; l'occupation du jour, la capacité et les animaux en box, sans emplacement connu et en familles d'accueil ; l'occupation dans le temps et par aile. La capacité n'est qu'indicative, il n'y a donc pas d'alerte de dépassement, et les mois passés sont comparés à la capacité actuelle.</li>
            <li><strong>Santé</strong> &mdash; vaccinations effectuées (par mois et par vaccin), vaccins en retard, diagnostics par maladie, cas en cours, les stérilisations effectuées par le refuge sur la période (celles qui ont une date et ont été faites par le refuge) et la part d&rsquo;animaux stérilisés au refuge.</li>
        </ul>
        <p>Survolez un graphique pour voir ses valeurs, ou ouvrez <strong>Afficher le tableau</strong> en dessous. Le bouton <strong>imprimer</strong> ouvre l'onglet actuel sous forme de rapport avec les coordonnées du refuge &mdash; par exemple le rapport d'activité annuel pour l'assemblée générale &mdash; prêt à imprimer ou à enregistrer en PDF depuis le navigateur.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Utilisateurs</h2>
        <p>Les gestionnaires et les administrateurs invitent de nouveaux utilisateurs par email ; la personne invitée reçoit un lien pour définir son mot de passe. Pour chaque refuge auquel elle appartient, vous choisissez le rôle (gestionnaire, employé ou consultation) et si l'utilisateur reçoit les notifications de vaccination (qui couvrent aussi les traitements).</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Un gestionnaire ne peut ajouter des utilisateurs qu'aux refuges qu'il gère.</li>
            <li>Un administrateur peut ajouter des utilisateurs à n'importe quel refuge et peut créer d'autres administrateurs.</li>
        </ul>
        <p>Chaque rattachement à un refuge peut aussi activer les <strong>Notifications de Demandes d'Adoption</strong> : ces utilisateurs reçoivent un email pour chaque nouvelle demande d'adoption.</p>
        <p>Tant que la personne invitée ne s'est pas connectée une première fois, la liste des utilisateurs affiche un bouton <strong>Renvoyer l'invitation</strong> sur sa ligne. Il envoie par email un nouveau lien et annule le précédent ; le lien expire au bout de 48 heures.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Portail Public</h2>
        <p>Une installation peut, en option, proposer un site public à côté du backoffice. Il est activé par la personne qui gère le serveur ; lorsqu'il est désactivé, la page d'accueil renvoie les visiteurs vers la page de connexion et les options ci-dessous sont masquées.</p>
        <p>Lorsqu'il est activé, n'importe qui (sans connexion) peut :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Parcourir les animaux de tous les refuges prêts à être adoptés, filtrés par espèce, sexe, taille, race et région.</li>
            <li>Ouvrir la fiche d'un animal pour voir ses photos, sa description publique et le refuge qui l'accueille.</li>
            <li>Consulter la liste des refuges partenaires, chacun avec sa propre page présentant ses coordonnées, sa description, son logo et ses animaux.</li>
            <li>Ouvrir le lien d'un animal partagé depuis le backoffice : il ouvre directement la fiche de cet animal, et les aperçus sur les réseaux sociaux affichent son nom, sa photo et sa description.</li>
        </ul>
        <p>Depuis la fiche d'un animal, les visiteurs peuvent aussi envoyer une demande d'adoption avec le bouton <strong>Je veux adopter</strong> (voir <a href="#adoptions" class="underline underline-offset-2">Adoptions</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ce qui est affiché publiquement</h3>
        <p>Un animal n'apparaît sur le portail que si <strong>toutes</strong> ces conditions sont réunies :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publier sur le Portail Public</strong> est activé (désactivé par défaut, pour que rien ne soit publié par erreur).</li>
            <li>L'animal est disponible à l'adoption (les animaux adoptés ou décédés disparaissent automatiquement).</li>
            <li>Son refuge n'a pas été supprimé.</li>
        </ul>
        <p>Les animaux marqués <strong>Mis en Avant</strong> sont affichés en premier, avec un badge « À la une ». Seuls le nom, la référence, les photos, la description publique et les informations descriptives (espèce, race, taille, sexe, âge, type de pelage, stérilisation) sont affichés &mdash; les notes internes, notes cliniques, puce électronique et cage ne sont jamais publiées.</p>
        <p>Les animaux qui vivent en famille d'accueil affichent le badge <strong>En famille d'accueil</strong> ; le nom et les coordonnées de la famille ne sont jamais publiés.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Conseils pour de bonnes annonces</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Ajoutez au moins une belle photo et une description publique chaleureuse &mdash; c'est ce que les adoptants voient en premier.</li>
            <li>Tenez à jour le profil du refuge (coordonnées, description, logo) : il apparaît sur la page publique du refuge, dans les résultats des moteurs de recherche et dans les aperçus de liens.</li>
            <li>Les pages publiques sont optimisées pour les moteurs de recherche (Google et autres) et un sitemap est généré automatiquement ; le backoffice n'est jamais indexé.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administration</h2>
        <p>Seuls les administrateurs voient ce menu. Il permet de gérer les refuges et les tables de référence partagées par tous les refuges :</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Refuges</strong> &mdash; créer, modifier et supprimer des refuges. En plus du nom et de la ville, un refuge a un nom court, des coordonnées (email, téléphone, site web), une adresse, une région, une description et un logo &mdash; utilisés sur le portail public lorsqu'il est activé. La liste des <strong>Espèces</strong> du formulaire du refuge détermine quelles espèces apparaissent dans le menu Animaux pour le gestionnaire et le personnel de ce refuge. L'email est obligatoire. Une fois que le refuge a un gestionnaire, celui-ci peut tout mettre à jour sauf le nom et les espèces dans <strong>Paramètres &gt; Refuge</strong>.</li>
            <li><strong>Régions</strong> &mdash; les régions auxquelles appartiennent les refuges, également utilisées comme filtre sur le portail public.</li>
            <li><strong>Espèces</strong>, <strong>Races</strong>, <strong>Tailles</strong> et <strong>Types de Pelage</strong> &mdash; les options utilisées pour décrire les animaux.</li>
            <li><strong>Vaccins</strong>, <strong>Traitements</strong> et <strong>Maladies</strong> &mdash; les options utilisées dans les dossiers de santé des animaux. Les vaccins et les traitements indiquent les espèces concernées et, en option, une fréquence en mois, utilisée pour remplir la prochaine date prévue.</li>
            <li><strong>Activités</strong> &mdash; les tâches auxquelles les bénévoles peuvent participer.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Paramètres</h2>
        <p>Depuis le menu utilisateur, ouvrez Paramètres pour consulter votre profil, changer votre mot de passe et choisir l'apparence (claire, sombre ou système) ainsi que votre langue. Votre nom ne peut être modifié que par un administrateur ou un gestionnaire, et votre email ne peut pas être modifié. La langue est enregistrée dans votre compte et utilisée aussi pour les emails que vous recevez. Sur les pages publiques et la page de connexion, chacun peut changer de langue dans le menu en haut. Les gestionnaires voient aussi un onglet <strong>Refuge</strong> pour mettre à jour les coordonnées, l'adresse, la région, la description et le logo du refuge actif ; l'email est obligatoire, et le nom et les espèces ne peuvent être modifiés que par un administrateur.</p>
        <p>Les gestionnaires peuvent télécharger une copie de toutes les données de leur refuge sous <strong>Paramètres &gt; Exporter les données</strong> : un ZIP avec un fichier CSV par domaine (animaux, vaccins, traitements, diagnostics, adoptions, demandes d'adoption, parrainages et paiements, membres et paiements, bénévoles et espaces). Utile pour vos propres sauvegardes ou pour passer à un autre système. Les fichiers s'ouvrent directement dans Excel. Ils contiennent des données personnelles de membres, bénévoles, adoptants et parrains : conservez-les en lieu sûr et ne les partagez pas. Chaque téléchargement est enregistré (qui, quand et depuis quelle adresse IP) et les dix derniers sont listés sur la même page.</p>
    </section>
</div>
