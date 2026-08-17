<?php
/**
 * Priority service decision-page content (RC2.3 P0).
 * Voice: Softkom RC2 Content Excellence + executive decision standard.
 * Rule: Explain approach, risks and trade-offs. Do not invent proof or ROI.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Priority service slugs with dedicated depth pages.
 *
 * @return string[]
 */
function softkom_v3_service_depth_slugs() {
	return array(
		'business-systems',
		'process-automation',
		'ai-automation',
		'compliance-platforms',
	);
}

/**
 * Full decision-page payload for one service.
 *
 * @param string $slug Service id (matches catalog solution id).
 * @return array<string,mixed>|null
 */
function softkom_v3_data_service_depth( $slug ) {
	$slug = sanitize_title( (string) $slug );
	$all  = softkom_v3_data_service_depth_all();
	return isset( $all[ $slug ] ) ? $all[ $slug ] : null;
}

/**
 * @return array<string, array<string,mixed>>
 */
function softkom_v3_data_service_depth_all() {
	return array(
		'business-systems'      => softkom_v3_depth_business_systems(),
		'process-automation'    => softkom_v3_depth_process_integrations(),
		'ai-automation'         => softkom_v3_depth_ai_automation(),
		'compliance-platforms'  => softkom_v3_depth_compliance_platforms(),
	);
}

/**
 * Business Systems decision page.
 *
 * @return array<string,mixed>
 */
function softkom_v3_depth_business_systems() {
	return array(
		'slug'    => 'business-systems',
		'eyebrow' => 'Business Systems',
		'title'   => 'When the operating picture no longer fits in spreadsheets',
		'lead'    => 'Softkom helps mid-market operators decide whether to integrate what they already run, adopt a packaged ERP, or build a controlled operating layer — and how to phase the change without freezing the business.',
		'cta'     => array(
			'title' => 'Ready to map whether systems, integration or process design comes first?',
			'body'  => 'Book a strategy call. Softkom will place the bottlenecks, outline integrate-vs-replace options, and say early if a full programme is premature.',
		),
		'sections' => array(
			array(
				'id'    => 'executive-summary',
				'title' => 'Executive summary',
				'body'  => "Growing companies usually outgrow spreadsheets before they outgrow the people who keep them alive. The question is not “which ERP brand?” — it is how work, ownership and visibility should run for the next stage of the business.\n\nSoftkom’s stance: diagnose the operating picture first. Packaged ERP is sometimes right. Integration of what you keep is often better first. Custom operating layers are justified when the process is the edge — or when packages force permanent workarounds.",
				'muted' => false,
			),
			array(
				'id'    => 'decision-snapshot',
				'title' => 'Decision Snapshot',
				'body'  => 'A short read of fit, stakeholders and disruption before the detail.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'         => 'decision-snapshot',
						'best_suited'  => 'Growing organisations experiencing increasing operational complexity.',
						'engagement'   => 'Discovery, roadmap or phased implementation.',
						'stakeholders' => 'Executive leadership, Operations, Finance, IT.',
						'objective'    => 'Improve visibility, consistency and scalable business processes.',
						'disruption'   => 'Typically phased.',
						'not_right'    => 'Very small businesses with stable manual processes.',
					),
				),
			),
			array(
				'id'    => 'operational-problem',
				'title' => 'The operational problem',
				'body'  => 'Why organisations outgrow spreadsheets — and what that costs leadership.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Critical facts live in files named after people — fragile when someone leaves or volumes rise.',
							'Finance, ops and sales each keep a version of orders, stock or customers; month-end becomes reconciliation theatre.',
							'Approvals and exceptions hide in email; nobody can see the queue until a customer or cashflow problem appears.',
							'Reporting arrives late because it is assembled, not produced from systems of record.',
						),
					),
					array(
						'type' => 'prose',
						'text' => 'Spreadsheets are not the enemy. Unowned critical process is. Softkom treats spreadsheet sprawl as a symptom of missing ownership, unclear handoffs and systems that do not match how work actually moves.',
					),
				),
			),
			array(
				'id'    => 'warning-signs',
				'title' => 'Warning signs Softkom looks for',
				'body'  => 'These patterns usually mean the current operating layer is past its useful life — even if individual tools still “work”.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'The same order, stock or customer record is typed into two or more places every day.',
							'Leaders ask for “the real number” and get three answers.',
							'Key processes stall when one person is on leave.',
							'Month-end reporting takes days of export and VLOOKUP work.',
							'New channels or products require another spreadsheet before they can run.',
							'IT or finance is blamed for visibility problems that are actually ownership problems.',
						),
					),
				),
			),
			array(
				'id'    => 'when-appropriate',
				'title' => 'When a Business Systems engagement is appropriate',
				'body'  => 'Softkom engages when leadership wants a clearer operating picture — not a software shopping exercise.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'         => 'when',
						'appropriate'  => array(
							'Multiple teams depend on the same operational facts but keep separate versions.',
							'You can name the three handoffs that hurt most (order → stock → invoice is a common set).',
							'Leadership will appoint process owners and a decision owner for scope.',
							'You are willing to standardise core process — and to leave some exceptions manual for a phase.',
						),
						'not'         => array(
							'Nobody owns master data (customer, stock, price) and nobody will.',
							'The brief is “implement ERP” with no map of how work moves today.',
							'The business wants a big-bang cutover with no dual-run or training window.',
							'The real need is a marketing site, campaign tooling, or a one-off report Softkom cannot defend as an operating system.',
						),
					),
				),
			),
			array(
				'id'    => 'erp-suitability',
				'title' => 'ERP suitability — and when it is not',
				'body'  => 'Softkom does not treat ERP as the automatic answer. Packaged systems earn their place when process fit and ownership are clear.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Situation', 'Usually better first move', 'Why' ),
						'rows'    => array(
							array( 'Core finance/ops process fits a mature package', 'Configure / adopt ERP (or deepen use of what you have)', 'Buy discipline and vendor roadmap instead of reinventing ledger and stock basics' ),
							array( 'Tools are mostly right; people are the integration layer', 'Integrate and standardise handoffs', 'Rip-and-replace freezes ops for problems that are handoff problems' ),
							array( 'Process is the competitive edge; packages force permanent workarounds', 'Custom operating layer + integrations', 'Staff will bypass a bad fit with new spreadsheets' ),
							array( 'Master data and ownership are unclear', 'Stabilise ownership before any big platform bet', 'A new ERP will encode the conflict if nobody owns the rule' ),
							array( 'Leadership wants “one system” but will not change process', 'Decline or re-scope to a focused integration', 'Technology cannot fix refused standardisation' ),
						),
					),
					array(
						'type'  => 'callout',
						'tone'  => 'note',
						'title' => 'Replacement versus integration',
						'text'  => 'Softkom usually prefers integrate-before-rip-and-replace. Replacement is appropriate when the current core cannot be trusted, licensing or support is ending, or process redesign requires a clean system of record. Softkom will say which case applies after discovery — not from a brand preference.',
					),
				),
			),
			array(
				'id'    => 'systems-stakeholders',
				'title' => 'Typical systems and stakeholders',
				'body'  => 'Engagements vary. These are the classes Softkom commonly maps — not a mandatory stack.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Usually involved', 'Typical stakeholder', 'What Softkom needs from them' ),
						'rows'    => array(
							array( 'ERP / accounting / stock', 'Finance + ops lead', 'Which system is system of record for each fact' ),
							array( 'CRM / sales tools', 'Sales ops / commercial lead', 'Customer and pricing ownership rules' ),
							array( 'Marketplace / e-commerce', 'Channel owner', 'Catalogue, price and fulfilment constraints' ),
							array( 'Workplace (email, SharePoint, M365)', 'Process owners', 'Where approvals and files currently hide' ),
							array( 'Reporting / BI exports', 'Leadership / FP&A', 'Which metrics must be trusted weekly vs month-end' ),
							array( 'IT / access / vendors', 'IT or managed service', 'API/access windows and change control' ),
						),
					),
				),
			),
			array(
				'id'    => 'softkom-approach',
				'title' => 'Softkom’s approach',
				'body'  => 'Work sits inside Softkom Transformation Framework™. The eight-stage Transformation Journey™ sequences the client conversation; the Delivery Lifecycle runs the project from discovery through support.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Map how work moves today — teams, tools, approvals, exceptions and spreadsheet gaps.',
							'Agree master data ownership before encoding integrations or migrations.',
							'Standardise the core path; design exception handling deliberately (not as afterthoughts).',
							'Choose integrate, configure, or build based on process fit — not vendor fashion.',
							'Phase for visibility: stabilise highest-friction handoffs, then extend, then automate where rules are clear.',
							'Plan change management with the build — training, dual-run, and what stays manual in each phase.',
						),
					),
				),
			),
			array(
				'id'    => 'delivery-phases',
				'title' => 'Delivery phases (qualified bands)',
				'body'  => 'Exact duration depends on scope, data quality and decision speed. Softkom sets timelines after discovery — not from a brochure calendar.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'phases',
						'items' => array(
							array(
								'label' => 'Discovery / assessment',
								'band'  => 'Typically days to a few weeks',
								'text'  => 'Operating map, ownership gaps, integrate-vs-replace options, and a scoped recommendation leadership can approve or decline.',
							),
							array(
								'label' => 'Focused stabilisation',
								'band'  => 'Often order of weeks to a few months',
								'text'  => 'Highest-friction handoffs, master data rules, and reporting from systems of record — enough control to stop the worst re-entry.',
							),
							array(
								'label' => 'Multi-system programme',
								'band'  => 'Months; sometimes longer',
								'text'  => 'ERP adoption or replacement, multi-entity integrations, or a custom operating layer with phased cutover and adoption.',
							),
							array(
								'label' => 'Support & iteration',
								'band'  => 'Ongoing after go-live',
								'text'  => 'Adoption fixes, exception tuning and reporting changes as volumes and products move.',
							),
						),
					),
				),
			),
			array(
				'id'    => 'implementation-risks',
				'title' => 'Implementation risks Softkom plans for',
				'body'  => 'These are the failure modes Softkom designs against — not scare tactics.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Risk', 'What goes wrong', 'How Softkom mitigates' ),
						'rows'    => array(
							array( 'Unowned master data', 'Integrations encode conflicting customer/stock/price rules', 'Ownership matrix before build; conflict examples in discovery' ),
							array( 'Big-bang cutover', 'Ops freezes; staff invent shadow spreadsheets', 'Phased releases, dual-run where needed, explicit manual fallbacks' ),
							array( 'Scope creep as “one more report”', 'Timeline and adoption collapse', 'Success criteria and change control agreed upfront' ),
							array( 'Process not standardised', 'Customisation forever; no one trusts the system', 'Core path first; exceptions designed and owned' ),
							array( 'Go-live treated as finish', 'Bypass and data drift return within months', 'Support and iteration in the Delivery Lifecycle' ),
						),
					),
				),
			),
			array(
				'id'    => 'governance',
				'title' => 'Governance requirements',
				'body'  => 'Without decision rights, Softkom cannot protect the programme from becoming a feature backlog.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'A named decision owner who can settle scope and priority conflicts.',
							'Process owners for each critical workflow (not only IT sponsors).',
							'A master data steward for customer, stock and price (or an agreed split with clear conflict rules).',
							'Change control for mid-build requests — logged, costed, sequenced.',
							'Steering cadence during multi-phase programmes so Ops, Finance and IT see the same status.',
						),
					),
				),
			),
			array(
				'id'    => 'client-responsibilities',
				'title' => 'What Softkom needs from the client',
				'body'  => 'Typical involvement — adjust after discovery. Softkom can work without a perfect brief; Softkom cannot work well without owners.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'matrix',
						'headers' => array( 'Role', 'Discovery', 'Build / cutover', 'After go-live' ),
						'rows'    => array(
							array( 'Decision owner', 'Settle scope and priorities', 'Approve phase gates', 'Own backlog priority' ),
							array( 'Process owners (Ops / Finance / Sales)', 'Walk real workflows', 'UAT, training, exception rules', 'Day-to-day ownership' ),
							array( 'Data steward', 'Define systems of record', 'Validate migration / sync rules', 'Resolve conflicts' ),
							array( 'IT / access', 'Vendor access, environments', 'Deploy windows, security', 'Support handoff' ),
							array( 'End users (sample)', 'Show workarounds honestly', 'Pilot and feedback', 'Adoption — not bypass' ),
						),
					),
				),
			),
			array(
				'id'    => 'success-measures',
				'title' => 'Measures of success Softkom agrees upfront',
				'body'  => 'Qualitative and operational indicators Softkom uses in proposals. Softkom does not invent ROI percentages Softkom cannot defend.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'Re-entry removed on the agreed handoffs (order / stock / invoice — or the client’s top three).',
							'One trusted source for each agreed master entity; conflict rate falling.',
							'Exception queue visible — age and owner — not buried in email.',
							'Weekly or month-open reporting from systems of record, not export spaghetti.',
							'Staff using the designed path for the core process (bypass rate watched in support).',
							'Phase exit criteria met before the next phase starts.',
						),
					),
				),
			),
			array(
				'id'    => 'faq',
				'title' => 'Practical questions executives ask',
				'body'  => 'Answers Softkom expects a manager to take into a board conversation. Ranges depend on scope.',
				'muted' => false,
				'faq'   => array(
					array(
						'title' => 'How long might this take?',
						'body'  => 'Assessment: usually days to a few weeks. A focused stabilisation of a few handoffs: often weeks to a few months. A multi-system or ERP-class programme: months, sometimes longer. Softkom only commits a calendar after discovery — when ownership, data quality and decision speed are visible.',
					),
					array(
						'title' => 'Which systems are normally involved?',
						'body'  => 'ERP or accounting, CRM, stock/warehouse, finance, marketplace or e-commerce, and workplace tools where approvals hide. Softkom maps your actual stack; Softkom does not require a preferred product list.',
					),
					array(
						'title' => 'What internal resources are required?',
						'body'  => 'A decision owner, process owners who run the work day to day, someone who can settle master data rules, and IT or vendor access. Discovery needs focused workshops; build needs UAT and training time. Exact hours depend on scope.',
					),
					array(
						'title' => 'How disruptive could implementation be?',
						'body'  => 'Disruption is usually highest at cutover and during training. Softkom prefers phased releases and dual-run where the risk justifies it. Some steps stay manual in early phases so ops does not freeze. Leadership must shield priority and training time — Softkom cannot absorb that for the client.',
					),
					array(
						'title' => 'What mistakes should we avoid?',
						'body'  => 'Buying ERP before mapping handoffs; integrating before anyone owns master data; big-bang go-live without adoption; treating go-live as the finish line; automating unclear process. Softkom designs against these explicitly.',
					),
					array(
						'title' => 'How is success measured?',
						'body'  => 'Agreed operational indicators: re-entry removed, trusted systems of record, visible exceptions, report latency, and adoption of the core path. Softkom writes these into scope — Softkom does not substitute invented financial ROI.',
					),
					array(
						'title' => 'When is this not the right approach?',
						'body'  => 'When nobody will own master data or process change; when the ask is a marketing site or vanity dashboard; when leadership wants a platform brand more than an operating design; when Softkom would only be encoding conflict. In those cases Softkom will decline or recommend a narrower next step.',
					),
				),
			),
		),
	);
}

/**
 * Process & Integrations decision page.
 *
 * @return array<string,mixed>
 */
function softkom_v3_depth_process_integrations() {
	return array(
		'slug'    => 'process-automation',
		'eyebrow' => 'Process & Integrations',
		'title'   => 'When people should stop being the integration layer',
		'lead'    => 'Most bottlenecks Softkom sees are not “missing software.” They are status, stock and customer facts copied between systems — with error, delay and nobody owning the failure when something breaks.',
		'cta'     => array(
			'title' => 'Ready to map the handoffs that hurt most?',
			'body'  => 'Book a strategy call. Softkom will identify the highest-friction connections, the system-of-record decisions, and whether integration — or replacement — is the honest first move.',
		),
		'sections' => array(
			array(
				'id'    => 'executive-summary',
				'title' => 'Executive summary',
				'body'  => "If ERP, CRM, finance and marketplace tools each hold a partial truth, staff become the middleware. That works until volume, leave patterns or channel growth break the informal process.\n\nSoftkom connects the systems you keep so data moves once — with ownership, monitoring and exception handling. Softkom will also say when integration is the wrong first move, and when replacing a brittle core is cheaper than wiring it forever.",
				'muted' => false,
			),
			array(
				'id'    => 'decision-snapshot',
				'title' => 'Decision Snapshot',
				'body'  => 'A short read of fit, stakeholders and disruption before the detail.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'         => 'decision-snapshot',
						'best_suited'  => 'Organisations where people are still the integration layer between systems worth keeping.',
						'engagement'   => 'Handoff mapping, system-of-record decisions, and controlled integration — often phased by flow.',
						'stakeholders' => 'Operations, Finance, IT/integration owners, and process owners for each connected system.',
						'objective'    => 'Move status, stock and customer facts once — with ownership, monitoring and exception handling.',
						'disruption'   => 'Typically phased by priority handoffs rather than a single cutover.',
						'not_right'    => 'When the source system cannot be trusted, master data has no owner, or replacing a brittle core is the honest first move.',
					),
				),
			),
			array(
				'id'    => 'operational-problem',
				'title' => 'The operational problem',
				'body'  => 'Weak integration shows up as operational pain before it shows up as an architecture diagram.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Re-entry: the same order or customer update typed into two systems — every re-entry is a drift opportunity.',
							'Conflicting truths: stock available on the channel but not in the warehouse system leaders trust.',
							'Silent failure: a sync stops overnight; nobody notices until invoices or fulfilment break.',
							'Permission debt: integrations run under personal accounts; people leave and the chain dies.',
							'Manual handoffs: CSVs, email attachments and “please update the other system” as the real process.',
						),
					),
					array(
						'type' => 'prose',
						'text' => 'The commercial consequence is not “tech debt.” It is delayed fulfilment, disputed invoices, untrusted reports and skilled people spending hours on work a controlled handoff should own.',
					),
				),
			),
			array(
				'id'    => 'warning-signs',
				'title' => 'Warning signs',
				'body'  => 'Patterns Softkom treats as integration risk — even when individual systems look fine.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'Point-to-point scripts nobody documents or monitors.',
							'CSV drops and shared folders as “the integration.”',
							'Duplicate customer or SKU records across systems with no conflict rule.',
							'Finance and ops disagree on order status every week.',
							'Only one person knows how to “kick” the sync.',
							'Security reviews cannot explain which accounts move production data.',
						),
					),
				),
			),
			array(
				'id'    => 'when-appropriate',
				'title' => 'When integration is appropriate — and when it is not',
				'body'  => 'Integration is Softkom’s commercial centre for good reason. It is still the wrong first move in some cases.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'        => 'when',
						'appropriate' => array(
							'Core systems are worth keeping; friction is in the handoffs.',
							'Leadership will name a system of record per entity (customer, stock, price, order).',
							'You can prioritise one or two high-friction flows before boiling the ocean.',
							'Someone will own monitoring, failures and access after Softkom leaves the build room.',
						),
						'not'        => array(
							'The “source” system cannot be trusted and will stay untrusted after wiring.',
							'Nobody will own master data — Softkom would only automate the argument.',
							'Process is so unclear that Softkom would be encoding tribal knowledge.',
							'A cleaner replacement of one core system removes most of the spaghetti — integrate-after-replace may be cheaper.',
						),
					),
				),
			),
			array(
				'id'    => 'integration-patterns',
				'title' => 'How work usually moves between systems',
				'body'  => 'Softkom chooses the mechanism from operating risk — not from a favourite tool.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Pattern', 'When it fits', 'Risk Softkom designs for' ),
						'rows'    => array(
							array( 'APIs / webhooks', 'Near-real-time status; vendor supports stable APIs', 'Auth, rate limits, partial failure, idempotency' ),
							array( 'File / batch exchange', 'Nightly finance or catalogue loads; API weak or costly', 'Late detection of bad files; reconciliation burden' ),
							array( 'Middleware / iPaaS / Power Automate', 'Many connectors; business-owned light flows', 'Sprawl of unowned flows; hidden personal connections' ),
							array( 'Manual handoff (deliberate)', 'Low volume, high judgment, temporary phase', 'Must be time-boxed — or it becomes permanent debt' ),
							array( 'Point-to-point custom scripts', 'Narrow, critical path Softkom can monitor', 'Becomes unmaintainable if used as default for everything' ),
						),
					),
					array(
						'type'  => 'callout',
						'tone'  => 'note',
						'title' => 'Point-to-point risk',
						'text'  => 'A few well-owned point-to-point links can be fine. A mesh of undocumented scripts is how mid-market ops quietly becomes fragile. Softkom prefers clear ownership, monitoring and a sequenced backlog over “connect everything.”',
					),
				),
			),
			array(
				'id'    => 'systems-stakeholders',
				'title' => 'Typical systems and stakeholders',
				'body'  => 'Classes Softkom commonly connects — your stack may differ.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'System class', 'Stakeholder', 'Decision Softkom needs' ),
						'rows'    => array(
							array( 'ERP / stock / WMS', 'Ops + finance', 'System of record for SKU, stock, fulfilment status' ),
							array( 'CRM / quotes', 'Commercial lead', 'Customer and opportunity ownership' ),
							array( 'Accounting / payments', 'Finance', 'Invoice and payment status rules' ),
							array( 'Marketplace / webstore', 'Channel owner', 'Listing, price and order capture constraints' ),
							array( 'Workplace automation', 'Process owner + IT', 'Which flows are business-owned vs Softkom-built' ),
						),
					),
				),
			),
			array(
				'id'    => 'softkom-approach',
				'title' => 'Softkom’s approach',
				'body'  => 'Highest-friction-first sequencing inside Softkom Transformation Framework™ — integrate before automate, ownership before wiring.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Map the real handoff — including the spreadsheet and email steps people forget to mention.',
							'Settle system-of-record and conflict rules before coding.',
							'Design failure handling: retries, alerts, reconciliation queues, who acts.',
							'Secure with service accounts and least privilege — not personal logins.',
							'Phase migration so operational continuity holds; dual-run where cutover risk is high.',
							'Hand ownership of monitoring to a named client role before Softkom steps back.',
						),
					),
				),
			),
			array(
				'id'    => 'delivery-phases',
				'title' => 'Delivery phases (qualified bands)',
				'body'  => 'Bands assume decision owners are available. Softkom re-estimates after discovery.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'phases',
						'items' => array(
							array(
								'label' => 'Handoff map + ownership',
								'band'  => 'Typically days to a few weeks',
								'text'  => 'Systems of record, conflict examples, security/access constraints, and a prioritised integration backlog.',
							),
							array(
								'label' => 'First critical flow',
								'band'  => 'Often weeks',
								'text'  => 'One high-friction path live with monitoring and exception handling — proof the operating model works.',
							),
							array(
								'label' => 'Expand controlled connections',
								'band'  => 'Weeks to months',
								'text'  => 'Additional flows only where re-entry, error or latency drops. Softkom resists boiling the ocean.',
							),
							array(
								'label' => 'Operate & improve',
								'band'  => 'Ongoing',
								'text'  => 'Client-owned monitoring, Softkom support for failures and backlog — not silent script debt.',
							),
						),
					),
				),
			),
			array(
				'id'    => 'implementation-risks',
				'title' => 'Implementation risks',
				'body'  => 'Operational continuity matters more than a pretty architecture slide.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Risk', 'Operational consequence', 'Mitigation' ),
						'rows'    => array(
							array( 'Duplicate / conflicting data', 'Wrong stock, wrong invoice, lost trust', 'SoR rules + reconciliation before scale-out' ),
							array( 'Unmonitored failure', 'Silent backlog; customer feels it first', 'Alerts, dashboards, named on-call owner' ),
							array( 'Cutover without dual-run', 'Orders or invoices stall', 'Parallel run or manual fallback window' ),
							array( 'Security via personal accounts', 'Breakage on staff change; audit exposure', 'Service identities + permission review' ),
							array( 'Integration instead of replace', 'Permanent spaghetti tax', 'Honest replace-vs-integrate gate in discovery' ),
						),
					),
				),
			),
			array(
				'id'    => 'governance',
				'title' => 'Governance and ownership',
				'body'  => 'Integration without ownership is how Softkom would create the next fragile layer.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Integration owner (client) for monitoring and priority of failures.',
							'Data steward for conflict resolution across systems of record.',
							'Change control when vendor APIs or field mappings change.',
							'Access review cadence for service accounts and connectors.',
							'Documented runbook: what fails, who is alerted, what stays manual.',
						),
					),
				),
			),
			array(
				'id'    => 'client-responsibilities',
				'title' => 'Internal client responsibilities',
				'body'  => 'Typical matrix — Softkom sizes effort after discovery.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'matrix',
						'headers' => array( 'Role', 'Discovery', 'Build', 'Operate' ),
						'rows'    => array(
							array( 'Process owner', 'Show real handoffs', 'UAT with exceptions', 'Own business rules' ),
							array( 'Data steward', 'SoR decisions', 'Validate sync/reconcile', 'Resolve conflicts' ),
							array( 'IT / security', 'Access & vendors', 'Deploy & secrets', 'Account lifecycle' ),
							array( 'Integration owner', 'Priority of flows', 'Accept monitoring', 'First response to alerts' ),
							array( 'Decision owner', 'Scope gates', 'Approve cutover', 'Backlog priority' ),
						),
					),
				),
			),
			array(
				'id'    => 'success-measures',
				'title' => 'Measures of success',
				'body'  => 'Indicators Softkom writes into scope — not invented ROI.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'Re-entry removed on the agreed flows.',
							'Measurable drop in sync/error exceptions (or clear queue with owners).',
							'Latency of status updates acceptable to ops and finance (agreed threshold).',
							'Failures detected by monitoring — not by angry customers.',
							'Named client owner can explain how each live integration works.',
						),
					),
				),
			),
			array(
				'id'    => 'faq',
				'title' => 'Practical questions executives ask',
				'body'  => 'Board-ready answers with honest ranges.',
				'muted' => false,
				'faq'   => array(
					array(
						'title' => 'How long might this take?',
						'body'  => 'A handoff map and first-flow design: often days to a few weeks. A single critical integration with monitoring: often weeks. A multi-system programme: months. Softkom sequences highest friction first so value is not trapped behind a two-year blueprint.',
					),
					array(
						'title' => 'Which systems are normally involved?',
						'body'  => 'ERP, CRM, finance/accounting, marketplace or e-commerce, warehouse/stock, and workplace automation tools. Softkom starts from your stack and vendor constraints.',
					),
					array(
						'title' => 'What internal resources are required?',
						'body'  => 'Process owners, a data steward, IT/vendor access, and a named integration owner after go-live. Build needs UAT time from people who do the work — not only sponsors.',
					),
					array(
						'title' => 'How disruptive could implementation be?',
						'body'  => 'Well-phased integrations should reduce day-to-day re-entry without freezing ops. Cutover windows, dual-run and temporary manual steps are the usual disruption points. Softkom plans those explicitly.',
					),
					array(
						'title' => 'What mistakes should we avoid?',
						'body'  => 'Point-to-point sprawl without monitoring; integrating before SoR decisions; personal accounts as connectors; assuming “API exists” means “safe to automate”; wiring a system that should be replaced.',
					),
					array(
						'title' => 'How is success measured?',
						'body'  => 'Re-entry removed, exception rate and age, update latency, monitored failures, and client ownership of runbooks. Softkom agrees the indicators before build.',
					),
					array(
						'title' => 'When is this not the right approach?',
						'body'  => 'When the core system is untrusted and will stay so; when nobody owns master data; when process is undefined; when replacement removes most of the spaghetti more cheaply than integration. Softkom will say so.',
					),
				),
			),
		),
	);
}

/**
 * AI Automation decision page.
 *
 * @return array<string,mixed>
 */
function softkom_v3_depth_ai_automation() {
	return array(
		'slug'    => 'ai-automation',
		'eyebrow' => 'AI Automation',
		'title'   => 'Remove volume work — keep people accountable',
		'lead'    => 'Softkom applies AI where volume is high and judgment is low, then designs human control points for exceptions, pricing, customer commitments and regulated decisions. AI is not Softkom’s default answer to every process problem.',
		'cta'     => array(
			'title' => 'Want a controlled pilot — not an AI slogan?',
			'body'  => 'Book a strategy call. Softkom will map suitable candidates on the AI Opportunity Map™, check data readiness, and design a pilot with exit criteria — or say AI should wait.',
		),
		'sections' => array(
			array(
				'id'    => 'executive-summary',
				'title' => 'Executive summary',
				'body'  => "AI helps when Softkom can define the work, the data, the control points and how success is judged. It fails when Softkom automates unclear process, weak data or decisions that must stay human.\n\nSoftkom’s governance rule: AI may remove volume work; people keep accountability for customer commitments, pricing exceptions and regulated decisions. Foundations and integrations come before automation.",
				'muted' => false,
			),
			array(
				'id'    => 'decision-snapshot',
				'title' => 'Decision Snapshot',
				'body'  => 'A short read of fit, stakeholders and disruption before the detail.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'         => 'decision-snapshot',
						'best_suited'  => 'High-volume, judgment-light work where Softkom can define data, control points and success criteria.',
						'engagement'   => 'Opportunity mapping, readiness check, and a controlled pilot with shared exit criteria.',
						'stakeholders' => 'Process owners, Operations, IT/security for access, and a decision owner for production changes.',
						'objective'    => 'Remove volume work while keeping people accountable for exceptions, pricing and regulated decisions.',
						'disruption'   => 'Typically limited to a pilot scope before any wider rollout.',
						'not_right'    => 'Unclear process, weak data ownership, or consequential decisions without a designed human gate — foundations first.',
					),
				),
			),
			array(
				'id'    => 'operational-problem',
				'title' => 'The operational problem',
				'body'  => 'Teams drown in repetitive classification, drafting, routing and first-pass checks — while the risky decisions still need people.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Skilled staff spend hours on judgement-light volume work.',
							'Inconsistent triage creates uneven customer and internal response.',
							'Leaders want “AI” without a map of which tasks are safe to assist or automate.',
							'Vendors push copilots into processes that still run on spreadsheet truth.',
						),
					),
					array(
						'type' => 'prose',
						'text' => 'Softkom separates deterministic automation (clear rules, predictable outcomes) from AI-assisted work (draft, classify, suggest — with human approval). Mixing them without control points is how reliability and auditability break.',
					),
				),
			),
			array(
				'id'    => 'warning-signs',
				'title' => 'Warning signs Softkom treats carefully',
				'body'  => 'These do not always mean “no AI.” They mean Softkom will slow down or decline a production path.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'Process owners cannot describe the happy path and the top exceptions.',
							'Source data is incomplete, conflicting or unowned.',
							'The proposed use case needs the model to invent facts Softkom cannot verify.',
							'No one can say who approves model or prompt changes in production.',
							'Success is defined only as “use AI,” not an operating measure.',
						),
					),
				),
			),
			array(
				'id'    => 'when-appropriate',
				'title' => 'Suitable and unsuitable candidates',
				'body'  => 'Softkom uses the AI Opportunity Map™ — classify, assist, automate, escalate — with a hard line on accountability.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'        => 'when',
						'appropriate' => array(
							'High volume, low judgment: sort, label, route, summarise, first-pass extract.',
							'Human approval is designed in for anything consequential.',
							'Data quality is good enough — or Softkom can scope a readiness fix first.',
							'Pilot has exit criteria Softkom and the client share.',
						),
						'not'        => array(
							'Unclear process Softkom would only scale as confusion.',
							'Final customer commitments, pricing exceptions or regulated decisions without a human gate.',
							'Environments where Softkom cannot control access to sensitive prompts or outputs.',
							'Cases where deterministic rules already solve the problem more reliably than a model.',
						),
					),
					array(
						'type'    => 'table',
						'headers' => array( 'Mode', 'What Softkom uses it for', 'Human role' ),
						'rows'    => array(
							array( 'Deterministic automation', 'Clear rules, stable outcomes, no generative guesswork', 'Own exceptions and rule changes' ),
							array( 'AI-assisted', 'Draft, classify, summarise, suggest next step', 'Approve before action that binds the business' ),
							array( 'Escalate', 'Ambiguity, risk, policy conflict', 'Accountable decision with audit trail' ),
						),
					),
				),
			),
			array(
				'id'    => 'risks-governance',
				'title' => 'Reliability, access and governance',
				'body'  => 'Hallucination, vendor dependence and weak audit trails are design problems Softkom treats as first-class — not footnotes.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Risk', 'Why it matters', 'Softkom control' ),
						'rows'    => array(
							array( 'Hallucination / unreliable output', 'Wrong facts enter ops or customer paths', 'Assist mode + approval; ground on approved sources where possible; no unbound “auto-send”' ),
							array( 'Poor data quality', 'Model amplifies garbage', 'Readiness gate; fix SoR / integrations first' ),
							array( 'Access control gaps', 'Sensitive data in prompts/logs', 'Least privilege, retention rules, environment separation' ),
							array( 'No auditability', 'Cannot explain who approved what', 'Logged approvals; versioned prompts/rules' ),
							array( 'Model / vendor dependence', 'Price, policy or quality shift mid-flight', 'Abstraction where practical; documented exit options; no single silent dependency' ),
						),
					),
					array(
						'type'  => 'callout',
						'tone'  => 'boundary',
						'title' => 'Production governance Softkom expects',
						'text'  => 'Who may change prompts, models, retrieval sources and automation rules must be named. Softkom does not leave production AI as an unowned experiment inside Ops.',
					),
				),
			),
			array(
				'id'    => 'systems-stakeholders',
				'title' => 'Typical systems and stakeholders',
				'body'  => 'AI sits on top of foundations — Softkom names the stack Softkom actually touches.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Usually involved', 'Stakeholder', 'Role in the engagement' ),
						'rows'    => array(
							array( 'Source systems of record', 'Data / process owner', 'Truth Softkom may ground on' ),
							array( 'Workflow / automation tools', 'Ops + IT', 'Where Softkom embeds control points' ),
							array( 'Model / API vendors', 'IT / security', 'Access, logging, commercial terms' ),
							array( 'Document / knowledge stores', 'Content owner', 'Approved corpora only' ),
							array( 'Compliance / risk (when relevant)', 'Risk owner', 'What must never be auto-actioned' ),
						),
					),
				),
			),
			array(
				'id'    => 'softkom-approach',
				'title' => 'Softkom’s approach and pilot design',
				'body'  => 'Pilots are how Softkom earns the right to production — not a soft launch of unbounded automation.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Map candidates on AI Opportunity Map™ bands; reject unsuitable work early.',
							'Confirm foundations: process clarity, integrations, data ownership.',
							'Choose deterministic vs AI-assisted deliberately.',
							'Design human approval points and audit logging before go-live.',
							'Run a bounded pilot with success/exit criteria and a rollback path.',
							'Only then expand volume — with named production governance.',
						),
					),
					array(
						'type'  => 'phases',
						'items' => array(
							array(
								'label' => 'Opportunity + readiness',
								'band'  => 'Typically days to a few weeks',
								'text'  => 'Candidate map, data/access constraints, governance baseline, go / no-go.',
							),
							array(
								'label' => 'Controlled pilot',
								'band'  => 'Often weeks',
								'text'  => 'Limited scope, human gates, measured quality, explicit exit criteria.',
							),
							array(
								'label' => 'Production hardening',
								'band'  => 'Weeks after a successful pilot',
								'text'  => 'Monitoring, access, change control, support ownership.',
							),
							array(
								'label' => 'Scale or stop',
								'band'  => 'Decision gate',
								'text'  => 'Expand only where measures hold — Softkom will recommend stop if they do not.',
							),
						),
					),
				),
			),
			array(
				'id'    => 'client-responsibilities',
				'title' => 'Internal client responsibilities',
				'body'  => 'AI without owners becomes shadow IT with a model bill.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'matrix',
						'headers' => array( 'Role', 'Pilot', 'Production' ),
						'rows'    => array(
							array( 'Process owner', 'Define exceptions and quality bar', 'Own day-to-day outcomes' ),
							array( 'Approvers', 'Exercise human gates honestly', 'Remain accountable for commitments' ),
							array( 'Data owner', 'Approve sources Softkom may use', 'Keep SoR quality' ),
							array( 'IT / security', 'Access, logging, vendor setup', 'Change control for models/prompts' ),
							array( 'Decision owner', 'Pilot exit criteria', 'Scale / stop decisions' ),
						),
					),
				),
			),
			array(
				'id'    => 'success-measures',
				'title' => 'Measures of success',
				'body'  => 'Softkom prefers operating indicators over vanity “AI adoption” metrics.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'Volume work removed on the agreed task types (hours or queue age — client’s measure).',
							'Quality bar met on assisted outputs (sample review rate agreed in pilot).',
							'Zero unbound auto-actions on commitments Softkom marked as human-only.',
							'Audit trail can answer who approved what.',
							'Pilot exit criteria met — or Softkom recommends stop.',
						),
					),
				),
			),
			array(
				'id'    => 'faq',
				'title' => 'Practical questions executives ask',
				'body'  => 'Straight answers Softkom uses on strategy calls.',
				'muted' => true,
				'faq'   => array(
					array(
						'title' => 'How long might this take?',
						'body'  => 'Readiness and candidate mapping: often days to a few weeks. A bounded pilot: often weeks. Production hardening after a successful pilot: additional weeks. Softkom does not promise enterprise-wide AI transformation on a fixed brochure timeline.',
					),
					array(
						'title' => 'Which systems are normally involved?',
						'body'  => 'Systems of record for the process, workflow/automation tooling, approved document stores, and model/API vendors Softkom already uses in delivery (for example OpenAI and Microsoft 365 / Power Automate where they fit). Stack follows the process.',
					),
					array(
						'title' => 'What internal resources are required?',
						'body'  => 'Process owners, designated human approvers, a data owner, IT/security for access and logging, and a decision owner for pilot exit. Pilots fail when sponsors attend and operators do not.',
					),
					array(
						'title' => 'How disruptive could implementation be?',
						'body'  => 'A well-scoped pilot should sit beside the live path with clear human gates. Disruption rises if Softkom is asked to auto-action customer or finance outcomes too early, or if data cleanup was skipped. Softkom phases to avoid that.',
					),
					array(
						'title' => 'What mistakes should we avoid?',
						'body'  => 'Automating unclear process; skipping data readiness; removing human gates on commitments; no owner for prompt/model changes; measuring success only as “we use AI.”',
					),
					array(
						'title' => 'How is success measured?',
						'body'  => 'Agreed volume reduction, output quality samples, audit completeness, and pilot exit criteria. Softkom does not invent ROI percentages.',
					),
					array(
						'title' => 'When should AI not be used?',
						'body'  => 'When rules already solve it deterministically; when data or process is unfit; when Softkom cannot place a human gate on consequential decisions; when governance and access cannot be controlled. Softkom will recommend process or integration work instead.',
					),
				),
			),
		),
	);
}

/**
 * Compliance Platforms decision page.
 *
 * @return array<string,mixed>
 */
function softkom_v3_depth_compliance_platforms() {
	return array(
		'slug'    => 'compliance-platforms',
		'eyebrow' => 'Compliance Platforms',
		'title'   => 'Evidence as work happens — not a scramble before the audit',
		'lead'    => 'Softkom builds structured capture, approvals, retention and reporting into the workflow so evidence already exists when clients, partners or auditors ask. Softkom does not certify that your organisation is compliant, and Softkom does not provide legal advice.',
		'cta'     => array(
			'title' => 'Need evidence systems — not a certificate Softkom cannot issue?',
			'body'  => 'Book a strategy call. Softkom will map where evidence should be captured in the process, what Softkom can build, and where you still need legal or certification advisors.',
		),
		'sections' => array(
			array(
				'id'    => 'executive-summary',
				'title' => 'Executive summary',
				'body'  => "Compliance pressure often shows up as last-minute document hunts. Softkom’s job is operational: design the workflow so who approved what, when, and with which evidence is a by-product of doing the work.\n\nSoftkom builds software and process control that support compliance programmes. Softkom does not issue certifications, does not act as your legal counsel, and does not warrant regulatory assurance Softkom cannot stand behind.",
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'callout',
						'tone'  => 'boundary',
						'title' => 'Certification and legal-advice boundary',
						'text'  => 'Softkom may help you design systems aligned to how your advisors interpret frameworks such as POPIA-minded handling or control practices relevant to standards discussions. Softkom does not certify ISO, SOC or any other standard for your organisation. Engage qualified legal and certification professionals for opinions, audits and attestations.',
					),
				),
			),
			array(
				'id'    => 'decision-snapshot',
				'title' => 'Decision Snapshot',
				'body'  => 'A short read of fit, stakeholders and disruption before the detail.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'         => 'decision-snapshot',
						'best_suited'  => 'Organisations with recurring evidence, approval and retention demands that currently end in last-minute document hunts.',
						'engagement'   => 'Control and evidence mapping, then phased workflow-and-capture build with reporting and retrieval.',
						'stakeholders' => 'Compliance/risk liaison, process owners, IT/security for access, a decision owner — plus separate legal or certification advisors.',
						'objective'    => 'Capture approvals and evidence in the flow of work so packs are retrievable without scramble.',
						'disruption'   => 'Typically phased on highest-risk processes first; change is mostly behavioural around approvals and capture.',
						'not_right'    => 'When the ask is certification or legal advice Softkom cannot provide, or a lighter workflow fix removes the scramble without a full platform.',
					),
				),
			),
			array(
				'id'    => 'what-softkom-does',
				'title' => 'What Softkom builds — and what Softkom does not',
				'body'  => 'Clear distinctions executives need before buying a “compliance platform.”',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Capability', 'Softkom role', 'Not Softkom’s role' ),
						'rows'    => array(
							array( 'Workflow support', 'Design controlled steps, roles and handoffs', 'Rewrite your legal obligations' ),
							array( 'Evidence management', 'Capture, store, retrieve artefacts with audit trail', 'Declare evidence “sufficient” for a regulator' ),
							array( 'Policy administration', 'Hold and version policies in the system if required', 'Write or approve legal policy content as counsel' ),
							array( 'Reporting', 'Operational reports and evidence packs on demand', 'Statutory filings Softkom is not authorised to make' ),
							array( 'Certification', 'Prepare systems and evidence trails for your auditors', 'Issue or grant certification' ),
							array( 'Legal advice', 'Implement controls your advisors specify', 'Provide legal opinions' ),
							array( 'Regulatory assurance', 'Improve process discipline and traceability', 'Guarantee regulatory outcomes' ),
						),
					),
				),
			),
			array(
				'id'    => 'operational-problem',
				'title' => 'The operational problem',
				'body'  => 'Scramble culture is a systems and ownership problem — Softkom addresses that part.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Evidence reconstructed after the fact from email and shared drives.',
							'Approvals without segregation of duties or clear records.',
							'Retention and access rules that exist on paper but not in the tools people use.',
							'POPIA and client due-diligence requests that stall operations because nothing is retrievable.',
						),
					),
				),
			),
			array(
				'id'    => 'warning-signs',
				'title' => 'Warning signs',
				'body'  => 'Patterns Softkom sees before a painful audit or client questionnaire.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'“We’ll gather the pack when they ask” is the operating model.',
							'Nobody can show who approved a sensitive action without inbox archaeology.',
							'Policies live in PDFs nobody can prove were acknowledged.',
							'Access is broad because “everyone needs the folder.”',
							'A compliance platform is proposed to fix process Softkom has not yet designed.',
						),
					),
				),
			),
			array(
				'id'    => 'when-appropriate',
				'title' => 'When a compliance platform engagement is appropriate',
				'body'  => 'Sometimes Softkom recommends workflow control without a full platform. Softkom will say which.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'        => 'when',
						'appropriate' => array(
							'Regulated or client-driven evidence demands are recurring — not one-off.',
							'Leadership will own process and access rules Softkom encodes.',
							'You want capture in the flow of work, not a parallel “compliance admin” team forever.',
							'Legal/certification advisors are engaged separately for opinions Softkom cannot give.',
						),
						'not'        => array(
							'You need Softkom to “get us certified” as the engagement outcome.',
							'You want Softkom to act as legal counsel or auditor.',
							'Process is so undefined that a platform would only store chaos neatly.',
							'A lighter controlled workflow (without a full platform) would solve the scramble.',
						),
					),
				),
			),
			array(
				'id'    => 'systems-stakeholders',
				'title' => 'Typical systems and stakeholders',
				'body'  => 'Compliance platforms sit beside the systems that already run the business.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Usually involved', 'Stakeholder', 'Softkom needs' ),
						'rows'    => array(
							array( 'Core ops / ERP / case systems', 'Process owners', 'Where work and evidence should meet' ),
							array( 'Document / records stores', 'Records owner', 'Retention and access rules' ),
							array( 'Identity / access', 'IT / security', 'Roles, SoD, joiner-mover-leaver' ),
							array( 'HR / training (if acknowledgements)', 'People ops', 'Policy acknowledgement requirements' ),
							array( 'Legal / compliance advisors', 'Client-appointed', 'Requirements Softkom implements — Softkom does not replace them' ),
						),
					),
				),
			),
			array(
				'id'    => 'softkom-approach',
				'title' => 'Softkom’s approach',
				'body'  => 'Design the controlled process first; platform features follow. Softkom does not sell certification theatre.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Map the obligations you care about as operating controls — with your advisors’ input where legal interpretation is required.',
							'Design approvals, segregation of duties and evidence capture in the workflow.',
							'Define retention, access and retrieval paths Softkom can implement.',
							'Build reporting that produces evidence packs without heroics.',
							'Train owners; Softkom does not leave a system nobody will run.',
							'Keep the certification/legal boundary visible in scope documents.',
						),
					),
					array(
						'type'  => 'phases',
						'items' => array(
							array(
								'label' => 'Control & evidence map',
								'band'  => 'Typically days to a few weeks',
								'text'  => 'Which controls, which artefacts, which roles — and what Softkom will not certify.',
							),
							array(
								'label' => 'Workflow + capture build',
								'band'  => 'Often weeks to a few months',
								'text'  => 'Phased delivery of controlled paths and evidence trails on the highest-risk processes first.',
							),
							array(
								'label' => 'Reporting & retrieval',
								'band'  => 'Overlaps build; hardens before audit windows',
								'text'  => 'Evidence packs and access logs leadership can actually use.',
							),
							array(
								'label' => 'Operate',
								'band'  => 'Ongoing',
								'text'  => 'Client-owned process; Softkom support for system iteration — advisors remain separate.',
							),
						),
					),
				),
			),
			array(
				'id'    => 'implementation-risks',
				'title' => 'Implementation risks',
				'body'  => 'A platform that is bypassed is worse than no platform — it creates false comfort.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'table',
						'headers' => array( 'Risk', 'Consequence', 'Mitigation' ),
						'rows'    => array(
							array( 'Bypass to email/files', 'Evidence gaps return', 'Adoption and exception design with the build' ),
							array( 'Over-collection', 'POPIA and access risk', 'Minimum necessary capture; access reviews' ),
							array( 'Unclear SoD', 'Approvals without real control', 'Role design before configuration' ),
							array( 'Scope sold as certification', 'False executive expectation', 'Boundary in proposal and page CTA' ),
							array( 'No retention rules', 'Either data sprawl or accidental deletion', 'Retention schedule agreed with client owners' ),
						),
					),
				),
			),
			array(
				'id'    => 'governance',
				'title' => 'Governance requirements',
				'body'  => 'Controlled processes need named owners — Softkom encodes what leadership will enforce.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'list',
						'items' => array(
							'Process owner per controlled workflow.',
							'Access / SoD owner (often IT + compliance liaison).',
							'Records retention owner.',
							'Change control for policy text and control configuration.',
							'Separate lane for legal/certification advisors — Softkom does not merge those roles.',
						),
					),
				),
			),
			array(
				'id'    => 'client-responsibilities',
				'title' => 'Internal client responsibilities',
				'body'  => 'Typical participation Softkom expects.',
				'muted' => true,
				'blocks' => array(
					array(
						'type'    => 'matrix',
						'headers' => array( 'Role', 'Discovery', 'Build', 'Operate' ),
						'rows'    => array(
							array( 'Compliance / risk liaison', 'Prioritise controls', 'Validate evidence paths', 'Own control effectiveness claims*' ),
							array( 'Process owners', 'Walk real work', 'UAT approvals/SoD', 'Day-to-day adherence' ),
							array( 'IT / security', 'Access model', 'Identity integration', 'Access reviews' ),
							array( 'Legal / cert advisors', 'Interpret obligations', 'Review Softkom cannot replace', 'Audits & opinions' ),
							array( 'Decision owner', 'Scope & boundary', 'Phase gates', 'Investment priority' ),
						),
					),
					array(
						'type' => 'prose',
						'text'  => '*Softkom can show that evidence was captured as designed. Whether that satisfies a regulator or certifier is a matter for your advisors and auditors — not Softkom’s warranty.',
					),
				),
			),
			array(
				'id'    => 'success-measures',
				'title' => 'Measures of success',
				'body'  => 'Operational measures Softkom can defend — not certification outcomes Softkom cannot grant.',
				'muted' => false,
				'blocks' => array(
					array(
						'type'  => 'checklist',
						'items' => array(
							'Evidence retrieval time for agreed artefact types drops from scramble to defined path.',
							'Completeness of required fields/approvals on controlled processes (agreed % or exception queue).',
							'Audit trail answers who / when / what evidence without inbox search.',
							'Access and retention rules enforceable in the system Softkom built.',
							'Boundary understood: Softkom scope ≠ certification.',
						),
					),
				),
			),
			array(
				'id'    => 'faq',
				'title' => 'Practical questions executives ask',
				'body'  => 'Including the certification boundary Softkom will not blur.',
				'muted' => true,
				'faq'   => array(
					array(
						'title' => 'How long might this take?',
						'body'  => 'Control and evidence mapping: often days to a few weeks. A focused workflow-and-capture build: often weeks to a few months depending on process count and integrations. Softkom phases highest-risk processes first.',
					),
					array(
						'title' => 'Which systems are normally involved?',
						'body'  => 'The operational systems where work already happens, document/records stores, identity/access, and sometimes HR or training tools for acknowledgements. Softkom connects rather than inventing a parallel universe when possible.',
					),
					array(
						'title' => 'What internal resources are required?',
						'body'  => 'Compliance/risk liaison, process owners, IT/security for access, a decision owner, and — separately — your legal or certification advisors for opinions Softkom does not give.',
					),
					array(
						'title' => 'How disruptive could implementation be?',
						'body'  => 'Disruption is mostly behavioural: approvals and capture in the flow of work. Softkom phases and trains to avoid a big-bang “compliance day.” Parallel scramble processes should be time-boxed and retired.',
					),
					array(
						'title' => 'What mistakes should we avoid?',
						'body'  => 'Buying a platform to “get certified”; skipping SoD design; capturing everything “just in case”; leaving legal interpretation to the software vendor; treating Softkom as auditor or counsel.',
					),
					array(
						'title' => 'How is success measured?',
						'body'  => 'Retrieval time, evidence completeness, usable audit trails, enforceable access/retention — agreed in scope. Certification pass/fail is outside Softkom’s measures unless Softkom is only preparing artefacts your auditor will judge.',
					),
					array(
						'title' => 'When is this not the right approach?',
						'body'  => 'When you need legal advice or a certification Softkom cannot issue; when a lighter workflow fix removes the scramble; when leadership will not own access and process discipline. Softkom will redirect or decline.',
					),
				),
			),
		),
	);
}
