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
			<p class="crumbs"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<?php if ( function_exists( 'esnad_reading_minutes' ) ) : ?>
						<span aria-hidden="true"> · </span><span><?php echo esc_html( sprintf( esnad_t( 'قراءة %d دقائق', '%d min read' ), esnad_reading_minutes( get_the_ID() ) ) ); ?></span>
					<?php endif; ?></p>
		</div>
	</header>
	<div class="entry">
		<div class="container entry__layout entry__layout--single">
			<article <?php post_class( 'entry-content' ); ?>>
				<?php the_content(); ?>
			</article>
		</div>
	</div>
<?php
	$rel = new WP_Query( array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'category__in'        => wp_get_post_categories( get_the_ID() ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
	if ( ! $rel->have_posts() ) {
		$rel = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => array( get_the_ID() ), 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
	}
	if ( $rel->have_posts() ) : ?>
		<section class="related" aria-labelledby="related-title">
			<div class="container">
				<h2 id="related-title"><?php echo esc_html( esnad_t( 'مقالات ذات صلة', 'Related articles' ) ); ?></h2>
				<div class="related__grid">
					<?php while ( $rel->have_posts() ) : $rel->the_post(); ?>
						<a class="related__item" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'esnad-card', array( 'loading' => 'lazy' ) ); } ?>
							<span class="related__date"><?php echo esc_html( get_the_date() ); ?></span>
							<span class="related__title"><?php the_title(); ?></span>
						</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
<?php endwhile; ?>
<?php get_template_part( 'template-parts/cta-band' ); ?>
<?php get_footer(); ?>
