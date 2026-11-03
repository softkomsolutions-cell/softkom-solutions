<?php
/**
 * Softkom high-intent problem landing pages.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$pages = array(
    'automate-quote-follow-up-south-africa' => array(
        'eyebrow' => 'QUOTE FOLLOW-UP AUTOMATION',
        'title' => 'Automate quote follow-up without losing the human touch',
        'intro' => 'If quotes are being sent and then left to memory, spreadsheets or WhatsApp, good opportunities quietly go cold. Softkom helps South African businesses put a consistent follow-up process around every quote while keeping staff in control.',
        'problem' => 'The problem is usually not a lack of leads. It is inconsistent follow-up after the quote has already been sent.',
        'signals' => array(
            'Quotes are sent but nobody owns the next action.',
            'Sales staff follow up differently or not at all.',
            'Older quotes sit in email or spreadsheets with no recovery process.',
            'Customers receive no reminder unless someone remembers.',
            'Management cannot see which quoted opportunities are still alive.'
        ),
        'outcomes' => array(
            'A clear follow-up sequence for every quote.',
            'Automatic reminders without replacing human judgement.',
            'A view of open, dormant and recovered opportunities.',
            'Escalation when a high-value quote has no next action.',
            'Less time spent manually checking who needs to be contacted.'
        ),
        'cta_label' => 'Review Our Quote Follow-Up',
        'cta_url' => '/business-efficiency-review/?source=seo-problem-page&offer=quote-follow-up',
        'secondary_label' => 'See Lost Lead Recovery',
        'secondary_url' => '/lost-lead-recovery/'
    ),
    'reduce-manual-admin-business' => array(
        'eyebrow' => 'REDUCE MANUAL ADMIN',
        'title' => 'Reduce repetitive admin without replacing every system you use',
        'intro' => 'Manual admin grows quietly as a business grows: copying information between systems, chasing approvals, updating spreadsheets and producing the same reports again and again. Softkom finds the work that should not still be manual.',
        'problem' => 'Most SMEs do not need a giant new platform. They need the repetitive work between their existing tools to stop consuming staff time.',
        'signals' => array(
            'The same information is typed into more than one system.',
            'Approvals are chased through email or WhatsApp.',
            'Reports are assembled manually every week or month.',
            'Important work depends on spreadsheets and individual memory.',
            'Staff spend time moving data rather than serving customers.'
        ),
        'outcomes' => array(
            'A map of repetitive work and hand-offs.',
            'An estimate of avoidable admin hours.',
            'The highest-value automation opportunities ranked first.',
            'Clear recommendations on what to automate and what to leave alone.',
            'A practical implementation path using existing systems where possible.'
        ),
        'cta_label' => 'Find Our Manual Waste',
        'cta_url' => '/business-efficiency-review/?source=seo-problem-page&offer=manual-admin',
        'secondary_label' => 'See Business Process Review',
        'secondary_url' => '/business-process-review/'
    ),
    'reduce-software-subscription-costs' => array(
        'eyebrow' => 'SOFTWARE COST CONTROL',
        'title' => 'Reduce software subscription costs before the next renewal',
        'intro' => 'SaaS costs creep up when licences stay active, teams buy overlapping tools and annual renewals happen without review. Softkom helps businesses identify software waste without cutting tools people genuinely need.',
        'problem' => 'The real cost is rarely one expensive app. It is the accumulation of subscriptions, duplicate licences and tools nobody owns anymore.',
        'signals' => array(
            'Nobody has a complete list of current subscriptions.',
            'Former staff may still have paid licences or access.',
            'Different teams use tools that overlap.',
            'AI and SaaS products renew automatically.',
            'Management cannot see monthly and annual software cost in one place.'
        ),
        'outcomes' => array(
            'A software and licence inventory.',
            'Monthly and annual spend visibility.',
            'Unused and duplicate licence identification.',
            'A renewal calendar and ownership map.',
            'A prioritised savings plan that protects useful systems.'
        ),
        'cta_label' => 'Check Our Software Spend',
        'cta_url' => '/software-cost-check/?source=seo-problem-page',
        'secondary_label' => 'Start With an Efficiency Review',
        'secondary_url' => '/business-efficiency-review/'
    ),
    'recover-old-sales-leads-south-africa' => array(
        'eyebrow' => 'LOST LEAD RECOVERY',
        'title' => 'Recover old sales leads before paying for more new ones',
        'intro' => 'Old enquiries, quotes, WhatsApps and previous customers often sit untouched while businesses keep spending money on new leads. Softkom helps identify which opportunities are still worth pursuing and builds a structured recovery process around them.',
        'problem' => 'You already paid to generate the lead. The opportunity is to recover the value sitting in the pipeline you already have.',
        'signals' => array(
            'Quotes were sent but never followed up properly.',
            'Website enquiries disappear into inboxes.',
            'Old leads sit in a CRM or spreadsheet untouched.',
            'Previous customers are not systematically re-engaged.',
            'Nobody knows why opportunities went quiet.'
        ),
        'outcomes' => array(
            'Historic lead and quote review.',
            'Recoverable opportunity segmentation.',
            'Dormant-customer identification.',
            'A repeatable follow-up workflow.',
            'Recovery tracking so results are measurable.'
        ),
        'cta_label' => 'Find Our Recoverable Leads',
        'cta_url' => '/lost-lead-recovery/?source=seo-problem-page',
        'secondary_label' => 'Start With an Efficiency Review',
        'secondary_url' => '/business-efficiency-review/'
    ),
    'invoice-chasing-automation-south-africa' => array(
        'eyebrow' => 'INVOICE CHASING AUTOMATION',
        'title' => 'Automate invoice chasing without annoying good customers',
        'intro' => 'Late payments create cash-flow pressure and repetitive admin. Softkom helps South African businesses build a controlled follow-up process around overdue invoices, with the right reminders, stop conditions, approvals and escalation points.',
        'problem' => 'The opportunity is not to send more messages. It is to make sure every overdue account gets the right next action, while disputes, promises to pay and recent payments are handled correctly.',
        'signals' => array(
            'Invoices are chased only when someone remembers.',
            'Finance staff work from ageing spreadsheets or exports.',
            'Customers who already replied still receive generic reminders.',
            'Promises to pay are not tracked consistently.',
            'Management cannot see what is overdue, disputed or genuinely at risk.'
        ),
        'outcomes' => array(
            'A clear reminder and escalation cadence.',
            'Stop rules for paid, disputed or promised accounts.',
            'Approval controls for sensitive or high-value cases.',
            'A focused queue for accounts that need human judgement.',
            'Better visibility of overdue debt and follow-up activity.'
        ),
        'cta_label' => 'Review Our Invoice Chasing Process',
        'cta_url' => '/business-process-review/?source=seo-problem-page&offer=invoice-chasing',
        'secondary_label' => 'See the Business Efficiency Review',
        'secondary_url' => '/business-efficiency-review/'
    ),
    'automate-management-reporting-south-africa' => array(
        'eyebrow' => 'MANAGEMENT REPORTING AUTOMATION',
        'title' => 'Automate recurring management reports without rebuilding them by hand',
        'intro' => 'Weekly and monthly reports often depend on someone exporting data, cleaning spreadsheets and combining information from several systems. Softkom helps businesses turn recurring reporting into a controlled, repeatable process.',
        'problem' => 'Reporting becomes expensive when the same data is extracted, checked and reformatted every period before management can use it.',
        'signals' => array(
            'The same report is rebuilt every week or month.',
            'Data is copied from accounting, CRM or operational systems into spreadsheets.',
            'Different teams produce different versions of the same number.',
            'Reports arrive late because one person has to assemble them.',
            'Management spends time checking the report instead of acting on it.'
        ),
        'outcomes' => array(
            'A defined source of truth for each metric.',
            'Automated data collection and recurring report generation.',
            'Exception flags where numbers need human review.',
            'Consistent distribution to the right managers.',
            'Less manual consolidation and faster decision-making.'
        ),
        'cta_label' => 'Review Our Reporting Process',
        'cta_url' => '/business-process-review/?source=seo-problem-page&offer=management-reporting',
        'secondary_label' => 'See the Business Efficiency Review',
        'secondary_url' => '/business-efficiency-review/'
    ),
    'reduce-duplicate-data-entry' => array(
        'eyebrow' => 'REDUCE DUPLICATE DATA ENTRY',
        'title' => 'Stop entering the same business information into multiple systems',
        'intro' => 'When customer, order or finance data is copied between CRM, accounting, ecommerce and spreadsheets, staff lose time and errors multiply. Softkom helps connect the hand-off so information is captured once and moved safely.',
        'problem' => 'Integration is not just moving data. Each system needs a clear role, record-matching rules and a safe way to handle failed or uncertain updates.',
        'signals' => array(
            'Customer details are retyped between CRM and accounting.',
            'Orders are copied manually into operational or finance systems.',
            'Staff correct mismatched records after imports.',
            'Nobody is sure which system owns a field.',
            'Failed updates are discovered only when a customer or manager notices.'
        ),
        'outcomes' => array(
            'Clear ownership for customer and transaction data.',
            'One automated hand-off at a time, starting with the highest-value process.',
            'Record matching and duplicate prevention rules.',
            'Failure alerts instead of silent data loss.',
            'Less retyping and fewer downstream corrections.'
        ),
        'cta_label' => 'Review Our System Hand-Offs',
        'cta_url' => '/business-efficiency-review/?source=seo-problem-page&offer=duplicate-data-entry',
        'secondary_label' => 'See Systems Integration',
        'secondary_url' => '/services/process-integrations/'
    ),
    'business-process-automation-johannesburg' => array(
        'eyebrow' => 'BUSINESS PROCESS AUTOMATION · JOHANNESBURG',
        'title' => 'Business process automation for Johannesburg SMEs',
        'intro' => 'Softkom helps growing businesses in Johannesburg remove repetitive work, connect disconnected systems and improve operational control without forcing a full platform replacement.',
        'problem' => 'Automation only creates value when it removes a real bottleneck. We start by mapping how the work is done today and identifying the few changes that have a measurable payoff.',
        'signals' => array(
            'Work moves between email, spreadsheets and WhatsApp.',
            'Staff capture the same data more than once.',
            'Approvals and follow-ups depend on memory.',
            'Systems do not share information cleanly.',
            'Management reporting requires manual consolidation.'
        ),
        'outcomes' => array(
            'Current-process and hand-off mapping.',
            'Manual-hours and friction estimate.',
            'Automation and integration opportunities.',
            'A ranked implementation roadmap.',
            'A clear recommendation on what should not be automated.'
        ),
        'cta_label' => 'Review Our Business Processes',
        'cta_url' => '/business-process-review/?source=seo-problem-page',
        'secondary_label' => 'Business Efficiency Review',
        'secondary_url' => '/business-efficiency-review/'
    ),
);

$slug = isset( $softkom_problem_slug ) ? sanitize_title( $softkom_problem_slug ) : '';
$page = isset( $pages[ $slug ] ) ? $pages[ $slug ] : null;

if ( ! $page ) {
    return;
}
?>
<section class="section sk-problem-hero">
  <div class="container sk-editorial-split">
    <div>
      <p class="eyebrow"><?php echo esc_html( $page['eyebrow'] ); ?></p>
      <h1 class="sk-display"><?php echo esc_html( $page['title'] ); ?></h1>
      <p class="lead"><?php echo esc_html( $page['intro'] ); ?></p>
      <div class="sk-actions">
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( home_url( $page['cta_url'] ) ); ?>"><?php echo esc_html( $page['cta_label'] ); ?></a>
        <a class="sk-btn sk-btn-secondary" href="<?php echo esc_url( home_url( $page['secondary_url'] ) ); ?>"><?php echo esc_html( $page['secondary_label'] ); ?></a>
      </div>
    </div>
    <aside class="sk-card">
      <p class="eyebrow eyebrow--tight">Why this matters</p>
      <p><?php echo esc_html( $page['problem'] ); ?></p>
    </aside>
  </div>
</section>

<section class="section section-muted">
  <div class="container">
    <div class="sk-editorial-heading">
      <p class="eyebrow">Common warning signs</p>
      <h2>Does any of this sound familiar?</h2>
    </div>
    <div class="sk-grid sk-grid--3">
      <?php foreach ( $page['signals'] as $signal ) : ?>
        <article class="sk-card"><p><?php echo esc_html( $signal ); ?></p></article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container sk-editorial-split">
    <div>
      <p class="eyebrow">What Softkom does</p>
      <h2>Start with the bottleneck, not the technology</h2>
      <p>We review the current process, the people involved and the systems already in use. The goal is to identify the few changes worth making before recommending software, automation or integration work.</p>
      <p>Where a simple process change is enough, we say so. Where automation has a clear business case, Softkom can implement and manage it.</p>
    </div>
    <div class="sk-card">
      <h3>Typical outcomes</h3>
      <ul class="sk-check-list">
        <?php foreach ( $page['outcomes'] as $outcome ) : ?>
          <li><?php echo esc_html( $outcome ); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="section section-muted">
  <div class="container sk-editorial-split">
    <div>
      <p class="eyebrow">A practical first step</p>
      <h2>See whether the opportunity is worth fixing</h2>
      <p>You do not need to know which system or automation you need. Bring the current process. Softkom will identify the friction, the likely value and the safest next step.</p>
    </div>
    <div class="sk-card">
      <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( home_url( $page['cta_url'] ) ); ?>"><?php echo esc_html( $page['cta_label'] ); ?></a>
    </div>
  </div>
</section>
