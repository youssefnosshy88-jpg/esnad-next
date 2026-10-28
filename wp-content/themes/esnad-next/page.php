<?php
/** Generic page and service-page layout. Content is whatever staff edit in the block editor. */
defined( 'ABSPATH' ) || exit;
get_header();
$is_service = esnad_is_service_page();
$home_url   = home_url( esnad_is_rtl() ? '/' : '/en/' );
?>
<?php while ( have_posts() ) : the_post(); ?>
	<header class="page-hero">
		<div class="container">
			<ol class="crumbs" aria-label="<?php echo esc_attr( esnad_t( 'مسار التنقل', 'Breadcrumb' ) ); ?>">
				<li><a href="<?php echo esc_url( $home_url ); ?>"><?php echo esc_html( esnad_t( 'الرئيسية', 'Home' ) ); ?></a></li>
				<?php if ( $is_service ) : ?>
					<li><a href="<?php echo esc_url( esnad_url( 'services' ) ); ?>"><?php echo esc_html( esnad_t( 'خدماتنا', 'Services' ) ); ?></a></li>
				<?php endif; ?>
				<li aria-current="page"><?php the_title(); ?></li>
			</ol>
			<h1><?php the_title(); ?></h1>
		</div>
	</header>

	<div class="entry">
		<div class="container entry__layout<?php echo $is_service ? '' : ' entry__layout--single'; ?>">
			<article <?php post_class( 'entry-content' ); ?>>
				<?php the_content(); ?>
			</article>

			<?php if ( $is_service ) : ?>
				<aside class="aside" aria-label="<?php echo esc_attr( esnad_t( 'خدمات أخرى', 'More services' ) ); ?>">
					<div class="aside__box">
						<h2><?php echo esc_html( esnad_t( 'هل تحتاج إلى استشارة؟', 'Need a consultation?' ) ); ?></h2>
						<p><?php echo esc_html( esnad_t( 'تواصل معنا لمناقشة احتياج جهتك.', 'Get in touch to discuss your organization\'s needs.' ) ); ?></p>
						<a class="btn btn--gold btn--sm" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
					</div>
					<nav class="aside__list">
						<h2><?php echo esc_html( esnad_t( 'خدماتنا', 'Our services' ) ); ?></h2>
						<ul>
							<?php foreach ( esnad_services() as $svc ) : ?>
								<li><a href="<?php echo esc_url( $svc['url'] ); ?>"><?php echo esc_html( $svc['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				</aside>
			<?php endif; ?>
		</div>
	</div>
<?php endwhile; ?>
<?php get_footer(); ?>
