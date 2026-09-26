<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Politique de Confidentialité</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Dernière mise à jour : 26 septembre 2026</p>
        <p>{{ config('app.name') }} est une plateforme qui réunit plusieurs refuges pour animaux. Nous prenons au sérieux la protection de vos données personnelles et les traitons conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi n° 78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés (loi Informatique et Libertés). Cette politique explique quelles données nous traitons, pourquoi, pendant combien de temps et quels sont vos droits.</p>
    </header>

    <section id="responsible">
        <h2>1. Qui est responsable de vos données</h2>
        <p>Chaque refuge qui utilise la plateforme est <strong>responsable du traitement</strong> des données qu'il collecte dans le cadre de son activité (par exemple, les données des adoptants, des parrains et des bénévoles). {{ config('app.name') }} fournit la plateforme technologique et agit en tant que <strong>sous-traitant</strong> pour ces refuges, en ne traitant les données que sur leurs instructions.</p>
        <p>{{ config('app.name') }} est responsable du traitement des données relatives aux comptes utilisateurs de la plateforme et aux visiteurs de la page publique.</p>
    </section>

    <section id="data">
        <h2>2. Quelles données nous traitons</h2>

        <h3>Visiteurs de la page publique</h3>
        <p>Vous pouvez consulter les animaux à l'adoption sans créer de compte. Nous ne traitons que les données techniques nécessaires au fonctionnement du site : adresse IP, type de navigateur et d'appareil, et un cookie de session. Si vous envoyez une demande d'adoption, nous traitons également les données décrites ci-dessous.</p>

        <h3>Utilisateurs de la plateforme (équipes des refuges)</h3>
        <ul>
            <li>Nom et adresse email ;</li>
            <li>Mot de passe (stocké uniquement sous forme hachée, jamais en clair) ;</li>
            <li>Le ou les refuges auxquels vous appartenez et votre rôle (administrateur, gestionnaire ou employé) ;</li>
            <li>Préférences de notification par e-mail (vaccins à échéance et nouvelles demandes d'adoption) et langue préférée ;</li>
            <li>Date de dernière connexion et données de session (adresse IP et navigateur).</li>
        </ul>

        <h3>Demandeurs d'adoption</h3>
        <ul>
            <li>Nom, e-mail, téléphone, code postal et ville ;</li>
            <li>L'animal concerné par votre demande, vos réponses sur votre logement (type de logement, jardin, enfants et autres animaux) et votre motivation ;</li>
            <li>La date de votre consentement et l'adresse IP depuis laquelle la demande a été envoyée, conservée uniquement pour protéger le formulaire contre les abus.</li>
        </ul>
        <p>La demande n'est transmise qu'au refuge qui prend soin de cet animal, qui l'utilise pour évaluer l'adoption. Si elle est acceptée, vos coordonnées sont reprises dans le dossier d'adoption.</p>

        <h3>Adoptants</h3>
        <ul>
            <li>Nom, email, téléphone, adresse, code postal et ville ;</li>
            <li>L'animal adopté, la date d'adoption et, le cas échéant, la date de retour ;</li>
            <li>Frais d'adoption et notes enregistrées par le refuge.</li>
        </ul>

        <h3>Parrains</h3>
        <ul>
            <li>Nom, email, téléphone, adresse, code postal et ville ;</li>
            <li>L'animal parrainé et l'historique des paiements (dates, périodes et montants) ;</li>
            <li>Préférences de communication (nouvelles de l'animal et/ou newsletter).</li>
        </ul>

        <h3>Bénévoles</h3>
        <ul>
            <li>Nom, sexe, date de naissance et photo ;</li>
            <li>Numéro de pièce d'identité et numéro fiscal de référence ;</li>
            <li>Coordonnées, adresse, profession et moyen de transport ;</li>
            <li>Disponibilité, dates de début et de fin de la collaboration, et évaluations d'assiduité et de performance ;</li>
            <li>S'ils accueillent des animaux en tant que famille d'accueil, et quels animaux sont sous leur garde ;</li>
            <li>Préférence de newsletter.</li>
        </ul>

        <h3>Membres</h3>
        <ul>
            <li>Nom, numéro d'identification fiscale, e-mail, téléphone, adresse, code postal et ville ;</li>
            <li>Numéro de membre, date d'adhésion, statut (actif, suspendu ou parti) et éventuel lien avec sa fiche de bénévole ;</li>
            <li>Droit d'entrée et cotisations, historique des paiements (dates, périodes couvertes, montants et moyen de paiement) et notes enregistrées par le refuge.</li>
        </ul>

        <p>La page publique n'affiche que des informations sur les animaux (photos, caractéristiques et description) et les coordonnées du refuge. Elle ne publie <strong>jamais</strong> de données sur les adoptants, parrains, bénévoles ou utilisateurs. Les animaux vivant dans une famille d'accueil sont seulement signalés comme tels, sans aucune information sur la famille. Les mêmes informations sur les animaux sont publiées dans le fil d'actualités (RSS) de chaque refuge et peuvent être partagées par le refuge sur les réseaux sociaux.</p>
    </section>

    <section id="purposes">
        <h2>3. Pourquoi nous utilisons les données et sur quelle base juridique</h2>
        <ul>
            <li><strong>Gérer les adoptions, parrainages et le bénévolat</strong> &mdash; exécution de l'accord conclu avec vous ou mesures précontractuelles prises à votre demande (art. 6, par. 1, point b) du RGPD) ;</li>
            <li><strong>Gérer les membres et leurs cotisations</strong> &mdash; exécution de l'accord conclu avec vous ou mesures précontractuelles prises à votre demande (art. 6, par. 1, point b) du RGPD) ;</li>
            <li><strong>Évaluer les demandes d'adoption</strong> &mdash; mesures précontractuelles prises à votre demande (art. 6, par. 1, point b) du RGPD), avec le consentement que vous donnez dans le formulaire ; l'adresse IP est conservée pour protéger le formulaire contre les abus &mdash; intérêt légitime (art. 6, par. 1, point f)) ;</li>
            <li><strong>Suivre le bien-être des animaux après l'adoption</strong> &mdash; intérêt légitime du refuge à la protection animale (art. 6, par. 1, point f)) ;</li>
            <li><strong>Respecter les obligations légales</strong>, telles que les règles fiscales et l'enregistrement et l'identification des animaux de compagnie (art. 6, par. 1, point c)) ;</li>
            <li><strong>Envoyer des newsletters et des nouvelles d'un animal parrainé</strong> &mdash; votre consentement, que vous pouvez retirer à tout moment (art. 6, par. 1, point a)) ;</li>
            <li><strong>Assurer la sécurité et le fonctionnement de la plateforme</strong>, y compris le contrôle d'accès et les journaux techniques &mdash; intérêt légitime (art. 6, par. 1, point f)).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookies</h2>
        <p>Nous n'utilisons que des cookies et un stockage local <strong>strictement nécessaires</strong> au fonctionnement du site ou à une fonctionnalité que vous avez expressément demandée ; en vertu de l'article 82 de la loi Informatique et Libertés, votre consentement n'est donc pas requis. Nous n'utilisons aucun cookie publicitaire, analytique ou tiers, et toutes les polices et ressources sont servies depuis nos propres serveurs.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Nom</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Finalité</th>
                        <th class="px-4 py-3">Durée</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Maintient votre session pendant votre navigation sur le site.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutes d'inactivité</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Protège les formulaires contre les requêtes falsifiées provenant d'autres sites.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutes d'inactivité</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Vous garde connecté, uniquement si vous cochez &laquo;&nbsp;Se souvenir de moi&nbsp;&raquo; lors de la connexion.</td>
                        <td class="px-4 py-3">400 jours ou jusqu'à la déconnexion</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Stockage local</td>
                        <td class="px-4 py-3">Enregistre votre préférence de thème clair ou sombre, uniquement si vous en choisissez un dans les paramètres.</td>
                        <td class="px-4 py-3">Jusqu'à ce que vous l'effaciez dans votre navigateur</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Vous pouvez supprimer ou bloquer les cookies dans les paramètres de votre navigateur ; si vous bloquez les cookies de session, vous ne pourrez pas vous connecter à l'espace des refuges.</p>
    </section>

    <section id="sharing">
        <h2>5. Avec qui nous partageons les données</h2>
        <p>Nous ne vendons ni ne cédons vos données à des fins commerciales. Les données de chaque refuge ne sont accessibles qu'à l'équipe de ce refuge et aux administrateurs de la plateforme. Elles peuvent également être traitées par des prestataires qui nous aident à exploiter la plateforme (hébergement et envoi d'emails), toujours avec des garanties contractuelles appropriées, ou communiquées aux autorités lorsque la loi l'exige.</p>
    </section>

    <section id="retention">
        <h2>6. Combien de temps nous conservons les données</h2>
        <ul>
            <li><strong>Comptes utilisateurs :</strong> tant que le compte est actif ;</li>
            <li><strong>Demandes d'adoption :</strong> supprimées automatiquement 6 mois après leur dernière modification ;</li>
            <li><strong>Adoptions et parrainages :</strong> aussi longtemps que nécessaire pour le suivi de l'animal et le respect des obligations légales applicables ;</li>
            <li><strong>Membres :</strong> pendant toute la durée de l'adhésion puis, ensuite, uniquement pendant la durée exigée par la loi (par exemple, pour les registres de cotisations) ;</li>
            <li><strong>Bénévoles :</strong> pendant la collaboration puis, ensuite, uniquement pendant la durée exigée par la loi ;</li>
            <li><strong>Sessions :</strong> expirent automatiquement après une période d'inactivité.</li>
        </ul>
        <p>Les enregistrements supprimés peuvent être conservés pendant une période limitée, hors de l'accès normal, à des fins d'audit et de récupération en cas d'erreur.</p>
    </section>

    <section id="rights">
        <h2>7. Vos droits</h2>
        <p>Vous pouvez à tout moment demander l'<strong>accès</strong> à vos données, leur <strong>rectification</strong> ou leur <strong>effacement</strong>, la <strong>limitation</strong> du traitement ou la <strong>portabilité</strong> des données, vous <strong>opposer</strong> au traitement fondé sur l'intérêt légitime et <strong>retirer tout consentement</strong> donné, sans que cela n'affecte le traitement effectué auparavant.</p>
        <p>Pour exercer ces droits, contactez directement le refuge avec lequel vous avez été en relation (ses coordonnées figurent sur la page de chaque animal)@if ($contactEmail), ou écrivez-nous à <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Nous vous répondrons dans un délai d'un mois au maximum.</p>
        <p>Vous avez également le droit d'introduire une réclamation auprès de la Commission nationale de l'informatique et des libertés (CNIL) sur <a href="https://www.cnil.fr" target="_blank" rel="noopener">www.cnil.fr</a>.</p>
    </section>

    <section id="security">
        <h2>8. Sécurité</h2>
        <p>Les mots de passe sont stockés sous forme hachée, l'accès se fait uniquement sur invitation et chaque utilisateur ne peut voir que les données du refuge auquel il appartient. Nous appliquons des mesures techniques et organisationnelles pour protéger les données contre tout accès non autorisé, perte ou altération.</p>
    </section>

    <section id="changes">
        <h2>9. Modifications de cette politique</h2>
        <p>Nous pouvons mettre à jour cette politique pour refléter des changements de la plateforme ou de la loi. La date de la dernière mise à jour est toujours indiquée en haut de cette page.</p>
    </section>
</div>
