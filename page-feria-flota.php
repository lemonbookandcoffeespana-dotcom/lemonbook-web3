<?php
/**
 * Template Name: Feria del Libro y Mercadillo (La Flota)
 *
 * II Feria del Libro y Mercadillo Artesanal en La Flota — organizada por la Junta Municipal
 * Vistalegre-La Flota (Lemon solo gestiona la venta de libros). Evento externo y puntual, no
 * pertenece a gestion: por eso el contenido va aquí en vez de salir de lemon_fair().
 *
 * Reutiliza la misma base visual que la landing de feria de gestion (clases .fair-*, ver
 * page-feria.php): un solo hero, introducción en tarjeta, programa agrupado en bloques,
 * información práctica unida. assets/css/flota.css solo cambia los colores propios de esta
 * página (sin fondo crema ni amarillo, pedido expreso para esta página en concreto).
 *
 * @package LemonBook
 */

$normativa_url = 'https://lemonbookandcoffe.es/wp-content/uploads/2026/03/Normativa-de-Participacion2.pdf';
$mapa_ocupacion = 'https://lemonbookandcoffe.es/wp-content/uploads/2025/04/mapa-de-situacion-576x1024.png';
$maps_url = 'https://maps.app.goo.gl/rL9vSwPQ9S1SA9YM9';
$whatsapp_channel = 'https://whatsapp.com/channel/0029Vb5pPbcEquiN3KsAtM3B';

$features = array(
	array(
		'title' => __( 'Talento literario', 'lemonbook' ),
		'text'  => __( 'Conoce a autores murcianos, asiste a firmas y descubre nuevas lecturas. Lemon Book and Coffee gestiona todas las ventas, así que los títulos están disponibles durante todo el evento.', 'lemonbook' ),
	),
	array(
		'title' => __( 'Artesanía con alma', 'lemonbook' ),
		'text'  => __( 'Un mercado de piezas únicas hechas a mano —cerámica, joyería, ilustración y más— directo de las manos de sus creadores.', 'lemonbook' ),
	),
	array(
		'title' => __( 'Cultura de barrio', 'lemonbook' ),
		'text'  => __( 'Un espacio de encuentro para disfrutar en familia y apoyar el talento de proximidad, adelantándonos a la gran fiesta de las letras.', 'lemonbook' ),
	),
);

// Autores agrupados por turno de firma (tal como los lista la Junta Municipal).
$shifts = array(
	array(
		'day'     => __( 'Viernes 17 de abril', 'lemonbook' ),
		'hours'   => '17:00–21:00',
		'authors' => array(
			array( 'Amor Belmonte Céspedes', 'Adiós Teti (Lemon Book)' ),
			array( 'Carmenza Lemos Ramírez', 'El encuentro de los nuestros' ),
			array( 'Chema Catarineu Guillén', 'Viaje al planeta de los insectos' ),
			array( 'Jose Antonio Enrique Jimenez', 'El diván la aljama y Lacrimatorios' ),
			array( 'José Julio Castro Revorio', 'Vivir sano sin sufrir' ),
			array( 'M. Carmen Briz Marín', 'Cuentos de chocolate' ),
			array( 'M.ª Ángeles Rodríguez', 'Candados para el corazón' ),
			array( 'María Merino Hidalgo', 'El legado de un imperio, Orión' ),
			array( 'Maryluz González', 'Ababaol, en tiempos del Rey Lobo' ),
			array( 'Pedro Juan Martín Castejón', 'La influencia del saber de las estrellas en la fundación de Madinat Mursiya' ),
			array( 'Ruby Ross', 'Power Love, Hasta mi final, Hasta nuestro final y Hasta la eternidad' ),
			array( 'Víctor Dus', 'Mi Juanito me jubila: cuando el fútbol es una presión' ),
		),
	),
	array(
		'day'     => __( 'Sábado 18 de abril', 'lemonbook' ),
		'hours'   => '10:00–14:00',
		'authors' => array(
			array( 'Francisco Javier Moñino Gómez', 'Frontera estelar: la semilla' ),
			array( 'Jose Antonio Enrique Jimenez', 'El diván la aljama y Lacrimatorios' ),
			array( 'José Antonio Sabater', 'Aurora de sueños / Matar no es tan difícil / Rosa blanca' ),
			array( 'Jose Carlos Soto Martínez', 'Manual de metroflexia' ),
			array( 'Lourdes Rey Palau', 'A través de tus ojos' ),
			array( 'María Merino Hidalgo', 'El legado de un imperio, Orión' ),
			array( 'Mary Angels', 'Manolito y los gusanitos' ),
			array( 'Vicente López López', 'Pensamientos del triste poeta' ),
		),
	),
	array(
		'day'     => __( 'Sábado 18 de abril', 'lemonbook' ),
		'hours'   => '17:00–21:00',
		'authors' => array(
			array( 'Carina Pérez Moreno', 'Lo que queda' ),
			array( 'Elizabeth Kaneth', 'Baltrium' ),
			array( 'Joaquín P. Sánchez Onteniente', 'Los fantasmas del Quin. Cuaderno 1' ),
			array( 'Jose Antonio Enrique Jimenez', 'El diván la aljama y Lacrimatorios' ),
			array( 'Juana Cava Martínez', 'Pulso de vida' ),
			array( 'M. Ángeles Amante Barba', 'La vida no es lo que parece' ),
			array( 'María Ángeles Sánchez', '365 pedazos de salud' ),
			array( 'María Merino', 'El legado de un imperio I: Orión' ),
		),
	),
);

$program = array(
	array(
		'day'   => __( 'Viernes 17, tarde', 'lemonbook' ),
		'items' => array(
			array( '14:00', __( 'Montaje', 'lemonbook' ) ),
			array( '17:00', __( 'Inauguración', 'lemonbook' ) ),
		),
	),
	array(
		'day'   => __( 'Sábado 18, mañana', 'lemonbook' ),
		'items' => array(
			array( '09:00', __( 'Montaje', 'lemonbook' ) ),
			array( '14:00', __( 'Cierre mediodía', 'lemonbook' ) ),
		),
	),
	array(
		'day'   => __( 'Sábado 18, tarde', 'lemonbook' ),
		'items' => array(
			array( '17:00', __( 'Apertura', 'lemonbook' ) ),
		),
	),
);

get_header();
?>
<main id="main-content" class="flota-page">
	<header class="fair-hero">
		<div class="shell">
			<p class="eyebrow"><?php esc_html_e( '17 y 18 de abril · Calle Salón, La Flota', 'lemonbook' ); ?></p>
			<h1><?php esc_html_e( 'II Feria del Libro y Mercadillo Artesanal', 'lemonbook' ); ?></h1>
			<p class="fair-hero__tagline"><?php esc_html_e( 'Mil historias te esperan en el barrio.', 'lemonbook' ); ?></p>
			<div class="button-row">
				<a class="button button--accent" href="#autores"><?php esc_html_e( 'Ver autores y horarios', 'lemonbook' ); ?></a>
				<a class="button button--ghost" href="<?php echo esc_url( $normativa_url ); ?>"><?php esc_html_e( 'Descargar normativa', 'lemonbook' ); ?></a>
			</div>
		</div>
	</header>

	<section class="section" id="sobre">
		<div class="shell">
			<div class="fair-intro__card">
				<p class="kicker"><?php esc_html_e( 'Organizada por la Junta Municipal Vistalegre-La Flota', 'lemonbook' ); ?></p>
				<p><?php esc_html_e( 'La II Feria del Libro y Mercadillo Artesanal regresa a la Calle Salón (C/ Juan García Abellán) los días 17 y 18 de abril. Esta edición fusiona la mejor literatura regional con la creatividad de nuestros artesanos en un ambiente primaveral único.', 'lemonbook' ); ?></p>
			</div>
		</div>
	</section>

	<section class="section section--line" aria-labelledby="flota-features-title">
		<div class="shell">
			<header class="section-header"><p class="kicker"><?php esc_html_e( 'La feria', 'lemonbook' ); ?></p><h2 id="flota-features-title"><?php esc_html_e( '¿Qué vas a encontrar?', 'lemonbook' ); ?></h2></header>
			<div class="grid grid--three">
				<?php foreach ( $features as $index => $feature ) : ?>
					<article class="fair-feature-card fair-feature-card--<?php echo esc_attr( $index % 3 ); ?>">
						<span class="fair-feature-card__number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<h3><?php echo esc_html( $feature['title'] ); ?></h3>
						<p><?php echo esc_html( $feature['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section" id="autores" aria-labelledby="flota-authors-title">
		<div class="shell">
			<header class="section-header"><p class="kicker"><?php esc_html_e( 'Programación', 'lemonbook' ); ?></p><h2 id="flota-authors-title"><?php esc_html_e( 'Autores que participan', 'lemonbook' ); ?></h2></header>
			<div class="fair-shifts">
				<?php foreach ( $shifts as $shift ) : ?>
					<article class="fair-shift">
						<h3><?php echo esc_html( $shift['day'] ); ?> <span class="fair-shift__hours"><?php echo esc_html( $shift['hours'] ); ?></span></h3>
						<ul class="fair-author-list">
							<?php foreach ( $shift['authors'] as $author ) : ?>
								<li><strong><?php echo esc_html( $author[0] ); ?></strong><span><?php echo esc_html( $author[1] ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--dark" aria-labelledby="flota-program-title">
		<div class="shell">
			<header class="section-header"><p class="kicker"><?php esc_html_e( 'Día a día', 'lemonbook' ); ?></p><h2 id="flota-program-title"><?php esc_html_e( 'Programación de actividades', 'lemonbook' ); ?></h2></header>
			<div class="grid grid--three">
				<?php foreach ( $program as $day ) : ?>
					<div class="fair-program__day">
						<h3><?php echo esc_html( $day['day'] ); ?></h3>
						<ul class="fair-program__list">
							<?php foreach ( $day['items'] as $item ) : ?>
								<li><span class="fair-program__time"><?php echo esc_html( $item[0] ); ?></span><span><?php echo esc_html( $item[1] ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section" aria-labelledby="flota-practical-title">
		<div class="shell">
			<header class="section-header"><p class="kicker"><?php esc_html_e( 'Información práctica', 'lemonbook' ); ?></p><h2 id="flota-practical-title"><?php esc_html_e( 'Ubicación, mapa y contacto', 'lemonbook' ); ?></h2></header>
			<div class="fair-practical__grid">
				<div class="fair-practical__map">
					<iframe src="https://maps.google.com/maps?q=Calle%20Sal%C3%B3n%2C%20La%20Flota%2C%20Murcia&amp;t=m&amp;z=16&amp;output=embed" title="<?php esc_attr_e( 'Mapa de la Calle Salón, La Flota', 'lemonbook' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					<a class="button button--ghost" href="<?php echo esc_url( $maps_url ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Cómo llegar', 'lemonbook' ); ?></a>
				</div>
				<div class="fair-practical__side">
					<div class="fair-practical__block">
						<h3><?php esc_html_e( 'Mapa de ocupación', 'lemonbook' ); ?></h3>
						<a href="<?php echo esc_url( $mapa_ocupacion ); ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo esc_url( $mapa_ocupacion ); ?>" alt="<?php esc_attr_e( 'Mapa de ocupación de la feria', 'lemonbook' ); ?>" loading="lazy" decoding="async"></a>
					</div>
					<div class="fair-practical__block">
						<h3><?php esc_html_e( 'Contacto', 'lemonbook' ); ?></h3>
						<p><a href="https://wa.me/34668514154"><?php esc_html_e( 'WhatsApp: +34 668 51 41 54', 'lemonbook' ); ?></a></p>
						<p><a href="mailto:ferialaflota@lemonbookandcoffe.es">ferialaflota@lemonbookandcoffe.es</a></p>
						<p><a href="<?php echo esc_url( $whatsapp_channel ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Canal de WhatsApp de Lemon Book and Coffee', 'lemonbook' ); ?></a></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--tertiary" aria-labelledby="flota-cta-title"><div class="shell fair-authors">
		<div>
			<p class="kicker"><?php esc_html_e( '¿Escribes o creas?', 'lemonbook' ); ?></p>
			<h2 id="flota-cta-title"><?php esc_html_e( 'Únete a la feria', 'lemonbook' ); ?></h2>
			<p><?php esc_html_e( 'Descárgate el reglamento y rellena el formulario de participación.', 'lemonbook' ); ?></p>
		</div>
		<div class="button-row">
			<a class="button button--accent" href="<?php echo esc_url( $normativa_url ); ?>"><?php esc_html_e( 'Descargar normativa', 'lemonbook' ); ?></a>
			<a class="button button--ghost" href="mailto:ferialaflota@lemonbookandcoffe.es"><?php esc_html_e( 'Escríbenos para participar', 'lemonbook' ); ?></a>
		</div>
	</div></section>

	<section class="section fair-closing">
		<div class="shell">
			<p class="fair-closing__lead"><?php esc_html_e( '¡Te esperamos con los libros abiertos!', 'lemonbook' ); ?></p>
			<p><?php esc_html_e( 'Gracias por tu interés en formar parte de la II Feria del Libro y Mercadillo Artesanal de La Flota. Nos vemos los días 17 y 18 de abril en la Calle Salón para compartir mil historias, descubrir tesoros artesanos y celebrar juntos el talento de nuestra Región.', 'lemonbook' ); ?></p>
			<p class="fair-closing__credit"><?php esc_html_e( 'Organizado por la Junta Municipal Vistalegre-La Flota.', 'lemonbook' ); ?></p>
		</div>
	</section>
</main>
<?php get_footer(); ?>
