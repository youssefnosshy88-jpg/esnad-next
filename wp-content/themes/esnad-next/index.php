<?php
/** Fallback: article listing, categories, search results. */
defined( 'ABSPATH' ) || exit;
get_header();
$title = is_search() ? sprintf( esnad_t( 'نتائج البحث عن: %s', 'Search results for: %s' ), get_search_query() ) : ( is_archive() ? get_the_archive_title() : esnad_t( 'المقالات', 'Insights' ) );
?>
<header class="page-hero">
	<div class="container">
		<h1><?php echo esc_html( $title ); ?></h1>
	</div>
</header>
<div class="entry">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="articles">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="article">
						<a href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'esnad-card', array( 'loading' => 'lazy' ) ); else : ?>
								<span class="article__ph" aria-hidden="true"></span>
							<?php endif; ?>
							<h2><?php the_title(); ?></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p><?php echo esc_html( esnad_t( 'لا توجد نتائج.', 'Nothing found.' ) ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>
