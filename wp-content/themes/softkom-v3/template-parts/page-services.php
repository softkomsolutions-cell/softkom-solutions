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
<div class="sk-site sk-services">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Solutions',
      'title'           => 'What do you need fixed, connected or built?',
      'lead'            => 'You do not need to know which technology you need before speaking to us. Start with the business problem: what is slow, manual, disconnected or simply not working well enough. We will help work out the right approach.',
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
      'title' => 'Start with the problem, not the product',
      'body'  => "A useful first conversation is usually very simple: what is happening now, where is the frustration, who is affected and what would a better outcome look like?\n\nFrom there we can tell you whether the job looks like a custom build, an integration, automation, an improvement to something you already have, or something that does not need software at all.",
      'note'  => 'You do not need a formal specification before contacting us. A clear explanation of the problem is enough to start.'
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'solutions',
    array(
      'show_links' => true,
      'show_tags'  => false,
      'ids'        => array(
        'business-systems',
        'custom-software',
        'ai-automation',
        'process-automation',
        'marketplace-solutions',
        'compliance-platforms',
      ),
      'muted'      => true,
      'title'      => 'The work we take on',
      'body'       => "Most of our work falls into a few practical areas: custom systems, integrations, automation, e-commerce, compliance and ongoing technical support.\n\nThe technology depends on the job. We do not start with a preferred stack and force the project around it.",
      'note'       => 'Where a project is large, we usually phase it so the most useful part gets into people’s hands first.'
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'integration',
    array(
      'muted' => false,
      'title' => 'Keep the systems that still make sense',
      'body'  => "Replacing every system at once is expensive and often unnecessary. If the tools you already use are doing their job, we would rather connect them properly than replace them for the sake of a cleaner diagram.\n\nThe usual starting point is the handoff where people are retyping data, chasing updates or correcting mistakes.",
      'note'  => 'The hard part of integration is often not the API. It is agreeing which system owns the truth when two systems disagree.'
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'ai-opportunity',
    array(
      'muted' => true,
      'title' => 'AI is useful when it removes real work',
      'body'  => "We use AI where it can save time, improve service or help people make better decisions. Good examples are document handling, repetitive enquiries, internal knowledge search and assisted workflows.\n\nIf the underlying process is confused, AI will not fix it. We sort out the process first.",
      'note'  => 'People should still own the decisions that affect customers, money and regulated work.'
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
      'title'   => 'Where we already have useful experience',
      'body'    => "Our current project work spans retail, marketplaces, professional services, infrastructure suppliers and operational business systems. We also take on adjacent work where the problem and delivery model are a good fit.",
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
      'title'             => 'A few examples',
      'body'              => "A small selection of recent client and product work. The useful question is not whether your company looks exactly like these — it is whether the problem feels familiar.",
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'faq',
    array(
      'muted' => false,
      'title' => 'Things clients usually ask us',
      'body'  => "Who owns the code? Can we keep our existing systems? How do you price the work? What happens after launch? These are all reasonable questions, and we answer them plainly.",
    )
  );
  ?>
  <?php softkom_v3_component_e( 'cta-band' ); ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
