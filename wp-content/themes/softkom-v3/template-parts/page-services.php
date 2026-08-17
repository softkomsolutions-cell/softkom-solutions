<?php
/**
 * Softkom V3 Services — RC2.1 composer (executive commercial standard).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-site">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Solutions',
      'title'           => 'Choose the systems path that matches how work is stuck today',
      'lead'            => 'Softkom designs custom software, controlled AI automation and business platforms around how your company operates. Start from the outcome leadership needs — less re-entry, faster handoffs, clearer reporting, fewer operational errors — then choose the technology that earns its cost.',
      'primary_label'   => softkom_v3_cta_label( 'discuss-problem' ),
      'primary_url'     => softkom_v3_cta_url( 'discuss-problem' ),
      'secondary_label' => softkom_v3_cta_label( 'review-selected-work' ),
      'secondary_url'   => softkom_v3_cta_url( 'review-selected-work' ),
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'paths',
    array(
      'title' => 'How to choose a path',
      'body'  => "Most engagements start in one of three places. Softkom helps you pick the right entry point — then designs the system around how work actually moves.\n\nYou do not need a perfect brief. You need a clear description of where work stalls, who owns the handoff, and what “better” would look like for Ops, Finance and IT.",
      'note'  => 'Common mistake: buying automation or a new platform before the handoff map is clear. If you are unsure which path fits, book a strategy call — Softkom will say whether the next step is assessment, a scoped proposal, or not Softkom’s work.',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'solutions',
    array(
      'show_links' => true,
      'muted'      => true,
      'title'      => 'What Softkom delivers — and why it matters',
      'body'       => "Eight solution areas Softkom uses to solve operating problems. Priority areas — Business Systems, Process & Integrations, AI Automation and Compliance Platforms — open into decision pages with progressive depth. Other areas remain summary cards until Softkom publishes the same depth.\n\nTags show typical technology Softkom uses. The stack always follows the process — Softkom does not start from a preferred product list.",
      'note'       => 'Planning note: Softkom usually phases delivery — stabilise the highest-friction handoffs first, then extend integrations, then add automation where rules are clear. Boiling the ocean in one release is a common failure mode Softkom designs against.',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'integration',
    array(
      'muted' => false,
      'title' => 'Connect the tools you keep',
      'body'  => "Rip-and-replace is rarely the first move. Softkom designs integrations around the systems already carrying your operations — ERP, CRM, finance, marketplace, workplace — then extends only where a new layer creates control.\n\nStart with the handoffs that create the most re-entry and error. Expand when the next connection removes measurable delay or reporting drift.",
      'note'  => 'Integration challenge Softkom plans for early: ownership of master data (customer, stock, price). If two systems disagree and nobody owns the rule, the integration will encode the conflict.',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'ai-opportunity',
    array(
      'muted' => true,
      'title' => 'Where AI belongs — and where it does not',
      'body'  => "Softkom’s AI Opportunity Map keeps intelligence controlled: classify, assist, automate, escalate. Delivery follows a clear lifecycle so foundations and integrations come before automation.\n\nIf a process is unclear or ownership is disputed, Softkom fixes that first. Automating confusion only scales the confusion — and the commercial or compliance risk that sits with it.",
      'note'  => 'Governance rule Softkom uses: AI may remove volume work; people keep accountability for customer commitments, pricing exceptions and regulated decisions.',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'industries',
    array(
      'muted'   => false,
      'grouped' => false,
      'group'   => 'experience',
      'title'   => 'Experience-backed operating environments',
      'body'    => "Sectors supported by verified Softkom project records. Adjacent environments — where Softkom’s methods may apply without specialist delivery claims — are listed on the Industries page.",
      'foot_label' => 'Review all industries →',
      'foot_url'   => '/industries/',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'projects',
    array(
      'limit'             => 3,
      'show_technologies' => false,
      'show_links'        => true,
      'muted'             => true,
      'id'                => 'selected-work',
      'title'             => 'Selected client work',
      'body'              => "Examples of Softkom delivery — labelled Client Project.\n\nUse these to judge fit before a call: ordering platforms, e-commerce connected to ops, and digital presence for professional and infrastructure buyers. Match the bottleneck, not the logo.",
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'faq',
    array(
      'muted' => false,
      'title' => 'Questions before you engage',
      'body'  => "The points Softkom expects a manager to answer for a board: how long, who owns the code, how AI is controlled, whether existing systems can stay, how pricing is set, and what support looks like after go-live.",
    )
  );
  ?>
  <?php softkom_v3_component_e( 'cta-band' ); ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
