<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'title' => 'Normas de la Comunidad',
                'slug' => 'normas',
                'excerpt' => 'Normas de la comunidad JaJa Project para mantener un ambiente respetuoso y divertido. Conoce las reglas antes de participar.',
                'content' => '¡Bienvenid@ a nuestra comunidad! Para mantener un ambiente divertido y respetuoso para todos, te pedimos que sigas estas normas:

## Respeto y Convivencia

- **Respeta a todos los miembros** de la comunidad, independientemente de su origen, orientación, religión o cualquier otra característica personal
- **No insultes ni ataques** a ningún colectivo, grupo o persona
- **Evita el lenguaje ofensivo** y las expresiones discriminatorias
- **Mantén un tono amigable** y constructivo en todas tus interacciones

## Contenido Apropiado

- **No subas contenido explícito**, violento o inapropiado
- **Evita temas controvertidos** como política partidista o religión
- **No hagas spam** ni subas contenido repetitivo
- **Verifica que tu contenido** sea original o que tengas derecho a compartirlo
- **Asegúrate de que sea humor sano** y no dañe la dignidad de ninguna persona

## Calidad del Contenido

- **Redacta con buena ortografía** y gramática en la medida de lo posible
- **Sé creativo y original** en tus aportes
- **Revisa tu contenido** antes de enviarlo
- **Utiliza títulos descriptivos** que ayuden a entender el contexto

## Uso de la API

- **No abuses de las consultas** a nuestra API
- **Respeta los límites de velocidad** establecidos
- **Utiliza la API de forma responsable** y ética
- **No intentes vulnerar** la seguridad del sistema

## Moderación

- **Nuestro equipo revisa** todo el contenido antes de publicarlo
- **Nos reservamos el derecho** de rechazar contenido que no cumpla estas normas
- **Las violaciones repetidas** pueden resultar en restricciones de acceso
- **Si tienes dudas**, contacta con nosotros antes de enviar contenido

## Colaboración

- **Ayuda a otros usuarios** cuando sea posible
- **Reporta contenido inapropiado** si lo encuentras
- **Sugiere mejoras** para la plataforma
- **Participa activamente** en la comunidad

---

**Recuerda**: Estamos aquí para divertirnos y crear juntos. ¡Mantengamos este espacio positivo para todos!

*Estas normas pueden actualizarse periódicamente. Te notificaremos de cualquier cambio importante.*',
                'keywords' => 'normas, reglas, comunidad, respeto, contenido apropiado, moderación, api, convivencia, humor sano',
            ],
            [
                'title' => 'Política de Privacidad',
                'slug' => 'politica-de-privacidad',
                'excerpt' => 'Política de privacidad de JaJa Project. Información sobre cómo tratamos tus datos personales y qué información recopilamos.',
                'content' => '*Última actualización: '.date('d/m/Y').'*

En **JaJa Project** respetamos tu privacidad y nos comprometemos a proteger tus datos personales. Esta política explica qué información recopilamos y cómo la utilizamos.

## Información que Recopilamos

### Información Mínima Necesaria
- **Nick de usuario** (opcional): Solo si decides proporcionarlo para atribuir la autoría de tu contenido
- **Dirección IP**: Únicamente para mantener la seguridad básica y prevenir abusos
- **Datos de navegación**: Información técnica básica para el funcionamiento del sitio

### Información que NO Recopilamos
- No solicitamos ni almacenamos datos personales identificables
- No requerimos registro obligatorio
- No utilizamos cookies de seguimiento propias
- No vendemos ni compartimos tus datos con terceros

## Uso de la Información

### Propósitos Legítimos
- **Seguridad**: Prevenir spam y abusos del sistema
- **Funcionalidad**: Garantizar el correcto funcionamiento de la plataforma
- **Autoría**: Atribuir el contenido al nick proporcionado voluntariamente

### Retención de Datos
- **Nick de usuario**: Se mantiene asociado al contenido publicado
- **Datos técnicos**: Se eliminan automáticamente según políticas de servidor
- **Contenido enviado**: Se conserva para su distribución según nuestras condiciones de uso

## Servicios de Terceros

### Google reCAPTCHA v3
- Implementado para prevenir spam automatizado
- Sujeto a la política de privacidad de Google
- Necesario para el funcionamiento de los formularios

## Tus Derechos

### Control de Datos
- **Anonimato**: Puedes participar sin proporcionar datos personales
- **Acceso**: Puedes solicitar información sobre datos asociados a tu IP
- **Rectificación**: Puedes solicitar correcciones de contenido publicado
- **Eliminación**: Puedes solicitar la eliminación de tu contenido

### Contacto
Para ejercer tus derechos o resolver dudas sobre privacidad, contacta con nosotros a través de nuestros canales oficiales.

## Menores de Edad

Esta plataforma está diseñada para uso general, pero recomendamos supervisión parental para menores de 13 años.

## Cambios en la Política

Cualquier modificación de esta política será comunicada en esta página. El uso continuado del sitio implica la aceptación de los cambios.

## Cumplimiento Legal

Esta política cumple con la normativa aplicable en materia de protección de datos, incluyendo el RGPD europeo cuando corresponda.

---

**Resumen**: En JaJa Project creemos en la privacidad por diseño. Recopilamos solo lo imprescindible para ofrecer nuestro servicio de forma segura.',
                'keywords' => 'privacidad, datos personales, protección datos, RGPD, cookies, reCAPTCHA, anonimato',
            ],
            [
                'title' => 'Política de Cookies',
                'slug' => 'politica-de-cookies',
                'excerpt' => 'Información sobre el uso de cookies en JaJa Project. Detalles sobre seguridad y Google reCAPTCHA v3.',
                'content' => '*Última actualización: '.date('d/m/Y').'*

## ¿Qué son las Cookies?

Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo cuando visitas un sitio web. Nos ayudan a mejorar tu experiencia y el funcionamiento del sitio.

## Cookies que Utilizamos

### Cookies Propias
**JaJa Project no utiliza cookies propias** para el seguimiento o almacenamiento de información personal.

### Cookies de Terceros

#### Google reCAPTCHA v3
- **Finalidad**: Protección contra spam y bots automatizados
- **Tipo**: Cookies de seguridad
- **Duración**: Según política de Google
- **Datos recopilados**:
  - Interacciones con el sitio
  - Puntuación de comportamiento humano
  - Datos técnicos del navegador
- **Base legal**: Interés legítimo para mantener la seguridad

## Gestión de Cookies

### Aceptación
Al continuar navegando por nuestro sitio, aceptas el uso de estas cookies de terceros necesarias para el funcionamiento básico.

### Control de Cookies
Puedes gestionar las cookies a través de:

#### Configuración del Navegador
- **Chrome**: Configuración > Privacidad y seguridad > Cookies
- **Firefox**: Opciones > Privacidad y seguridad > Cookies
- **Safari**: Preferencias > Privacidad > Cookies
- **Edge**: Configuración > Cookies y permisos del sitio

### Consecuencias de Desactivar Cookies
- **Google reCAPTCHA**: Puede impedir el envío de formularios

## Cookies Técnicas

### Cookies de Sesión
Utilizamos cookies de sesión mínimas para:
- Mantener la funcionalidad básica del sitio
- Recordar preferencias temporales
- Gestionar formularios de envío

Estas cookies son **estrictamente necesarias** y no requieren consentimiento.

## Transferencias Internacionales

Las cookies de Google pueden implicar transferencias de datos a Estados Unidos y otros países. Google cumple con los marcos de protección de datos aplicables.

## Actualización de la Política

Esta política se actualiza cuando cambian nuestras prácticas de cookies. La fecha de última actualización aparece al inicio del documento.

## Más Información

### Enlaces Útiles
- [Política de Privacidad de Google](https://policies.google.com/privacy)
- [Cómo gestionar cookies en tu navegador](https://www.aboutcookies.org/)

### Contacto
Para preguntas específicas sobre cookies, contacta con nosotros a través de nuestros canales oficiales.

---

**En resumen**: Utilizamos únicamente cookies de terceros esenciales (reCAPTCHA) para ofrecer un servicio seguro y mejorado.',
                'keywords' => 'cookies, reCAPTCHA, navegador, privacidad, seguimiento, terceros, configuración',
            ],
            [
                'title' => 'Condiciones de Uso',
                'slug' => 'condiciones-de-uso',
                'excerpt' => 'Condiciones de uso de JaJa Project. Términos legales sobre el contenido que compartes y los derechos de la plataforma.',
                'content' => '*Última actualización: '.date('d/m/Y').'*

## Aceptación de las Condiciones

Al acceder y utilizar **JaJa Project**, aceptas estar legalmente vinculado por estas condiciones de uso. Si no aceptas alguna de estas condiciones, no utilices nuestros servicios.

## Cesión de Derechos sobre el Contenido

### Licencia Otorgada
Al enviar cualquier contenido (texto, imágenes, chistes, adivinanzas, preguntas, etc.) a JaJa Project, **otorgas automáticamente** a la plataforma una licencia:

- **Perpetua**: Sin límite de tiempo
- **Irrevocable**: No puede ser retirada posteriormente
- **Mundial**: Válida en todo el mundo
- **No exclusiva**: Conservas derechos sobre tu contenido original
- **Gratuita**: Sin compensación económica
- **Transferible**: Podemos ceder estos derechos a terceros

### Derechos Cedidos
Esta licencia incluye el derecho a:
- **Reproducir** el contenido en cualquier formato
- **Distribuir** a través de cualquier medio o canal
- **Comunicar públicamente** sin restricciones
- **Transformar y adaptar** según necesidades técnicas
- **Sublicenciar** a terceros para los mismos fines
- **Utilizar comercialmente** sin limitaciones

### Reconocimiento de Autoría
- **Nos comprometemos** a reconocer tu autoría cuando sea técnicamente posible
- **El nick proporcionado** se mantendrá asociado al contenido
- **No garantizamos** que terceros que utilicen nuestro contenido mantengan la atribución

## Garantías y Responsabilidades del Usuario

### Declaraciones del Usuario
Al enviar contenido, declaras y garantizas que:
- **Eres el autor original** o tienes todos los derechos necesarios
- **El contenido no infringe** derechos de terceros
- **Tienes capacidad legal** para otorgar esta licencia
- **El contenido cumple** con nuestras normas de comunidad

### Exoneración de Responsabilidad
El usuario **exonera completamente** a JaJa Project de cualquier reclamación relacionada con:
- Infracciones de derechos de autor
- Conflictos de autoría
- Uso del contenido por terceros
- Pérdida de control sobre el contenido cedido

## Uso de la Plataforma

### Servicios Ofrecidos
JaJa Project proporciona:
- Plataforma web para compartir contenido humorístico
- API para acceso programático al contenido
- Herramientas de comunidad y moderación

### Limitaciones de Uso
- **No utilices** la plataforma para fines ilegales
- **Respeta** los límites técnicos establecidos
- **No intentes** vulnerar la seguridad del sistema
- **Cumple** con nuestras normas de comunidad

## Propiedad Intelectual

### Contenido de la Plataforma
- El **software y diseño** de JaJa Project están compartidos de forma abierta y gratuita bajo licencia GNU GPLv3
- **Nuestras marcas** y logotipos son propiedad exclusiva
- **El contenido enviado** por usuarios está sujeto a las condiciones aquí establecidas

### Respeto a Terceros
Nos comprometemos a:
- **Retirar contenido** que infrinja derechos de terceros cuando seamos notificados
- **Cooperar** con titulares de derechos legítimos
- **Mantener** procedimientos de notificación y retirada

## Modificaciones del Servicio

### Cambios en la Plataforma
- **Podemos modificar** o discontinuar servicios en cualquier momento
- **No garantizamos** la disponibilidad perpetua del contenido
- **Los cambios importantes** serán comunicados con antelación razonable

### Cambios en las Condiciones
- **Estas condiciones** pueden ser actualizadas periódicamente
- **El uso continuado** tras las modificaciones implica aceptación
- **Los cambios sustanciales** serán notificados prominentemente

## Limitación de Responsabilidad

### Descargo de Garantías
JaJa Project se proporciona **"tal como está"**, sin garantías de:
- Disponibilidad ininterrumpida
- Ausencia de errores
- Adecuación para fines específicos
- Seguridad absoluta de los datos

### Limitación de Daños
En ningún caso seremos responsables de:
- **Daños indirectos** o consecuenciales
- **Pérdida de beneficios** u oportunidades
- **Daños superiores** al valor de los servicios utilizados

## Resolución de Disputas

### Ley Aplicable
Estas condiciones se rigen por la **legislación española** y los tribunales competentes de España.

### Resolución Amistosa
Antes de iniciar acciones legales, las partes intentarán resolver las disputas de forma amistosa.

## Contacto

Para cuestiones relacionadas con estas condiciones, contacta con nosotros a través de nuestros canales oficiales.

---

**IMPORTANTE**: Al enviar contenido a JaJa Project, cedes irrevocablemente los derechos de distribución y uso comercial, manteniendo únicamente el reconocimiento de autoría cuando sea posible.',
                'keywords' => 'condiciones uso, cesión derechos, licencia contenido, propiedad intelectual, responsabilidad legal, términos servicio',
            ],
            [
                'title' => 'Agradecimientos',
                'slug' => 'agradecimientos',
                'excerpt' => 'Agradecimientos a toda la comunidad que hace posible JaJa Project. Reconocimiento a colaboradores y contribuidores.',
                'content' => '## ¡Gracias por Hacer Esto Posible!

En **JaJa Project** creemos que la risa se multiplica cuando se comparte, y esto solo es posible gracias a una increíble comunidad de personas que aportan su tiempo, talento y humor para crear algo especial juntos.

## A Nuestra Comunidad de Creadores

### Los Jajajeros Incansables
**¡Sois el corazón de esta plataforma!** Cada chiste que envías, cada adivinanza que compartes, cada pregunta ingeniosa que propones hace que JaJa Project crezca y sea más divertido para todos.

## A Los Desarrolladores y Colaboradores Técnicos

### Equipo Principal
Gracias a todos los que han puesto código, ideas y esfuerzo en hacer realidad esta plataforma:

- **Desarrolladores**: Por convertir ideas en funcionalidades
- **Diseñadores**: Por hacer que todo se vea genial y sea fácil de usar
- **Testers**: Por encontrar bugs antes que los usuarios (¡casi siempre!)
- **Documentadores**: Por explicar cómo funciona todo esto

## A Los Bots y Sus Creadores

### Desarrolladores de Bots
**¡Los verdaderos MVP!** Gracias a quienes:
- Integran nuestra API en sus proyectos
- Crean bots que llevan nuestro contenido a Discord, Telegram, Twitch y más
- Innovan con formas creativas de usar nuestro contenido
- Reportan bugs y sugieren mejoras en la API

## A Los Moderadores Silenciosos

### El Equipo de Moderación
Aunque trabajáis en las sombras, sabemos que:
- **Revisáis cada envío** para mantener la calidad
- **Filtráis contenido inapropiado** para mantener un ambiente sano
- **Respondéis consultas** y ayudáis a resolver problemas
- **Mantenéis viva** la comunidad día tras día

## A Los Early Adopters

### Los Primeros en Llegar
Gracias a quienes creyeron en este proyecto desde el principio:
- **Beta testers**: Por probar funcionalidades cuando aún tenían bugs
- **Primeros usuarios**: Por enviar contenido cuando la plataforma era nueva
- **Evangelistas**: Por compartir JaJa Project con sus amigos y comunidades

## A Los Críticos Constructivos

### Feedback Valioso
Gracias a quienes nos ayudan a mejorar:
- **Reportando errores** de forma detallada
- **Sugiriendo nuevas funcionalidades** útiles
- **Dando feedback** sobre usabilidad y experiencia
- **Siendo pacientes** mientras arreglamos problemas

## A Las Plataformas y Servicios

### Infraestructura que Nos Sostiene
Reconocimiento a los servicios que hacen posible JaJa Project:
- **Hosting providers**: Por mantener nuestros servidores funcionando
- **CDN services**: Por hacer que todo cargue rápido
- **Monitoring tools**: Por avisarnos cuando algo va mal
- **Open source projects**: Por las herramientas que usamos

## Mensaje Personal

### De Parte del Equipo
No somos una gran corporación con presupuestos millonarios. Somos un grupo de personas que cree que el mundo necesita más risas y que la tecnología puede ayudar a conectar a las personas a través del humor.

**Cada chiste que envías, cada vez que usas nuestra API, cada sonrisa que generamos juntos hace que todo este esfuerzo valga la pena.**

## ¿Quieres Aparecer Aquí?

### Cómo Contribuir
Hay muchas formas de formar parte de esta historia:
- **Envía contenido** original y de calidad
- **Desarrolla un bot** usando nuestra API
- **Comparte** JaJa Project con tus amigos
- **Reporta bugs** y sugiere mejoras
- **Participa** en nuestra comunidad

---

## ¡Sigamos Riendo Juntos!

**JaJa Project** es más que una plataforma: es una comunidad de personas que cree que la risa puede hacer el mundo un poquito mejor. Gracias por ser parte de esta aventura.

*¿Se nos olvida alguien? ¡Contáctanos! Queremos asegurarnos de que todos los que contribuyen reciban el reconocimiento que merecen.*

---

**PD**: Si tu bot cuenta mejores chistes que los nuestros, prometemos no tener envidia... bueno, quizá un poquito 😉',
                'keywords' => 'agradecimientos, comunidad, colaboradores, desarrolladores, bots, moderadores, contribuidores, open source',
            ],
            [
                'title' => 'Sobre Nosotros',
                'slug' => 'about',
                'excerpt' => 'Conoce más sobre JaJa Project y nuestra comunidad. Descubre quiénes somos y qué nos motiva a crear esta plataforma de humor.',
                'content' => '**JaJa Project** nace de una idea simple pero poderosa: **el mundo necesita más risas**, y la tecnología puede ayudarnos a compartirlas de formas nuevas e innovadoras.

Somos una **comunidad abierta** de desarrolladores, humoristas amateur, creadores de contenido y, sobre todo, personas que creen que una buena carcajada puede alegrar el día a cualquiera.

## Nuestra Misión

### Democratizar el Humor
Creemos que **todos tenemos algo gracioso que compartir**. No importa si eres un comediante profesional o alguien que acaba de recordar un chiste de su infancia: aquí tu contenido tiene cabida.

### Conectar Comunidades
A través de nuestra **API abierta**, llevamos el humor a diferentes plataformas: bots de Discord, canales de Twitch, aplicaciones móviles, y cualquier lugar donde la gente se reúna digitalmente.

### Crear Juntos
No somos solo una plataforma donde consumir contenido. Somos un espacio donde **la comunidad crea, comparte y mejora** el contenido de forma colaborativa.

## Qué Nos Hace Diferentes

### Open Source Spirit
- **API gratuita** para uso personal y comunitario
- **Código abierto** (cuando sea posible) para transparencia
- **Comunidad primero**, beneficios después

### Tecnología al Servicio del Humor
- **Moderación inteligente** para mantener la calidad
- **Distribución automatizada** a través de APIs
- **Estadísticas transparentes** sobre el contenido

### Humor Responsable
- **Sin límites creativos** pero con respeto hacia todos
- **Moderación humana** para mantener un ambiente sano
- **Diversidad de formatos**: chistes, adivinanzas, quiz

## Nuestros Valores

### 🤝 Comunidad
Creemos en el poder de las personas trabajando juntas. Cada contribución, por pequeña que sea, hace la diferencia.

### 😄 Humor Positivo
Promovemos la risa que une, no la que divide. El humor puede ser inteligente, absurdo, sutil o directo, pero siempre debe ser respetuoso.

### 🔧 Innovación Abierta
Experimentamos con nuevas formas de crear, compartir y disfrutar el contenido humorístico a través de la tecnología.

### 🌍 Accesibilidad
Queremos que cualquier persona, independientemente de sus habilidades técnicas, pueda participar y beneficiarse de nuestra plataforma.

## La Historia Detrás del Proyecto

### Los Inicios
Todo empezó con una pregunta simple: **"¿Por qué los bots siempre cuentan los mismos chistes aburridos?"**

Queríamos crear algo diferente: una plataforma donde los bots pudieran acceder a contenido fresco, creado por personas reales, y donde la comunidad fuera la verdadera protagonista.

### El Crecimiento
Lo que comenzó como un experimento se convirtió en una comunidad vibrante de:
- **Creadores de contenido** que aportan humor original
- **Desarrolladores** que integran nuestra API en sus proyectos
- **Usuarios** que disfrutan del contenido en diferentes plataformas

### El Presente
Hoy, JaJa Project es:
- Una **biblioteca viva** de contenido humorístico
- Una **API robusta** utilizada por múltiples aplicaciones
- Una **comunidad activa** que crece cada día

## Nuestro Enfoque Técnico

### Calidad sobre Cantidad
- **Moderación manual** de todo el contenido
- **Filtros inteligentes** para evitar duplicados
- **Categorización precisa** para facilitar el descubrimiento

### Escalabilidad Responsable
- **Infraestructura eficiente** que crece con la demanda
- **Rate limiting inteligente** para uso justo de recursos
- **Caché optimizado** para respuestas rápidas

### Privacidad por Diseño
- **Mínima recolección de datos** personales
- **Anonimato opcional** para los contribuidores
- **Transparencia total** sobre el uso de la información

## La Comunidad

### Desarrolladores
Programadores que crean bots, aplicaciones y servicios que consumen nuestra API, llevando el humor a nuevas plataformas.

### Creadores de Contenido
Personas con sentido del humor que aportan chistes originales, adivinanzas ingeniosas y preguntas divertidas.

### Usuarios Finales
Gente que disfruta del contenido a través de bots en Discord, comandos en Twitch, o directamente en nuestra web.

### Moderadores
Voluntarios que ayudan a mantener la calidad y el ambiente positivo de nuestra comunidad.

## Mirando al Futuro

### Próximos Pasos
- **Expansión internacional** con soporte multiidioma
- **Nuevos formatos** de contenido interactivo
- **Herramientas avanzadas** para creadores de bots
- **Integración** con más plataformas y servicios

### La Visión a Largo Plazo
Queremos que **JaJa Project** se convierta en la referencia mundial para contenido humorístico generado por comunidades, donde cualquier desarrollador pueda acceder fácilmente a humor de calidad y cualquier persona pueda contribuir con su creatividad.

## ¿Cómo Puedes Formar Parte?

### Para Creadores
- **Envía tu contenido** original a través de nuestro formulario
- **Participa** en eventos y concursos de la comunidad
- **Ayuda** a moderar y mejorar el contenido existente

### Para Desarrolladores
- **Úsala nuestra API** en tus proyectos
- **Contribuye** al desarrollo de la plataforma
- **Comparte** tus creaciones con la comunidad

### Para Usuarios
- **Disfruta** del contenido en tus plataformas favoritas
- **Comparte** JaJa Project con tus amigos
- **Sugiere mejoras** y nuevas funcionalidades

---

## El Equipo

Somos un grupo diverso de **desarrolladores, diseñadores, community managers y humoristas amateur** unidos por la pasión de crear algo único y útil.

**¿Quieres conocernos mejor o tienes ideas para colaborar?** ¡Contáctanos!

---

*En JaJa Project creemos que la mejor tecnología es la que conecta personas y genera sonrisas. ¿Te unes a nosotros en esta misión?*',
                'keywords' => 'sobre nosotros, comunidad, misión, valores, historia, equipo, futuro, colaboración, humor, tecnología',
            ],
            [
                'title' => 'Documentación API',
                'slug' => 'api',
                'excerpt' => 'Documentación técnica de la API de JaJa Project. Aprende cómo integrar nuestro contenido en tus aplicaciones y bots.',
                'content' => '¡Bienvenid@ a la documentación de nuestra API! Aquí encontrarás toda la información necesaria para integrar el contenido de **JaJa Project** en tus aplicaciones, bots y proyectos.

---

*Para acceder a la documentación completa con ejemplos, endpoints, autenticación y casos de uso, visita nuestra documentación técnica detallada en los enlaces más abajo.*',
                'keywords' => 'API, documentación, endpoints, autenticación, integración, bots, desarrollo, aplicaciones',
            ],
            [
                'title' => 'Bot para Twitch',
                'slug' => 'bot-twitch',
                'excerpt' => 'Información sobre el desarrollo del bot para twitch de JaJa Project.',
                'content' => 'Aún no disponible. ¡Estará pronto muahahaha!',
                'keywords' => 'API, documentación, endpoints, autenticación, integración, bots, desarrollo, aplicaciones',
            ],
        ];

        foreach ($data as $page) {
            if (Page::where('slug', $page['slug'])->exists()) {
                continue;
            }

            Page::create(array_merge($page, [
                'status' => 'published',
            ]));
        }
    }
}
