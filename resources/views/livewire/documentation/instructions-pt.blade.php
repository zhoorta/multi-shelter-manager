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
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Sócios</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Instalações</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Relatórios</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Utilizadores</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Portal Público</a>
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
            <li><strong>Consulta</strong> &mdash; acesso só de leitura ao abrigo: pode ver animais, vacinações e instalações e imprimir fichas e listas de animais, mas não pode criar, editar nem apagar nada, e não vê os dados pessoais de adotantes, padrinhos, voluntários nem sócios.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Trabalhar com vários abrigos</h3>
        <p>Um utilizador pode pertencer a mais do que um abrigo, com um perfil diferente em cada um. Use o seletor de abrigo para mudar o abrigo ativo; todas as listas, contagens e formulários passam a mostrar apenas os dados desse abrigo. Os dados nunca são partilhados entre abrigos.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Ordem de configuração recomendada</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Um administrador preenche as tabelas de referência (distritos, espécies, raças, tamanhos, tipos de pelo, vacinas, doenças, atividades).</li>
            <li>O administrador cria o abrigo, completa o seu perfil (contactos, distrito, descrição, logótipo) e escolhe as espécies com que trabalha.</li>
            <li>O administrador convida o gestor do abrigo.</li>
            <li>O gestor configura as instalações, alas e jaulas, e convida os funcionários.</li>
            <li>A equipa começa a registar os animais.</li>
            <li>Se o portal público estiver ativo, a equipa publica os animais que estão prontos para adoção.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orientar-se na aplicação</h3>
        <p>O menu lateral mostra apenas o que o seu perfil pode usar. Gestores e funcionários veem o menu Animais (uma entrada por cada espécie ativa no abrigo, mais Apadrinhamentos, Adoções e Vacinações), Voluntários, Sócios e Instalações; os gestores veem também Utilizadores. Os administradores veem, em vez disso, Utilizadores e o menu Administração. Os utilizadores de consulta veem os mesmos menus que os funcionários, exceto Apadrinhamentos, Adoções, Voluntários e Sócios, e as páginas não lhes mostram botões para criar, editar ou apagar. Esta documentação está sempre disponível no fundo do menu lateral.</p>
        <p>O menu Animais inclui também <strong>Candidaturas de Adoção</strong> para gestores e funcionários, e só os gestores veem os <strong>Relatórios</strong>.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Painel de Controlo</h2>
        <p>O painel de controlo dá-lhe uma visão geral do abrigo ativo:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Contadores de animais no abrigo, capacidade disponível nas jaulas e adoções deste ano. Gestores e funcionários veem também as candidaturas de adoção pendentes, as vacinas em atraso e as quotas em atraso, cada uma com ligação para a respetiva lista.</li>
            <li>As entradas, adoções, apadrinhamentos e óbitos mais recentes.</li>
            <li>Animais sem localização conhecida, para que possam ser atribuídos a uma jaula.</li>
            <li>Avisos quando algo ainda falta, como não haver jaulas definidas ou espécies sem raças.</li>
        </ul>
        <p>A capacidade disponível conta apenas as jaulas do próprio abrigo: as alas de famílias de acolhimento ficam de fora, tal como os animais que vivem com famílias de acolhimento.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Animais</h2>
        <p>O menu Animais lista os animais do abrigo por espécie. Cada ficha inclui identificação (referência, nome, microchip), descrição física (raça, cores, tipo de pelo, tamanho, sexo, esterilização), datas (nascimento, entrada, saída, óbito), fotografias, uma descrição pública, notas internas e notas clínicas. Só aparecem as espécies que o administrador ativou para o seu abrigo.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Estado</h3>
        <p>O estado de um animal é calculado automaticamente, por isso nunca é definido à mão:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Falecido</strong> &mdash; a data de óbito está preenchida.</li>
            <li><strong>Adotado</strong> &mdash; o animal tem uma adoção sem data de devolução.</li>
            <li><strong>Disponível</strong> / <strong>Não disponível</strong> &mdash; nos restantes casos, conforme o animal esteja marcado como adotável.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Opções e localização</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Disponível para Adoção</strong> &mdash; o animal pode ser adotado; determina se o estado é disponível ou não disponível.</li>
            <li><strong>Disponível para Apadrinhamento</strong> &mdash; o animal pode receber apadrinhamentos. A ação de apadrinhar só é oferecida para animais com esta opção ativa, e continua disponível mesmo depois de o animal ser adotado.</li>
            <li><strong>Jaula</strong> &mdash; a lista de jaulas está agrupada por instalação e ala e mostra quantos lugares livres tem cada jaula, com um marcador verde, amarelo ou vermelho à medida que enche.</li>
        </ul>
        <p>Quando o portal público está ativo, aparecem mais duas opções: <strong>Publicar no Portal Público</strong> e <strong>Destaque</strong>. Veja <a href="#public-portal" class="underline underline-offset-2">Portal Público</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Pesquisa e filtros</h3>
        <p>Pesquise por nome, referência, microchip ou notas internas, e filtre por estado, espécie ou localização (instalação, ala ou jaula). O filtro de <em>dados em falta</em> encontra animais sem idade, sem fotografia, sem data de entrada ou sem localização, o que ajuda a manter as fichas completas.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Impressão</h3>
        <p>Pode imprimir a ficha de um animal a partir da sua página, ou imprimir a lista de animais; a lista impressa usa os mesmos filtros que estão ativos no ecrã.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Partilhar nas redes sociais</h3>
        <p>Os animais adotáveis e disponíveis têm um botão de partilha no topo da sua página. Prepara um texto com os dados do animal e os contactos do abrigo, pronto a copiar, e permite descarregar a fotografia principal para publicar no Facebook, Instagram ou WhatsApp. Quando o animal está publicado no portal público, o texto inclui uma ligação para o animal, e também pode partilhá-lo diretamente no Facebook ou no WhatsApp.</p>
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
        <p>A página Adoções (em Animais, no menu lateral) lista todas as adoções do abrigo; pesquise pelo nome, telefone, email ou notas do adotante, ou pelo nome ou referência do animal.</p>
        <p>Se um animal adotado regressar ao abrigo, preencha a <strong>data de devolução</strong> na adoção: o animal volta a ficar disponível e a adoção fica no seu histórico.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Candidaturas de adoção</h3>
        <p>Com o portal público ativo, os visitantes podem enviar uma candidatura de adoção a partir da ficha do animal, com o botão <strong>Quero adotar</strong>. O formulário pede os contactos, o tipo de casa, se há quintal, crianças ou outros animais, e porque querem adotar.</p>
        <p>As candidaturas aparecem em <strong>Candidaturas de Adoção</strong>, no menu Animais, primeiro as pendentes, e a barra lateral mostra quantas estão pendentes. Em cada uma pode:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Aprovar</strong> &mdash; abre o formulário de adoção já preenchido com os dados do candidato; ao guardar, a adoção fica registada e a candidatura é marcada como aprovada.</li>
            <li><strong>Rejeitar</strong> &mdash; marca-a como rejeitada.</li>
            <li><strong>Eliminar</strong> &mdash; apaga-a.</li>
        </ul>
        <p>Quando um animal já foi adotado ou deixou de estar disponível, as suas candidaturas pendentes são assinaladas e podem ser rejeitadas de uma só vez. Os utilizadores com as <strong>Notificações de Candidaturas de Adoção</strong> ativas recebem um email por cada nova candidatura; o candidato não recebe email, por isso contacte-o diretamente. As candidaturas são apagadas automaticamente seis meses após a última alteração, como indicado na política de privacidade.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Apadrinhamentos</h2>
        <p>Os padrinhos apoiam os cuidados de um animal sem o adotar. Um apadrinhamento guarda os contactos do padrinho e se este pretende receber notícias do animal ou a newsletter.</p>
        <p>Só é possível criar apadrinhamentos para animais marcados como <strong>Disponível para Apadrinhamento</strong>. A página Apadrinhamentos (em Animais, no menu lateral) lista-os todos, com a mesma pesquisa das adoções: nome, telefone, email ou notas do padrinho, ou o nome ou referência do animal.</p>
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
        <p>A lista de voluntários pode ser pesquisada por nome, telefone, email, NIF ou notas, e filtrada por espécie preferida, dia de disponibilidade e atividade &mdash; útil para saber quem pode ajudar num determinado dia.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Sócios</h2>
        <p>Mantenha o registo dos sócios da associação e das suas quotas. Cada sócio tem um número de sócio, dados pessoais e de contacto, uma data de adesão, um estado e as suas quotas, e pode ser ligado ao seu registo de voluntário quando é a mesma pessoa.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Quotas e joia</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Joia</strong> &mdash; paga uma única vez, na adesão. Pode ser 0, e nesse caso não há nada a pagar.</li>
            <li><strong>Quota</strong> &mdash; o valor periódico: Mensal, Trimestral, Semestral ou Anual.</li>
        </ul>
        <p>Os gestores definem os valores por omissão do abrigo no botão <strong>Quotas e joia</strong> da lista de sócios. Os novos sócios começam com esses valores, que depois podem ser alterados em cada sócio.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Números de sócio</h3>
        <p>Deixe o número vazio e é atribuído automaticamente o seguinte, ou escreva um número para manter a numeração que já usa. Cada número só pode ser usado uma vez no abrigo, e os números de sócios apagados nunca são reutilizados.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Pagamentos</h3>
        <p>Registe a joia ou uma quota na página do sócio. A quota vem preenchida com o próximo período a pagar (a partir do dia seguinte ao último período pago, ou da data de adesão) e com o valor da quota do sócio. Cada pagamento regista também a data de pagamento, o valor, o método (Numerário, Transferência bancária, Pagamento móvel (MB Way) ou Outro) e notas.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Quotas em atraso</h3>
        <p>Um sócio ativo fica marcado com <strong>Quotas em atraso</strong> quando a joia não está paga ou nenhuma quota cobre o dia de hoje; a marca desaparece assim que o pagamento é registado. Ative <strong>Só quotas em atraso</strong> na lista para ver a quem enviar um lembrete.</p>
        <p>O estado (Ativo, Suspenso ou Ex-sócio) nunca muda automaticamente: altere-o no formulário do sócio, de acordo com as regras da associação. A lista mostra os sócios ativos por omissão; use o filtro Estado para ver os restantes.</p>
        <p>Gestores e funcionários podem adicionar e editar sócios e registar pagamentos; só os gestores podem apagar sócios ou alterar os valores por omissão. Os utilizadores de consulta não têm acesso aos sócios.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Instalações</h2>
        <p>Um abrigo está organizado em três níveis: as <strong>instalações</strong> (locais físicos, com morada) contêm <strong>alas</strong>, e as alas contêm <strong>jaulas</strong>. Cada jaula tem um código e uma capacidade.</p>
        <p>A capacidade total das jaulas determina quantos animais o abrigo pode acolher, e é nas jaulas que os animais são colocados. Configure pelo menos uma jaula antes de registar animais, para lhes poder atribuir uma localização.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Famílias de acolhimento</h3>
        <p>Se o abrigo coloca animais em famílias de acolhimento, crie uma ala para elas (por exemplo numa instalação chamada "Famílias de acolhimento") e ative <strong>Ala de famílias de acolhimento</strong> no formulário da ala. Nessa ala, cada jaula é uma família: use o nome da família como nome da jaula e, como capacidade, o número de animais que pode acolher.</p>
        <p>Opcionalmente, escolha um <strong>Contacto (voluntário)</strong> para cada família; a ficha do voluntário guarda o telefone e a morada. Para colocar um animal numa família, escolha a jaula da família no formulário do animal, como faria com qualquer outra jaula.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>A página do animal mostra <strong>Família de acolhimento</strong> com o nome da família e, para gestores e funcionários, o nome e o telefone do contacto. Os utilizadores com a função <strong>Consulta</strong> veem o nome da família, mas não o contacto.</li>
            <li>A página do voluntário lista os animais que estão atualmente com a sua família.</li>
            <li>As famílias de acolhimento não contam para a capacidade do abrigo no painel de controlo nem no relatório de Ocupação, que conta à parte os animais em famílias de acolhimento.</li>
            <li>No portal público, o animal mostra o distintivo <strong>Em família de acolhimento</strong>; a família nunca é mostrada publicamente.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Relatórios</h2>
        <p>Só os gestores veem os Relatórios. Escolha o período no topo (últimos 12 meses, um ano, desde sempre ou datas à sua escolha); períodos de dois anos ou mais são mostrados por ano em vez de por mês. Os relatórios estão divididos em quatro separadores:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Animais</strong> &mdash; entradas, adoções, devoluções e óbitos; o número de animais no abrigo ao longo do tempo; entradas e adoções por espécie; adoções por idade e a mediana de dias até à adoção; e os animais disponíveis que esperam há mais tempo.</li>
            <li><strong>Finanças</strong> &mdash; receitas por origem (apadrinhamentos, quotas, joias e taxas de adoção), contadas pela data de pagamento; apadrinhamentos ativos ao longo do tempo e o seu valor mensal; sócios ativos, novos e com quotas em atraso, com as quotas previstas e recebidas; pagamentos de sócios por método; e os apadrinhamentos cujo período pago termina nos próximos 30 dias.</li>
            <li><strong>Ocupação</strong> &mdash; a ocupação de hoje, a capacidade e os animais em jaulas, sem localização conhecida e em famílias de acolhimento; a ocupação ao longo do tempo e por ala. A capacidade é apenas indicativa, por isso não há avisos de lotação excedida, e os meses anteriores são comparados com a capacidade atual.</li>
            <li><strong>Saúde</strong> &mdash; vacinas administradas (por mês e por vacina), vacinas em atraso, diagnósticos por doença, casos em aberto e a percentagem de animais esterilizados no abrigo.</li>
        </ul>
        <p>Passe o rato sobre um gráfico para ver os valores, ou abra <strong>Ver tabela</strong> por baixo dele. O botão <strong>imprimir</strong> abre o separador atual como relatório com os dados do abrigo &mdash; por exemplo o relatório de atividades anual para a assembleia geral &mdash; pronto a imprimir ou a guardar em PDF a partir do navegador.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Utilizadores</h2>
        <p>Gestores e administradores convidam novos utilizadores por email; a pessoa convidada recebe uma ligação para definir a sua palavra-passe. Para cada abrigo a que o utilizador pertence escolhe-se o perfil (gestor, funcionário ou consulta) e se recebe notificações de vacinação.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Um gestor só pode adicionar utilizadores aos abrigos que gere.</li>
            <li>Um administrador pode adicionar utilizadores a qualquer abrigo e criar outros administradores.</li>
        </ul>
        <p>Em cada ligação a um abrigo também pode ativar as <strong>Notificações de Candidaturas de Adoção</strong>: esses utilizadores recebem um email por cada nova candidatura de adoção.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Portal Público</h2>
        <p>Uma instalação pode, opcionalmente, ter um site público ao lado do backoffice. É ativado por quem gere o servidor; quando está desligado, a página inicial envia os visitantes para a página de login e as opções abaixo ficam escondidas.</p>
        <p>Quando está ligado, qualquer pessoa (sem login) pode:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Ver os animais de todos os abrigos que estão prontos para adoção, filtrando por espécie, género, tamanho, raça e distrito.</li>
            <li>Abrir a ficha de um animal para ver as fotografias, a descrição pública e o abrigo onde está.</li>
            <li>Ver a lista de abrigos parceiros, cada um com a sua página com contactos, descrição, logótipo e animais.</li>
            <li>Abrir a ligação de um animal partilhada a partir do backoffice: abre diretamente a ficha desse animal, e as pré-visualizações nas redes sociais mostram o nome, a fotografia e a descrição.</li>
        </ul>
        <p>A partir da ficha de um animal, os visitantes também podem enviar uma candidatura de adoção com o botão <strong>Quero adotar</strong> (ver <a href="#adoptions" class="underline underline-offset-2">Adoções</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">O que é mostrado publicamente</h3>
        <p>Um animal só aparece no portal quando <strong>todas</strong> estas condições se verificam:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publicar no Portal Público</strong> está ativo (vem desligado por omissão, para que nada seja publicado por engano).</li>
            <li>O animal está disponível para adoção (animais adotados ou falecidos desaparecem automaticamente).</li>
            <li>O seu abrigo não foi removido.</li>
        </ul>
        <p>Os animais marcados como <strong>Destaque</strong> aparecem primeiro, com um selo de destaque. Só são mostrados o nome, a referência, as fotografias, a descrição pública e os dados descritivos (espécie, raça, tamanho, género, idade, tipo de pelo, esterilização) &mdash; as notas internas, notas clínicas, microchip e jaula nunca são publicados.</p>
        <p>Os animais que vivem com uma família de acolhimento mostram o distintivo <strong>Em família de acolhimento</strong>; o nome e os contactos da família nunca são publicados.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Dicas para bons anúncios</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Adicione pelo menos uma boa fotografia e uma descrição pública simpática &mdash; é o que os adotantes veem primeiro.</li>
            <li>Mantenha o perfil do abrigo (contactos, descrição, logótipo) atualizado: aparece na página pública do abrigo, nos resultados dos motores de busca e nas pré-visualizações de links.</li>
            <li>As páginas públicas estão preparadas para os motores de busca (Google e outros) e é gerado automaticamente um sitemap; o backoffice nunca é indexado.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administração</h2>
        <p>Apenas os administradores veem este menu. Nele mantêm-se os abrigos e as tabelas de referência partilhadas por todos os abrigos:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Abrigos</strong> &mdash; criar, editar e remover abrigos. Além do nome e da localidade, um abrigo tem um nome curto, contactos (email, telefone, site), morada, distrito, uma descrição e um logótipo &mdash; usados no portal público quando ativo. A lista de <strong>Espécies</strong> no formulário do abrigo define que espécies aparecem no menu Animais para o gestor e os funcionários desse abrigo.</li>
            <li><strong>Distritos</strong> &mdash; os distritos a que os abrigos pertencem, também usados como filtro no portal público.</li>
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
