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
        <a href="#adoptions" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Adopciones</a>
        <a href="#sponsorships" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Apadrinamientos</a>
        <a href="#volunteers" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Voluntarios</a>
        <a href="#members" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Socios</a>
        <a href="#facilities" class="underline underline-offset-2 hover:text-neutral-900 dark:hover:text-white">Instalaciones</a>
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
            <li><strong>Gestor</strong> &mdash; dirige un refugio: todo lo que puede hacer un empleado, además de invitar y gestionar a los usuarios de ese refugio.</li>
            <li><strong>Empleado</strong> &mdash; se encarga del trabajo diario del refugio: animales, vacunaciones, adopciones, apadrinamientos, voluntarios e instalaciones.</li>
            <li><strong>Consulta</strong> &mdash; acceso de solo lectura al refugio: puede ver animales, vacunaciones e instalaciones e imprimir fichas y listas de animales, pero no puede crear, editar ni eliminar nada, y no ve los datos personales de adoptantes, padrinos, voluntarios ni socios.</li>
        </ul>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Trabajar con varios refugios</h3>
        <p>Un usuario puede pertenecer a más de un refugio, con una función distinta en cada uno. Use el selector de refugio para cambiar el refugio activo; a partir de ese momento, todas las listas, recuentos y formularios muestran solo los datos de ese refugio. Los datos nunca se comparten entre refugios.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orden de configuración recomendado</h3>
        <ol class="list-decimal space-y-1 ps-6">
            <li>Un administrador completa las tablas de referencia (regiones, especies, razas, tamaños, tipos de pelo, vacunas, enfermedades, actividades).</li>
            <li>El administrador crea el refugio, completa su perfil (contactos, región, descripción, logotipo) y elige las especies con las que trabaja.</li>
            <li>El administrador invita al gestor del refugio.</li>
            <li>El gestor configura las instalaciones, alas y jaulas, e invita a los empleados.</li>
            <li>El equipo empieza a registrar animales.</li>
            <li>Si el portal público está activado, el equipo publica los animales que están listos para ser adoptados.</li>
        </ol>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Orientarse en la aplicación</h3>
        <p>El menú lateral solo muestra lo que tu rol puede usar. Gestores y personal ven el menú Animales (una entrada por cada especie activada en el refugio, más Apadrinamientos, Adopciones y Vacunaciones), Voluntarios, Socios e Instalaciones; los gestores ven también Usuarios. Los administradores ven, en su lugar, Usuarios y el menú Administración. Los usuarios de consulta ven los mismos menús que los empleados, salvo Apadrinamientos, Adopciones, Voluntarios y Socios, y las páginas no les muestran botones para crear, editar o eliminar. Esta documentación está siempre disponible al final del menú lateral.</p>
    </section>

    <section id="dashboard" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Panel de Control</h2>
        <p>El panel de control le ofrece una visión general del refugio activo:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Contadores de animales en el refugio, capacidad disponible en las jaulas y adopciones de este año. Los gestores y empleados ven también las solicitudes de adopción pendientes, las vacunas atrasadas y las cuotas atrasadas, cada una con enlace a su lista.</li>
            <li>Los ingresos, adopciones, apadrinamientos y fallecimientos más recientes.</li>
            <li>Animales sin ubicación conocida, para que puedan asignarse a una jaula.</li>
            <li>Avisos cuando todavía falta algo, como no tener jaulas definidas o especies sin razas.</li>
        </ul>
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
        <p>Busque por nombre, referencia, microchip o notas internas, y filtre por estado, especie o ubicación (instalación, ala o jaula). El filtro de <em>datos en falta</em> encuentra animales sin edad, sin fotografía, sin fecha de ingreso o sin ubicación, lo que ayuda a mantener las fichas completas.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Impresión</h3>
        <p>Puede imprimir la ficha de un animal desde su página, o imprimir la lista de animales; la lista impresa usa los mismos filtros que están activos en pantalla.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Compartir en redes sociales</h3>
        <p>Los animales adoptables y disponibles tienen un botón de compartir en la parte superior de su página. Prepara un texto con los datos del animal y los contactos del refugio, listo para copiar, y permite descargar la foto principal para publicar en Facebook, Instagram o WhatsApp. Cuando el animal está publicado en el portal público, el texto incluye un enlace al animal y también puede compartirlo directamente en Facebook o WhatsApp.</p>
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Salud</h3>
        <p>Registre en cada animal las enfermedades (con fecha de diagnóstico, estado y notas de tratamiento), las vacunaciones y las notas clínicas. Los tamaños solo se ofrecen para las especies que tienen tamaños configurados.</p>
    </section>

    <section id="vaccinations" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Vacunaciones</h2>
        <p>Cada vacunación registra la vacuna, la fecha en que se administró o en que está prevista, el número de lote, el veterinario y notas. La página Vacunaciones las muestra para todos los animales del refugio.</p>
        <p>Cada día, los usuarios que tienen activadas las notificaciones de vacunación para un refugio reciben un email con las vacunaciones de ese refugio previstas para los próximos siete días que aún no se han administrado. Cada vacunación solo se notifica una vez. Un icono de campana en la lista de usuarios indica quién recibe estos emails.</p>
    </section>

    <section id="adoptions" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Adopciones</h2>
        <p>Una adopción registra los datos de contacto del adoptante, la fecha de adopción, la tasa, notas y el estado de la solicitud (Pendiente, Aprobada o Rechazada). Iníciela desde la página del animal.</p>
        <p>La página Adopciones (en Animales, en el menú lateral) lista todas las adopciones del refugio; busca por nombre, teléfono, email o notas del adoptante, o por el nombre o la referencia del animal.</p>
        <p>Si un animal adoptado vuelve al refugio, rellene la <strong>fecha de devolución</strong> en la adopción: el animal vuelve a estar disponible y la adopción se mantiene en su historial.</p>
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
    </section>

    <section id="users" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Usuarios</h2>
        <p>Los gestores y administradores invitan a nuevos usuarios por email; la persona invitada recibe un enlace para crear su contraseña. Para cada refugio al que pertenece se elige la función (gestor, empleado o consulta) y si el usuario recibe notificaciones de vacunación.</p>
        <ul class="list-disc space-y-1 ps-6">
            <li>Un gestor solo puede añadir usuarios a los refugios que gestiona.</li>
            <li>Un administrador puede añadir usuarios a cualquier refugio y puede crear otros administradores.</li>
        </ul>
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
        <h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Qué se muestra públicamente</h3>
        <p>Un animal solo aparece en el portal cuando se cumplen <strong>todas</strong> estas condiciones:</p>
        <ul class="list-disc space-y-1 ps-6">
            <li><strong>Publicar en el Portal Público</strong> está activado (viene desactivado por defecto, para que nada se publique por error).</li>
            <li>El animal está disponible para adopción (los animales adoptados o fallecidos desaparecen automáticamente).</li>
            <li>Su refugio no ha sido eliminado.</li>
        </ul>
        <p>Los animales marcados como <strong>Destacado</strong> se muestran primero, con una insignia de destacado. Solo se muestran el nombre, la referencia, las fotos, la descripción pública y los datos descriptivos (especie, raza, tamaño, sexo, edad, tipo de pelo, esterilización) &mdash; las notas internas, notas clínicas, microchip y jaula nunca se publican.</p>
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
            <li><strong>Refugios</strong> &mdash; crear, editar y eliminar refugios. Además del nombre y la ciudad, un refugio tiene un nombre corto, contactos (email, teléfono, web), dirección, región, una descripción y un logotipo &mdash; usados en el portal público cuando está activado. La lista de <strong>Especies</strong> del formulario del refugio define qué especies aparecen en el menú Animales para el gestor y el personal de ese refugio.</li>
            <li><strong>Regiones</strong> &mdash; las regiones a las que pertenecen los refugios, también usadas como filtro en el portal público.</li>
            <li><strong>Especies</strong>, <strong>Razas</strong>, <strong>Tamaños</strong> y <strong>Tipos de Pelo</strong> &mdash; las opciones usadas para describir a los animales.</li>
            <li><strong>Vacunas</strong> y <strong>Enfermedades</strong> &mdash; las opciones usadas en los registros de salud de los animales.</li>
            <li><strong>Actividades</strong> &mdash; las tareas en las que pueden ayudar los voluntarios.</li>
        </ul>
    </section>

    <section id="settings" class="flex scroll-mt-6 flex-col gap-2">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Ajustes</h2>
        <p>Desde el menú de usuario, abra Ajustes para ver su perfil, cambiar su contraseña y elegir la apariencia (clara, oscura o del sistema). Su nombre solo puede cambiarlo un administrador o gestor, y su email no se puede cambiar.</p>
    </section>
</div>
