<?php
/**
 * Softkom signature diagram markup — reusable across web, proposals, workshops.
 * Voice: Softkom RC2 Content Excellence Standard.
 *
 * Visual grammar: numbered stages, navy/blue, white panels, restrained mono labels.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Softkom Transformation Journey™ stages (eight-stage client journey
 * within Softkom Transformation Framework™).
 *
 * Canonical stage copy lives in inc/data/frameworks/library.php.
 *
 * @return array<int, array{0:string,1:string,2:string}>
 */
function softkom_v3_data_journey_stages() {
	if ( function_exists( 'softkom_v3_framework_journey_stages' ) ) {
		return softkom_v3_framework_journey_stages();
	}
	return array();
}

/**
 * Softkom Connected Business™ — hero / architecture visual.
 *
 * @return string
 */
function softkom_v3_graphic_connected_business() {
	ob_start();
	?>
	<div class="sk-diagram sk-diagram--connected" aria-label="Softkom Connected Business architecture">
	  <div class="sk-diagram-head">
	    <span class="sk-diagram-eyebrow">Softkom Connected Business™</span>
	    <span class="sk-diagram-meta">Architecture</span>
	  </div>
	  <div class="sk-diagram-stack">
	    <div class="sk-diagram-row"><div class="sk-diagram-num">01</div><div><strong>Operations layer</strong><span>Orders · inventory · service workflows</span></div></div>
	    <div class="sk-diagram-row"><div class="sk-diagram-num">02</div><div><strong>Integrations</strong><span>ERP · CRM · finance · marketplace</span></div></div>
	    <div class="sk-diagram-row"><div class="sk-diagram-num">03</div><div><strong>Automation &amp; AI assist</strong><span>Repetitive work removed · people in control</span></div></div>
	    <div class="sk-diagram-row"><div class="sk-diagram-num">04</div><div><strong>Leadership visibility</strong><span>Current operational picture for directors — not competing spreadsheet exports</span></div></div>
	  </div>
	  <div class="sk-diagram-nodes">
	    <span>Systems</span>
	    <span>Workflows</span>
	    <span>Outcomes</span>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Softkom Systems Maturity Model™.
 *
 * @return string
 */
function softkom_v3_graphic_maturity() {
	$levels = array(
		array( '01', 'Spreadsheet dependent', 'Critical work lives in files and tribal knowledge — fragile when people leave or volumes rise.' ),
		array( '02', 'Fragmented tools', 'Systems exist, but handoffs still rely on people copying data between them.' ),
		array( '03', 'Connected operations', 'Data and workflows move with control — one operating picture across teams.' ),
		array( '04', 'Intelligent operations', 'Automation and AI improve decisions on stable foundations — humans keep accountability.' ),
	);
	ob_start();
	?>
	<div class="sk-diagram sk-diagram--maturity" aria-label="Softkom Systems Maturity Model">
	  <div class="sk-diagram-head">
	    <span class="sk-diagram-eyebrow">Softkom Systems Maturity Model™</span>
	    <span class="sk-diagram-meta">Diagnostic</span>
	  </div>
	  <ol class="sk-maturity">
	    <?php foreach ( $levels as $i => $level ) : ?>
	      <li class="sk-maturity-level<?php echo 2 === $i ? ' is-emphasis' : ''; ?>">
	        <span class="sk-diagram-num"><?php echo esc_html( $level[0] ); ?></span>
	        <strong><?php echo esc_html( $level[1] ); ?></strong>
	        <span><?php echo esc_html( $level[2] ); ?></span>
	      </li>
	    <?php endforeach; ?>
	  </ol>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Softkom AI Opportunity Map™.
 *
 * @return string
 */
function softkom_v3_graphic_ai_map() {
	$bands = array(
		array( '01', 'Classify', 'Sort, label and route information at volume.' ),
		array( '02', 'Assist', 'Draft, summarise and prepare work for people to approve.' ),
		array( '03', 'Automate', 'Remove repetitive execution where rules and outcomes are clear.' ),
		array( '04', 'Escalate', 'Hand exceptions to people with judgment and accountability.' ),
	);
	ob_start();
	?>
	<div class="sk-diagram sk-diagram--ai" aria-label="Softkom AI Opportunity Map">
	  <div class="sk-diagram-head">
	    <span class="sk-diagram-eyebrow">Softkom AI Opportunity Map™</span>
	    <span class="sk-diagram-meta">Intelligence</span>
	  </div>
	  <div class="sk-ai-map">
	    <?php foreach ( $bands as $band ) : ?>
	      <div class="sk-ai-band">
	        <span class="sk-diagram-num"><?php echo esc_html( $band[0] ); ?></span>
	        <strong><?php echo esc_html( $band[1] ); ?></strong>
	        <span><?php echo esc_html( $band[2] ); ?></span>
	      </div>
	    <?php endforeach; ?>
	  </div>
	  <p class="sk-diagram-note">AI comes after foundations. Softkom leads with operating clarity — then applies AI where volume is high, rules are clear, and humans still own the exceptions that matter.</p>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Integration ecosystem diagram.
 *
 * @return string
 */
function softkom_v3_graphic_integration() {
	$nodes = array( 'ERP', 'CRM', 'Finance', 'Marketplace', 'Workplace', 'Custom apps' );
	ob_start();
	?>
	<div class="sk-diagram sk-diagram--integration" aria-label="Softkom integration ecosystem">
	  <div class="sk-diagram-head">
	    <span class="sk-diagram-eyebrow">Integration ecosystem</span>
	    <span class="sk-diagram-meta">Fabric</span>
	  </div>
	  <div class="sk-integration">
	    <div class="sk-integration-hub">Softkom<br />systems layer</div>
	    <ul class="sk-integration-ring">
	      <?php foreach ( $nodes as $node ) : ?>
	        <li><?php echo esc_html( $node ); ?></li>
	      <?php endforeach; ?>
	    </ul>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Delivery lifecycle diagram.
 *
 * @return string
 */
function softkom_v3_graphic_delivery() {
	$steps = array(
		array( '01', 'Discover', 'Map friction, ownership and priorities.' ),
		array( '02', 'Design', 'Target workflows and data before code.' ),
		array( '03', 'Build', 'Ship in controlled, demonstrable increments.' ),
		array( '04', 'Integrate', 'Connect the tools you keep and trust.' ),
		array( '05', 'Adopt', 'Train teams and embed day-to-day habits.' ),
		array( '06', 'Support', 'Iterate as volumes and reporting needs change.' ),
	);
	ob_start();
	?>
	<div class="sk-diagram sk-diagram--delivery" aria-label="Softkom Delivery Lifecycle">
	  <div class="sk-diagram-head">
	    <span class="sk-diagram-eyebrow">Delivery Lifecycle</span>
	    <span class="sk-diagram-meta">Project execution</span>
	  </div>
	  <ol class="sk-lifecycle">
	    <?php foreach ( $steps as $step ) : ?>
	      <li>
	        <span class="sk-diagram-num"><?php echo esc_html( $step[0] ); ?></span>
	        <strong><?php echo esc_html( $step[1] ); ?></strong>
	        <span><?php echo esc_html( $step[2] ); ?></span>
	      </li>
	    <?php endforeach; ?>
	  </ol>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Back-compat alias for homepage masthead media.
 *
 * @return string
 */
function softkom_v3_home_workflow_media() {
	return softkom_v3_graphic_platforms_hero();
}

/**
 * MarketplaceOS operations dashboard preview (CSS UI — no stock art).
 *
 * @param string $density hero|card.
 * @return string
 */
function softkom_v3_graphic_marketplaceos_ui( $density = 'hero' ) {
	$density = ( 'card' === $density ) ? 'card' : 'hero';
	ob_start();
	?>
	<div class="sk-pv-chrome">
	  <span></span><span></span><span></span>
	  <em>MarketplaceOS · Operations</em>
	  <b class="sk-pv-chip">Live preview</b>
	</div>
	<div class="sk-pv-body sk-pv-body--mos">
	  <aside class="sk-pv-nav">
	    <i class="is-active"><u></u><em>Overview</em></i>
	    <i><u></u><em>Channels</em></i>
	    <i><u></u><em>Catalogue</em></i>
	    <i><u></u><em>Orders</em></i>
	    <i><u></u><em>Pricing</em></i>
	  </aside>
	  <div class="sk-pv-main">
	    <div class="sk-pv-toolbar">
	      <strong>Multi-channel control</strong>
	      <span>Today · SA</span>
	    </div>
	    <div class="sk-pv-kpis">
	      <div class="sk-pv-kpi"><strong>Channels</strong><em>12</em></div>
	      <div class="sk-pv-kpi"><strong>Active SKUs</strong><em>4.8k</em></div>
	      <div class="sk-pv-kpi"><strong>Open orders</strong><em>186</em></div>
	      <?php if ( 'hero' === $density ) : ?>
	        <div class="sk-pv-kpi"><strong>Margin Δ</strong><em class="is-up">+2.4%</em></div>
	      <?php endif; ?>
	    </div>
	    <div class="sk-pv-panel">
	      <div class="sk-pv-panel-head">
	        <strong>Fulfilment load</strong>
	        <span>7-day</span>
	      </div>
	      <div class="sk-pv-chart">
	        <span style="--h:42%"></span>
	        <span style="--h:68%"></span>
	        <span style="--h:55%"></span>
	        <span style="--h:82%"></span>
	        <span style="--h:70%"></span>
	        <span style="--h:88%"></span>
	      </div>
	    </div>
	    <?php if ( 'hero' === $density ) : ?>
	      <div class="sk-pv-table">
	        <div class="sk-pv-tr sk-pv-tr--head"><span>Channel</span><span>Orders</span><span>Status</span></div>
	        <div class="sk-pv-tr"><span>Takealot</span><span>64</span><span class="is-ok">Synced</span></div>
	        <div class="sk-pv-tr"><span>Amazon</span><span>41</span><span class="is-ok">Synced</span></div>
	        <div class="sk-pv-tr"><span>Shopify</span><span>28</span><span class="is-warn">Review</span></div>
	      </div>
	    <?php endif; ?>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Brick Alpha investment workspace preview (CSS UI — no stock art).
 *
 * @param string $density hero|card.
 * @return string
 */
function softkom_v3_graphic_brick_alpha_ui( $density = 'hero' ) {
	$density = ( 'card' === $density ) ? 'card' : 'hero';
	ob_start();
	?>
	<div class="sk-pv-chrome sk-pv-chrome--dark">
	  <span></span><span></span><span></span>
	  <em>Brick Alpha · Portfolio</em>
	  <b class="sk-pv-chip sk-pv-chip--gold">Private preview</b>
	</div>
	<div class="sk-pv-body sk-pv-body--dark sk-pv-body--ba">
	  <div class="sk-pv-ba-top">
	    <div>
	      <strong>Portfolio value</strong>
	      <em>R 1.24m</em>
	    </div>
	    <div class="sk-pv-ba-delta">+3.1% · 30d</div>
	  </div>
	  <div class="sk-pv-spark"></div>
	  <?php if ( 'hero' === $density ) : ?>
	    <div class="sk-pv-ba-cards">
	      <div class="sk-pv-ba-card"><strong>Creator Expert</strong><span>Signal · Strong</span></div>
	      <div class="sk-pv-ba-card"><strong>Architecture</strong><span>Watch · Stable</span></div>
	    </div>
	  <?php else : ?>
	    <div class="sk-pv-line"><i></i><i></i><i></i></div>
	  <?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Product Studio workspace preview (CSS UI — until cleared screenshots land).
 *
 * @param string $density hero|card.
 * @return string
 */
function softkom_v3_graphic_product_studio_ui( $density = 'hero' ) {
	$density = ( 'card' === $density ) ? 'card' : 'hero';
	ob_start();
	?>
	<div class="sk-pv-chrome">
	  <span></span><span></span><span></span>
	  <em>Product Studio · Workspace</em>
	  <b class="sk-pv-chip sk-pv-chip--studio">Maturing</b>
	</div>
	<div class="sk-pv-body sk-pv-body--studio">
	  <div class="sk-pv-studio-rail" aria-hidden="true">
	    <i class="is-active"></i><i></i><i></i><i></i>
	  </div>
	  <div class="sk-pv-studio-main">
	    <div class="sk-pv-toolbar">
	      <strong>Specialised build canvas</strong>
	      <span>Draft · v0</span>
	    </div>
	    <div class="sk-pv-studio-grid">
	      <div class="sk-pv-studio-pane"><strong>Domain</strong><span>Market fit</span></div>
	      <div class="sk-pv-studio-pane"><strong>Workflows</strong><span>Mapped</span></div>
	      <?php if ( 'hero' === $density ) : ?>
	        <div class="sk-pv-studio-pane"><strong>Data</strong><span>Owned</span></div>
	        <div class="sk-pv-studio-pane"><strong>Release</strong><span>Staged</span></div>
	      <?php endif; ?>
	    </div>
	    <?php if ( 'hero' === $density ) : ?>
	      <div class="sk-pv-line"><i></i><i></i><i></i></div>
	    <?php endif; ?>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Optional real product screenshot when cleared assets exist in theme.
 *
 * Drop files at assets/images/products/{slug}-hero.webp|png|jpg
 * Falls back to CSS UI mock when missing.
 *
 * @param string $slug marketplaceos|brick-alpha|product-studio.
 * @param string $alt  Accessible label (decorative wrappers remain aria-hidden upstream).
 * @return string Empty when no file.
 */
function softkom_v3_graphic_product_screenshot( $slug, $alt = '' ) {
	$slug = sanitize_key( (string) $slug );
	$dir  = get_stylesheet_directory() . '/assets/images/products/';
	$uri  = get_stylesheet_directory_uri() . '/assets/images/products/';
	$exts = array( 'webp', 'png', 'jpg', 'jpeg' );
	$file = '';
	$url  = '';
	foreach ( $exts as $ext ) {
		$candidate = $dir . $slug . '-hero.' . $ext;
		if ( is_readable( $candidate ) ) {
			$file = $candidate;
			$url  = $uri . $slug . '-hero.' . $ext;
			break;
		}
	}
	if ( ! $file ) {
		return '';
	}
	$alt = $alt ? $alt : ( $slug . ' product screenshot' );
	return sprintf(
		'<img class="sk-pv-shot" src="%1$s" alt="%2$s" width="1200" height="800" loading="lazy" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt )
	);
}

/**
 * Product-led homepage hero — stacked MarketplaceOS + Brick Alpha previews.
 * Uses real screenshots when present; otherwise premium CSS UI mocks.
 *
 * @return string
 */
function softkom_v3_graphic_platforms_hero() {
	$mos = softkom_v3_graphic_product_screenshot( 'marketplaceos', 'MarketplaceOS operations' );
	$ba  = softkom_v3_graphic_product_screenshot( 'brick-alpha', 'Brick Alpha portfolio' );
	ob_start();
	?>
	<div class="sk-product-visual sk-product-visual--hero" aria-hidden="true">
	  <div class="sk-pv-glow"></div>
	  <div class="sk-pv-light"></div>
	  <div class="sk-pv-window sk-pv-window--primary sk-pv-float">
	    <?php
	    if ( $mos ) {
	      echo $mos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	    } else {
	      echo softkom_v3_graphic_marketplaceos_ui( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	    }
	    ?>
	  </div>
	  <div class="sk-pv-window sk-pv-window--secondary sk-pv-float sk-pv-float--delay">
	    <?php
	    if ( $ba ) {
	      echo $ba; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	    } else {
	      echo softkom_v3_graphic_brick_alpha_ui( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	    }
	    ?>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Compact product UI mock for cards / product pages.
 *
 * @param string $variant marketplaceos|brick-alpha|product-studio|future|markets|specialised|compound.
 * @return string
 */
function softkom_v3_graphic_product_ui( $variant = 'marketplaceos' ) {
	$variant = sanitize_key( (string) $variant );
	if ( 'future-platforms' === $variant ) {
		$variant = 'future';
	}
	$shot = '';
	if ( in_array( $variant, array( 'marketplaceos', 'brick-alpha', 'product-studio' ), true ) ) {
		$shot = softkom_v3_graphic_product_screenshot( $variant );
	}
	$mod = ' sk-product-visual--' . $variant;
	ob_start();
	?>
	<div class="sk-product-visual sk-product-visual--card<?php echo esc_attr( $mod ); ?>" aria-hidden="true">
	  <div class="sk-pv-window">
	    <?php if ( $shot ) : ?>
	      <?php echo $shot; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	    <?php elseif ( 'brick-alpha' === $variant ) : ?>
	      <?php echo softkom_v3_graphic_brick_alpha_ui( 'card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	    <?php elseif ( 'marketplaceos' === $variant ) : ?>
	      <?php echo softkom_v3_graphic_marketplaceos_ui( 'card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	    <?php elseif ( 'product-studio' === $variant ) : ?>
	      <?php echo softkom_v3_graphic_product_studio_ui( 'card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	    <?php elseif ( 'future' === $variant ) : ?>
	      <div class="sk-pv-chrome">
	        <span></span><span></span><span></span>
	        <em>Future Platform</em>
	        <b class="sk-pv-chip">Coming soon</b>
	      </div>
	      <div class="sk-pv-body">
	        <div class="sk-pv-placeholder">
	          <span></span><span></span><span></span>
	        </div>
	      </div>
	    <?php elseif ( 'markets' === $variant ) : ?>
	      <div class="sk-pv-chrome">
	        <span></span><span></span><span></span>
	        <em>Market Map</em>
	      </div>
	      <div class="sk-pv-body sk-pv-body--simple">
	        <div class="sk-pv-map">
	          <i class="is-a"></i><i class="is-b"></i><i class="is-c"></i>
	        </div>
	        <div class="sk-pv-line"><i></i><i></i><i></i></div>
	      </div>
	    <?php elseif ( 'compound' === $variant ) : ?>
	      <div class="sk-pv-chrome">
	        <span></span><span></span><span></span>
	        <em>Product Stack</em>
	      </div>
	      <div class="sk-pv-body sk-pv-body--simple">
	        <div class="sk-pv-stack-viz">
	          <span>Platform core</span>
	          <span>Integrations</span>
	          <span>Intelligence</span>
	        </div>
	      </div>
	    <?php else : ?>
	      <div class="sk-pv-chrome">
	        <span></span><span></span><span></span>
	        <em>Platform Core</em>
	      </div>
	      <div class="sk-pv-body sk-pv-body--simple">
	        <div class="sk-pv-kpis sk-pv-kpis--3">
	          <div class="sk-pv-kpi"><strong>Workflow</strong><em>Fit</em></div>
	          <div class="sk-pv-kpi"><strong>Data</strong><em>Owned</em></div>
	          <div class="sk-pv-kpi"><strong>Control</strong><em>Clear</em></div>
	        </div>
	        <div class="sk-pv-chart">
	          <span style="--h:40%"></span>
	          <span style="--h:65%"></span>
	          <span style="--h:50%"></span>
	          <span style="--h:78%"></span>
	          <span style="--h:60%"></span>
	        </div>
	      </div>
	    <?php endif; ?>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}
