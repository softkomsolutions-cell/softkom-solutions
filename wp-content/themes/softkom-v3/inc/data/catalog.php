<?php
/**
 * Shared content data — single source for section cards.
 * Voice: Softkom RC2 Content Excellence Standard (consultant, operational, no filler).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function softkom_v3_data_solutions() {
	return array(
		array(
			'id'      => 'business-systems',
			'icon'    => 'layout',
			'title'   => 'Business Systems',
			'outcome' => 'One operating picture instead of scattered tools',
			'body'    => 'When finance, ops and sales each keep their own version of the truth, leaders spend time reconciling rather than deciding. Softkom designs the operating layer that captures how work actually moves — orders, inventory, approvals, service — so teams stop rebuilding the same answers in spreadsheets.',
			'tags'    => array( 'Workflows', 'Integrations' ),
			'url'     => '/services/business-systems/',
		),
		array(
			'id'      => 'custom-software',
			'icon'    => 'code',
			'title'   => 'Custom Software',
			'outcome' => 'Software that fits the process — not the other way around',
			'body'    => 'Off-the-shelf tools force workarounds when your process is the competitive edge. Softkom builds applications around the workflows your teams already run: the exception paths, the approval gates, the data that must not be lost at handoff. You get software staff will use because it matches how the business works.',
			'tags'    => array( 'PHP', 'React', 'Node.js' ),
			'url'     => '/services/#custom-software',
		),
		array(
			'id'      => 'ai-automation',
			'icon'    => 'spark',
			'title'   => 'AI Automation',
			'outcome' => 'Less repetitive work, accountability retained',
			'body'    => 'AI is useful where volume is high and judgment is low: classification, drafting, routing, first-pass checks. Softkom maps those tasks first, then designs human control points for exceptions and decisions that carry commercial or compliance risk. The result is fewer hours on admin — without transferring accountability for pricing, customer commitments or regulated decisions to an opaque model.',
			'tags'    => array( 'OpenAI', 'Automation' ),
			'url'     => '/services/ai-automation/',
		),
		array(
			'id'      => 'process-automation',
			'icon'    => 'flow',
			'title'   => 'Process & Integrations',
			'outcome' => 'Clean handoffs between the systems you keep',
			'body'    => 'Most bottlenecks are not “missing software” — they are people copying data between ERP, CRM, finance and messaging. Softkom connects those systems so status, stock and customer records move once. Start with the highest-friction handoffs; expand only where integration reduces re-entry and error.',
			'tags'    => array( 'APIs', 'Power Automate' ),
			'url'     => '/services/process-integrations/',
		),
		array(
			'id'      => 'marketplace-solutions',
			'icon'    => 'bag',
			'title'   => 'Marketplace Solutions',
			'outcome' => 'Catalogue, pricing and fulfilment under control',
			'body'    => 'Multi-channel sellers lose margin when listings, stock and pricing drift apart across platforms. Softkom builds operational tools for catalogue hygiene, pricing discipline and order flow — so marketplace activity stays aligned with what the warehouse and finance team can actually fulfil and invoice.',
			'tags'    => array( 'Catalogue', 'Pricing' ),
			'url'     => '/services/#marketplace-solutions',
		),
		array(
			'id'      => 'mobile-applications',
			'icon'    => 'phone',
			'title'   => 'Mobile Applications',
			'outcome' => 'Field and customer work connected to core systems',
			'body'    => 'When site, warehouse or sales teams work offline or away from the desk, paper and delayed capture create back-office debt. Softkom extends your business systems to phones and tablets — including offline capture where the job requires it — so field data lands in the same system leadership already uses.',
			'tags'    => array( 'React Native' ),
			'url'     => '/services/#mobile-applications',
		),
		array(
			'id'      => 'compliance-platforms',
			'icon'    => 'shield',
			'title'   => 'Compliance Platforms',
			'outcome' => 'Evidence ready without the month-end scramble',
			'body'    => 'Audit and POPIA pressure often show up as last-minute document hunts. Softkom designs structured capture, retention and reporting into the workflow itself — who approved what, when, and with which evidence — so compliance is a by-product of how work is done, not a separate project every quarter.',
			'tags'    => array( 'POPIA', 'Controls' ),
			'url'     => '/services/compliance-platforms/',
		),
		array(
			'id'      => 'business-intelligence',
			'icon'    => 'chart',
			'title'   => 'Business Intelligence',
			'outcome' => 'Reporting leaders can manage against while the month is open',
			'body'    => 'If the board pack is still assembled from exports, leaders are managing last month’s story. Softkom builds reporting from the systems that run the business — the metrics Ops, Finance and Sales actually use in weekly and month-end decisions — so exceptions surface while there is still time to act, not after cashflow or service levels have already moved.',
			'tags'    => array( 'MySQL', 'Reporting' ),
			'url'     => '/services/#business-intelligence',
		),
	);
}

function softkom_v3_data_problems() {
	return array(
		array(
			'icon'  => 'list',
			'title' => 'Duplicate data capture',
			'body'  => 'The same order, customer or stock figure is typed into email, spreadsheets and separate systems. Mistakes hide in cells, and skilled people spend days on work a controlled process should own.',
			'url'   => '/services/ai-automation/',
		),
		array(
			'icon'  => 'nodes',
			'title' => 'Disconnected order and stock information',
			'body'  => 'ERP, CRM, finance and marketplace tools each hold a partial truth. Staff re-enter data between systems — and every re-entry is a chance for drift.',
			'url'   => '/services/business-systems/',
		),
		array(
			'icon'  => 'clock',
			'title' => 'Delayed approvals and unclear ownership',
			'body'  => 'Approvals sit in inboxes; handoffs stall between departments; nobody owns the exception queue. Service levels suffer even when the team is busy.',
			'url'   => '/services/#custom-software',
		),
		array(
			'icon'  => 'bars',
			'title' => 'Manual reconciliation and hand-built reports',
			'body'  => 'Month-end becomes a scramble of exports and late reconciliations. Leadership manages last month’s story instead of exceptions while the month is still open.',
			'url'   => '/services/#business-intelligence',
		),
	);
}

/**
 * Industry environments — RC2.4 evidence separation.
 *
 * group: experience | adjacent
 * Cards stay short: pressure · capability · evidence_status.
 *
 * @return array<int, array<string, string>>
 */
function softkom_v3_data_industries() {
	return array(
		array(
			'id'              => 'infrastructure-transport',
			'group'           => 'experience',
			'icon'            => 'build',
			'title'           => 'Infrastructure and transport',
			'pressure'        => 'Buyers need a clear view of capability, projects and the right enquiry path.',
			'capability'      => 'Corporate digital presence and content structure for infrastructure-facing organisations.',
			'evidence_status' => 'Verified Softkom delivery (named client work).',
			'body'            => 'Buyers need a clear view of capability, projects and the right enquiry path. Softkom has delivered corporate digital presence and content structure for infrastructure-facing organisations. Evidence: verified Softkom delivery (named client work).',
		),
		array(
			'id'              => 'retail-distribution',
			'group'           => 'experience',
			'icon'            => 'retail',
			'title'           => 'Retail and distribution',
			'pressure'        => 'Manual ordering and back-office handling slow customers and inflate ops cost as volume grows.',
			'capability'      => 'Digital ordering platforms spanning web and mobile, with a controlled fulfilment handoff.',
			'evidence_status' => 'Verified Softkom delivery (public description anonymised pending naming confirmation).',
			'body'            => 'Manual ordering and back-office handling slow customers and inflate ops cost as volume grows. Softkom has delivered digital ordering platforms spanning web and mobile. Evidence: verified Softkom delivery (public description anonymised pending naming confirmation).',
		),
		array(
			'id'              => 'ecommerce',
			'group'           => 'experience',
			'icon'            => 'bag',
			'title'           => 'E-commerce',
			'pressure'        => 'Storefront activity and stock or fulfilment records drift apart as channel volume grows.',
			'capability'      => 'E-commerce and systems delivery that connect sales activity to operational records.',
			'evidence_status' => 'Verified Softkom delivery (public description anonymised pending naming confirmation).',
			'body'            => 'Storefront activity and stock or fulfilment records drift apart as channel volume grows. Softkom has delivered e-commerce and systems work that connects sales activity to operational records. Evidence: verified Softkom delivery (public description anonymised pending naming confirmation).',
		),
		array(
			'id'              => 'professional-services',
			'group'           => 'experience',
			'icon'            => 'services',
			'title'           => 'Professional services',
			'pressure'        => 'Practices need a clear digital presence that supports enquiry and professional trust.',
			'capability'      => 'Practice websites structured around services, credibility and contact pathways.',
			'evidence_status' => 'Verified Softkom delivery (named client work).',
			'body'            => 'Practices need a clear digital presence that supports enquiry and professional trust. Softkom has delivered practice websites structured around services, credibility and contact pathways. Evidence: verified Softkom delivery (named client work).',
		),
		array(
			'id'              => 'manufacturing',
			'group'           => 'adjacent',
			'icon'            => 'factory',
			'title'           => 'Manufacturing',
			'pressure'        => 'Schedules, inventory accuracy and quality records often live in separate tools.',
			'capability'      => 'Business systems, integrations and workflow design for shop-floor and office handoffs.',
			'evidence_status' => 'Adjacent application — not presented as specialist manufacturing delivery.',
			'body'            => 'Schedules, inventory accuracy and quality records often live in separate tools. Softkom’s systems and integration methods may apply to shop-floor and office handoffs. Evidence status: adjacent application — not specialist manufacturing delivery.',
		),
		array(
			'id'              => 'logistics',
			'group'           => 'adjacent',
			'icon'            => 'truck',
			'title'           => 'Logistics',
			'pressure'        => 'Dispatch status and exception ownership become unreliable when handoffs are manual.',
			'capability'      => 'Process design and integrations for status visibility and exception handling.',
			'evidence_status' => 'Adjacent application — not presented as specialist logistics delivery.',
			'body'            => 'Dispatch status and exception ownership become unreliable when handoffs are manual. Softkom’s process and integration methods may apply. Evidence status: adjacent application — not specialist logistics delivery.',
		),
		array(
			'id'              => 'construction',
			'group'           => 'adjacent',
			'icon'            => 'build',
			'title'           => 'Construction',
			'pressure'        => 'Site and office often run on different documents, delaying commercial and programme decisions.',
			'capability'      => 'Workflow and capture design so site information can feed a shared operating picture.',
			'evidence_status' => 'Adjacent application — not presented as specialist construction delivery.',
			'body'            => 'Site and office often run on different documents. Softkom’s workflow methods may apply to capture and coordination. Evidence status: adjacent application — not specialist construction delivery.',
		),
		array(
			'id'              => 'education',
			'group'           => 'adjacent',
			'icon'            => 'edu',
			'title'           => 'Education',
			'pressure'        => 'Enrolment, fees and document chasing consume staff time when administration is fragmented.',
			'capability'      => 'Business-systems methods for recurring administrative workflows and clearer status visibility.',
			'evidence_status' => 'Adjacent application — not presented as specialist education delivery.',
			'body'            => 'Enrolment, fees and document chasing consume staff time when administration is fragmented. Softkom’s business-systems methods may apply. Evidence status: adjacent application — not specialist education delivery.',
		),
		array(
			'id'              => 'healthcare',
			'group'           => 'adjacent',
			'icon'            => 'health',
			'title'           => 'Healthcare',
			'pressure'        => 'Admin workflows and record handoffs create delay when process and access control are weak.',
			'capability'      => 'Operational workflow design with POPIA-minded data handling — not clinical or regulatory certification.',
			'evidence_status' => 'Adjacent application — no clinical, legal or certification claims.',
			'body'            => 'Admin workflows and record handoffs create delay when process and access control are weak. Softkom may support operational workflow design with POPIA-minded data handling. Evidence status: adjacent application — no clinical, legal or certification claims.',
		),
		array(
			'id'              => 'government-municipal',
			'group'           => 'adjacent',
			'icon'            => 'gov',
			'title'           => 'Government and municipal administration',
			'pressure'        => 'Service requests need accountable process: who requested, who approved, what was delivered.',
			'capability'      => 'Structured workflow and evidence capture for transparency — not a claim of public-sector specialism.',
			'evidence_status' => 'Adjacent application — conservative wording; no public-sector specialism claimed.',
			'body'            => 'Service requests need accountable process: who requested, who approved, what was delivered. Softkom’s structured workflow methods may apply. Evidence status: adjacent application — no public-sector specialism claimed.',
		),
	);
}

/**
 * Industries filtered by evidence group.
 *
 * @param string $group experience|adjacent|all
 * @return array<int, array<string, string>>
 */
function softkom_v3_data_industries_by_group( $group = 'all' ) {
	$items = softkom_v3_data_industries();
	if ( 'all' === $group ) {
		return $items;
	}
	$out = array();
	foreach ( $items as $item ) {
		if ( isset( $item['group'] ) && $item['group'] === $group ) {
			$out[] = $item;
		}
	}
	return $out;
}

function softkom_v3_data_why() {
	return array(
		array(
			'id'    => 'purpose-built',
			'icon'  => 'target',
			'title' => 'Purpose-built software',
			'body'  => 'Every platform starts from a real industry problem — not a generic feature list. Softkom designs software around how work actually moves.',
		),
		array(
			'id'    => 'long-term',
			'icon'  => 'horizon',
			'title' => 'Long-term product investment',
			'body'  => 'Softkom builds products meant to compound: stable foundations first, then automation and intelligence where they earn their place.',
		),
		array(
			'id'    => 'ai-enhanced',
			'icon'  => 'spark',
			'title' => 'Human-centred AI',
			'body'  => 'AI removes volume work where rules are clear. People keep accountability where judgment, pricing or compliance risk sits.',
		),
		array(
			'id'    => 'customer-success',
			'icon'  => 'handshake',
			'title' => 'Ongoing partnership',
			'body'  => 'Go-live is not the finish line. Softkom stays for adoption, iteration and support as volumes, channels and reporting needs change.',
		),
	);
}

/**
 * Security & reliability principles — engineering posture Softkom can defend.
 * Not certifications or invented audits.
 *
 * @return array<int, array<string, string>>
 */
function softkom_v3_data_security() {
	return array(
		array(
			'id'    => 'enterprise-ready',
			'icon'  => 'shield',
			'title' => 'Enterprise-ready engineering',
			'body'  => 'Controlled delivery, clear ownership and systems designed for operators who cannot afford fragile workarounds.',
		),
		array(
			'id'    => 'modern-architecture',
			'icon'  => 'nodes',
			'title' => 'Modern architecture',
			'body'  => 'Modular platforms that integrate with the tools you keep — without locking the business into a brittle stack.',
		),
		array(
			'id'    => 'performance-first',
			'icon'  => 'chart',
			'title' => 'Performance-first',
			'body'  => 'Interfaces and workflows built for daily use under load — not demos that slow down when volume arrives.',
		),
		array(
			'id'    => 'accessibility-first',
			'icon'  => 'layout',
			'title' => 'Accessibility-first',
			'body'  => 'Keyboard paths, readable contrast and inclusive patterns so teams can use Softkom platforms with confidence.',
		),
		array(
			'id'    => 'privacy-conscious',
			'icon'  => 'shield',
			'title' => 'Privacy-conscious',
			'body'  => 'Data handling designed with POPIA-minded discipline — collect what operations need, keep people accountable for exceptions.',
		),
	);
}

/**
 * Public product roadmap — extensible timeline (add nodes without redesigning IA).
 *
 * status: active | soon | future
 *
 * @return array<int, array{title:string,body:string,status:string,url:string}>
 */
function softkom_v3_data_roadmap() {
	return array(
		array(
			'title'  => 'MarketplaceOS',
			'body'   => 'Multi-channel operations',
			'status' => 'active',
			'url'    => '/platforms/marketplaceos/',
		),
		array(
			'title'  => 'Brick Alpha',
			'body'   => 'Collectibles intelligence',
			'status' => 'active',
			'url'    => '/platforms/brick-alpha/',
		),
		array(
			'title'  => 'Product Studio',
			'body'   => 'Third platform in the ecosystem',
			'status' => 'soon',
			'url'    => '/platforms/#product-studio',
		),
		array(
			'title'  => 'Future ERP',
			'body'   => 'Operating layer for specialised markets',
			'status' => 'future',
			'url'    => '/platforms/#future',
		),
		array(
			'title'  => 'Future Platforms',
			'body'   => 'Industry lines still to come',
			'status' => 'future',
			'url'    => '/platforms/#future',
		),
	);
}

/**
 * Projects hub cards — Phase 4 case-studies registry is the source of truth.
 *
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_data_projects() {
	if ( function_exists( 'softkom_v3_case_studies_as_project_cards' ) ) {
		$cards = softkom_v3_case_studies_as_project_cards();
		if ( $cards ) {
			return $cards;
		}
	}
	return array();
}

/**
 * Softkom product platforms — product-led portfolio.
 *
 * status: live | beta | coming | future
 *
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_data_products() {
	return array(
		array(
			'id'       => 'marketplaceos',
			'title'    => 'MarketplaceOS',
			'tagline'  => 'Multi-channel operations, under control',
			'body'     => 'Catalogue discipline, pricing intelligence and fulfilment visibility across selling channels — built for multi-channel operators.',
			'eyebrow'  => 'Platform',
			'status'   => 'beta',
			'status_label' => 'In Development',
			'accent'   => 'ops',
			'pills'    => array(
				array( 'class' => 'pill-product', 'label' => 'Softkom Product' ),
				array( 'class' => 'pill-dev', 'label' => 'In Development' ),
			),
			'url'      => '/platforms/marketplaceos/',
			'cta'      => 'Explore MarketplaceOS',
		),
		array(
			'id'       => 'brick-alpha',
			'title'    => 'Brick Alpha',
			'tagline'  => 'Investment intelligence for collectibles',
			'body'     => 'Market signal, valuation context and portfolio clarity for LEGO and collectibles investors — deliberate and precise.',
			'eyebrow'  => 'Platform',
			'status'   => 'beta',
			'status_label' => 'In Development',
			'accent'   => 'luxury',
			'pills'    => array(
				array( 'class' => 'pill-product', 'label' => 'Softkom Product' ),
				array( 'class' => 'pill-dev', 'label' => 'In Development' ),
			),
			'url'      => '/platforms/brick-alpha/',
			'cta'      => 'Explore Brick Alpha',
		),
		array(
			'id'       => 'product-studio',
			'title'    => 'Product Studio',
			'tagline'  => 'The third platform in the Softkom ecosystem',
			'body'     => 'Maturing as Softkom’s product creation and specialised-build layer — designed to sit alongside MarketplaceOS and Brick Alpha.',
			'eyebrow'  => 'Platform',
			'status'   => 'soon',
			'status_label' => 'Maturing',
			'accent'   => 'studio',
			'pills'    => array(
				array( 'class' => 'pill-product', 'label' => 'Softkom Product' ),
				array( 'class' => 'pill-dev', 'label' => 'Maturing' ),
			),
			'url'      => '/platforms/#product-studio',
			'cta'      => 'See Product Studio',
		),
		array(
			'id'       => 'future-platforms',
			'title'    => 'Future Platforms',
			'tagline'  => 'Specialised products for specialised industries',
			'body'     => 'Future ERP, AI Copilot and industry platforms — added to the ecosystem without redesigning the information architecture.',
			'eyebrow'  => 'Coming Soon',
			'status'   => 'future',
			'status_label' => 'Coming Soon',
			'accent'   => 'future',
			'pills'    => array(
				array( 'class' => 'pill-product', 'label' => 'Softkom Product' ),
				array( 'class' => 'pill-dev', 'label' => 'Coming Soon' ),
			),
			'url'      => '/platforms/#future',
			'cta'      => 'See the roadmap',
		),
	);
}

/**
 * Ecosystem timeline nodes for Platforms hub and homepage vision.
 *
 * @return array<int, array{title:string,body:string,status:string,url:string}>
 */
function softkom_v3_data_ecosystem() {
	return array(
		array(
			'title'  => 'MarketplaceOS',
			'body'   => 'Multi-channel operations platform',
			'status' => 'active',
			'url'    => '/platforms/marketplaceos/',
		),
		array(
			'title'  => 'Product Studio',
			'body'   => 'Specialised product creation layer',
			'status' => 'soon',
			'url'    => '/platforms/#product-studio',
		),
		array(
			'title'  => 'Brick Alpha',
			'body'   => 'Collectibles investment intelligence',
			'status' => 'active',
			'url'    => '/platforms/brick-alpha/',
		),
		array(
			'title'  => 'Future ERP',
			'body'   => 'Operating layer for specialised markets',
			'status' => 'future',
			'url'    => '/platforms/#future',
		),
		array(
			'title'  => 'AI Copilot',
			'body'   => 'Intelligence layered where volume work earns it',
			'status' => 'future',
			'url'    => '/platforms/#future',
		),
		array(
			'title'  => 'Future Platforms',
			'body'   => 'Industry lines still to come',
			'status' => 'future',
			'url'    => '/platforms/#future',
		),
	);
}

/**
 * Philosophy alternating blocks for homepage storytelling.
 *
 * @return array<int, array{title:string,body:string,visual:string}>
 */
function softkom_v3_data_philosophy() {
	return array(
		array(
			'title'   => 'Why specialised software wins',
			'body'    => 'When the process is the competitive edge, software must fit the work — exceptions, approvals and the data that must not be lost at handoff. Specialised platforms remove permanent workarounds.',
			'visual'  => 'specialised',
		),
		array(
			'title'   => 'Why generic software reaches limits',
			'body'    => 'Off-the-shelf tools serve the average case. As volume grows, teams invent spreadsheets and side channels to cover what the product never modelled. The tool still works — the business pays for the gaps.',
			'visual'  => 'markets',
		),
		array(
			'title'   => 'Why Softkom exists',
			'body'    => 'Softkom builds specialised platforms for markets where generic tools force those gaps. Foundations first — then automation and intelligence where they earn their place.',
			'visual'  => 'compound',
		),
	);
}

/**
 * Insights hub cards — Phase 4 insights registry is the source of truth.
 *
 * @return array<int, array{eyebrow:string,title:string,body:string,url:string}>
 */
function softkom_v3_data_insights() {
	if ( function_exists( 'softkom_v3_insights_as_hub_cards' ) ) {
		$cards = softkom_v3_insights_as_hub_cards();
		if ( $cards ) {
			return $cards;
		}
	}
	return array();
}

function softkom_v3_data_technologies() {
	return array(
		array( 'mark' => 'WP', 'title' => 'WordPress', 'body' => 'Content platforms and marketing sites when the job is structured publishing and enquiry — not a substitute for operational systems.' ),
		array( 'mark' => 'EL', 'title' => 'Elementor', 'body' => 'Structured page building with a maintainable design system Softkom can keep consistent across pages.' ),
		array( 'mark' => 'AS', 'title' => 'Astra', 'body' => 'Lightweight WordPress theme foundation Softkom uses for performance-minded site delivery.' ),
		array( 'mark' => 'WC', 'title' => 'WooCommerce', 'body' => 'Commerce storefronts Softkom connects to inventory, fulfilment and finance where online sales must match ops.' ),
		array( 'mark' => 'MG', 'title' => 'Magento', 'body' => 'Larger catalogue and commerce platform work when catalogue complexity requires it.' ),
		array( 'mark' => 'PHP', 'title' => 'PHP', 'body' => 'Server-side delivery for WordPress and custom backends Softkom builds and maintains.' ),
		array( 'mark' => 'RX', 'title' => 'React', 'body' => 'Web interfaces for custom business applications where operators need speed and clarity at the desk.' ),
		array( 'mark' => 'RN', 'title' => 'React Native', 'body' => 'Cross-platform mobile apps Softkom connects to core systems — including offline capture where field work requires it.' ),
		array( 'mark' => 'ND', 'title' => 'Node.js', 'body' => 'APIs and services that power integrations, automation and custom application backends.' ),
		array( 'mark' => 'SQL', 'title' => 'MySQL', 'body' => 'Reliable data layer for applications and reporting Softkom designs around operational truth.' ),
		array( 'mark' => 'M365', 'title' => 'Microsoft 365', 'body' => 'Workplace process automation across the tools many SA teams already licence and use daily.' ),
		array( 'mark' => 'PA', 'title' => 'Power Automate', 'body' => 'Workflow automation across Microsoft and connected apps when the bottleneck is handoff, not a new core system.' ),
		array( 'mark' => 'SP', 'title' => 'SharePoint', 'body' => 'Document and process hubs for teams that need structure, retention and controlled access.' ),
		array( 'mark' => 'AI', 'title' => 'OpenAI', 'body' => 'Practical AI-assisted workflows Softkom designs with human control points for exceptions and judgment calls.' ),
		array( 'mark' => 'DK', 'title' => 'Docker', 'body' => 'Consistent environments for build and deployment so delivery stays repeatable.' ),
		array( 'mark' => 'GH', 'title' => 'GitHub', 'body' => 'Source control, review and release discipline Softkom uses on client and product work.' ),
		array( 'mark' => 'GT', 'title' => 'Git', 'body' => 'Version control foundation for sustainable software delivery.' ),
		array( 'mark' => 'CF', 'title' => 'Cloudflare', 'body' => 'Performance, DNS and edge protection where Softkom judges it appropriate for the property.' ),
	);
}

function softkom_v3_data_faq() {
	return array(
		array(
			'title' => 'How long do projects take?',
			'body'  => 'It depends on scope and how clear the operating problem is. A focused business website can move in weeks; a custom business system or multi-system integration takes longer because Softkom maps processes, ownership and data before writing code. After discovery you get a timeline tied to scope — not a generic promise.',
		),
		array(
			'title' => 'Who owns the source code?',
			'body'  => 'For client projects, ownership is agreed in the engagement contract — Softkom does not hide IP terms in the fine print. Softkom products (MarketplaceOS AI, Brick Alpha) remain Softkom intellectual property and are labelled separately from client work on this site.',
		),
		array(
			'title' => 'How does AI automation work in practice?',
			'body'  => 'Softkom identifies repetitive work with clear rules — classify, draft, route, first-pass check — then designs workflows with human control points for exceptions and decisions that carry commercial or compliance risk. AI removes volume; people keep accountability.',
		),
		array(
			'title' => 'Can existing systems be integrated?',
			'body'  => 'Yes. Softkom frequently connects ERP, CRM, finance, marketplace and workplace tools so teams stop re-entering data. Softkom starts with the handoffs that create the most error and delay, then expands only where integration reduces re-work.',
		),
		array(
			'title' => 'How is pricing determined?',
			'body'  => 'By scope, complexity and the operating outcome you need — not a one-size package. Softkom scopes every engagement individually after understanding the process. Strategy calls clarify fit before you invest in a full proposal.',
		),
		array(
			'title' => 'Do you provide ongoing support?',
			'body'  => 'Yes. Systems need to stay usable as volumes, products and reporting needs change. Softkom offers long-term support and iteration after go-live — the same partnership posture Softkom brings to discovery and build.',
		),
		array(
			'title' => 'What delivery mistakes does Softkom design against?',
			'body'  => 'Automating unclear process; integrating systems before anyone owns master data; shipping a large release without adoption plans; and treating go-live as the finish line. Softkom phases work — diagnose, design ownership, stabilise the highest-friction handoffs, then extend — so risk stays visible to Ops, Finance and IT.',
		),
		array(
			'title' => 'What does Softkom need from the client to start well?',
			'body'  => 'Access to the people who run the process day to day, a named decision owner, and a short list of the three bottlenecks that hurt most. Softkom can work without a perfect brief; Softkom cannot work well without process owners and decision rights.',
		),
	);
}

function softkom_v3_data_paths() {
	return array(
		array(
			'icon'  => 'chart',
			'title' => 'Fix operations',
			'body'  => 'Choose this when fragmented tools and manual handoffs are already hurting throughput, accuracy or month-end. Softkom maps the bottlenecks first, then replaces spreadsheet shuttling with a clearer operating picture — often by integrating what you already run before building anything new.',
		),
		array(
			'icon'  => 'layout',
			'title' => 'Build a platform',
			'body'  => 'Choose this when off-the-shelf software forces permanent workarounds, or when your process is the competitive edge. Softkom designs purpose-built software around how your teams actually work — exceptions included — so staff are not fighting the tool every day.',
		),
		array(
			'icon'  => 'spark',
			'title' => 'Automate & integrate',
			'body'  => 'Choose this when the systems are mostly right but people are still the integration layer. Softkom connects tools and removes repetitive execution — with humans in control where judgment, customer commitment or compliance risk sits.',
		),
	);
}

function softkom_v3_data_engage() {
	return array(
		array(
			'outcome' => '01',
			'title'   => 'Discovery',
			'body'    => 'Softkom maps how work moves today: teams, tools, approvals, exceptions and blind spots. The output is a shared picture of friction — not a feature wishlist — so scope is grounded in operating reality.',
		),
		array(
			'outcome' => '02',
			'title'   => 'Design',
			'body'    => 'Target workflows, data ownership and integrations are designed before code ships. Softkom agrees what success looks like for Ops, Finance and IT — so build does not invent process on the fly.',
		),
		array(
			'outcome' => '03',
			'title'   => 'Build',
			'body'    => 'Custom software and platforms ship in controlled increments Softkom can demonstrate and adjust. Foundations and integrations come before automation — so AI and scripts sit on stable process.',
		),
		array(
			'outcome' => '04',
			'title'   => 'Support',
			'body'    => 'After go-live Softkom stays for adoption, fixes and iteration. Systems remain usable as the business changes — volumes, products, reporting and compliance demands included.',
		),
	);
}

function softkom_v3_data_topics() {
	return array(
		'Business systems & process design',
		'AI automation operators will use',
		'Marketplace & e-commerce operations',
		'Build vs buy for SA SMEs',
		'POPIA-minded system design',
	);
}

/* softkom_v3_home_workflow_media() lives in graphics.php (Connected Business™). */
