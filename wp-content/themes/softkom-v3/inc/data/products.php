<?php
/**
 * Softkom product platform detail content — MarketplaceOS & Brick Alpha.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MarketplaceOS product page content.
 *
 * @return array<string, mixed>
 */
function softkom_v3_product_marketplaceos() {
	return array(
		'id'       => 'marketplaceos',
		'title'    => 'MarketplaceOS',
		'tagline'  => 'Specialised software for multi-channel sellers who have outgrown spreadsheets and disconnected tools',
		'eyebrow'  => 'Softkom Platform',
		'hero_lead'=> 'Catalogue discipline, pricing intelligence, fulfilment visibility and AI-assisted control — built for operators who sell across channels and need one operating picture.',
		'challenge'=> array(
			'title' => 'The business challenge',
			'body'  => "Multi-channel sellers lose margin when listings, stock and pricing drift apart across platforms. Teams re-enter the same data into marketplaces, ERP, finance and messaging — and every re-entry is a chance for error.\n\nMonth-end becomes a scramble of exports. Leadership manages last week’s story instead of exceptions while there is still time to act.",
		),
		'solution' => array(
			'title' => 'The MarketplaceOS solution',
			'body'  => 'MarketplaceOS is Softkom’s specialised operations platform for multi-channel commerce. It connects catalogue, pricing, orders and fulfilment into a controlled operating layer — so marketplace activity stays aligned with what the warehouse and finance team can actually fulfil and invoice.',
		),
		'modules'  => array(
			array(
				'title' => 'Catalogue control',
				'body'  => 'Keep listings, attributes and channel mappings consistent so catalogue hygiene is not a weekend project.',
			),
			array(
				'title' => 'Pricing intelligence',
				'body'  => 'Repricing and margin guardrails designed for operators — not black-box automation without accountability.',
			),
			array(
				'title' => 'Order & fulfilment flow',
				'body'  => 'Status, exceptions and handoffs in one place so fulfilment matches what was sold.',
			),
			array(
				'title' => 'Operational visibility',
				'body'  => 'A living picture for Ops and Finance — stock, channel performance and exceptions while the week is still open.',
			),
		),
		'ai'       => array(
			'title' => 'AI features',
			'body'  => 'AI assists where volume is high and judgment is low: classification, first-pass checks, drafting and routing. Humans stay in control for pricing exceptions, customer commitments and commercial decisions.',
			'items' => array(
				'Assisted catalogue hygiene and anomaly detection',
				'First-pass exception triage for ops queues',
				'Pricing signal support with human approval gates',
				'Operational summaries leaders can act on',
			),
		),
		'integrations' => array(
			'title' => 'Integrations',
			'body'  => 'MarketplaceOS connects to the systems sellers already run — marketplaces, ERP, finance and fulfilment — starting with the highest-friction handoffs.',
			'items' => array( 'Marketplaces', 'ERP / inventory', 'Finance', 'Fulfilment', 'Messaging & alerts' ),
		),
		'benefits' => array(
			array( 'title' => 'Fewer re-entries', 'body' => 'Data moves once between channels and core systems.' ),
			array( 'title' => 'Pricing discipline', 'body' => 'Margin guardrails without losing operator control.' ),
			array( 'title' => 'Clearer exceptions', 'body' => 'Ownership of stuck orders and stock mismatches.' ),
			array( 'title' => 'Leadership visibility', 'body' => 'Current operational truth — not competing spreadsheets.' ),
		),
		'testimonials' => array(
			array(
				'quote'  => 'Customer proof will appear here once MarketplaceOS early adopters are ready to be named.',
				'author' => 'Early adopter feedback',
				'role'   => 'Coming soon',
			),
		),
	);
}

/**
 * Brick Alpha product page content.
 *
 * @return array<string, mixed>
 */
function softkom_v3_product_brick_alpha() {
	return array(
		'id'       => 'brick-alpha',
		'title'    => 'Brick Alpha',
		'tagline'  => 'Investment intelligence for LEGO and collectibles — deliberate, premium, precise',
		'eyebrow'  => 'Softkom Platform',
		'hero_lead'=> 'Market signal, valuation context and portfolio clarity for collectors and investors who treat the category seriously.',
		'challenge'=> array(
			'title' => 'The investment challenge',
			'body'  => "Collectibles markets move on incomplete signal. Prices live across forums, marketplaces and private sales. Portfolios are tracked in spreadsheets that go stale the moment a set retires or a secondary market shifts.\n\nSerious investors need clarity — not noise.",
		),
		'solution' => array(
			'title' => 'The Brick Alpha approach',
			'body'  => 'Brick Alpha is Softkom’s premium intelligence platform for LEGO and collectibles. Minimal interface. Investment-grade presentation. Built to surface signal, valuation context and portfolio position — without the playful clutter of consumer hobby apps.',
		),
		'modules'  => array(
			array(
				'title' => 'Market signal',
				'body'  => 'Structured views of secondary-market movement — designed for judgment, not impulse.',
			),
			array(
				'title' => 'Valuation context',
				'body'  => 'Contextual pricing and rarity framing so decisions sit on evidence, not anecdote.',
			),
			array(
				'title' => 'Portfolio clarity',
				'body'  => 'A clean picture of holdings, concentration and exposure across the collection.',
			),
			array(
				'title' => 'Watchlists & alerts',
				'body'  => 'Focused monitoring for sets and categories that matter to the investor — not endless feeds.',
			),
		),
		'ai'       => array(
			'title' => 'Intelligence layer',
			'body'  => 'AI assists with pattern recognition and summarisation. Investment decisions remain with the user — Brick Alpha informs; it does not replace judgment.',
			'items' => array(
				'Pattern detection across market data',
				'Concise set and category briefings',
				'Anomaly flags on unusual movement',
				'Portfolio narrative summaries',
			),
		),
		'integrations' => array(
			'title' => 'Data sources',
			'body'  => 'Brick Alpha aggregates structured market and catalogue data into a single investment workspace.',
			'items' => array( 'Market feeds', 'Catalogue references', 'Portfolio import', 'Export & reporting' ),
		),
		'benefits' => array(
			array( 'title' => 'Clarity over clutter', 'body' => 'A luxury interface that respects attention.' ),
			array( 'title' => 'Evidence over hype', 'body' => 'Signal framed for serious decision-making.' ),
			array( 'title' => 'Portfolio discipline', 'body' => 'See concentration and exposure at a glance.' ),
			array( 'title' => 'Long-term product', 'body' => 'Built as a Softkom platform — not a throwaway tool.' ),
		),
		'testimonials' => array(
			array(
				'quote'  => 'Investor testimonials will appear here as Brick Alpha enters private preview.',
				'author' => 'Private preview',
				'role'   => 'Coming soon',
			),
		),
	);
}


/**
 * Softkom Growth Engine product page content.
 *
 * @return array<string, mixed>
 */
function softkom_v3_product_autoseo() {
	return array(
		'id'        => 'growth-engine',
		'title'     => 'Softkom Growth Engine',
		'tagline'   => 'Continuous SEO and AI-search improvement for businesses that want their website to keep getting better',
		'eyebrow'   => 'Softkom Platform · Early Access',
		'hero_lead' => 'Softkom Growth Engine monitors search visibility, finds the highest-value opportunities, prepares safe improvements and helps publish approved changes — with business impact and lead generation at the centre.',
		'challenge' => array(
			'title' => 'SEO work is too often reactive, fragmented and hard to measure',
			'body'  => "Most businesses check SEO occasionally, commission a report, fix a handful of issues and then move on. Rankings change, competitors publish, content decays and technical problems reappear.\n\nThe result is a website that slowly loses visibility while nobody has a clear view of what matters commercially.",
		),
		'solution' => array(
			'title' => 'A continuous improvement layer for search and conversion',
			'body'  => 'Softkom Growth Engine combines technical monitoring, Search Console intelligence, competitor and content-gap analysis, AI-search visibility and controlled WordPress publishing. It prioritises work by likely business value, not by the size of an SEO checklist.',
		),
		'modules' => array(
			array( 'title' => 'Technical SEO monitor', 'body' => 'Track indexing, metadata, canonicals, redirects, broken links, sitemaps, robots directives and other technical issues that affect discoverability.' ),
			array( 'title' => 'Search opportunity engine', 'body' => 'Use Search Console and site data to find declining pages, high-impression low-click queries, content gaps and pages with realistic upside.' ),
			array( 'title' => 'AI-search visibility', 'body' => 'Track the signals that help a business surface in AI-assisted discovery and identify where stronger source content, entities and evidence are needed.' ),
			array( 'title' => 'Content & publishing workflow', 'body' => 'Prepare briefs, updates and new pages, then send them through an approval gate before WordPress publication.' ),
			array( 'title' => 'Conversion layer', 'body' => 'Connect search visibility to actual enquiries, calls and form submissions so growth activity is measured against business outcomes.' ),
			array( 'title' => 'Executive reporting', 'body' => 'Show what changed, why it mattered, what improved and what should be done next without burying decision-makers in SEO jargon.' ),
		),
		'ai' => array(
			'title' => 'AI where it speeds up analysis and execution',
			'body'  => 'AI helps classify issues, analyse page intent, draft improvements and surface patterns. High-impact edits remain reviewable, with approval gates before publishing.',
			'items' => array(
				'Issue clustering and commercial prioritisation',
				'Content decay and opportunity detection',
				'Brief and page-update drafting',
				'Internal-link and metadata recommendations',
				'AI-search visibility research',
				'Monthly growth summaries',
			),
		),
		'integrations' => array(
			'title' => 'Connected to the systems that show real performance',
			'body'  => 'Softkom Growth Engine is designed around first-party data and controlled publishing rather than isolated keyword reports.',
			'items' => array( 'Google Search Console', 'GA4', 'WordPress', 'Google Business Profile', 'CRM / lead data', 'AI-search research' ),
		),
		'benefits' => array(
			array( 'title' => 'Continuous improvement', 'body' => 'Problems and opportunities are found while they still matter.' ),
			array( 'title' => 'Commercial prioritisation', 'body' => 'Work is ranked by likely traffic, lead and conversion impact.' ),
			array( 'title' => 'Controlled automation', 'body' => 'Speed up execution without giving up approval over important website changes.' ),
			array( 'title' => 'One growth picture', 'body' => 'Search visibility, content performance and lead outcomes are viewed together.' ),
		),
		'status' => 'early-access',
	);
}
