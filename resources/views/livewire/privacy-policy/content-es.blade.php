<div class="flex flex-col gap-8 leading-relaxed text-stone-600 dark:text-stone-300 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-stone-900 dark:[&_h2]:text-white [&_h3]:font-semibold [&_h3]:text-stone-800 dark:[&_h3]:text-stone-100 [&_section]:flex [&_section]:scroll-mt-24 [&_section]:flex-col [&_section]:gap-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:ps-6 [&_a]:font-semibold [&_a]:text-orange-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-orange-300">
    <header class="flex flex-col gap-2">
        <span class="text-4xl">🔒</span>
        <h1 class="font-display text-4xl font-bold text-stone-900 dark:text-white">Política de Privacidad</h1>
        <p class="text-sm text-stone-500 dark:text-stone-400">Última actualización: 25 de septiembre de 2026</p>
        <p>{{ config('app.name') }} es una plataforma que reúne a varios refugios de animales. Nos tomamos en serio la protección de sus datos personales y los tratamos de acuerdo con el Reglamento General de Protección de Datos (RGPD) y la Ley Orgánica 3/2018, de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD). Esta política explica qué datos tratamos, por qué, durante cuánto tiempo y cuáles son sus derechos.</p>
    </header>

    <section id="responsible">
        <h2>1. Quién es responsable de sus datos</h2>
        <p>Cada refugio que utiliza la plataforma es el <strong>responsable del tratamiento</strong> de los datos que recoge en el ejercicio de su actividad (por ejemplo, datos de adoptantes, padrinos y voluntarios). {{ config('app.name') }} proporciona la plataforma tecnológica y actúa como <strong>encargado del tratamiento</strong> para esos refugios, tratando los datos únicamente siguiendo sus instrucciones.</p>
        <p>{{ config('app.name') }} es el responsable de los datos relativos a las cuentas de usuario de la plataforma y a los visitantes de la página pública.</p>
    </section>

    <section id="data">
        <h2>2. Qué datos tratamos</h2>

        <h3>Visitantes de la página pública</h3>
        <p>Puede ver los animales en adopción sin crear una cuenta. Solo tratamos los datos técnicos necesarios para que el sitio funcione: dirección IP, tipo de navegador y dispositivo, y una cookie de sesión. Si envía una solicitud de adopción, también tratamos los datos que se describen a continuación.</p>

        <h3>Usuarios de la plataforma (equipos de los refugios)</h3>
        <ul>
            <li>Nombre y dirección de email;</li>
            <li>Contraseña (guardada únicamente de forma cifrada, nunca en texto legible);</li>
            <li>El refugio o refugios a los que pertenece y su función (administrador, gestor o empleado);</li>
            <li>Fecha del último acceso y datos de sesión (dirección IP y navegador).</li>
        </ul>

        <h3>Solicitantes de adopción</h3>
        <ul>
            <li>Nombre, correo electrónico, teléfono, código postal y ciudad;</li>
            <li>El animal que solicita, sus respuestas sobre su hogar (tipo de vivienda, jardín, niños y otros animales) y su motivación;</li>
            <li>La fecha en que dio su consentimiento y la dirección IP desde la que se envió la solicitud, conservada solo para proteger el formulario contra abusos.</li>
        </ul>
        <p>La solicitud solo se envía al refugio que cuida de ese animal, que la utiliza para valorar la adopción. Si se aprueba, sus datos de contacto pasan a formar parte del registro de adopción.</p>

        <h3>Adoptantes</h3>
        <ul>
            <li>Nombre, email, teléfono, dirección, código postal y ciudad;</li>
            <li>El animal adoptado, la fecha de adopción y, en su caso, la fecha de devolución;</li>
            <li>Tasa de adopción y notas registradas por el refugio.</li>
        </ul>

        <h3>Padrinos</h3>
        <ul>
            <li>Nombre, email, teléfono, dirección, código postal y ciudad;</li>
            <li>El animal apadrinado y el historial de pagos (fechas, periodos e importes);</li>
            <li>Preferencias de comunicación (novedades sobre el animal y/o boletín).</li>
        </ul>

        <h3>Voluntarios</h3>
        <ul>
            <li>Nombre, sexo, fecha de nacimiento y fotografía;</li>
            <li>Número de documento de identidad y número de identificación fiscal (NIF);</li>
            <li>Datos de contacto, dirección, profesión y medio de transporte;</li>
            <li>Disponibilidad, fechas de inicio y fin de la colaboración, y evaluaciones de asistencia y desempeño;</li>
            <li>Preferencia de boletín.</li>
        </ul>

        <p>La página pública solo muestra información sobre los animales (fotografías, características y descripción) y los datos de contacto del refugio. <strong>Nunca</strong> publica datos de adoptantes, padrinos, voluntarios o usuarios.</p>
    </section>

    <section id="purposes">
        <h2>3. Para qué usamos los datos y con qué base jurídica</h2>
        <ul>
            <li><strong>Gestionar adopciones, apadrinamientos y voluntariado</strong> &mdash; ejecución del acuerdo con usted o aplicación de medidas precontractuales a petición suya (art. 6.1.b) RGPD);</li>
            <li><strong>Valorar las solicitudes de adopción</strong> &mdash; aplicación de medidas precontractuales a petición suya (art. 6.1.b) RGPD), con el consentimiento que da en el formulario; la dirección IP se conserva para proteger el formulario contra abusos &mdash; interés legítimo (art. 6.1.f));</li>
            <li><strong>Hacer el seguimiento del bienestar de los animales tras la adopción</strong> &mdash; interés legítimo del refugio en la protección animal (art. 6.1.f));</li>
            <li><strong>Cumplir obligaciones legales</strong>, como las normas fiscales y el registro e identificación de animales de compañía (art. 6.1.c));</li>
            <li><strong>Enviar boletines y novedades sobre un animal apadrinado</strong> &mdash; su consentimiento, que puede retirar en cualquier momento (art. 6.1.a));</li>
            <li><strong>Mantener la plataforma segura y en funcionamiento</strong>, incluido el control de acceso y los registros técnicos &mdash; interés legítimo (art. 6.1.f)).</li>
        </ul>
    </section>

    <section id="cookies">
        <h2>4. Cookies</h2>
        <p>Solo utilizamos cookies y almacenamiento local <strong>estrictamente necesarios</strong> para el funcionamiento del sitio o para una funcionalidad que usted haya solicitado expresamente, por lo que, según el artículo 22.2 de la Ley 34/2002, de servicios de la sociedad de la información y de comercio electrónico (LSSI-CE), no se requiere su consentimiento. No utilizamos cookies publicitarias, analíticas ni de terceros, y todas las fuentes y recursos se sirven desde nuestros propios servidores.</p>
        <div class="overflow-x-auto rounded-2xl ring-1 ring-amber-100 dark:ring-stone-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold tracking-wide text-stone-500 uppercase dark:bg-stone-800 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Finalidad</th>
                        <th class="px-4 py-3">Duración</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 dark:divide-stone-800">
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ config('session.cookie') }}</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Mantiene su sesión mientras navega por el sitio.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutos de inactividad</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Protege los formularios frente a peticiones falsificadas desde otros sitios.</td>
                        <td class="px-4 py-3">{{ config('session.lifetime') }} minutos de inactividad</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Cookie</td>
                        <td class="px-4 py-3">Mantiene su sesión iniciada, solo si marca &ldquo;Recordarme&rdquo; al iniciar sesión.</td>
                        <td class="px-4 py-3">400 días o hasta que cierre la sesión</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">flux.appearance</td>
                        <td class="px-4 py-3">Almacenamiento local</td>
                        <td class="px-4 py-3">Guarda su preferencia de tema claro u oscuro, solo si elige uno en los ajustes.</td>
                        <td class="px-4 py-3">Hasta que lo borre en su navegador</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>Puede eliminar o bloquear las cookies en la configuración de su navegador; si bloquea las cookies de sesión, no podrá iniciar sesión en el área de los refugios.</p>
    </section>

    <section id="sharing">
        <h2>5. Con quién compartimos los datos</h2>
        <p>No vendemos ni cedemos sus datos con fines comerciales. Los datos de cada refugio solo son accesibles para el equipo de ese refugio y para los administradores de la plataforma. También pueden ser tratados por proveedores de servicios que nos ayudan a operar la plataforma (alojamiento y envío de emails), siempre con las garantías contractuales adecuadas, o comunicados a las autoridades cuando así lo exija la ley.</p>
    </section>

    <section id="retention">
        <h2>6. Durante cuánto tiempo conservamos los datos</h2>
        <ul>
            <li><strong>Cuentas de usuario:</strong> mientras la cuenta esté activa;</li>
            <li><strong>Solicitudes de adopción:</strong> se eliminan automáticamente 6 meses después de su última modificación;</li>
            <li><strong>Adopciones y apadrinamientos:</strong> durante el tiempo necesario para el seguimiento del animal y para cumplir las obligaciones legales aplicables;</li>
            <li><strong>Voluntarios:</strong> durante la colaboración y, después, solo durante el periodo exigido por la ley;</li>
            <li><strong>Sesiones:</strong> caducan automáticamente tras un periodo de inactividad.</li>
        </ul>
        <p>Los registros eliminados pueden conservarse durante un periodo limitado, fuera del acceso normal, con fines de auditoría y recuperación de errores.</p>
    </section>

    <section id="rights">
        <h2>7. Sus derechos</h2>
        <p>Puede solicitar en cualquier momento el <strong>acceso</strong>, la <strong>rectificación</strong> o la <strong>supresión</strong> de sus datos, la <strong>limitación</strong> del tratamiento o la <strong>portabilidad</strong> de los datos, <strong>oponerse</strong> al tratamiento basado en el interés legítimo y <strong>retirar cualquier consentimiento</strong> que haya dado, sin que ello afecte al tratamiento realizado con anterioridad.</p>
        <p>Para ejercer estos derechos, póngase en contacto directamente con el refugio con el que trató (sus datos de contacto aparecen en la página de cada animal)@if ($contactEmail), o escríbanos a <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif. Le responderemos en el plazo máximo de un mes.</p>
        <p>También tiene derecho a presentar una reclamación ante la Agencia Española de Protección de Datos (AEPD) en <a href="https://www.aepd.es" target="_blank" rel="noopener">www.aepd.es</a>.</p>
    </section>

    <section id="security">
        <h2>8. Seguridad</h2>
        <p>Las contraseñas se guardan cifradas, el acceso es solo por invitación y cada usuario solo puede ver los datos del refugio al que pertenece. Aplicamos medidas técnicas y organizativas para proteger los datos frente a accesos no autorizados, pérdida o alteración.</p>
    </section>

    <section id="changes">
        <h2>9. Cambios en esta política</h2>
        <p>Podemos actualizar esta política para reflejar cambios en la plataforma o en la ley. La fecha de la última actualización se muestra siempre en la parte superior de esta página.</p>
    </section>
</div>
