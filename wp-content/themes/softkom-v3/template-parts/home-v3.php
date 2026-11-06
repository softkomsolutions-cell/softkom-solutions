<?php
/**
 * Softkom homepage — clean isolated commercial layout.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_url = home_url( '/#contact' );
?>
<div class="sk-site skx-home">
	<?php softkom_v3_component_e( 'header' ); ?>

	<main id="sk-main">
		<section class="skx-hero">
			<div class="skx-wrap skx-hero-grid">
				<div class="skx-hero-copy">
					<p class="skx-kicker">Softkom Solutions · Johannesburg</p>
					<h1>We fix the parts of the business that take too much effort.</h1>
					<p>Staff copying information between systems. Orders checked by hand. Reports rebuilt every week. Customers chasing for updates. Processes that worked when the business was smaller but do not anymore. Show us what is happening and we will work out the right way to fix it.</p>
					<div class="skx-actions">
						<a class="skx-btn skx-btn--primary" href="<?php echo esc_url( $contact_url ); ?>">Tell Us What Is Not Working</a>
						<a class="skx-btn skx-btn--ghost" href="<?php echo esc_url( home_url( '/#proof' ) ); ?>">See Client Work</a>
					</div>
				</div>

				<aside class="skx-hero-panel" aria-label="Operational improvement overview">
					<p class="skx-kicker">Operational overview</p>
					<h2>Make the business easier to run.</h2>
					<p>We improve the process first, then use integration, automation or custom software where it genuinely helps.</p>
					<div class="skx-metrics">
						<div class="skx-metric"><strong>Less admin</strong><span>Fewer manual steps</span></div>
						<div class="skx-metric"><strong>Faster answers</strong><span>Better visibility</span></div>
						<div class="skx-metric"><strong>Fewer mistakes</strong><span>Connected systems</span></div>
					</div>
					<div class="skx-chart" aria-hidden="true">
						<i style="--h:18%"></i><i style="--h:31%"></i><i style="--h:43%"></i><i style="--h:56%"></i><i style="--h:68%"></i><i style="--h:79%"></i><i style="--h:92%"></i>
					</div>
				</aside>
			</div>
		</section>

		<section class="skx-strip" aria-label="Softkom capabilities">
			<div class="skx-wrap skx-strip-grid">
				<div class="skx-strip-item"><strong>Business systems</strong><span>Workflows built around how the business actually operates.</span></div>
				<div class="skx-strip-item"><strong>Automation</strong><span>Remove repetitive work that should not still be manual.</span></div>
				<div class="skx-strip-item"><strong>Integration</strong><span>Connect the software and data you already use.</span></div>
				<div class="skx-strip-item"><strong>Custom software</strong><span>Build the missing part when off-the-shelf tools do not fit.</span></div>
			</div>
		</section>

		<section class="skx-section skx-section--white" id="problems-we-solve">
			<div class="skx-wrap">
				<header class="skx-section-head">
					<p class="skx-kicker">Where things usually start</p>
					<h2>You already know which part of the business is causing the frustration.</h2>
					<p>It is normally the work everybody complains about: too much checking, too much capturing, too many follow-ups and too many workarounds.</p>
				</header>
				<div class="skx-problems">
					<article class="skx-problem"><strong>“We are capturing the same information twice.”</strong><p>Your systems are not sharing what they should, so people fill the gap manually.</p></article>
					<article class="skx-problem"><strong>“Our staff spend half the day following things up.”</strong><p>The process depends on email, WhatsApp, memory and somebody remembering what happens next.</p></article>
					<article class="skx-problem"><strong>“It takes too long to get a clear answer.”</strong><p>Reporting is spread across systems, spreadsheets and people.</p></article>
					<article class="skx-problem"><strong>“This used to work when we were smaller.”</strong><p>The business grew, but the way the work gets done did not.</p></article>
				</div>
			</div>
		</section>

		<section class="skx-section" id="solutions">
			<div class="skx-wrap skx-split">
				<div class="skx-split-copy">
					<p class="skx-kicker">What we do</p>
					<h2>We first understand the problem. Then we fix the right thing.</h2>
					<p>Sometimes that means changing a process. Sometimes two systems need to talk to each other. Sometimes repetitive work should disappear. And sometimes there really is a missing piece of software.</p>
				</div>
				<div class="skx-service-list">
					<article class="skx-service"><span class="skx-service-num">01</span><div><h3>Make the process simpler</h3><p>Cut unnecessary steps, repeated admin and handovers that add no value.</p></div></article>
					<article class="skx-service"><span class="skx-service-num">02</span><div><h3>Connect the systems you already have</h3><p>Stop staff moving the same information from one system to another by hand.</p></div></article>
					<article class="skx-service"><span class="skx-service-num">03</span><div><h3>Automate repetitive work</h3><p>Remove predictable tasks that do not need a person to repeat them every day.</p></div></article>
					<article class="skx-service"><span class="skx-service-num">04</span><div><h3>Build what is missing</h3><p>Create the portal, workflow, integration, app or internal system that closes the gap.</p></div></article>
				</div>
			</div>
		</section>

		<section class="skx-section skx-section--warm" id="proof">
			<div class="skx-wrap">
				<header class="skx-section-head">
					<p class="skx-kicker">Client work</p>
					<h2>A few problems we have helped solve.</h2>
					<p>Real work for businesses that needed something to run better, look clearer or take less effort to manage.</p>
				</header>
				<div class="skx-work">
					<article class="skx-case skx-case--feature">
						<small>Retail &amp; distribution · Client project</small>
						<h3>Retail ordering platform</h3>
						<p>A growing stationery business needed to handle parent, school and business orders without creating more admin behind the scenes.</p>
						<p>We built a multi-portal platform that brings catalogue, checkout, delivery choices and back-office order handling into one place.</p>
						<a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">Read the case study →</a>
					</article>
					<article class="skx-case">
						<small>Infrastructure · Website</small>
						<h3>Traffic Signals &amp; Accessories</h3>
						<p>A specialist supplier with decades of experience needed a website that matched the quality of the business and made its products and project experience easier to understand.</p>
						<a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">View client work →</a>
					</article>
					<article class="skx-case">
						<small>Marketplace operations · Softkom product</small>
						<h3>MarketplaceOS</h3>
						<p>MarketplaceOS brings listings, pricing, stock and fulfilment workflows together so sellers spend less time reconciling systems.</p>
						<a href="<?php echo esc_url( home_url( '/platforms/marketplaceos/' ) ); ?>">Explore MarketplaceOS →</a>
					</article>
				</div>
			</div>
		</section>

		<section class="skx-section skx-section--dark" id="how-we-work">
			<div class="skx-wrap">
				<header class="skx-section-head">
					<p class="skx-kicker">How a project starts</p>
					<h2 style="color:#fff">Show us the problem. We will take it from there.</h2>
				</header>
				<div class="skx-process">
					<article class="skx-step"><span>01</span><h3>Show us what is happening</h3><p>We talk through the current process and where it becomes difficult.</p></article>
					<article class="skx-step"><span>02</span><h3>We work out the right fix</h3><p>We look at the process, systems and realistic options.</p></article>
					<article class="skx-step"><span>03</span><h3>We agree the work</h3><p>You know what we are doing, what it costs and what we need from you.</p></article>
					<article class="skx-step"><span>04</span><h3>We build and test it</h3><p>You see the work as it develops and it is tested with real users.</p></article>
					<article class="skx-step"><span>05</span><h3>We put it into use</h3><p>We deploy properly and support the changeover where needed.</p></article>
				</div>
			</div>
		</section>

		<section class="skx-section skx-section--white" id="contact">
			<div class="skx-wrap skx-contact">
				<div class="skx-contact-copy">
					<p class="skx-kicker">Start here</p>
					<h2>Tell us what is taking too much time, effort or attention.</h2>
					<p>You do not need to know whether you need automation, an integration, a new system or simply a better process. A few sentences is enough.</p>
					<div class="skx-contact-meta">
						<a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a>
						<a href="tel:+27749933805">+27 74 993 3805</a>
						<span>Johannesburg, South Africa</span>
					</div>
				</div>
				<div class="skx-form">
					<p class="skx-kicker">Your enquiry</p>
					<h3>What is not working as well as it should?</h3>
					<p>Tell us what is happening now and what you would like to be easier.</p>
					<?php echo do_shortcode( '[wpforms id="2501" title="false" description="false"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</section>
	</main>

	<?php softkom_v3_component_e( 'footer' ); ?>
</div>
