<?php
/** Single article. */
defined( 'ABSPATH' ) || exit;
get_header();
$home_url = home_url( esnad_is_rtl() ? '/' : '/en/' );
?>
<?php while ( have_posts() ) : the_post(); ?>
	<header class="page-hero">
		<div class="container">
			<ol class="crumbs" aria-label="<?php echo esc_attr( esnad_t( 'مسار التنقل', 'Breadcrumb' ) ); ?>">
				<li><a href="<?php echo esc_url( $home_url ); ?>"><?php echo esc_html( esnad_t( 'الرئيسية', 'Home' ) ); ?></a></li>
				<li><a href="<?php echo esc_url( esnad_url( 'blog' ) ); ?>"><?php echo esc_html( esnad_t( 'المقالات', 'Insights' ) ); ?></a></li>
			</ol>
			<?php the_category( ' · ' ); ?>
			<h1><?php the_title(); ?></h1>
			<p class="crumbs"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		</div>
	</header>
	<div class="entry">
		<div class="container entry__layout entry__layout--single">
			<article <?php post_class( 'entry-content' ); ?>>
				<?php the_content(); ?>
			</article>
		</div>
	</div>
<?php endwhile; ?>
<?php get_template_part( 'template-parts/cta-band' ); ?>
<?php get_footer(); ?>
