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
			<?php if ( in_array( get_the_ID(), array( 1483, 29484 ), true ) ) :
				$c   = esnad_company();
				$img = preg_match( '/<img[^>]+src="([^"]+)"/', get_the_content(), $m ) ? $m[1] : '';
				?>
				<div class="contact-grid">
					<div class="contact-info">
						<h2><?php echo esc_html( esnad_t( 'مكتب الرياض', 'Riyadh Office' ) ); ?></h2>
						<ul class="info-cards">
							<li><?php echo esnad_icon( 'pin', 26 ); // phpcs:ignore ?><span><?php echo esc_html( esnad_t( 'العنوان', 'Address' ) ); ?></span><b><?php echo esc_html( esnad_t( $c['street_ar'] . ' - ' . $c['city_ar'] . ' - السعودية', $c['street_en'] . ', ' . $c['city_en'] . ', K.S.A' ) ); ?></b></li>
							<li><?php echo esnad_icon( 'phone', 26 ); // phpcs:ignore ?><span><?php echo esc_html( esnad_t( 'هاتف', 'Phone' ) ); ?></span><a href="tel:<?php echo esc_attr( $c['phone_tel'] ); ?>" dir="ltr"><?php echo esc_html( $c['phone'] ); ?></a></li>
							<li><?php echo esnad_icon( 'mail', 26 ); // phpcs:ignore ?><span><?php echo esc_html( esnad_t( 'البريد الإلكتروني', 'E-Mail' ) ); ?></span><a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a></li>
						</ul>
						<?php if ( $img ) : ?>
							<img class="contact-img" src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( esnad_t( 'موقع المكتب', 'Office location' ) ); ?>" loading="lazy" decoding="async">
						<?php endif; ?>
					</div>
					<div class="contact-form" id="contact-form">
						<h2><?php echo esc_html( esnad_t( 'نموذج الاتصال', 'Contact form' ) ); ?></h2>
						<p><?php echo esc_html( esnad_t( 'لا تتردد في الاتصال بنا إذا كان لديك أي استفسارات أو أسئلة أو كنت ترغب في إرسال ملاحظات.', 'Please feel free to contact us if you have any inquiries, questions or would like to send a feedback or complaint.' ) ); ?></p>
						<?php echo do_shortcode( '[esnad_contact_form]' ); ?>
					</div>
				</div>
			<?php else : ?>
			<article <?php post_class( 'entry-content' ); ?>>
				<?php the_content(); ?>
				<?php if ( in_array( get_the_ID(), array( 36118, 36126, 31392 ), true ) ) {
					get_template_part( 'template-parts/service-cards' );
				} ?>
				<?php if ( esnad_is_blog_page() ) :
					$list = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 24, 'no_found_rows' => true, 'ignore_sticky_posts' => true ) );
					?>
					<div class="articles">
						<?php while ( $list->have_posts() ) : $list->the_post(); ?>
							<article class="article">
								<a href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'esnad-card', array( 'loading' => 'lazy' ) ); else : ?>
										<span class="article__ph" aria-hidden="true"></span>
									<?php endif; ?>
									<h2><?php the_title(); ?></h2>
									<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
								</a>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				<?php endif; ?>
			</article>
			<?php endif; ?>

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
