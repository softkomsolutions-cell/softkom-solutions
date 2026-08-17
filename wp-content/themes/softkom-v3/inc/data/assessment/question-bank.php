<?php
/**
 * Business Systems Assessment — question bank.
 *
 * Questions are diagnostic prompts for Softkom and future self-assessment UI.
 * Scoring 1–5 uses softkom_v3_assessment_score_bands(). No fake results stored.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Full question bank.
 *
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_assessment_question_bank() {
	$q = function ( $id, $section, $prompt, $observation_hint, $frameworks = array(), $services = array(), $insights = array() ) {
		return array(
			'id'                => $id,
			'section'           => $section,
			'prompt'            => $prompt,
			'scale'             => '1-5',
			'observation_hint'  => $observation_hint,
			'frameworks'        => $frameworks,
			'services'          => $services,
			'insights'          => $insights,
		);
	};

	return array(
		// Business visibility
		$q( 'visibility-01', 'business-visibility', 'Can directors see current operational status without waiting for a manually assembled pack?', 'Look for competing spreadsheets and delayed packs.', array( 'connected-business', 'systems-maturity' ), array( 'business-intelligence' ), array( 'why-most-businesses-are-still-running-on-spreadsheets' ) ),
		$q( 'visibility-02', 'business-visibility', 'When two teams disagree on “what is true,” how quickly can Softkom-style source-of-truth resolve it?', 'Note which entity lacks a system of record.', array( 'connected-business' ), array( 'business-systems' ) ),
		$q( 'visibility-03', 'business-visibility', 'Are exceptions visible before customers escalate them?', 'Chase culture vs exception queues.', array( 'systems-maturity' ), array( 'process-automation' ) ),

		// Reporting
		$q( 'reporting-01', 'reporting', 'How are weekly/monthly leadership reports produced today?', 'Manual assembly vs system-generated.', array( 'systems-maturity' ), array( 'business-intelligence' ) ),
		$q( 'reporting-02', 'reporting', 'Do leaders trust the numbers enough to make decisions without re-checking offline?', 'Trust gaps and shadow trackers.', array( 'connected-business' ), array( 'business-intelligence' ) ),
		$q( 'reporting-03', 'reporting', 'Are KPIs defined from decisions Softkom/leadership must make — or leftover exports?', 'Decision-led vs export-led reporting.', array( 'connected-business' ), array( 'business-systems' ) ),

		// Process maturity
		$q( 'process-01', 'process-maturity', 'Are the top recurring processes documented with clear start, finish and ownership?', 'Tribal knowledge risk.', array( 'systems-maturity', 'transformation-journey' ), array( 'business-systems' ) ),
		$q( 'process-02', 'process-maturity', 'Where does work stall most often — and is the stall owned?', 'Approvals, idle handoffs, rework.', array( 'transformation-journey' ), array( 'process-automation' ) ),
		$q( 'process-03', 'process-maturity', 'Would the process survive if one key person left this month?', 'Single points of failure.', array( 'systems-maturity' ), array( 'business-systems' ) ),

		// Systems integration
		$q( 'integration-01', 'systems-integration', 'Which systems should exchange data but currently rely on people copying?', 'ERP/CRM/finance/marketplace gaps.', array( 'connected-business' ), array( 'process-automation' ) ),
		$q( 'integration-02', 'systems-integration', 'Is there an agreed source of truth for customers, stock, orders and money?', 'Master ownership clarity.', array( 'connected-business' ), array( 'business-systems' ) ),
		$q( 'integration-03', 'systems-integration', 'Have integrate vs replace decisions been made with cost of handoffs in view?', 'Avoid premature rip-and-replace.', array( 'delivery-lifecycle' ), array( 'business-systems' ) ),

		// Automation
		$q( 'automation-01', 'automation', 'Which high-volume tasks have clear rules Softkom could automate without inventing process?', 'Volume + rule clarity + error cost.', array( 'ai-opportunity-map' ), array( 'process-automation' ), array( 'how-to-identify-automation-opportunities' ) ),
		$q( 'automation-02', 'automation', 'Are Softkom and the client refusing to automate broken or undocumented workflows?', 'Discipline against automation theatre.', array( 'systems-maturity' ), array( 'process-automation' ), array( 'how-to-identify-automation-opportunities' ) ),
		$q( 'automation-03', 'automation', 'Do status updates and notifications still depend on chasing people?', 'Handoff automation opportunity.', array( 'connected-business' ), array( 'process-automation' ) ),

		// AI readiness
		$q( 'ai-01', 'ai-readiness', 'Is data quality good enough that AI output would not amplify garbage?', 'Foundations first.', array( 'ai-opportunity-map', 'systems-maturity' ), array( 'ai-automation' ) ),
		$q( 'ai-02', 'ai-readiness', 'Are human review points named for customer, pricing and regulated decisions?', 'Accountability retained.', array( 'ai-opportunity-map' ), array( 'ai-automation' ) ),
		$q( 'ai-03', 'ai-readiness', 'Are there constraints on using AI with client/personal data Softkom must honour?', 'Privacy and contractual limits.', array( 'ai-opportunity-map' ), array( 'ai-automation', 'compliance-platforms' ) ),

		// Data quality
		$q( 'data-01', 'data-quality', 'Where is the same information entered more than once?', 'Re-entry map.', array( 'connected-business' ), array( 'business-systems' ), array( 'why-most-businesses-are-still-running-on-spreadsheets' ) ),
		$q( 'data-02', 'data-quality', 'How are master data changes controlled (if at all)?', 'Ownership and change rights.', array( 'systems-maturity' ), array( 'business-systems' ) ),
		$q( 'data-03', 'data-quality', 'How often do teams disagree because systems show different numbers?', 'Conflict frequency.', array( 'connected-business' ), array( 'process-automation' ) ),

		// Governance
		$q( 'governance-01', 'governance', 'Who can approve process or system changes that affect operations?', 'Decision rights.', array( 'delivery-lifecycle' ), array( 'business-systems' ) ),
		$q( 'governance-02', 'governance', 'Is there a named owner for each critical workflow after go-live?', 'Adoption ownership.', array( 'delivery-lifecycle' ), array( 'business-systems' ) ),
		$q( 'governance-03', 'governance', 'Can Softkom and the client describe what “done” means before build starts?', 'Success definition.', array( 'transformation-framework' ), array( 'business-systems' ) ),

		// Compliance
		$q( 'compliance-01', 'compliance', 'Which compliance or assurance obligations matter now (e.g. POPIA, customer audits, ISO programmes)?', 'Obligation list — do not invent.', array( 'systems-maturity' ), array( 'compliance-platforms' ), array( 'automating-compliance-soc2-iso-popia' ) ),
		$q( 'compliance-02', 'compliance', 'Is audit evidence captured in the workflow or assembled ad hoc before reviews?', 'Evidence design.', array( 'delivery-lifecycle' ), array( 'compliance-platforms' ), array( 'automating-compliance-soc2-iso-popia' ) ),
		$q( 'compliance-03', 'compliance', 'Are access control and retention practices clear for operational and personal data?', 'Control gaps.', array( 'systems-maturity' ), array( 'compliance-platforms' ) ),

		// Operational risk
		$q( 'risk-01', 'operational-risk', 'What fails first when volume spikes or a key person is away?', 'Fragility under stress.', array( 'systems-maturity' ), array( 'business-systems' ) ),
		$q( 'risk-02', 'operational-risk', 'Where could a single spreadsheet or mailbox create material business exposure?', 'Critical file/mailbox risk.', array( 'systems-maturity' ), array( 'process-automation' ), array( 'why-most-businesses-are-still-running-on-spreadsheets' ) ),
		$q( 'risk-03', 'operational-risk', 'Are customer commitments dependent on undocumented exceptions Softkom would struggle to govern?', 'Exception governance.', array( 'transformation-journey' ), array( 'business-systems' ) ),
	);
}

/**
 * Questions for one section.
 *
 * @param string $section_id Section id.
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_assessment_questions_for_section( $section_id ) {
	$section_id = sanitize_title( (string) $section_id );
	$out        = array();
	foreach ( softkom_v3_assessment_question_bank() as $question ) {
		if ( $question['section'] === $section_id ) {
			$out[] = $question;
		}
	}
	return $out;
}

/**
 * Single question by id.
 *
 * @param string $id Question id.
 * @return array<string, mixed>|null
 */
function softkom_v3_assessment_question( $id ) {
	$id = sanitize_title( (string) $id );
	// Question ids use hyphens; sanitize_title is fine.
	foreach ( softkom_v3_assessment_question_bank() as $question ) {
		if ( $question['id'] === $id ) {
			return $question;
		}
	}
	return null;
}
