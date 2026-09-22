<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Instruções da Aplicação</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Um guia sobre as principais áreas da aplicação {{ config('app.name') }} e como usá-las no dia a dia.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Primeiros Passos</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Painel de Controlo</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Animais</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vacinações</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adoções</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Apadrinhamentos</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Voluntários</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Instalações</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Utilizadores</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administração</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Definições</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Primeiros Passos</h2>
        <p>Na primeira utilização, a aplicação pede a criação da conta de administrador inicial. A partir daí, as novas contas são criadas apenas por convite.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Perfis</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrador</strong> &mdash; gere toda a plataforma: abrigos, tabelas de referência partilhadas e contas de utilizador. Os administradores não gerem animais nem instalações.</li>
            <li><strong>Gestor</strong> &mdash; gere um abrigo: tudo o que um funcionário pode fazer, mais convidar e gerir os utilizadores desse abrigo.</li>
            <li><strong>Funcionário</strong> &mdash; trata do trabalho diário do abrigo: animais, vacinações, adoções, apadrinhamentos, voluntários e instalações.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Trabalhar com vários abrigos</h3>
        <p>Um utilizador pode pertencer a mais do que um abrigo, com um perfil diferente em cada um. Use o seletor de abrigo para mudar o abrigo ativo; todas as listas, contagens e formulários passam a mostrar apenas os dados desse abrigo. Os dados nunca são partilhados entre abrigos.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ordem de configuração recomendada</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Um administrador cria o abrigo e preenche as tabelas de referência (espécies, raças, tamanhos, tipos de pelo, vacinas, doenças, atividades).</li>
            <li>O administrador convida o gestor do abrigo.</li>
            <li>O gestor configura as instalações, alas e jaulas, e convida os funcionários.</li>
            <li>A equipa começa a registar os animais.</li>
        </ol>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Painel de Controlo</h2>
        <p>O painel de controlo dá-lhe uma visão geral do abrigo ativo:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Contadores de animais ativos, adoções, capacidade disponível nas jaulas e funcionários.</li>
            <li>As entradas, adoções, apadrinhamentos e óbitos mais recentes.</li>
            <li>Animais sem localização conhecida, para que possam ser atribuídos a uma jaula.</li>
            <li>Avisos quando algo ainda falta, como não haver jaulas definidas ou espécies sem raças.</li>
        </ul>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Animais</h2>
        <p>O menu Animais lista os animais do abrigo por espécie. Cada ficha inclui identificação (referência, nome, microchip), descrição física (raça, cores, tipo de pelo, tamanho, sexo, esterilização), datas (nascimento, entrada, saída, óbito), fotografias, uma descrição pública, notas internas e notas clínicas.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Estado</h3>
        <p>O estado de um animal é calculado automaticamente, por isso nunca é definido à mão:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Falecido</strong> &mdash; a data de óbito está preenchida.</li>
            <li><strong>Adotado</strong> &mdash; o animal tem uma adoção sem data de devolução.</li>
            <li><strong>Disponível</strong> / <strong>Não disponível</strong> &mdash; nos restantes casos, conforme o animal esteja marcado como adotável.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Pesquisa e filtros</h3>
        <p>Pesquise por nome, referência, microchip ou notas internas, e filtre por estado, espécie ou localização (instalação, ala ou jaula). O filtro de <em>dados em falta</em> encontra animais sem idade, sem fotografia, sem data de entrada ou sem localização, o que ajuda a manter as fichas completas.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Impressão</h3>
        <p>Pode imprimir a ficha de um animal a partir da sua página, ou imprimir a lista de animais; a lista impressa usa os mesmos filtros que estão ativos no ecrã.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Saúde</h3>
        <p>Registe doenças (com data de diagnóstico, estado e notas de tratamento), vacinações e notas clínicas em cada animal. Os tamanhos só são apresentados para espécies que tenham tamanhos configurados.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vacinações</h2>
        <p>Cada vacinação regista a vacina, a data em que foi administrada ou em que está prevista, o número de lote, o veterinário e notas. A página Vacinações lista-as para todos os animais do abrigo.</p>
        <p>Todos os dias, os utilizadores com as notificações de vacinação ativas num abrigo recebem um email com as vacinações desse abrigo previstas para os próximos sete dias e ainda não administradas. Cada vacinação é notificada apenas uma vez. Um ícone de sino na lista de utilizadores indica quem recebe estes emails.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adoções</h2>
        <p>Uma adoção regista os contactos do adotante, a data de adoção, o valor, notas e o estado da candidatura (Pendente, Aprovada ou Rejeitada). Inicie-a a partir da página do animal.</p>
        <p>Se um animal adotado regressar ao abrigo, preencha a <strong>data de devolução</strong> na adoção: o animal volta a ficar disponível e a adoção fica no seu histórico.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Apadrinhamentos</h2>
        <p>Os padrinhos apoiam os cuidados de um animal sem o adotar. Um apadrinhamento guarda os contactos do padrinho e se este pretende receber notícias do animal ou a newsletter.</p>
        <p>Cada apadrinhamento tem uma lista de pagamentos. Um pagamento regista o período que cobre (datas de início e fim), a data de pagamento e o valor, suportando assim contribuições pontuais e recorrentes.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Voluntários</h2>
        <p>Mantenha um registo das pessoas que ajudam o seu abrigo, de forma independente das contas de utilizador. Para cada voluntário pode guardar dados pessoais e de contacto, uma fotografia, datas de início e fim, meio de transporte e preferência de newsletter, e ainda:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>As atividades em que ajuda e as espécies com que prefere trabalhar.</li>
            <li>A disponibilidade por dia da semana (manhãs e/ou tardes, ocasionalmente, quinzenalmente ou semanalmente).</li>
            <li>Avaliações de assiduidade e de desempenho.</li>
        </ul>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Instalações</h2>
        <p>Um abrigo está organizado em três níveis: as <strong>instalações</strong> (locais físicos, com morada) contêm <strong>alas</strong>, e as alas contêm <strong>jaulas</strong>. Cada jaula tem um código e uma capacidade.</p>
        <p>A capacidade total das jaulas determina quantos animais o abrigo pode acolher, e é nas jaulas que os animais são colocados. Configure pelo menos uma jaula antes de registar animais, para lhes poder atribuir uma localização.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Utilizadores</h2>
        <p>Gestores e administradores convidam novos utilizadores por email; a pessoa convidada recebe uma ligação para definir a sua palavra-passe. Para cada abrigo a que o utilizador pertence escolhe-se o perfil (gestor ou funcionário) e se recebe notificações de vacinação.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Um gestor só pode adicionar utilizadores aos abrigos que gere.</li>
            <li>Um administrador pode adicionar utilizadores a qualquer abrigo e criar outros administradores.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administração</h2>
        <p>Apenas os administradores veem este menu. Nele mantêm-se os abrigos e as tabelas de referência partilhadas por todos os abrigos:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Abrigos</strong> &mdash; criar, editar e remover abrigos.</li>
            <li><strong>Espécies</strong>, <strong>Raças</strong>, <strong>Tamanhos</strong> e <strong>Tipos de Pelo</strong> &mdash; as opções usadas para descrever os animais.</li>
            <li><strong>Vacinas</strong> e <strong>Doenças</strong> &mdash; as opções usadas nos registos de saúde.</li>
            <li><strong>Atividades</strong> &mdash; as tarefas em que os voluntários podem ajudar.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Definições</h2>
        <p>No menu do utilizador, abra Definições para ver o seu perfil, alterar a palavra-passe e escolher a aparência (clara, escura ou do sistema). O nome só pode ser alterado por um administrador ou gestor, e o email não pode ser alterado.</p>
    </section>
</div>
