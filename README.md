# NixoList

Aquí tienes una propuesta de README.md profesional y equilibrada. Está redactada en plural, reflejando el trabajo en equipo y cubriendo todos los módulos que hemos ido ajustando (filtros de medios, amistades, seguridad y diseño).

NixoList 🎬📖
NixoList es una plataforma web integral diseñada para que los entusiastas del contenido multimedia puedan organizar, puntuar y compartir sus listas de Anime, Manga, Películas y Series. El proyecto nace con el objetivo de ofrecer una experiencia personalizada y social, permitiendo a los usuarios conectar entre sí mientras mantienen un registro detallado de su progreso.

🚀 Funcionalidades Principales
Gestión de Listas Personalizadas: Los usuarios pueden añadir contenido a diferentes estados: Viendo, Completado, En Pausa, Abandonado o Planeado.

Filtros Inteligentes: Separación estricta de contenidos. La web diferencia automáticamente entre títulos provenientes de MyAnimeList (Anime/Manga) y TMDB (Películas/Series de TV).

Sistema de Amistades: Un módulo social completo que permite enviar, aceptar y gestionar solicitudes de amistad, facilitando la visualización de listas de otros usuarios.

Sección de Juegos: Minijuegos interactivos como "Adivina el Opening", "Adivina el Personaje" y "Wordle Anime" para fomentar el engagement.

🛠️ Stack Tecnológico
Frontend: HTML5, CSS3 (con un estilo minimalista en tonos gris, negro y amarillo) y JavaScript para interactividad y validaciones en tiempo real.

Backend: PHP 8.x para la lógica de servidor y gestión de sesiones seguras.

Base de Datos: MySQL, con una arquitectura relacional para usuarios, medios y relaciones de amistad.

Seguridad: Implementación de password_hash para credenciales y validaciones de servidor mediante expresiones regulares para prevenir datos corruptos o malintencionados.

🔐 Seguridad y Validaciones
Hemos puesto especial énfasis en la integridad del sistema:

Validación de Usuario: Restricción a caracteres alfanuméricos (sin símbolos especiales) y comprobación de disponibilidad en la base de datos.

Validación de Correo: Verificación de formato estándar para garantizar comunicaciones efectivas.

Fortaleza de Contraseña: Requisito mínimo de 6 caracteres, incluyendo obligatoriamente una mayúscula y un número.

🎨 Identidad Visual
El diseño de NixoList apuesta por el minimalismo geométrico. La paleta de colores se centra en un contraste elegante entre negro mate, gris neutro y un amarillo vibrante para los elementos de acción (CTAs), eliminando reflejos y efectos complejos para priorizar la usabilidad y la claridad del contenido.

📌 Instalación y Uso
Clonar el repositorio.

Importar la base de datos nixolist.sql en tu servidor MySQL.

Configurar el archivo conexion.php con tus credenciales locales.

Iniciar sesión o registrarse para empezar a gestionar tu lista de medios.

Desarrollado como un proyecto de gestión de medios y redes sociales.
