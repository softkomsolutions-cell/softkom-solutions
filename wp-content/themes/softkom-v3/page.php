<?php
/**
 * Softkom V3 — generic commercial page template.
 *
 * Used by WordPress pages that do not have a more specific template.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php wp_head(); ?>
<style>
.sk-page{background:#f6f3e8;color:#111827}
.sk-page-hero{padding:136px 24px 66px;border-bottom:1px solid rgba(17,24,39,.12);background:
radial-gradient(circle at 84% 20%,rgba(38,99,235,.10),transparent 29%),
linear-gradient(180deg,#fbfaf4 0%,#f4f0e3 100%)}
.sk-page-shell{width:min(1180px,calc(100% - 48px));margin:0 auto}
.sk-page-kicker{display:flex;align-items:center;gap:12px;margin-bottom:22px;font-size:.76rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#536070}
.sk-page-kicker:before{content:"";width:34px;height:2px;background:#1f5eff}
.sk-page-title{max-width:930px;margin:0;font-size:clamp(2.7rem,6vw,5.15rem);line-height:.98;letter-spacing:-.045em;font-weight:760;color:#0b1220}
.sk-page-intro{max-width:760px;margin:26px 0 0;font-size:1.16rem;line-height:1.65;color:#4b5563}
.sk-page-layout{display:grid;grid-template-columns:minmax(0,760px) 280px;gap:96px;align-items:start;padding:72px 0 96px}
.sk-page-body{font-size:1.08rem;line-height:1.82;color:#28323f}
.sk-page-body>h1:first-child{display:none}
.sk-page-body>p:first-child{margin-top:0;font-size:1.18rem;line-height:1.72;color:#3e4a59}
.sk-page-body h2{margin:52px 0 16px;font-size:clamp(1.75rem,3vw,2.4rem);line-height:1.1;letter-spacing:-.028em;color:#111827}
.sk-page-body h3{margin:34px 0 12px;font-size:1.35rem;line-height:1.25;color:#111827}
.sk-page-body p{margin:0 0 22px}
.sk-page-body ul,.sk-page-body ol{margin:0 0 30px;padding-left:1.35rem}
.sk-page-body li{margin:0 0 10px;padding-left:.25rem}
.sk-page-body a{color:#174ea6;text-decoration-thickness:1px;text-underline-offset:3px}
.sk-page-body strong{color:#111827}
.sk-page-aside{position:sticky;top:112px}
.sk-page-card{padding:28px;border:1px solid rgba(17,24,39,.12);background:#fff}
.sk-page-card-label{margin:0 0 10px;font-size:.75rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#667085}
.sk-page-card h2{margin:0 0 12px;font-size:1.45rem;line-height:1.12;color:#111827}
.sk-page-card p{margin:0 0 20px;font-size:.94rem;line-height:1.6;color:#5b6573}
.sk-page-links{display:grid;gap:10px;margin-top:22px;padding-top:20px;border-top:1px solid rgba(17,24,39,.1)}
.sk-page-links a{font-size:.9rem;font-weight:700;color:#27364a;text-decoration:none}
.sk-page-end{margin-top:58px;padding:32px;border:1px solid rgba(17,24,39,.12);background:#fff}
.sk-page-end strong{display:block;margin-bottom:8px;font-size:1.08rem;color:#111827}
.sk-page-end p{margin-bottom:18px}
@media(max-width:900px){
 .sk-page-hero{padding:112px 20px 54px}
 .sk-page-shell{width:min(100% - 40px,760px)}
 .sk-page-layout{grid-template-columns:1fr;gap:42px;padding:54px 0 72px}
 .sk-page-aside{position:static}
}
</style>
</head>
<body <?php body_class( 'softkom-v3 sk-page' ); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<?php if ( is_page( 'growth-engine' ) ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
	<?php wp_footer(); ?>
	</body>
	</html>
	<?php return; ?>
<?php endif; ?>

<div class="sk-site">
	<?php if ( function_exists( 'softkom_v3_component_e' ) ) : ?>
		<?php softkom_v3_component_e( 'header' ); ?>
	<?php endif; ?>

	<main id="sk-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="sk-page-hero">
			<div class="sk-page-shell">
				<div class="sk-page-kicker">Business systems · Automation · Practical AI</div>
				<h1 class="sk-page-title"><?php the_title(); ?></h1>
				<p class="sk-page-intro">Practical ways to reduce operational friction, improve visibility and make the business easier to run.</p>
			</div>
		</header>

		<div class="sk-page-shell">
			<div class="sk-page-layout">
				<article class="sk-page-body">
					<?php the_content(); ?>
					<div class="sk-page-end">
						<strong>Find what is slowing the business down</strong>
						<p>Use Softkom’s Business Systems &amp; AI Readiness Assessment to identify the processes, hand-offs and disconnected tools worth fixing first.</p>
						<a class="sk-btn sk-btn-primary" href="<?php echo esc_url( home_url( '/assessment/' ) ); ?>">Start the Business Systems Assessment</a>
					</div>
				</article>

				<aside class="sk-page-aside" aria-label="Softkom next step">
					<div class="sk-page-card">
						<p class="sk-page-card-label">Softkom Solutions</p>
						<h2>Turn operational friction into a practical systems plan.</h2>
						<p>Start with the process that is costing the most time, creating the most admin or losing the most follow-up.</p>
						<a class="sk-btn sk-btn-primary" href="<?php echo esc_url( home_url( '/assessment/' ) ); ?>">Start the assessment</a>
						<div class="sk-page-links">
							<a href="<?php echo esc_url( home_url( '/business-process-automation-south-africa/' ) ); ?>">Business Process Automation</a>
							<a href="<?php echo esc_url( home_url( '/business-system-integration-south-africa/' ) ); ?>">Systems Integration</a>
							<a href="<?php echo esc_url( home_url( '/ai-automation-south-africa/' ) ); ?>">AI Automation</a>
						</div>
					</div>
				</aside>
			</div>
		</div>
	<?php endwhile; ?>
	</main>

	<?php if ( function_exists( 'softkom_v3_component_e' ) ) : ?>
		<?php softkom_v3_component_e( 'footer' ); ?>
	<?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>

