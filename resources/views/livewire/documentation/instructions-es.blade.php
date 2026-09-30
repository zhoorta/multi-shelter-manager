<div class="flex flex-col gap-8 text-neutral-700 dark:text-neutral-300">
    <div>
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Instrucciones de la Aplicación</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Una guía de las principales áreas de {{ config('app.name') }} y de cómo usarlas en el día a día.</p>
    </div>

    <nav class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
        <a href="#getting-started" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Primeros Pasos</a>
        <a href="#dashboard" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Panel de Control</a>
        <a href="#pets" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Animales</a>
        <a href="#vaccinations" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Vacunaciones</a>
        <a href="#treatments" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Tratamientos</a>
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopciones</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Apadrinamientos</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Voluntarios</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Socios</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Instalaciones</a>
        <a href="#reports" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Informes</a>
        <a href="#users" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Usuarios</a>
        <a href="#public-portal" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Portal Público</a>
        <a href="#administration" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Administración</a>
        <a href="#settings" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Ajustes</a>
    </nav>

    <section id="getting-started" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Primeros Pasos</h2>
        <p>En la primera ejecución, la aplicación le pide que cree la cuenta de administrador inicial. A partir de ese momento, las nuevas cuentas solo se crean por invitación.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Funciones</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Administrador</strong> &mdash; gestiona toda la plataforma: los refugios, las tablas de referencia compartidas y las cuentas de usuario. Los administradores no gestionan animales ni instalaciones.</li>
            <li><strong>Gestor</strong> &mdash; dirige un refugio: todo lo que puede hacer un empleado, además de invitar y gestionar a los usuarios de ese refugio. El gestor también mantiene actualizado el perfil del refugio (contactos, dirección, descripción, logotipo) en <strong>Configuración &gt; Refugio</strong>; solo un administrador puede cambiar el nombre o las especies del refugio.</li>
            <li><strong>Empleado</strong> &mdash; se encarga del trabajo diario del refugio: animales, vacunaciones, tratamientos, adopciones, apadrinamientos, voluntarios e instalaciones.</li>
            <li><strong>Consulta</strong> &mdash; acceso de solo lectura al refugio: puede ver animales, vacunaciones, tratamientos e instalaciones e imprimir fichas y listas de animales, pero no puede crear, editar ni eliminar nada, y no ve los datos personales de adoptantes, padrinos, voluntarios ni socios.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Trabajar con varios refugios</h3>
        <p>Un usuario puede pertenecer a más de un refugio, con una función distinta en cada uno. Use el selector de refugio para cambiar el refugio activo; a partir de ese momento, todas las listas, recuentos y formularios muestran solo los datos de ese refugio. Los datos nunca se comparten entre refugios.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orden de configuración recomendado</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Un administrador completa las tablas de referencia (regiones, especies, razas, tamaños, tipos de pelo, vacunas, tratamientos, enfermedades, actividades).</li>
            <li>El administrador crea el refugio, completa su perfil (contactos, región, descripción, logotipo) y elige las especies con las que trabaja.</li>
            <li>El administrador invita al gestor del refugio.</li>
            <li>El gestor configura las instalaciones, alas y jaulas, e invita a los empleados.</li>
            <li>El equipo empieza a registrar animales.</li>
            <li>Si el portal público está activado, el equipo publica los animales que están listos para ser adoptados.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orientarse en la aplicación</h3>
        <p>El menú lateral solo muestra lo que tu rol puede usar. Gestores y personal ven el menú Animales (una entrada por cada especie activada en el refugio, más Apadrinamientos, Adopciones, Vacunaciones y Tratamientos), Voluntarios, Socios e Instalaciones; los gestores ven también Usuarios. Los administradores ven, en su lugar, Usuarios y el menú Administración. Los usuarios de consulta ven los mismos menús que los empleados, salvo Apadrinamientos, Adopciones, Voluntarios y Socios, y las páginas no les muestran botones para crear, editar o eliminar. Esta documentación está siempre disponible al final del menú lateral.</p>
        <p>El menú Animales incluye también <strong>Solicitudes de Adopción</strong> para gestores y personal, y solo los gestores ven los <strong>Informes</strong>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Módulos</h3>
        <p>Un refugio que no utiliza todas las partes de la aplicación puede desactivar módulos: <strong>Socios</strong>, <strong>Voluntarios</strong>, <strong>Apadrinamientos</strong>, <strong>Solicitudes de adopción</strong>, <strong>Informes</strong> y <strong>Vacunas y tratamientos</strong>. Los gestores lo hacen en <strong>Ajustes &gt; Refugio</strong> y los administradores en el formulario de edición del refugio, en la sección <strong>Módulos</strong>. Un módulo desactivado desaparece del menú lateral, del panel y de las fichas de los animales, y sus páginas dejan de abrirse. Con las solicitudes de adopción desactivadas, la página pública del animal deja de mostrar el botón &ldquo;Quiero adoptar&rdquo;. No se borra nada: al volver a activar un módulo se recuperan todos sus datos. Los animales, las adopciones, los diagnósticos y las instalaciones están siempre activos.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Panel de Control</h2>
        <p>El panel de control le ofrece una visión general del refugio activo:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Contadores de animales en el refugio, capacidad disponible en las jaulas y adopciones de este año. Los gestores y empleados ven también las solicitudes de adopción pendientes, las vacunas atrasadas y las cuotas atrasadas, cada una con enlace a su lista.</li>
            <li>Avisos cuando todavía falta algo, como no tener jaulas definidas o especies sin razas.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Requiere atención</h3>
        <p>Listas breves de animales que necesitan alguna acción. Cada una muestra hasta cinco animales y solo aparece cuando tiene alguno; <strong>Ver todos</strong> abre la lista de animales con el filtro correspondiente.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Animales con Ubicación Desconocida</strong> &mdash; animales del refugio sin jaula, y desde hace cuánto, para poder ubicarlos.</li>
            <li><strong>Problemas de salud abiertos</strong> &mdash; animales con un diagnóstico activo o crónico, el diagnóstico más reciente primero, con los diagnósticos.</li>
            <li><strong>Apadrinamientos por Renovar</strong> &mdash; apadrinamientos cuyo periodo pagado terminó en los últimos 30 días o termina en los próximos 30, con el nombre del padrino, para poder contactarle. Solo para gestores y empleados.</li>
            <li><strong>Animales sin Foto</strong> &mdash; sin foto, un animal no se luce en el portal público ni se puede compartir en redes sociales.</li>
            <li><strong>Más Tiempo en el Refugio</strong> &mdash; los animales disponibles que llevan más tiempo esperando desde su ingreso, y cuánto: buenos candidatos para promocionar.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Actividad reciente</h3>
        <p>Los ingresos más recientes (con fecha de ingreso y jaula), adopciones (con la fecha y el nombre del adoptante, oculto para los usuarios de consulta) y fallecimientos (con la fecha).</p>
        <p>La capacidad disponible solo cuenta las jaulas del propio refugio: las alas de familias de acogida quedan fuera, igual que los animales que viven con familias de acogida.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Panel del administrador</h3>
        <p>Los administradores no pertenecen a un refugio, así que su panel muestra toda la plataforma: contadores de refugios, usuarios activos en los últimos 30 días, animales a cargo y adopciones de este año; una tabla de los refugios con sus animales, adopciones, último acceso y última actualización de animales, los menos usados primero y los accesos antiguos resaltados; <strong>Configuración pendiente</strong> (refugios sin especies, jaulas o usuarios, y especies sin razas); e <strong>Invitaciones sin aceptar</strong>, los usuarios invitados que nunca han entrado. Solo muestra totales, nunca animales ni datos de personas.</p>
    </section>

    <section id="pets" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Animales</h2>
        <p>El menú Animales muestra los animales del refugio por especie. Cada ficha incluye la identificación (referencia, nombre, microchip), la descripción física (raza, colores, tipo de pelo, tamaño, sexo, esterilizado), las fechas (nacimiento, ingreso, salida, fallecimiento), fotografías, una descripción pública, notas internas y notas clínicas. Solo aparecen las especies que el administrador ha activado para tu refugio.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Estado</h3>
        <p>El estado de un animal se calcula automáticamente, por lo que nunca se establece a mano:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Fallecido</strong> &mdash; se ha rellenado una fecha de fallecimiento.</li>
            <li><strong>Adoptado</strong> &mdash; el animal tiene una adopción sin fecha de devolución.</li>
            <li><strong>Disponible</strong> / <strong>No disponible</strong> &mdash; en los demás casos, según si el animal está marcado como adoptable.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Opciones y ubicación</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Disponible para Adopción</strong> &mdash; el animal puede ser adoptado; determina si su estado es disponible o no disponible.</li>
            <li><strong>Disponible para Apadrinamiento</strong> &mdash; el animal puede recibir apadrinamientos. La acción de apadrinar solo se ofrece para animales con esta opción activada, y sigue disponible incluso después de que el animal sea adoptado.</li>
            <li><strong>Jaula</strong> &mdash; la lista de jaulas se agrupa por instalación y ala y muestra cuántas plazas libres tiene cada jaula, con un indicador verde, amarillo o rojo a medida que se llena.</li>
        </ul>
        <p>Cuando el portal público está activado, aparecen dos opciones más: <strong>Publicar en el Portal Público</strong> y <strong>Destacado</strong>. Consulta <a href="#public-portal" class="underline underline-offset-2">Portal Público</a>.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Búsqueda y filtros</h3>
        <p>Busque por nombre, referencia, microchip o notas internas, y filtre por estado, especie o ubicación (instalación, ala o jaula). El último filtro encuentra animales con <strong>Problemas de salud abiertos</strong>, por esterilización (<strong>Esterilizado</strong>, <strong>No esterilizado</strong>, <strong>Esterilizado, faltan detalles</strong>) o con datos que faltan (sin edad, sin foto, sin fecha de ingreso o sin ubicación), lo que ayuda a mantener las fichas completas. Los animales con un problema de salud abierto muestran un corazón junto a su nombre en la lista.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Impresión</h3>
        <p>Puede imprimir la ficha de un animal desde su página, o imprimir la lista de animales; la lista impresa usa los mismos filtros que están activos en pantalla.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Compartir en redes sociales</h3>
        <p>Los animales adoptables y disponibles tienen un botón de compartir en la parte superior de su página. Prepara un texto con los datos del animal y los contactos del refugio, listo para copiar, y permite descargar la foto principal para publicar en Facebook, Instagram o WhatsApp. Cuando el animal está publicado en el portal público, el texto incluye un enlace al animal y también puede compartirlo directamente en Facebook o WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Salud</h3>
        <p>Registre diagnósticos en la página del animal con <strong>Nuevo diagnóstico</strong>: la enfermedad, la fecha del diagnóstico, el estado (<strong>Activa</strong>, <strong>Crónica</strong> o <strong>Tratada</strong>) y notas del tratamiento. Un diagnóstico marcado como Tratada recibe una fecha de resolución (hoy, por defecto). Los diagnósticos activos y crónicos son los problemas de salud abiertos del animal: aparecen en su página, en el panel de control y en el filtro de la lista de animales. Las vacunaciones y las notas clínicas también se guardan en cada animal. Los tamaños solo se ofrecen para especies que tengan tamaños configurados.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Esterilización</h3>
        <p>Con <strong>Esterilizado / Castrado</strong> activado, registre la <strong>Fecha de esterilización</strong> y quién la hizo (<strong>El refugio</strong> o <strong>Antes de la entrada</strong>); déjelos vacíos si no se sabe. Desactivado, elija el <strong>Estado de la esterilización</strong> (Pendiente, Programada con su fecha, o No recomendada) y añada notas. Los animales nuevos que no están esterilizados empiezan como Pendiente.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vacunaciones</h2>
        <p>Cada vacunación registra la vacuna, la fecha de administración, la próxima fecha prevista, el número de lote, el veterinario y notas. Registre la última dosis y la próxima fecha en el mismo registro: queda pendiente hasta que se registre una dosis posterior de esa vacuna. Para planificar una vacunación, rellene solo la próxima fecha; al registrar después la dosis, queda completada. Cuando la vacuna tiene una frecuencia (por ejemplo la rabia, cada 36 meses), la próxima fecha se rellena a partir de la fecha de la dosis y se puede cambiar. La página Vacunaciones las muestra para todos los animales del refugio.</p>
        <p>Cada día, los usuarios que tienen activadas las notificaciones de vacunación para un refugio reciben un email con las vacunaciones de ese refugio previstas para los próximos siete días que siguen pendientes. Cada vacunación solo se notifica una vez. Un icono de campana en la lista de usuarios indica quién recibe estos emails.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Plan de vacunación</h3>
        <p>Los refugios que vacunan en grupo pueden abrir el <strong>Plan de Vacunación</strong> en la página Vacunaciones. Para el año elegido muestra, por vacuna, cuántas vacunaciones pendientes de los animales del refugio están previstas en cada mes; la primera columna cuenta las que ya estaban previstas antes de ese año. Haga clic en un número para ver la lista de animales, con su microchip y ubicación, y use <strong>Imprimir lista para el veterinario</strong> para llevarla el día de la vacunación.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Vacunación en grupo</h3>
        <p>La <strong>Vacunación en Grupo</strong> registra la misma vacuna para varios animales a la vez, por ejemplo el día en que el veterinario vacuna a un grupo. Elija la vacuna y qué animales listar: los que tienen la vacuna prevista en un mes (por defecto, el mes actual), los atrasados o todos los animales del refugio de la especie de esa vacuna, y filtre por especie o ubicación si lo necesita. Los animales listados empiezan marcados; desmarque las excepciones. La fecha, la próxima fecha prevista, el número de lote, el veterinario y las notas se rellenan una sola vez para todos. En el plan de vacunación, el botón <strong>Vacunación en Grupo</strong> junto a la lista de un mes abre este formulario con esos animales ya listados.</p>
    </section>

    <section id="treatments" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Tratamientos</h2>
        <p>Los tratamientos son cuidados preventivos periódicos que no son vacunas, como la desparasitación interna y externa. Cada tratamiento registra el tratamiento, la fecha de administración, la próxima fecha prevista, el producto usado, el veterinario y notas. Funcionan como las vacunaciones: un registro queda pendiente hasta que se registra una nueva toma del mismo tratamiento y, cuando el tratamiento tiene una frecuencia (por ejemplo la desparasitación cada 3 meses), la próxima fecha se rellena a partir de la fecha de administración.</p>
        <p>Regístrelos en la página del animal con <strong>Nuevo Tratamiento</strong>. La página <strong>Tratamientos</strong>, en el menú Animales, los muestra para todos los animales del refugio, con búsqueda y un filtro por próxima fecha prevista; las fechas atrasadas aparecen en rojo y las de los próximos siete días en ámbar.</p>
        <p>El <strong>Tratamiento en Grupo</strong> registra una ronda para muchos animales a la vez: elija el tratamiento y, si quiere, una especie o ubicación; todos los animales del refugio a los que se aplica empiezan marcados, así que desmarque las excepciones y rellene una sola vez la fecha, el producto, el veterinario y las notas.</p>
        <p>Los usuarios con las notificaciones de vacunación activadas reciben también un email diario con los tratamientos previstos para los próximos siete días, agrupados por tratamiento y fecha, de modo que una ronda de desparasitación llega como un solo recordatorio y no uno por animal.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adopciones</h2>
        <p>Una adopción registra los datos de contacto del adoptante, la fecha de adopción, la tasa, notas y el estado de la solicitud (Pendiente, Aprobada o Rechazada). Iníciela desde la página del animal.</p>
        <p>La página Adopciones (en Animales, en el menú lateral) lista todas las adopciones del refugio; busca por nombre, teléfono, email o notas del adoptante, o por el nombre o la referencia del animal.</p>
        <p>Si un animal adoptado vuelve al refugio, rellene la <strong>fecha de devolución</strong> en la adopción: el animal vuelve a estar disponible y la adopción se mantiene en su historial.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Solicitudes de adopción</h3>
        <p>Con el portal público activo, los visitantes pueden enviar una solicitud de adopción desde la ficha del animal con el botón <strong>Quiero adoptar</strong>. El formulario pide sus contactos, el tipo de vivienda, si hay jardín, niños u otros animales, y por qué quieren adoptar.</p>
        <p>Las solicitudes aparecen en <strong>Solicitudes de Adopción</strong>, en el menú Animales, primero las pendientes, y la barra lateral muestra cuántas hay pendientes. En cada una puede:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Aprobar</strong> &mdash; abre el formulario de adopción ya rellenado con los datos del solicitante; al guardarlo se registra la adopción y la solicitud queda aprobada.</li>
            <li><strong>Rechazar</strong> &mdash; la marca como rechazada.</li>
            <li><strong>Eliminar</strong> &mdash; la borra.</li>
        </ul>
        <p>Cuando un animal ya ha sido adoptado o deja de estar disponible, sus solicitudes pendientes se señalan y pueden rechazarse todas a la vez. Los usuarios con las <strong>Notificaciones de Solicitudes de Adopción</strong> activadas reciben un email por cada nueva solicitud; el solicitante no recibe ningún email, así que contacte con él directamente. Las solicitudes se eliminan automáticamente seis meses después de su último cambio, como indica la política de privacidad.</p>
    </section>

    <section id="sponsorships" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Apadrinamientos</h2>
        <p>Los padrinos contribuyen al cuidado de un animal sin adoptarlo. Un apadrinamiento guarda los datos de contacto del padrino y si desea recibir novedades sobre el animal o el boletín.</p>
        <p>Solo se pueden crear apadrinamientos para animales marcados como <strong>Disponible para Apadrinamiento</strong>. La página Apadrinamientos (en Animales, en el menú lateral) los lista todos, con la misma búsqueda que las adopciones: nombre, teléfono, email o notas del padrino, o el nombre o la referencia del animal.</p>
        <p>Cada apadrinamiento tiene una lista de pagos. Un pago registra el periodo que cubre (fechas de inicio y fin), la fecha de pago y el importe, por lo que se admiten tanto contribuciones puntuales como periódicas.</p>
    </section>

    <section id="volunteers" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Voluntarios</h2>
        <p>Lleve un registro de las personas que ayudan a su refugio, de forma independiente a las cuentas de usuario. Para cada voluntario puede guardar datos personales y de contacto, una fotografía, fechas de inicio y fin, medio de transporte y preferencia de boletín, además de:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Las actividades en las que ayuda y las especies con las que prefiere trabajar.</li>
            <li>Su disponibilidad por día de la semana (mañanas y/o tardes, ocasionalmente, cada dos semanas o semanalmente).</li>
            <li>Evaluaciones de asistencia y desempeño.</li>
        </ul>
        <p>La lista de voluntarios se puede buscar por nombre, teléfono, email, NIF o notas, y filtrar por especie preferida, día de disponibilidad y actividad &mdash; útil para saber quién puede ayudar un día concreto.</p>
    </section>

    <section id="members" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Socios</h2>
        <p>Lleve el registro de los socios de la asociación y de sus cuotas. Cada socio tiene un número de socio, datos personales y de contacto, una fecha de alta, un estado y sus cuotas, y puede vincularse a su ficha de voluntario cuando es la misma persona.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Cuotas</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Cuota de inscripción</strong> &mdash; se paga una sola vez, al darse de alta. Puede ser 0, y en ese caso no se debe nada.</li>
            <li><strong>Cuota</strong> &mdash; el importe periódico: Mensual, Trimestral, Semestral o Anual.</li>
        </ul>
        <p>Los gestores definen los valores por defecto del refugio con el botón <strong>Cuotas</strong> de la lista de socios. Los nuevos socios empiezan con esos valores, que luego se pueden cambiar en cada socio.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Números de socio</h3>
        <p>Deje el número vacío y se asigna automáticamente el siguiente, o escriba uno para mantener la numeración que ya usa. Cada número solo puede usarse una vez en el refugio, y los números de socios eliminados nunca se reutilizan.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Pagos</h3>
        <p>Registre la cuota de inscripción o una cuota en la página del socio. La cuota viene rellenada con el próximo periodo a pagar (desde el día siguiente al último periodo pagado, o desde la fecha de alta) y con la cuota del socio. Cada pago registra también la fecha de pago, el importe, el método (Efectivo, Transferencia bancaria, Pago móvil u Otro) y notas.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Cuotas pendientes</h3>
        <p>Un socio activo queda marcado con <strong>Cuotas pendientes</strong> cuando la cuota de inscripción no está pagada o ninguna cuota cubre el día de hoy; la marca desaparece en cuanto se registra el pago. Active <strong>Solo cuotas pendientes</strong> en la lista para ver a quién enviar un recordatorio.</p>
        <p>El estado (Activo, Suspendido o Antiguo socio) nunca cambia automáticamente: cámbielo en el formulario del socio, según las normas de la asociación. La lista muestra los socios activos por defecto; use el filtro Estado para ver los demás.</p>
        <p>Gestores y personal pueden añadir y editar socios y registrar pagos; solo los gestores pueden eliminar socios o cambiar los valores por defecto. Los usuarios de consulta no tienen acceso a los socios.</p>
    </section>

    <section id="facilities" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Instalaciones</h2>
        <p>Un refugio se organiza en tres niveles: las <strong>instalaciones</strong> (ubicaciones físicas, con dirección) contienen <strong>alas</strong>, y las alas contienen <strong>jaulas</strong>. Cada jaula tiene un código y una capacidad.</p>
        <p>La capacidad total de las jaulas determina cuántos animales puede alojar el refugio, y las jaulas son donde se asignan los animales. Configure al menos una jaula antes de registrar animales para que se les pueda asignar una ubicación.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Familias de acogida</h3>
        <p>Si su refugio coloca animales en familias de acogida, cree un ala para ellas (por ejemplo en una instalación llamada "Familias de acogida") y active <strong>Ala de familias de acogida</strong> en el formulario del ala. En esa ala cada jaula es una familia: use el nombre de la familia como nombre de la jaula y, como capacidad, el número de animales que puede acoger.</p>
        <p>Opcionalmente elija un <strong>Contacto (voluntario)</strong> para cada familia; la ficha del voluntario guarda su teléfono y dirección. Para colocar un animal con una familia, elija la jaula de la familia en el formulario del animal, como con cualquier otra jaula.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>La página del animal muestra <strong>Familia de acogida</strong> con el nombre de la familia y, para gestores y personal, el nombre y el teléfono del contacto. Los usuarios con el rol <strong>Consulta</strong> ven el nombre de la familia pero no el contacto.</li>
            <li>La página del voluntario lista los animales que están actualmente con su familia.</li>
            <li>Las familias de acogida no cuentan para la capacidad del refugio en el panel ni en el informe de Ocupación, que cuenta aparte los animales en familias de acogida.</li>
            <li>En el portal público el animal muestra la insignia <strong>En familia de acogida</strong>; la familia nunca se muestra públicamente.</li>
        </ul>
    </section>

    <section id="reports" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Informes</h2>
        <p>Solo los gestores ven los Informes. Elija el período arriba (últimos 12 meses, un año, todo el período o sus propias fechas); los períodos de dos años o más se muestran por año en lugar de por mes. Los informes se dividen en cuatro pestañas:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Animales</strong> &mdash; entradas, adopciones, devoluciones y fallecimientos; el número de animales en el refugio a lo largo del tiempo; entradas y adopciones por especie; adopciones por edad y la mediana de días hasta la adopción; y los animales disponibles que llevan más tiempo esperando.</li>
            <li><strong>Finanzas</strong> &mdash; ingresos por origen (apadrinamientos, cuotas, cuotas de inscripción y tasas de adopción), contados por fecha de pago; apadrinamientos activos a lo largo del tiempo y su valor mensual; socios activos, nuevos y con cuotas atrasadas, con las cuotas previstas y cobradas; pagos de socios por método; y los apadrinamientos cuyo período pagado termina en los próximos 30 días.</li>
            <li><strong>Ocupación</strong> &mdash; la ocupación de hoy, la capacidad y los animales en jaulas, sin ubicación conocida y en familias de acogida; la ocupación a lo largo del tiempo y por ala. La capacidad es solo orientativa, así que no hay avisos de exceso, y los meses pasados se comparan con la capacidad actual.</li>
            <li><strong>Salud</strong> &mdash; vacunaciones administradas (por mes y por vacuna), vacunas atrasadas, diagnósticos por enfermedad, casos abiertos, las esterilizaciones realizadas por el refugio en el periodo (las que tienen fecha y las hizo el refugio) y el porcentaje de animales esterilizados en el refugio.</li>
        </ul>
        <p>Pase el ratón sobre un gráfico para ver sus valores, o abra <strong>Ver tabla</strong> debajo. El botón <strong>imprimir</strong> abre la pestaña actual como informe con los datos del refugio &mdash; por ejemplo la memoria anual de actividades para la asamblea general &mdash; lista para imprimir o guardar en PDF desde el navegador.</p>
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Usuarios</h2>
        <p>Los gestores y administradores invitan a nuevos usuarios por email; la persona invitada recibe un enlace para crear su contraseña. Para cada refugio al que pertenece se elige la función (gestor, empleado o consulta) y si el usuario recibe notificaciones de vacunación (que incluyen también los tratamientos).</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Un gestor solo puede añadir usuarios a los refugios que gestiona.</li>
            <li>Un administrador puede añadir usuarios a cualquier refugio y puede crear otros administradores.</li>
        </ul>
        <p>En cada vinculación con un refugio también se pueden activar las <strong>Notificaciones de Solicitudes de Adopción</strong>: esos usuarios reciben un email por cada nueva solicitud de adopción.</p>
        <p>Mientras la persona invitada no haya iniciado sesión por primera vez, la lista de usuarios muestra un botón <strong>Reenviar invitación</strong> en su fila. Envía por email un enlace nuevo y anula el anterior; el enlace caduca a las 48 horas.</p>
    </section>

    <section id="public-portal" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Portal Público</h2>
        <p>Una instalación puede tener, opcionalmente, un sitio web público junto al backoffice. Lo activa quien gestiona el servidor; cuando está desactivado, la página de inicio envía a los visitantes a la página de inicio de sesión y las opciones siguientes quedan ocultas.</p>
        <p>Cuando está activado, cualquier persona (sin iniciar sesión) puede:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Ver los animales de todos los refugios que están listos para adopción, filtrando por especie, sexo, tamaño, raza y región.</li>
            <li>Abrir la ficha de un animal para ver sus fotos, su descripción pública y el refugio donde está.</li>
            <li>Ver la lista de refugios colaboradores, cada uno con su propia página con contactos, descripción, logotipo y animales.</li>
            <li>Abrir el enlace de un animal compartido desde el backoffice: abre directamente la ficha de ese animal, y las vistas previas en las redes sociales muestran su nombre, foto y descripción.</li>
        </ul>
        <p>Desde la ficha de un animal, los visitantes también pueden enviar una solicitud de adopción con el botón <strong>Quiero adoptar</strong> (ver <a href="#adoptions" class="underline underline-offset-2">Adopciones</a>).</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Qué se muestra públicamente</h3>
        <p>Un animal solo aparece en el portal cuando se cumplen <strong>todas</strong> estas condiciones:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publicar en el Portal Público</strong> está activado (viene desactivado por defecto, para que nada se publique por error).</li>
            <li>El animal está disponible para adopción (los animales adoptados o fallecidos desaparecen automáticamente).</li>
            <li>Su refugio no ha sido eliminado.</li>
        </ul>
        <p>Los animales marcados como <strong>Destacado</strong> se muestran primero, con una insignia de destacado. Solo se muestran el nombre, la referencia, las fotos, la descripción pública y los datos descriptivos (especie, raza, tamaño, sexo, edad, tipo de pelo, esterilización) &mdash; las notas internas, notas clínicas, microchip y jaula nunca se publican.</p>
        <p>Los animales que viven con una familia de acogida muestran la insignia <strong>En familia de acogida</strong>; el nombre y los contactos de la familia nunca se publican.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Consejos para buenos anuncios</h3>
        <ul class="list-disc space-y-1 ps-6">
            <li>Añade al menos una buena foto y una descripción pública cercana &mdash; es lo primero que ven los adoptantes.</li>
            <li>Mantén actualizado el perfil del refugio (contactos, descripción, logotipo): aparece en la página pública del refugio, en los resultados de los buscadores y en las vistas previas de enlaces.</li>
            <li>Las páginas públicas están preparadas para los buscadores (Google y otros) y se genera automáticamente un sitemap; el backoffice nunca se indexa.</li>
        </ul>
    </section>

    <section id="administration" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Administración</h2>
        <p>Solo los administradores ven este menú. En él se mantienen los refugios y las tablas de referencia compartidas por todos los refugios:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Refugios</strong> &mdash; crear, editar y eliminar refugios. Además del nombre y la ciudad, un refugio tiene un nombre corto, contactos (email, teléfono, web), dirección, región, una descripción y un logotipo &mdash; usados en el portal público cuando está activado. La lista de <strong>Especies</strong> del formulario del refugio define qué especies aparecen en el menú Animales para el gestor y el personal de ese refugio. El email es obligatorio. Cuando el refugio tiene un gestor, este puede actualizar todo excepto el nombre y las especies en <strong>Configuración &gt; Refugio</strong>.</li>
            <li><strong>Regiones</strong> &mdash; las regiones a las que pertenecen los refugios, también usadas como filtro en el portal público.</li>
            <li><strong>Especies</strong>, <strong>Razas</strong>, <strong>Tamaños</strong> y <strong>Tipos de Pelo</strong> &mdash; las opciones usadas para describir a los animales.</li>
            <li><strong>Vacunas</strong>, <strong>Tratamientos</strong> y <strong>Enfermedades</strong> &mdash; las opciones usadas en los registros de salud de los animales. Las vacunas y los tratamientos indican las especies a las que se aplican y, opcionalmente, una frecuencia en meses, que se usa para rellenar la próxima fecha prevista.</li>
            <li><strong>Actividades</strong> &mdash; las tareas en las que pueden ayudar los voluntarios.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Ajustes</h2>
        <p>Desde el menú de usuario, abra Ajustes para ver su perfil, cambiar su contraseña y elegir la apariencia (clara, oscura o del sistema) y su idioma. Su nombre solo puede cambiarlo un administrador o gestor, y su email no se puede cambiar. El idioma se guarda en su cuenta y también se usa en los emails que recibe. En las páginas públicas y en la de inicio de sesión, cualquiera puede cambiar el idioma en el menú superior. Los gestores ven también una pestaña <strong>Refugio</strong> para actualizar los contactos, la dirección, la región, la descripción y el logotipo del refugio activo; el email es obligatorio, y el nombre y las especies solo puede cambiarlos un administrador.</p>
        <p>Los gestores pueden descargar una copia de todos los datos de su refugio en <strong>Ajustes &gt; Exportar datos</strong>: un ZIP con un archivo CSV por área (animales, vacunas, tratamientos, diagnósticos, adopciones, solicitudes de adopción, apadrinamientos y pagos, socios y pagos, voluntarios y espacios). Sirve para sus propias copias de seguridad o para cambiar de sistema. Los archivos se abren directamente en Excel. Contienen datos personales de socios, voluntarios, adoptantes y padrinos, así que guárdelos de forma segura y no los comparta. Cada descarga queda registrada (quién, cuándo y desde qué dirección IP) y las diez últimas aparecen en la misma página.</p>
    </section>
</div>
