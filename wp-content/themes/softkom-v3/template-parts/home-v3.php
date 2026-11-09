<?php
/**
 * Softkom V3 homepage — one-page commercial funnel.
 *
 * Sequence: hero → operational problems → solutions → case studies → fit → process → contact.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$start_discovery_url = home_url( '/#contact' );
?>
<div class="sk-site sk-home sk-home--product">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">
    <?php
    softkom_v3_component_e(
      'masthead',
      array(
        'variant'         => 'split',
        'eyebrow'         => 'Softkom Solutions · Johannesburg',
        'title'           => 'We make the work easier to run.',
        'lead'            => 'Softkom helps growing businesses improve the processes behind day-to-day operations. We find where work slows down, gets repeated, falls between systems or depends too heavily on manual effort — then we fix it with the simplest practical solution.',
        'primary_label'   => 'Tell Us What Is Not Working',
        'primary_url'     => $start_discovery_url,
        'secondary_label' => 'See Client Work',
        'secondary_url'   => home_url( '/#proof' ),
      )
    );
    ?>

    <section class="section" id="problems-we-solve">
      <div class="container">
        <div class="sk-editorial-split">
          <div class="sk-reveal">
            <p class="eyebrow">Business Process &amp; Operations Improvement</p>
            <h2 class="sk-display">The software may be fine. The way the work moves through the business often is not.</h2>
          </div>
          <div class="sk-reveal">
            <p class="lead">Teams end up waiting, chasing, re-entering information, checking the same things twice, managing approvals manually or working around systems that do not quite fit.</p>
            <p><strong>The aim is not to add more software. It is to remove friction from the operation.</strong></p>
          </div>
        </div>

        <div class="sk-friction-list">
          <blockquote>“We keep following up on the same things.”</blockquote>
          <blockquote>“The information exists, but not where we need it.”</blockquote>
          <blockquote>“Management only sees the problem after the fact.”</blockquote>
          <blockquote>“The business has grown, but the process has not.”</blockquote>
        </div>
      </div>
    </section>

    <section class="section section-muted" id="solutions">
      <div class="container">
        <div class="sk-reveal">
          <p class="eyebrow">What we actually do</p>
          <h2 class="sk-display">We work out what needs to change, then we implement it.</h2>
        </div>

        <div class="sk-service-ledger">
          <article class="sk-service-ledger-item sk-reveal">
            <span>01</span>
            <div>
              <h3>Improve the process</h3>
              <p>Remove unnecessary steps, simplify hand-offs and clarify how work should move through the business before adding technology.</p>
            </div>
          </article>
          <article class="sk-service-ledger-item sk-reveal">
            <span>02</span>
            <div>
              <h3>Connect the systems you already use</h3>
              <p>Move information between the tools your team already relies on so the same data does not have to be captured, checked or reconciled repeatedly.</p>
            </div>
          </article>
          <article class="sk-service-ledger-item sk-reveal">
            <span>03</span>
            <div>
              <h3>Automate the repetitive parts</h3>
              <p>Automate routine checks, notifications, document handling, approvals and follow-up where doing so saves real effort and reduces errors.</p>
            </div>
          </article>
          <article class="sk-service-ledger-item sk-reveal">
            <span>04</span>
            <div>
              <h3>Build the missing piece</h3>
              <p>When an important workflow, portal, integration or management view is genuinely missing, build only what the operation needs.</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section class="section" id="proof">
      <div class="container">
        <div class="sk-reveal">
          <p class="eyebrow">Case Studies</p>
          <h2 class="sk-display">A few client projects</h2>
        </div>

        <div class="sk-home-case-list">
          <article class="sk-reveal">
            <p class="eyebrow">Retail ordering platform</p>
            <h3>From customer ordering through to operational processing</h3>
            <p>Multi-portal platform work spanning customer journeys, catalogue, authentication, payments, staff operations and supplier processing.</p>
            <a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">View case studies</a>
          </article>

          <article class="sk-reveal">
            <p class="eyebrow">Traffic Signals &amp; Accessories</p>
            <h3>A clearer commercial platform for an established infrastructure business</h3>
            <p>Reworked the digital presence around products, manufacturing capability, services, projects and stronger lead-generation paths.</p>
            <a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">View client work</a>
          </article>

          <article class="sk-reveal">
            <p class="eyebrow">MarketplaceOS</p>
            <h3>Operational software across catalogue, pricing and inventory workflows</h3>
            <p>Multi-product engineering spanning listing workflows, repricing, inventory intelligence, integrations and management visibility.</p>
            <a href="<?php echo esc_url( home_url( '/platforms/marketplaceos/' ) ); ?>">View MarketplaceOS</a>
          </article>
        </div>
      </div>
    </section>

    <section class="section section-muted" id="engagement-fit">
      <div class="container">
        <div class="sk-editorial-split">
          <div class="sk-reveal">
            <p class="eyebrow">Working with Softkom</p>
            <h2 class="sk-display">No theatre. No transformation programme for the sake of it.</h2>
          </div>
          <div class="sk-reveal">
            <p>Show us how the work happens today. Tell us what takes too long, where things keep getting stuck, or what the team has to keep checking, chasing or doing by hand.</p>
            <p>We look at the process first. If the right answer is a small integration, a workflow change or a focused automation, that is what we recommend. If a custom system is genuinely needed, we build it.</p>
            <p>For larger custom systems, projects typically start around R75k. Smaller process, integration and improvement work is scoped around the actual problem rather than forced into that number.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section" id="how-we-work">
      <div class="container">
        <div class="sk-reveal">
          <p class="eyebrow">How We Work</p>
          <h2 class="sk-display">You should always know what is happening and what comes next</h2>
        </div>
        <div class="sk-build-steps">
          <?php
          $steps = array(
            array( 'Understand', 'We learn how the work happens today, where the friction is and what a useful improvement would look like.' ),
            array( 'Scope', 'We define the smallest practical change that can solve the problem properly.' ),
            array( 'Build', 'You see the work as it develops, not only at the end.' ),
            array( 'Launch', 'We validate the solution with the people who will actually use it.' ),
            array( 'Support', 'We stay available to improve the process as the business changes.' ),
          );
          foreach ( $steps as $i => $step ) :
            ?>
            <article class="sk-build-step sk-reveal">
              <span class="sk-build-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
              <h3><?php echo esc_html( $step[0] ); ?></h3>
              <p><?php echo esc_html( $step[1] ); ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section section-muted" id="contact">
      <div class="container">
        <div class="sk-editorial-split sk-home-contact">
          <div class="sk-reveal">
            <p class="eyebrow">Start a conversation</p>
            <h2 class="sk-display">Tell us which part of the business is taking more effort than it should.</h2>
            <p class="lead">A few sentences is enough. You do not need to know whether the answer is process improvement, integration, automation or a custom system.</p>
            <p><a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a><br><a href="tel:+27749933805">+27 74 993 3805</a><br>Johannesburg, South Africa</p>
          </div>

          <div class="sk-card sk-form sk-reveal">
            <p class="eyebrow">Your enquiry</p>
            <h3>What is not working as well as it should?</h3>
            <p>Tell us what is happening today and what you would like to improve.</p>
            <?php echo do_shortcode( '[sureforms id="2722" show_title="false"]' ); ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
