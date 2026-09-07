<?php
/**
 * Plugin Name: Softkom Organic Traffic Sprint
 * Description: High-intent organic acquisition pages feeding the Softkom assessment funnel.
 *              Sprint 1 adds four commercial pages: replace-Excel/custom-software comparison,
 *              automate-manual-processes, and business-system-integration, plus richer
 *              buyer content for the spreadsheet modernisation page. Sprint 2 adds six
 *              problem-led pages: automate-data-entry, automate-business-reporting,
 *              automate-approvals-workflows, connect-business-software,
 *              automate-lead-follow-up, and operations-management-system.
 *
 * All pages are substantial, single-purpose buyer pages. Primary conversion is the
 * Softkom Business Systems / Automation Assessment at /assessment/.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Definitions for the four new traffic pages.
 *
 * Slugs: replace-excel-with-custom-software-south-africa
 *        custom-software-vs-spreadsheets
 *        automate-manual-business-processes
 *        business-system-integration-south-africa
 *        automate-data-entry-south-africa
 *        automate-business-reporting-south-africa
 *        automate-approvals-workflows-south-africa
 *        connect-business-software-south-africa
 *        automate-lead-follow-up-south-africa
 *        operations-management-system-south-africa
 */
function softkom_traffic_sprint_pages() {
	return array(
		'replace-spreadsheets-manual-processes-south-africa' => array(
			'title'    => 'Replace Spreadsheets and Manual Processes South Africa',
			'eyebrow'  => 'WORKFLOW MODERNISATION · SOUTH AFRICA',
			'headline' => 'Outgrown spreadsheets? Turn fragile manual processes into a connected business system',
			'intro'    => 'When your business has outgrown spreadsheets, WhatsApp and disconnected software, Softkom designs the system that replaces the manual work. This guide covers the signs that spreadsheets are now limiting your operations, when Excel should be replaced by a business system, and how Softkom helps South African businesses move from manual processes to connected, trackable systems.',
			'problem_block_title'   => 'Signs your business has outgrown spreadsheets',
			'problems' => array(
				'Teams work from different versions of the same spreadsheet.',
				'Information is copied between email, WhatsApp and spreadsheets by hand.',
				'Approvals and hand-offs are difficult to track.',
				'Reporting requires manual consolidation and is hard to trust.',
				'A key process depends on one person knowing the workaround.',
				'Errors, delays and reporting gaps grow with every new team member.',
			),
			'why_replace_title' => 'Which spreadsheet processes should be replaced',
			'why_replace' => array(
				array(
					'head'  => 'Shared operational spreadsheets',
					'body'  => 'When a spreadsheet becomes the system of record for orders, stock, leads or jobs, multi-user conflict and version drift become real operational risk. These are prime candidates for a business system.',
				),
				array(
					'head'  => 'Processes with approvals and hand-offs',
					'body'  => 'Where work moves through stages and approvals, tracking is difficult in a spreadsheet. A workflow system guides work through consistent steps with reliable status.',
				),
				array(
					'head'  => 'Manual reporting and reconciliation',
					'body'  => 'If reporting depends on consolidating many files by hand, a centralised system gives management an accurate, current view without the manual work.',
				),
			),
			'options_title' => 'Custom software vs spreadsheets',
			'options_intro' => 'Spreadsheets remain powerful analysis tools. The decision is about operations, not about removing every spreadsheet.',
			'options' => array(
				array(
					'head' => 'Keep spreadsheets for',
					'body'  => 'one-off analysis, modelling and simple personal tracking. They are fast, familiar and ideal for exploring numbers.',
				),
				array(
					'head' => 'Replace spreadsheets when',
					'body'  => 'a process becomes a shared operational workflow that needs reliable data, controlled access, status tracking, approvals and trustworthy reporting.',
				),
				array(
					'head' => 'Integrate instead where possible',
					'body'  => 'Before building, check whether connecting existing systems removes the manual work. Integration often delivers most of the value at lower cost.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, or Build it',
			'framework_intro' => 'Softkom\'s decision framework for replacing spreadsheets: Automate it, Integrate it, then Build it.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Use rules, workflows and existing tools to remove repetitive capture, approvals and notifications without a large build.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Where data already lives in accounting, CRM or other tools, connect them so people stop re-entering information.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Build a custom system when a core process needs more control, scale or differentiation than existing tools allow.',
				),
			),
			'sa_title' => 'South African implementation considerations',
			'sa_points' => array(
				'Existing spreadsheet data can be cleaned, mapped and migrated into a structured system as part of implementation.',
				'Designs account for local realities: variable uptime, remote and WhatsApp-based communication, and mixed desktop and mobile use.',
				'Phased rollout works well in South African SMEs — modernise the highest-friction process first, then extend.',
				'Softkom custom business-system engagements typically begin around R75,000, with larger platforms scoped separately.',
			),
			'decision_title' => 'Does your operation need a business system?',
			'decision' => array(
				'Spreadsheets have become a shared operational system.',
				'Information is repeatedly copied between tools by hand.',
				'Approvals and hand-offs are hard to track.',
				'Management lacks a reliable, current operational view.',
				'Growth requires adding people mainly to control administration.',
			),
			'decision_note' => 'If any of these feels familiar, start with the Softkom assessment to see which processes are worth automating, integrating or rebuilding first.',
			'proof' => array(
				'Business systems decision pages' => home_url( '/services/' ),
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
			),
			'faq' => array(
				array( 'When should a business replace its spreadsheets?', 'When spreadsheets become a shared operational system that creates duplicate data, requires repeated manual work, or makes it difficult to control access, status and reporting.' ),
				array( 'Do we have to replace every spreadsheet?', 'No. The right approach is to modernise the processes where spreadsheet limitations create meaningful operational risk or wasted time, and keep analysis spreadsheets.' ),
				array( 'Can existing spreadsheet data be migrated?', 'Yes. Data can be cleaned, mapped and migrated into a structured system as part of implementation, so the business starts with accurate records.' ),
				array( 'What is the first step?', 'Start by understanding which processes cause the most manual work and risk. The Softkom assessment prioritises candidates for automation, integration or a custom build.' ),
			),
		),

		'replace-excel-with-custom-software-south-africa' => array(
			'title'    => 'Replace Excel with Custom Software South Africa',
			'eyebrow'  => 'EXCEL REPLACEMENT · SOUTH AFRICA',
			'headline' => 'Outgrown your Excel operational system? Replace it with software built around the way your business works',
			'intro'    => 'When your business has outgrown spreadsheets, WhatsApp and disconnected software, Softkom designs the system that replaces the manual work. This page explains the signs that Excel is now holding your operations back, when replacement is worth it, and how to move from fragile spreadsheets to a custom business system without disrupting the business.',
			'problem_block_title'   => 'Signs your business has outgrown spreadsheets',
			'problems' => array(
				'Several people work from different versions of the same Excel file.',
				'Information is copied by hand between email, WhatsApp, PDFs and spreadsheets.',
				'Approvals and hand-offs depend on someone remembering to act.',
				'Reporting takes hours of manual consolidation and is hard to trust.',
				'A critical process depends on one person who knows the workaround.',
				'Access controls, change tracking and audit trails are hard to manage in Excel.',
			),
			'why_replace_title' => 'When Excel replacement is worth it',
			'why_replace' => array(
				array(
					'head'  => 'It has become a shared operational system',
					'body'  => 'Once a spreadsheet is the system of record for orders, stock, leads or jobs, its limits become the business\'s limits. Multi-user conflict, version drift and fragile formulas turn into real operational risk.',
				),
				array(
					'head'  => 'Manual re-entry is creating errors and bottlenecks',
					'body'  => 'Every time information is copied between systems it can be mistyped, delayed or lost. Maturity indicators such as duplicate capture and reconciliation work point to a process that needs a system, not another spreadsheet.',
				),
				array(
					'head'  => 'Management cannot see the live state of the business',
					'body'  => 'A system built around the business gives an accurate, current operational view. With spreadsheets, the truth is hidden across files and only visible after manual consolidation.',
				),
			),
			'options_title' => 'Custom software vs spreadsheets: the honest comparison',
			'options_intro' => 'Spreadsheets remain excellent analysis tools. They are rarely a good operational system of record once they become multi-user and multi-step. The comparison below is about operations, not about replacing every spreadsheet.',
			'options' => array(
				array(
					'head' => 'Spreadsheets',
					'body'  => 'Great for one-off analysis, modelling and simple tracking. Weak when many people share live data, when processes cross stages and approvals, and when you need reliable access control and audit trails. Departmental workarounds grow until information becomes inconsistent.',
				),
				array(
					'head' => 'Custom business systems',
					'body'  => 'Built around your workflows: one source of truth, role-based access, automated approvals and alerts, and reporting you can trust. They require an upfront build, but they remove the manual work that scales poorly as the business grows.',
				),
			),
			'framework_title' => 'Automate it, integrate it, or build it',
			'framework_intro' => 'Softkom\'s decision framework for replacing spreadsheets is simple: Automate it, Integrate it, then Build it.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Where a spreadsheet process can be automated with rules, workflows and existing tools, we automate it first. This handles repetitive capture, approvals, follow-up and notifications without a large build.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Where the data already lives in accounting, CRM or other systems, we connect them so people stop re-entering information. Integration often removes more manual work than replacement.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Where a core process needs more control, scale or differentiation than existing tools allow, we build a custom system designed around the business. This is the right answer when the spreadsheet is structurally the wrong tool.',
				),
			),
			'sa_title' => 'South African implementation considerations',
			'sa_points' => array(
				'Budget for data migration — cleaning and mapping existing spreadsheet data is part of the work, not an afterthought.',
				'Local context matters: eskom-prone uptime, remote and WhatsApp-based communication, and a mix of desktop and mobile users shape the design.',
				'Phased rollout works best in South African SMEs — start with the process causing the most manual work, then extend.',
				'Realistic cost: Softkom custom business-system engagements typically begin around R75,000, with larger platforms scoped separately.',
			),
			'decision_title' => 'Which path is right for you?',
			'decision' => array(
				'The process is repetitive and has clear rules',
				'Existing tools can be connected instead of replaced',
				'The process is core, differentiates the business, or needs strong control',
			),
			'decision_note' => 'If most of these sound like your situation, start by taking the Softkom assessment to see exactly where automation, integration or a custom build will create the most value.',
			'proof' => array(
				'Business decision pages' => home_url( '/services/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
			),
			'faq' => array(
				array( 'When should Excel be replaced by a business system?', 'When a spreadsheet becomes a shared operational system that requires manual re-entry, controlled access, reliable status tracking or trustworthy reporting, it is usually time to replace it.' ),
				array( 'Can software replace every Excel spreadsheet?', 'No. Analysis and modelling in Excel remain useful. The right target is operational processes that depend on duplicate capture, approvals, hand-offs and live multi-user data.' ),
				array( 'What happens to our existing spreadsheet data?', 'Data can be cleaned, mapped and migrated into the new system as part of implementation so the business starts with accurate, structured records.' ),
				array( 'Will staff struggle to switch from Excel?', 'A system built around the way people already work reduces training time. Softkom designs workflows that match realistic working styles rather than forcing a rigid new process.' ),
			),
		),

		'custom-software-vs-spreadsheets' => array(
			'title'    => 'Custom Software vs Spreadsheets',
			'eyebrow'  => 'BUYER DECISION GUIDE · SOFTWARE',
			'headline' => 'Spreadsheets or custom software? A practical comparison for growing businesses',
			'intro'    => 'The short answer: keep spreadsheets for analysis, but move operational processes to a proper system once they involve multiple people, live shared data, approvals or reporting. Softkom helps South African businesses make this call with confidence — and choose the cheapest option that actually solves the problem.',
			'problem_block_title'   => 'When the answer stops being obvious',
			'problems' => array(
				'Your spreadsheet works, but only for the person who built it.',
				'Every growth milestone seems to add more manual administration.',
				'Different tools hold different versions of the same information.',
				'You have outgrown the free or low-cost tools but are not sure what is justified.',
				'Cost, disruption and fear of the wrong system keep you on spreadsheets longer than you should stay.',
			),
			'options_title' => 'Spreadsheets vs custom software: the honest comparison',
			'options_intro' => 'This is not a contest where custom software always wins. The right choice depends on what the tool is being asked to do.',
			'options' => array(
				array(
					'head' => 'Choose spreadsheets when',
					'body'  => 'you need one-off analysis, a flexible model, or lightweight tracking that only you use. They are fast, familiar and free. They are the wrong tool once the process becomes a shared operational workflow.',
				),
				array(
					'head' => 'Choose custom software when',
					'body'  => 'a core process is difficult to manage with standard tools, creates repeated manual work, or is important enough to justify a system designed around the business. Custom software removes the workarounds that spreadsheets force.',
				),
				array(
					'head' => 'Sometimes, integrate instead',
					'body'  => 'Before building, check whether connecting the systems you already use removes the manual work. Automation and integration often deliver most of the value at a fraction of the cost of a full build.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, or Build it',
			'framework_intro' => 'Instead of asking only "spreadsheets or custom software", Softkom works through a three-step framework.',
			'framework' => array(
				array(
					'step' => 'Automate it',
					'body' => 'Remove repetitive capture, follow-up, approvals and notifications with rules and existing tools. This is the fastest, lowest-risk win.',
				),
				array(
					'step' => 'Integrate it',
					'body' => 'Connect accounting, CRM, email and other systems so data moves automatically instead of being re-entered.',
				),
				array(
					'step' => 'Build it',
					'body' => 'Design a custom system when a process needs control, scale or differentiation that standard software cannot provide.',
				),
			),
			'decision_title' => 'Decision criteria: how Softkom helps you choose',
			'decision' => array(
				'How often does the process run, and how much manual time does it consume?',
				'How much does an error, a delay or lost information cost the business?',
				'Can existing tools be connected to remove the work?',
				'Is the process core enough to justify software built around it?',
				'What will the operation look like in two or three years if you do nothing?',
			),
			'decision_note' => 'Rather than guessing, run your situation through the Softkom assessment to see whether automation, integration or a custom build is the right investment for your actual processes.',
			'proof' => array(
				'Business systems decision pages' => home_url( '/services/' ),
				'Replace Excel with custom software' => home_url( '/replace-excel-with-custom-software-south-africa/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
			),
			'faq' => array(
				array( 'Are spreadsheets really that bad?', 'No — they are excellent analysis tools. They become a problem when they are used as a shared operational system that needs live data, access control, approvals and reliable reporting.' ),
				array( 'Is custom software always cheaper than spreadsheets?', 'No. For simple tracking, spreadsheets win on cost. For operational processes that consume staff time and create errors, a custom system is usually cheaper over time because it removes the manual work.' ),
				array( 'Should we build custom software or buy off-the-shelf?', 'Off-the-shelf software is attractive when your process matches the product. Custom software is right when a core process is different enough that standard tools force workarounds.' ),
				array( 'How do we avoid wasting money on the wrong system?', 'Start with an assessment of the actual processes, quantify the manual cost, and work through automate, integrate and build before committing to a large build.' ),
			),
		),

		'automate-manual-business-processes' => array(
			'title'    => 'Automate Manual Business Processes',
			'eyebrow'  => 'PROCESS AUTOMATION · GUIDE',
			'headline' => 'Automate manual business processes: what to automate first and how to do it safely',
			'intro'    => 'Many growing businesses add administrative effort faster than they add revenue. The practical answer is often not a big system project but targeted automation of the repetitive processes that consume the most time and cause the most errors. This guide explains what can be automated and how to choose the right first project.',
			'problem_block_title'   => 'The cost of manual processes',
			'problems' => array(
				'The same information is captured more than once because tools do not share it.',
				'Approvals and hand-offs depend on email or WhatsApp reminders.',
				'Teams cannot easily see the current status of work.',
				'Errors happen when steps are skipped or data is re-entered.',
				'Reporting requires manual consolidation and is always slightly out of date.',
				'Growth means hiring people mainly to control administration.',
			),
			'what_title' => 'Which manual processes should be automated first',
			'what_points' => array(
				array('head'=>'Repetitive data entry','body'=>'Anywhere information is copied from one system to another is a candidate. Removing re-entry removes errors and frees staff time.'),
				array('head'=>'Routine follow-up and reminders','body'=>'Lead follow-up, payment reminders and task reminders are rule-based and automatable with human escalation.'),
				array('head'=>'Approvals and routing','body'=>'Work that follows a defined sequence — quotes, leave, orders, invoices — can be guided through consistent steps with notifications.'),
				array('head'=>'Status tracking and reporting','body'=>'If reporting depends on people updating a spreadsheet, automation can keep the source of truth current so reports are trustworthy.'),
			),
			'framework_title' => 'Automate it, Integrate it, then Build it',
			'framework_intro' => 'Softkom does not start with new software. It works through a pragmatic framework.',
			'framework' => array(
				array(
					'step' => 'Automate it',
					'body' => 'Use rules, workflows and existing tools to remove repetitive steps. This is the fastest way to regain time.',
				),
				array(
					'step' => 'Integrate it',
					'body' => 'Connect the systems you already use so information flows automatically between them.',
				),
				array(
					'step' => 'Build it',
					'body' => 'Build a custom workflow application only when the process needs more control, scale or differentiation.',
				),
			),
			'sa_title' => 'Automation vs integration vs custom development',
			'sa_points' => array(
				'Automation: use rules and workflows to perform repetitive tasks in the tools you already use. Best for speed and low cost.',
				'Integration: connect existing systems through APIs and workflow connectivity so data moves automatically. Best when you already have good tools that are disconnected.',
				'Custom development: build a tailored system when the process is core, complex or different from what off-the-shelf tools provide. Best when automation and integration are not enough.',
				'In South African SMEs, a phased approach usually works best: automate one painful process first, prove the value, then extend.',
			),
			'decision_title' => 'How to pick your first automation project',
			'decision' => array(
				'It runs frequently — daily or weekly, not yearly.',
				'It follows repeatable steps that can be described as rules.',
				'It currently causes delays, errors or missed opportunities.',
				'Its value can be measured (time saved, faster response, fewer errors).',
				'Staff are ready to be moved from repetitive work to higher-value work.',
			),
			'decision_note' => 'If you can tick most of these, you have a strong first automation candidate. Use the Softkom assessment to prioritise the highest-value process for your business.',
			'proof' => array(
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
				'AI automation for SMEs in South Africa' => home_url( '/ai-automation-for-smes-south-africa/' ),
				'Business systems decision pages' => home_url( '/services/' ),
			),
			'faq' => array(
				array( 'What does it mean to automate a business process?', 'It means using software, rules and integrations to perform repetitive tasks, move information between systems and guide work through consistent steps with less human effort.' ),
				array( 'Will automation replace jobs?', 'The goal is normally to remove repetitive administration and give staff better information, while people remain responsible for exceptions, relationships and important decisions.' ),
				array( 'How much does process automation cost?', 'It depends on scope and integrations. Small workflow automations can be delivered affordably; larger connected workflows are scoped individually. An assessment helps you quantify the value first.' ),
				array( 'Can we automate processes that still run in spreadsheets?', 'Yes. Spreadsheet-driven processes are strong candidates when they involve repeated capture, approvals, status tracking or reporting.' ),
				array( 'What is the difference between automation and integration?', 'Automation performs repetitive tasks using rules and workflows. Integration connects separate systems so data moves between them automatically. They often work together.' ),
			),
		),

		'business-system-integration-south-africa' => array(
			'title'    => 'Business System Integration South Africa',
			'eyebrow'  => 'SYSTEM INTEGRATION · SOUTH AFRICA',
			'headline' => 'Connect your disconnected business systems with APIs and workflow integration',
			'intro'    => 'You may not need to replace your systems at all. If the tools are good but disconnected, business system integration connects them so data flows automatically between your accounting, CRM, ecommerce and operations software — removing manual re-entry without a big rebuild. Softkom designs and delivers reliable integrations for South African businesses.',
			'problem_block_title'   => 'Signs your systems are disconnected',
			'problems' => array(
				'Data is entered into your CRM and then re-entered into accounting or operations.',
				'Orders, stock or invoices do not automatically update across systems.',
				'None of your systems provides a complete, current view of the business.',
				'Integrations that exist break silently or require constant maintenance.',
				'Teams maintain their own copies of information because the tools do not share it.',
			),
			'options_title' => 'What integration can connect',
			'options_intro' => 'Business system integration covers APIs, workflow connectivity and automated data flows between the tools you already rely on.',
			'options' => array(
				array('head'=>'CRM and sales','body'=>'Keep lead and pipeline data in sync, trigger follow-up and reporting without manual CRM updates.'),
				array('head'=>'Accounting and finance','body'=>'Automatically move invoices, payments and reconciliations between operations and accounting instead of re-entering them.'),
				array('head'=>'Ecommerce and inventory','body'=>'Sync orders, stock and fulfilment so inventory is accurate across sales channels.'),
				array('head'=>'Operations and workflows','body'=>'Route work, approvals and notifications between systems so teams see one current status.'),
			),
			'framework_title' => 'Integration within Softkom\'s decision framework',
			'framework_intro' => 'Integration is the middle step of Softkom\'s Automate, Integrate, Build framework — and it is often the right answer before any replacement.',
			'framework' => array(
				array('step'=>'Automate it','body'=>'Add rules and workflows to perform repetitive tasks in the tools you already use.'),
				array('step'=>'Integrate it','body'=>'Connect existing systems through APIs and workflow connectivity so data moves automatically between them. This removes the manual re-entry that replacement would otherwise just relocate.'),
				array('step'=>'Build it','body'=>'Only build custom software when the process needs control, scale or differentiation that integration cannot provide.'),
			),
			'sa_title' => 'Integration in the South African context',
			'sa_points' => array(
				'Integrations are valuable where internet and power are not always stable — reliable connection and error handling matter more than in some markets.',
				'WhatsApp-based communication needs to connect cleanly with CRM and operations systems.',
				'Many South African SMEs run a mix of local and SaaS tools; integration removes the copy-paste burden between them.',
				'Softkom prioritises practical connectivity with clear error handling rather than fragile point-to-point scripts.',
			),
			'decision_title' => 'Should you integrate or replace?',
			'decision' => array(
				'The systems themselves do the job — they just do not share data.',
				'Manual re-entry is the bottleneck, not the tools.',
				'You want an accurate, current operational view without replacing core software.',
				'You want to add new capabilities to existing systems rather than rebuild.',
			),
			'decision_note' => 'If your systems are good but disconnected, integration is very likely the right first move. Use the Softkom assessment to confirm where connecting your tools will create the most value before you commit.',
			'proof' => array(
				'Process integration service page' => home_url( '/services/process-integrations/' ),
				'Business systems decision pages' => home_url( '/services/' ),
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
			),
			'faq' => array(
				array( 'What is business system integration?', 'It is connecting separate software systems — through APIs and workflow connectivity — so that data and processes flow automatically between them instead of being manually re-entered.' ),
				array( 'Do we need to replace our current systems?', 'Not necessarily. Integration is often the right answer when the tools work but are disconnected. Replacement is reserved for processes that need more than integration can provide.' ),
				array( 'What is the difference between an API and an integration?', 'An API is the technical interface a system exposes. An integration is the working connection built on top of it — including data mapping, error handling, security and monitoring.' ),
				array( 'What if a system has no API?', 'Softkom assesses each system. Where no API exists, options include import/export workflows, middleware, or a lightweight layer that connects and synchronises the data.' ),
				array( 'How much does system integration cost in South Africa?', 'It depends on the number and complexity of systems involved. A focused integration between two tools is scoped individually and can be far cheaper than replacement.' ),
			),
		),

		'automate-data-entry-south-africa' => array(
			'title'    => 'Automate Data Entry South Africa',
			'eyebrow'  => 'DATA ENTRY AUTOMATION · SOUTH AFRICA',
			'headline' => 'Stop re-entering the same information. Automate data entry between the systems you already use',
			'intro'    => 'Staff repeatedly entering the same information into multiple systems is one of the most avoidable costs in a growing business. Every copy-paste adds delay and a chance for errors. Softkom helps South African businesses automate data entry by connecting the CRM, accounting, operations and ecommerce tools they already use — and only builds custom software when automation and integration are not enough.',
			'problem_block_title'   => 'Signs that manual data entry is costing your business',
			'problems' => array(
				'Staff type the same customer, order or invoice details into more than one system every day.',
				'Data entry errors surface in invoices, deliveries or reconciliations only after they have already caused damage.',
				'Information is copied between email, WhatsApp, Excel and your accounting system by hand.',
				'Month-end reconciliation is spent hunting mismatches that re-entry created.',
				'The business grows by adding administrators whose main job is copying data between tools.',
				'The same information exists in several places and nobody is sure which version is correct.',
			),
			'why_replace_title' => 'What manual data entry really costs the business',
			'why_replace' => array(
				array(
					'head'  => 'Time stuck in transcription, not customers',
					'body'  => 'Every transaction that passes through human hands adds minutes and a queue. The bottleneck becomes data entry capacity rather than business demand.',
				),
				array(
					'head'  => 'Errors that compound downstream',
					'body'  => 'Each manual copy is a chance to mistype. Errors move silently into billing, stock and reporting, where they cost time and trust to find and fix.',
				),
				array(
					'head'  => 'Reconciliation becomes a costly habit',
					'body'  => 'When data is re-entered, it drifts. Teams burn hours reconciling systems that should agree automatically without manual effort.',
				),
			),
			'options_title' => 'What data entry can be automated, integrated or rebuilt',
			'options_intro' => 'Most data-entry pain is not solved by replacing your systems. It is solved by making information move automatically — and only then considering a build.',
			'options' => array(
				array(
					'head' => 'Automate capture at the source',
					'body'  => 'Orders, invoices, forms and enquiries capture data once, with validation, so nothing needs to be retyped into another system.',
				),
				array(
					'head' => 'Integrate the systems that touch the data',
					'body'  => 'Connect CRM, accounting, ecommerce and operations so transactions flow between them automatically instead of being re-keyed.',
				),
				array(
					'head' => 'Build only when the workflow is unique',
					'body'  => 'When no off-the-shelf connection exists and the process is core, a small custom layer or business system removes the re-entry permanently.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, or Build it',
			'framework_intro' => 'Softkom does not default to custom software. Data entry is usually solved by the simplest option that actually removes the manual work.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Use forms, rules and existing tools to capture data once, validate it, and stop the re-typing at the source.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Connect the systems you already use through APIs so data moves automatically between accounting, CRM and operations.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Build a custom data flow or business system only when a process is unique enough that automation and integration cannot remove the manual work.',
				),
			),
			'sa_title' => 'Data entry automation in the South African context',
			'sa_points' => array(
				'South African SMEs constantly move data between banks, accounting packages, ecommerce and WhatsApp — integration removes the copy-paste between these systems.',
				'Designs account for local realities such as intermittent connectivity and load-shedding, so automation continues to work reliably and queues data safely.',
				'Typical first projects include invoicing feeds, stock synchronisation between sales channels, and order capture from WhatsApp or email into a system of record.',
				'A phased approach works well: automate one repeated capture step, measure the time saved, then extend to the next flow.',
			),
			'decision_title' => 'Is your data entry a candidate for automation?',
			'decision' => array(
				'A staff member re-enters the same information more than once a day.',
				'Errors or delays cause rework, chargebacks or customer complaints.',
				'Several systems hold copies of the same customer, order or stock data.',
				'Reports and reconciliations constantly need correction.',
				'You are hiring administrators mainly to copy data between tools.',
			),
			'decision_note' => 'If you recognise more than a few of these, run the Softkom assessment to see exactly which data flows should be automated, integrated or rebuilt first.',
			'proof' => array(
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
				'Business system integration in South Africa' => home_url( '/business-system-integration-south-africa/' ),
				'Replace spreadsheets and manual processes' => home_url( '/replace-spreadsheets-manual-processes-south-africa/' ),
			),
			'faq' => array(
				array( 'How do I automate manual data entry?', 'Start with the flows where the same information is typed into more than one system. Those are the highest-value candidates to integrate or automate first. The Softkom assessment identifies which flows to tackle.' ),
				array( 'What types of data can be automated in a South African business?', 'Customer and order data, invoicing and payments, stock levels, supplier details and internal records are common candidates. Any information that is routinely copied between systems can usually flow automatically.' ),
				array( 'Does automating data entry mean building custom software?', 'Usually not. Most data entry is fixed by automating capture at the source and integrating the existing systems through APIs. Custom software is reserved for processes that cannot be handled any other way.' ),
				array( 'Can data entry automation work with my existing accounting software?', 'Yes. Modern accounting packages expose APIs, and Softkom can also work through import-export workflows where a direct connection is not available.' ),
				array( 'How much time can data entry automation save?', 'The value depends on the volume of re-entry in your business. The assessment quantifies the size and cost of the manual work so you can measure what automation will return before you invest.' ),
			),
		),

		'automate-business-reporting-south-africa' => array(
			'title'    => 'Automate Business Reporting South Africa',
			'eyebrow'  => 'REPORTING AUTOMATION · SOUTH AFRICA',
			'headline' => 'Automate management reporting: replace manual spreadsheet consolidation with reports you can trust',
			'intro'    => 'When management reports are assembled by hand from spreadsheets and several systems, they arrive late, take days to produce and are hard to trust. Softkom helps South African businesses automate business reporting so the numbers flow from source systems into reports and dashboards automatically — replacing the manual Excel consolidation without losing the flexibility teams rely on.',
			'problem_block_title'   => 'Signs your reporting is manual and unreliable',
			'problems' => array(
				'Month end means days of consolidating spreadsheets and copying numbers by hand.',
				'Different departments send their own versions of the same figures, and they disagree.',
				'Management decisions rely on reports that are already out of date when they are produced.',
				'Numbers that should agree between systems do not, and nobody can explain the difference.',
				'Producing a new report means asking a person to rebuild it from scratch.',
				'Problems are discovered weeks later because nothing is surfaced automatically.',
			),
			'why_replace_title' => 'What manual reporting costs the business',
			'why_replace' => array(
				array(
					'head'  => 'Slow decisions',
					'body'  => 'The gap between an event in the business and a management response becomes weeks rather than days, which is exactly when small issues grow into costly ones.',
				),
				array(
					'head'  => 'Low trust in numbers',
					'body'  => 'When figures must be rechecked before use, teams stop relying on reports for decisions and start arguing about data instead of acting on it.',
				),
				array(
					'head'  => 'Hidden staff cost every cycle',
					'body'  => 'Each reporting cycle consumes hours of capable staff time that could have served customers or improved operations — and then it repeats next month.',
				),
			),
			'options_title' => 'How business reporting can be automated',
			'options_intro' => 'Excel is still useful for analysis. It is the manual assembly around it — gathering, copying and reconciling — that should be automated or removed.',
			'options' => array(
				array(
					'head' => 'Automate the consolidation',
					'body'  => 'Pull data automatically from accounting, sales and operations into standard, trustworthy reports — current on request, not rebuilt by hand.',
				),
				array(
					'head' => 'Integrate the data sources',
					'body'  => 'Connect the systems that already hold the numbers so reports use one consistent set of facts instead of each team\'s copy.',
				),
				array(
					'head' => 'Build a reporting system when needed',
					'body'  => 'When reporting must be interactive, role-specific or central to operations, a tailored reporting layer or system replaces spreadsheet gymnastics.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, then Build it',
			'framework_intro' => 'Reporting automation follows the same framework as every Softkom engagement: use the cheapest capable option first.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Automate the assembly of standard reports so they are generated from source data instead of re-keyed from spreadsheets.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Integrate the data sources so accounting, sales and operations feed one consistent reporting view without manual exports.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Build a tailored reporting layer only when standard tools and integration cannot deliver the control or insight the business needs.',
				),
			),
			'sa_title' => 'Automated reporting in the South African context',
			'sa_points' => array(
				'Management packs can be produced automatically from accounting and operational systems, cutting the month-end consolidation work that South African finance teams know well.',
				'Reports and dashboards can be designed around local management questions — sales per store or region, cash flow, stock turns and debtors — so leaders see the business as it stands.',
				'Delivery is best phased: automate the one report that takes the most time first, prove the value, then extend to a full reporting pack.',
				'Automated reporting works on top of the accounting and operations systems already in use, so it rarely requires a system replacement.',
			),
			'decision_title' => 'Should you automate your reporting?',
			'decision' => array(
				'Management reports take more than a few hours to assemble every reporting cycle.',
				'Figures have to be rechecked because different sources disagree.',
				'Decisions wait on reports that are already out of date.',
				'Building a new report means a manual rebuild each time.',
				'You want leadership to see current numbers instead of last month\'s story.',
			),
			'decision_note' => 'If any of these sound familiar, run the Softkom assessment to identify which reports give you the fastest return on automation.',
			'proof' => array(
				'Replace Excel with custom software' => home_url( '/replace-excel-with-custom-software-south-africa/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
				'Business system integration in South Africa' => home_url( '/business-system-integration-south-africa/' ),
			),
			'faq' => array(
				array( 'Can Excel reporting be automated?', 'Yes. The data behind Excel reports can be generated automatically from source systems, or the source systems can be connected so reports assemble themselves. Excel remains useful for analysis; automation removes the manual gathering.' ),
				array( 'What reports should we automate first?', 'Start with the report that takes the most hands-on time or causes the most disagreement each cycle — usually sales, stock or finance. A focused first report proves the value quickly.' ),
				array( 'Do we need to build a custom reporting system?', 'Not necessarily. Many reporting problems are solved by integrating the data sources and automating the consolidation. A custom reporting layer is reserved for advanced, interactive or role-specific reporting needs.' ),
				array( 'How do we make sure reports use the same numbers as our accounting system?', 'Integration pulls the same records from the source system into the report, so accounting and reporting share one set of facts instead of separate copies.' ),
				array( 'How long does automating management reporting take?', 'It depends on the number of data sources and the depth of the report. A focused first report can often be delivered quickly; larger reporting packs are scoped individually.' ),
			),
		),

		'automate-approvals-workflows-south-africa' => array(
			'title'    => 'Automate Approvals Workflows South Africa',
			'eyebrow'  => 'APPROVAL WORKFLOW AUTOMATION · SOUTH AFRICA',
			'headline' => 'Stop approvals stuck in email and WhatsApp. Automate the workflow and track every step',
			'intro'    => 'Quotes, purchases, documents and internal requests that wait in email and WhatsApp cost time, hide progress and leave no audit trail. Softkom automates approval workflows so requests route through the right people in the right order, with automatic reminders and a complete record of who approved what and when.',
			'problem_block_title'   => 'Signs approvals are stuck in email and WhatsApp',
			'problems' => array(
				'Quotes, purchases and documents wait for days because approval lives in inboxes and chat.',
				'Nobody can see where an approval is or who is holding it up.',
				'Approvals happen verbally or in chat, leaving no reliable record.',
				'The same approvals are chased manually, week after week.',
				'People spend time asking what stage a request has reached instead of working.',
			),
			'why_replace_title' => 'What slow approvals cost the business',
			'why_replace' => array(
				array(
					'head'  => 'Delayed revenue and purchases',
					'body'  => 'Quotes and orders wait on approvals while customers wait on a decision. A stalled approval is a stalled transaction.',
				),
				array(
					'head'  => 'Management time spent chasing',
					'body'  => 'Leaders become the traffic system — nudging, forwarding and asking for status instead of making the decisions that matter.',
				),
				array(
					'head'  => 'No audit trail',
					'body'  => 'Without a record of who approved what, disputes, audits and rework are harder to resolve, and accountability is unclear.',
				),
			),
			'options_title' => 'What approval workflows can be automated',
			'options_intro' => 'Approval automation turns a list of steps into a guided workflow with automatic routing, reminders and a record of every action.',
			'options' => array(
				array(
					'head' => 'Automate routing and notifications',
					'body'  => 'Define the approval sequence once so each request moves to the next person automatically, with reminders when an action is due.',
				),
				array(
					'head' => 'Integrate with email, WhatsApp and your tools',
					'body'  => 'Approvals can be reviewed and approved from the channels people already use, while the workflow and its history stay in one system.',
				),
				array(
					'head' => 'Build a workflow system when approvals are core',
					'body'  => 'When multi-step, multi-role approvals are central to operations, a simple workflow system gives control, visibility and history.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, or Build it',
			'framework_intro' => 'Approval automation is a perfect candidate for the Automate, Integrate, Build framework — most approvals are solved without a large build.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Automate the routing, reminders and status so requests move through the approval sequence without manual chasing.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Connect the workflow to email, WhatsApp and your existing systems so people stay in tools they already use.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Build a tailored approval or workflow system when approval sequences are complex, compliance-driven or core to how you operate.',
				),
			),
			'sa_title' => 'Approval automation in the South African context',
			'sa_points' => array(
				'In many South African businesses, approvals happen over WhatsApp. Automation can turn those chats into tracked, auditable workflow steps without changing how people like to work.',
				'Workflows can be designed for mobile-first use, which matters for owners and managers who are often away from a desk or on the road.',
				'Approval trails give South African SMEs a reliable record for financial and compliance purposes without manual logbooks.',
				'Common first targets are purchase approvals, quote sign-off, leave requests and document sign-off — processes that already follow a clear sequence.',
			),
			'decision_title' => 'Is your approval process costing you time?',
			'decision' => array(
				'Quotes, orders or purchases regularly wait on approvals for days.',
				'People ask around to find out where an approval has got to.',
				'Approvals leave no reliable record of who approved what.',
				'The process depends on someone remembering to act.',
				'Management time is spent chasing approvals rather than deciding.',
			),
			'decision_note' => 'If you tick any of these, run the Softkom assessment to map which approval flows will pay back automation fastest.',
			'proof' => array(
				'Business system integration in South Africa' => home_url( '/business-system-integration-south-africa/' ),
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
			),
			'faq' => array(
				array( 'How do I automate approval workflows?', 'Map each approval sequence — who acts, in what order, with what deadline — then route it through a workflow that moves steps automatically and records every action. The Softkom assessment helps you map these first.' ),
				array( 'Can approvals happen over WhatsApp?', 'Yes. Workflows can notify approvers and collect approvals from WhatsApp and email while the history, order and status stay controlled in one system of record.' ),
				array( 'Do automated approvals replace email?', 'Not necessarily. Email can remain the channel; what changes is that requests are automatically routed, tracked and reminded instead of sitting in an inbox.' ),
				array( 'How do we keep an audit trail of approvals?', 'An automated workflow records who approved, declined or forwarded each request and when, so the history is available whenever it is needed.' ),
				array( 'Which approvals should we automate first?', 'Start with the sequence that causes the most delay or the least accountability — typically purchase approvals or quote sign-off. A focused first workflow proves the value quickly.' ),
			),
		),

		'connect-business-software-south-africa' => array(
			'title'    => 'Connect Business Software South Africa',
			'eyebrow'  => 'SYSTEM CONNECTIVITY · SOUTH AFRICA',
			'headline' => 'Connect your business software: CRM, accounting, operations and ecommerce working as one',
			'intro'    => 'CRM, accounting, operations, ecommerce and other systems that do not communicate leave staff doing the work between them by hand. Softkom connects business software through APIs and workflow integration so data flows automatically between the tools you already use — without forcing you to rebuild or replace them.',
			'problem_block_title'   => 'Signs your software is not talking to each other',
			'problems' => array(
				'Salespeople update the CRM while finance re-enter the same data into accounting.',
				'Orders from ecommerce are retyped into operations and then into invoicing.',
				'No single system gives a complete, current view of a customer or an order.',
				'Integrations that exist break silently and take time to notice.',
				'Stock, invoices and lead statuses disagree between systems.',
			),
			'why_replace_title' => 'What disconnected software costs the business',
			'why_replace' => array(
				array(
					'head'  => 'Time spent re-entering instead of serving',
					'body'  => 'Every hand-off between systems consumes staff time and slows the business, and the cost grows with every added system.',
				),
				array(
					'head'  => 'Errors at every manual hand-off',
					'body'  => 'Each retype is a chance for wrong data to enter accounting, stock or customer records, where it is expensive to find later.',
				),
				array(
					'head'  => 'Reporting that is never quite right',
					'body'  => 'When systems disagree, every report requires explanation and judgement, and decisions are made on whichever copy is trusted.',
				),
			),
			'options_title' => 'What connecting your software looks like',
			'options_intro' => 'Connectivity is delivered with the tools you already run — through APIs, workflow connectivity and lightweight sync layers — not by starting over.',
			'options' => array(
				array(
					'head' => 'API integration',
					'body'  => 'Connect systems through their official interfaces so transactions, customers and stock flow between them automatically.',
				),
				array(
					'head' => 'Workflow connectivity',
					'body'  => 'Use middleware and workflow tools to move data and trigger actions between systems that do not connect directly.',
				),
				array(
					'head' => 'A system of record when no API exists',
					'body'  => 'Where a system has no API, a lightweight layer can synchronise and control the data so the business gets one version of the truth.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, then Build it',
			'framework_intro' => 'Connecting software is the middle step of the Softkom framework — and it is often the right answer before any replacement is considered.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Automate repetitive steps in the tools you already have, starting with the manual connections between systems.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Connect CRM, accounting, operations and ecommerce through APIs and workflow connectivity so data moves automatically.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Only build custom software when a process needs control, scale or differentiation that integration cannot provide.',
				),
			),
			'sa_title' => 'Software connectivity in the South African context',
			'sa_points' => array(
				'Many South African SMEs run a practical mix of local and SaaS tools, and connectivity removes the constant copy-paste between them.',
				'Reliable integration matters in a market where connectivity and uptime are not always guaranteed — errors must be handled and retried cleanly.',
				'WhatsApp is often the front door for customer communication; connecting it to CRM and operations creates a complete customer record.',
				'Integrations are built to be resilient and visible so they do not break silently, which is a common pain with point-to-point scripts.',
			),
			'decision_title' => 'Should you connect or replace your software?',
			'decision' => array(
				'The systems themselves do the job — they just do not share data.',
				'Manual re-entry between systems is the bottleneck, not the tools.',
				'You want one accurate, current view without replacing core software.',
				'You need new capabilities for existing systems rather than a rebuild.',
			),
			'decision_note' => 'If your tools are good but disconnected, integration is very likely the right first move. Use the Softkom assessment to confirm where connecting your systems creates the most value.',
			'proof' => array(
				'Business system integration in South Africa' => home_url( '/business-system-integration-south-africa/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
				'Replace Excel with custom software' => home_url( '/replace-excel-with-custom-software-south-africa/' ),
			),
			'faq' => array(
				array( 'Can my CRM connect to my accounting system?', 'In most cases, yes. CRM and accounting packages expose APIs and workflow interfaces that Softkom can use to keep customers, orders and invoices in sync automatically.' ),
				array( 'What software can be connected with APIs in South Africa?', 'Common targets are CRM, accounting, ecommerce platforms, payment providers, stock and inventory tools, and communication channels such as WhatsApp and email.' ),
				array( 'What if one of my systems has no API?', 'Softkom assesses the system and can use import/export workflows, middleware or a lightweight sync layer so the business still gets one version of the truth.' ),
				array( 'How much does connecting business software cost?', 'It depends on the number and complexity of systems involved. A focused integration between two tools is scoped individually and is usually far cheaper than replacing them.' ),
				array( 'Do we need custom software just to connect our tools?', 'Usually not. Integration connects the tools you already have. Custom software is reserved for processes that cannot be handled by connecting what exists.' ),
			),
		),

		'automate-lead-follow-up-south-africa' => array(
			'title'    => 'Automate Lead Follow Up South Africa',
			'eyebrow'  => 'SALES FOLLOW-UP AUTOMATION · SOUTH AFRICA',
			'headline' => 'Automate lead follow-up so no enquiry is forgotten and no revenue is lost',
			'intro'    => 'Enquiries arrive, but salespeople forget or delay the follow-up — and revenue is quietly lost to competitors who responded first. Softkom automates lead follow-up so every enquiry is acknowledged immediately, routed to the right person, and scheduled with reminders until the conversation moves forward.',
			'problem_block_title'   => 'Signs your lead follow-up is costing you sales',
			'problems' => array(
				'Enquiries sit for days before anyone responds.',
				'Leads are forgotten because follow-up depends on memory.',
				'The conversation cools because the first response arrives too late.',
				'No system enforces a next action or a deadline for each lead.',
				'Sales reporting relies on what salespeople remember, not what happened.',
			),
			'why_replace_title' => 'What slow follow-up costs the business',
			'why_replace' => array(
				array(
					'head'  => 'Lost enquiries become lost revenue',
					'body'  => 'An enquiry that is not followed up is revenue given to a more responsive competitor. The cost is every enquiry that never converts.',
				),
				array(
					'head'  => 'Inconsistent responsiveness',
					'body'  => 'Without a system, response quality depends on who is on duty and how busy they are, rather than on a standard the business sets.',
				),
				array(
					'head'  => 'No measurable process to improve',
					'body'  => 'If follow-up is not tracked, the business cannot see where leads stall or what changed when it improves.',
				),
			),
			'options_title' => 'How lead follow-up automation works',
			'options_intro' => 'Lead follow-up automation makes response fast, consistent and trackable — without replacing the personal conversation that closes sales.',
			'options' => array(
				array(
					'head' => 'Automate the first response',
					'body'  => 'Acknowledge every enquiry immediately with a fast, relevant reply so the lead knows they have been heard, even outside business hours.',
				),
				array(
					'head' => 'Route and schedule follow-ups',
					'body'  => 'Assign each lead to the right person and generate the next action and reminder, so nothing slips because someone forgot.',
				),
				array(
					'head' => 'Integrate with your channels',
					'body'  => 'Bring WhatsApp, web forms, email and calls into one queue so every enquiry is captured, tracked and followed up from one place.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, or Build it',
			'framework_intro' => 'Follow-up automation usually follows the cheapest effective path: automate the routine, integrate the channels, and only build when the sales process is unique.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Automate the immediate response, the assignment and the reminders so every lead gets consistent, timely attention.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Connect WhatsApp, email, web forms and your CRM so enquiries flow into one tracked funnel instead of scattered inboxes.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Build a tailored lead-management layer when the sales process, hand-offs or reporting need more control than standard tools provide.',
				),
			),
			'sa_title' => 'Lead follow-up automation in the South African context',
			'sa_points' => array(
				'WhatsApp is one of the most common first-contact channels for South African businesses; automating capture and follow-up there keeps enquiries from sitting unread.',
				'Automation suits the South African SME reality of lean teams — responses happen consistently even when the salesperson is on the road or after hours.',
				'Simple CRM-led follow-up often delivers the majority of the value without custom development, which matches the Softkom Automate-first approach.',
				'Phased delivery: fix the first-response delay first, then build out scheduled, persona-based follow-up sequences.',
			),
			'decision_title' => 'Is your business losing leads to slow follow-up?',
			'decision' => array(
				'Enquiries regularly wait hours or days for a first response.',
				'Your team has no deadline for contacting a new lead.',
				'Follow-up depends on individual memory rather than a system.',
				'You cannot say reliably how many leads went quiet and why.',
				'Other businesses in your market respond to customers faster.',
			),
			'decision_note' => 'If any of these ring true, run the Softkom assessment to see which follow-up steps to automate and integrate first for the fastest gain.',
			'proof' => array(
				'Sales & lead automation in South Africa' => home_url( '/sales-lead-generation-automation-south-africa/' ),
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
			),
			'faq' => array(
				array( 'How can I automate sales lead follow-up?', 'Automate the immediate acknowledgement, assign each lead to the right person, and schedule reminders and next actions so every enquiry is handled consistently. The Softkom assessment shows which tools and flows to connect first.' ),
				array( 'Can WhatsApp leads be followed up automatically?', 'Yes. WhatsApp enquiries can be captured into a tracked flow, acknowledged automatically, and assigned with reminders so none sit unread.' ),
				array( 'Do I need a CRM to automate follow-up?', 'A simple CRM or workflow layer is the usual foundation because it holds the sequence and history. Softkom can configure existing tools before building anything custom.' ),
				array( 'How fast should a lead be contacted?', 'The sooner the better — enquiries are most responsive when acknowledged immediately. Automation makes a fast, consistent first response possible for every lead, not just the ones someone remembers.' ),
				array( 'Will automated follow-up replace my salespeople?', 'No. Automation handles the routine capture, response and reminders. Salespeople still build the relationship and close the sale — and they get complete, timely information to do it with.' ),
			),
		),

		'operations-management-system-south-africa' => array(
			'title'    => 'Operations Management System South Africa',
			'eyebrow'  => 'OPERATIONS SOFTWARE · SOUTH AFRICA',
			'headline' => 'One system of record for your operations — instead of spreadsheets, WhatsApp and disconnected software',
			'intro'    => 'When operations are spread across spreadsheets, WhatsApp, email and disconnected software, nobody has a single view of the business. Softkom designs operations management systems for South African businesses that need one reliable way to run and see their jobs, stock, orders and teams — and it decides honestly whether the answer is automation, integration, configuration or a custom build.',
			'problem_block_title'   => 'Signs your operations have outgrown ad hoc tools',
			'problems' => array(
				'Operations run across spreadsheets, WhatsApp, email and disconnected software.',
				'No single screen shows the current state of jobs, stock, orders or teams.',
				'Management sees what happened last week, not what is happening now.',
				'Status updates depend on people remembering to send them.',
				'Onboarding a new team member means explaining dozens of workarounds.',
				'Information lives with the person, not in the business.',
			),
			'why_replace_title' => 'What a scattered operation costs the business',
			'why_replace' => array(
				array(
					'head'  => 'Decisions made late or on stale information',
					'body'  => 'Without a current operational view, leaders react to last week\'s problems instead of today\'s, and small issues grow while nobody sees them.',
				),
				array(
					'head'  => 'Effort duplicated across channels',
					'body'  => 'The same status is reported by hand in WhatsApp groups, emails, calls and spreadsheets — work that a single system of record removes.',
				),
				array(
					'head'  => 'Workarounds held in one person\'s head',
					'body'  => 'When critical knowledge is informal, the business is fragile. A system captures how work actually runs and makes it repeatable.',
				),
			),
			'options_title' => 'What an operations management system can cover',
			'options_intro' => 'An operations system gives the business one trackable place to run core work — from intake to delivery — with status and history visible to everyone who needs it.',
			'options' => array(
				array(
					'head' => 'Jobs, orders and delivery',
					'body'  => 'Track work from enquiry and intake through production, dispatch and delivery, with status visible at every step.',
				),
				array(
					'head' => 'Stock, suppliers and purchasing',
					'body'  => 'Know what is on hand, what is on order and what is committed, so purchasing reacts to demand rather than guesswork.',
				),
				array(
					'head' => 'Teams, tasks and hand-offs',
					'body'  => 'Assign work, control hand-offs and see what each person has on, with an audit trail of what happened and when.',
				),
				array(
					'head' => 'Reporting you can rely on',
					'body'  => 'Get current, consistent operational reporting from one source instead of consolidating many files by hand.',
				),
			),
			'framework_title' => 'Automate it, Integrate it, or Build it',
			'framework_intro' => 'Softkom does not automatically recommend custom software. It determines whether the best answer is automation, integration, configuration of existing tools, or a custom operations system.',
			'framework' => array(
				array(
					'step' => '1. Automate it',
					'body' => 'Automate the repetitive capture, tracking and notifications that currently ride on spreadsheets and chat.',
				),
				array(
					'step' => '2. Integrate it',
					'body' => 'Connect the accounting, CRM, ecommerce and operations tools you already run so they feed one operational view.',
				),
				array(
					'step' => '3. Build it',
					'body' => 'Build a custom operations system when the core process needs control, scale or differentiation that existing tools cannot provide.',
				),
			),
			'sa_title' => 'Operations systems in the South African context',
			'sa_points' => array(
				'South African SMEs often run growth on spreadsheets and WhatsApp; a structured system captures that work without stopping the business.',
				'Systems are designed for real local conditions — load-shedding, intermittent connectivity and remote or mobile teams working from the road.',
				'A phased rollout delivers best: put the highest-friction operation on the system first, prove the value, then extend.',
				'The decision is made honestly — configuration of existing tools or integration can be the right answer, not automatically a custom build.',
			),
			'decision_title' => 'Does your operation need an operations management system?',
			'decision' => array(
				'You cannot see the current state of core work from one place.',
				'Growth is creating more administration spread across more channels.',
				'Onboarding and cross-training depend on people, not a system.',
				'Operations data is scattered across spreadsheets, email and chat.',
				'Management needs a reliable, current operational view.',
			),
			'decision_note' => 'If you recognise these, start with the Softkom assessment to see whether the right answer is automation, integration, configuring existing tools, or a custom operations system.',
			'proof' => array(
				'Custom business systems in South Africa' => home_url( '/custom-business-systems-south-africa/' ),
				'Custom software vs spreadsheets' => home_url( '/custom-software-vs-spreadsheets/' ),
				'Business process automation in South Africa' => home_url( '/business-process-automation-south-africa/' ),
			),
			'faq' => array(
				array( 'When does a business need an operations management system?', 'When core operations run across spreadsheets, WhatsApp, email and disconnected software, and you cannot see the current state of the business from one place, a structured operations system changes how the business runs and scales.' ),
				array( 'What are the signs a business has outgrown spreadsheets and WhatsApp?', 'Multiple versions of the same data, status that depends on people reporting, workarounds held by one person, and management seeing last week instead of now are all signs it is time for a system.' ),
				array( 'Should we buy off-the-shelf or build a custom operations system?', 'Softkom decides honestly: if existing tools and configuration can do the job, that is the answer. A custom system is for processes that need control, scale or differentiation that standard tools cannot provide.' ),
				array( 'Can an operations system work for a South African SME?', 'Yes, when it is phased and designed around local realities — load-shedding, mobile and remote teams — so the business keeps running while it transitions from spreadsheets and chat.' ),
				array( 'How do we move our operations from spreadsheets to a system?', 'With a phased, data-first approach: map the current process, clean and migrate the data, put the highest-friction operation on the system first, then extend to the rest of the business.' ),
			),
		),
	);
}

/**
 * Build full HTML for a traffic-sprint page from its definition.
 */
function softkom_traffic_sprint_html( $p ) {
	$assessment = esc_url( home_url( '/assessment/' ) );

	$problems = '';
	foreach ( $p['problems'] as $x ) {
		$problems .= '<li><span>✓</span>' . esc_html( $x ) . '</li>';
	}

	$why = '';
	if ( ! empty( $p['why_replace'] ) ) {
		foreach ( $p['why_replace'] as $block ) {
			$why .= '<div class="sks-card"><h3>' . esc_html( $block['head'] ) . '</h3><p>' . esc_html( $block['body'] ) . '</p></div>';
		}
	}

	$options = '';
	if ( ! empty( $p['options'] ) ) {
		foreach ( $p['options'] as $opt ) {
			$options .= '<div class="sks-opt"><h3>' . esc_html( $opt['head'] ) . '</h3><p>' . esc_html( $opt['body'] ) . '</p></div>';
		}
	}

	$framework = '';
	foreach ( $p['framework'] as $step ) {
		$framework .= '<div class="sks-step"><span>' . esc_html( $step['step'] ) . '</span><p>' . esc_html( $step['body'] ) . '</p></div>';
	}

	$what = '';
	if ( ! empty( $p['what_points'] ) ) {
		foreach ( $p['what_points'] as $w ) {
			$what .= '<div class="sks-card"><h3>' . esc_html( $w['head'] ) . '</h3><p>' . esc_html( $w['body'] ) . '</p></div>';
		}
	}

	$sa = '';
	if ( ! empty( $p['sa_points'] ) ) {
		foreach ( $p['sa_points'] as $x ) {
			$sa .= '<li>' . esc_html( $x ) . '</li>';
		}
	}

	$decision = '';
	foreach ( $p['decision'] as $x ) {
		$decision .= '<li><span>✓</span>' . esc_html( $x ) . '</li>';
	}

	$faq = '';
	foreach ( $p['faq'] as $x ) {
		$faq .= '<details><summary>' . esc_html( $x[0] ) . '<span>+</span></summary><p>' . esc_html( $x[1] ) . '</p></details>';
	}

	$proof = '';
	foreach ( $p['proof'] as $label => $url ) {
		$proof .= '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '<span>→</span></a>';
	}

	$html  = '<div class="sks-page">';

	$html .= '<section class="sks-hero"><div class="sks-inner"><div class="sks-pill">' . esc_html( $p['eyebrow'] ) . '</div><h1>' . esc_html( $p['headline'] ) . '</h1><p class="sks-lead">' . esc_html( $p['intro'] ) . '</p><div class="sks-actions"><a class="sks-primary" href="' . $assessment . '">Start My Free Assessment →</a><a class="sks-secondary" href="' . esc_url( home_url( '/contact/' ) ) . '">Book a Strategy Call</a></div><div class="sks-proof"><span>✓ South African business focus</span><span>✓ Practical recommendations</span><span>✓ No-obligation assessment</span></div></div></section>';

	$html .= '<section class="sks-main"><div class="sks-inner"><p class="sks-label">THE PROBLEM</p><h2>' . esc_html( $p['problem_block_title'] ) . '</h2><ul class="sks-problems">' . $problems . '</ul></div></section>';

	if ( ! empty( $p['why_replace'] ) ) {
		$html .= '<section class="sks-white"><div class="sks-inner"><p class="sks-label">WHY IT MATTERS</p><h2>' . esc_html( $p['why_replace_title'] ) . '</h2><div class="sks-grid">' . $why . '</div></div></section>';
	}

	if ( ! empty( $p['options'] ) ) {
		$html .= '<section class="sks-main"><div class="sks-inner"><p class="sks-label">THE OPTIONS</p><h2>' . esc_html( $p['options_title'] ) . '</h2><p class="sks-sub">' . esc_html( $p['options_intro'] ) . '</p><div class="sks-grid">' . $options . '</div></div></section>';
	}

	if ( ! empty( $p['what_points'] ) ) {
		$html .= '<section class="sks-white"><div class="sks-inner"><p class="sks-label">WHERE TO START</p><h2>' . esc_html( $p['what_title'] ) . '</h2><div class="sks-grid">' . $what . '</div></div></section>';
	}

	$html .= '<section class="sks-band"><div class="sks-inner"><div><p>FREE BUSINESS SYSTEMS AND AUTOMATION ASSESSMENT</p><h2>' . esc_html( $p['decision_title'] ) . '</h2><span>' . esc_html( $p['decision_note'] ) . '</span></div><a href="' . $assessment . '">Get My Readiness Score →</a></div></section>';

	if ( ! empty( $p['sa_points'] ) ) {
		$html .= '<section class="sks-main"><div class="sks-inner"><p class="sks-label">SOUTH AFRICAN CONTEXT</p><h2>' . esc_html( $p['sa_title'] ) . '</h2><ul class="sks-sa">' . $sa . '</ul></div></section>';
	}

	$html .= '<section class="sks-white"><div class="sks-inner"><p class="sks-label">THE SOFTKOM FRAMEWORK</p><h2>' . esc_html( $p['framework_title'] ) . '</h2><p class="sks-sub">' . esc_html( $p['framework_intro'] ) . '</p><div class="sks-steps">' . $framework . '</div></div></section>';

	$html .= '<section class="sks-main"><div class="sks-inner"><p class="sks-label">DECISION GUIDE</p><h2>How to know the framework fits your business</h2><ul class="sks-problems">' . $decision . '</ul></div></section>';

	$html .= '<section class="sks-white"><div class="sks-inner"><p class="sks-label">FREQUENTLY ASKED QUESTIONS</p><h2>What businesses need to know</h2><div class="sks-faq">' . $faq . '</div></div></section>';

	if ( ! empty( $p['proof'] ) ) {
		$html .= '<section class="sks-main"><div class="sks-inner"><p class="sks-label">EXPLORE MORE</p><h2>Related Softkom solutions</h2><nav class="sks-proof">' . $proof . '</nav></div></section>';
	}

	$html .= '<section class="sks-final"><div class="sks-inner"><p>READY TO FIND THE RIGHT PATH?</p><h2>Start with clarity, not another software subscription.</h2><a class="sks-primary" href="' . $assessment . '">Start My Free Assessment →</a></div></section>';

	$html .= '</div>';
	return $html;
}

/**
 * Sync the four traffic pages.
 */
function softkom_traffic_sprint_sync() {
	$version = '2.0.0';
	if ( get_option( 'softkom_traffic_sprint_version' ) === $version ) {
		return;
	}
	foreach ( softkom_traffic_sprint_pages() as $slug => $p ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		$post = array(
			'post_title'   => $p['title'],
			'post_name'    => $slug,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => softkom_traffic_sprint_html( $p ),
		);
		if ( $page ) {
			$post['ID'] = $page->ID;
			wp_update_post( wp_slash( $post ) );
		} else {
			wp_insert_post( wp_slash( $post ) );
		}
	}
	update_option( 'softkom_traffic_sprint_version', $version, false );
}
add_action( 'init', 'softkom_traffic_sprint_sync', 25 );

/**
 * Is the current request one of the four traffic-sprint pages?
 */
function softkom_traffic_sprint_is_page() {
	return is_page() && array_key_exists( get_post_field( 'post_name', get_queried_object_id() ), softkom_traffic_sprint_pages() );
}

/**
 * Body class.
 */
add_filter( 'body_class', function ( $c ) {
	if ( softkom_traffic_sprint_is_page() ) {
		$c[] = 'softkom-traffic-sprint';
	}
	return $c;
} );

/**
 * Indexable, followable.
 */
add_filter( 'wp_robots', function ( $r ) {
	if ( softkom_traffic_sprint_is_page() ) {
		unset( $r['noindex'], $r['nofollow'] );
		$r['index']                = true;
		$r['follow']               = true;
		$r['max-image-preview']    = 'large';
		$r['max-snippet']          = -1;
		$r['max-video-preview']    = -1;
	}
	return $r;
}, 100 );

/**
 * Canonical + Service + FAQ structured data + document title.
 */
add_action( 'wp_head', function () {
	if ( ! softkom_traffic_sprint_is_page() ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$p    = softkom_traffic_sprint_pages()[ $slug ];

	echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";

	$faq = array();
	foreach ( $p['faq'] as $x ) {
		$faq[] = array(
			'@type'          => 'Question',
			'name'           => $x[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $x[1] ),
		);
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type' => 'Service',
				'name'  => $p['title'],
				'provider' => array( '@type' => 'Organization', 'name' => 'Softkom Solutions', 'url' => home_url( '/' ) ),
				'areaServed' => array( '@type' => 'Country', 'name' => 'South Africa' ),
				'url' => get_permalink(),
			),
			array(
				'@type'      => 'Organization',
				'@id'        => home_url( '/#organization' ),
				'name'       => 'Softkom Solutions',
				'url'        => home_url( '/' ),
				'address'    => array( '@type' => 'PostalAddress', 'addressLocality' => 'Johannesburg', 'addressCountry' => 'ZA' ),
				'areaServed' => array( '@type' => 'Country', 'name' => 'South Africa' ),
			),
			array( '@type' => 'FAQPage', 'mainEntity' => $faq ),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 5 );

/**
 * Meta description.
 */
add_action( 'wp_head', function () {
	if ( ! softkom_traffic_sprint_is_page() ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$p    = softkom_traffic_sprint_pages()[ $slug ];
	echo '<meta name="description" content="' . esc_attr( mb_substr( wp_strip_all_tags( $p['intro'] ), 0, 158 ) ) . '">' . "\n";
}, 4 );

/**
 * Enqueue design-system CSS for these pages.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! softkom_traffic_sprint_is_page() ) {
		return;
	}
	wp_enqueue_style( 'softkom-traffic-sprint-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', array(), null );
	wp_register_style( 'softkom-traffic-sprint', false, array( 'softkom-traffic-sprint-font' ), '1.0.0' );
	wp_enqueue_style( 'softkom-traffic-sprint' );
	wp_add_inline_style( 'softkom-traffic-sprint',
		'body.softkom-traffic-sprint{background:#fff}.softkom-traffic-sprint .entry-header,.softkom-traffic-sprint .page-header,.softkom-traffic-sprint .entry-title{display:none!important}.softkom-traffic-sprint .site-content,.softkom-traffic-sprint #content,.softkom-traffic-sprint .content-area,.softkom-traffic-sprint .site-main,.softkom-traffic-sprint article,.softkom-traffic-sprint .entry-content{width:100%!important;max-width:none!important;margin:0!important;padding:0!important}.softkom-traffic-sprint #colophon,.softkom-traffic-sprint footer.site-footer,.softkom-traffic-sprint .site-footer,.softkom-traffic-sprint .ast-footer-overlay,.softkom-traffic-sprint .site-below-footer-wrap,.softkom-traffic-sprint .site-primary-footer-wrap,.softkom-traffic-sprint .site-above-footer-wrap{display:none!important}.sks-page{font-family:Inter,system-ui,sans-serif;color:#1e293b}.sks-inner{width:min(1180px,calc(100% - 48px));margin:auto}.sks-hero{padding:105px 0 80px;background:radial-gradient(circle at 85% 12%,#dbeafe 0,transparent 30%),linear-gradient(180deg,#f8fafc,#fff);border-bottom:1px solid #e2e8f0}.sks-pill{display:inline-flex;padding:8px 12px;border:1px solid #bfdbfe;background:#eff6ff;border-radius:999px;color:#2563eb;font-size:12px;font-weight:800;letter-spacing:.08em}.sks-page h1{max-width:960px;margin:22px 0 24px;color:#0f172a;font-size:clamp(40px,5.4vw,68px);line-height:1.03;letter-spacing:-.045em}.sks-lead{max-width:880px;font-size:19px;line-height:1.7;color:#475569}.sks-actions{display:flex;gap:12px;flex-wrap:wrap;margin:34px 0 28px}.sks-primary,.sks-secondary,.sks-band a{display:inline-flex;align-items:center;gap:12px;padding:15px 21px;border-radius:10px;text-decoration:none!important;font-weight:700}.sks-primary{background:#0f172a;color:#fff!important}.sks-primary:hover{transform:translateY(-2px);background:#1e293b}.sks-secondary{border:1px solid #cbd5e1;background:#fff;color:#0f172a!important}.sks-proof{display:flex;gap:14px;flex-wrap:wrap;color:#64748b;font-size:14px;font-weight:600}.sks-main,.sks-white{padding:90px 0}.sks-white{background:#fff}.sks-main p.sks-label,.sks-white p.sks-label{color:#2563eb;font-size:12px;font-weight:800;letter-spacing:.09em;margin:0 0 12px}.sks-main h2,.sks-white h2{max-width:760px;margin:0 0 16px;color:#0f172a;font-size:clamp(28px,4vw,42px);line-height:1.1;letter-spacing:-.03em}.sks-sub{max-width:820px;color:#475569;font-size:17px;line-height:1.65}.sks-problems{padding:0;margin:22px 0 0;list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}.sks-problems li{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;color:#334155;font-size:16px;line-height:1.55}.sks-problems li span{color:#2563eb;margin-right:8px;font-weight:800}.sks-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-top:24px}.sks-card,.sks-opt{background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px}.sks-card h3,.sks-opt h3{color:#0f172a;margin:0 0 8px;font-size:17px}.sks-card p,.sks-opt p{color:#475569;line-height:1.6;margin:0}.sks-steps{display:grid;gap:14px;margin-top:24px}.sks-step{display:flex;gap:16px;align-items:flex-start;background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:18px 20px}.sks-step span{flex:0 0 auto;background:#2563eb;color:#fff;font-weight:800;font-size:13px;border-radius:8px;padding:4px 10px}.sks-step p{color:#334155;line-height:1.6;margin:0}.sks-sa{padding:0;margin:20px 0 0;list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}.sks-sa li{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;color:#334155;line-height:1.55}.sks-band{background:#0b1220;color:#fff;padding:64px 0}.sks-band .sks-inner{display:flex;gap:28px;align-items:center;justify-content:space-between;flex-wrap:wrap}.sks-band p{color:#93c5fd;font-size:12px;font-weight:800;letter-spacing:.09em;margin:0 0 10px}.sks-band h2{color:#fff;margin:0 0 10px;font-size:clamp(26px,3.6vw,38px);line-height:1.12}.sks-band span{color:#cbd5e1;max-width:640px;display:block;line-height:1.6}.sks-band a{background:#2563eb;color:#fff!important}.sks-faq{margin-top:24px}.sks-faq details{border:1px solid #e2e8f0;border-radius:12px;margin-bottom:10px;background:#f8fafc}.sks-faq summary{cursor:pointer;padding:16px 18px;font-weight:700;color:#0f172a;display:flex;justify-content:space-between;gap:12px;align-items:center}.sks-faq summary span{color:#2563eb;font-weight:800}.sks-faq details p{padding:0 18px 16px;margin:0;color:#475569;line-height:1.6}.sks-proof a{display:inline-flex;align-items:center;gap:10px;padding:14px 16px;background:#fff;border:1px solid #e2e8f0;border-radius:10px;color:#0f172a!important;text-decoration:none!important;font-weight:700}.sks-proof a span{color:#2563eb}.sks-final{background:radial-gradient(circle at 80% 0%,#dbeafe 0,transparent 30%),#f8fafc;padding:80px 0;text-align:center}.sks-final p{color:#2563eb;font-size:12px;font-weight:800;letter-spacing:.09em;margin:0 0 12px}.sks-final h2{color:#0f172a;font-size:clamp(28px,4vw,42px);line-height:1.12;letter-spacing:-.03em;max-width:760px;margin:0 auto 26px}.sks-final .sks-primary{margin:auto}.sks-compact-footer{background:#0b1220;color:#cbd5e1;padding:28px 0;font-family:Inter,system-ui,sans-serif}.sks-compact-footer .sks-footer-inner{width:min(1180px,calc(100% - 48px));margin:auto;display:flex;gap:24px;align-items:center;justify-content:space-between;flex-wrap:wrap}.sks-compact-footer strong{color:#fff;display:block}.sks-compact-footer nav{display:flex;gap:18px;flex-wrap:wrap}.sks-compact-footer nav a{color:#cbd5e1!important;text-decoration:none!important}.sks-compact-footer small{color:#64748b}@media(max-width:760px){.sks-hero{padding:70px 0}.sks-grid{grid-template-columns:1fr}.sks-problems{grid-template-columns:1fr}.sks-sa{grid-template-columns:1fr}}'
	);
} );

/**
 * Compact footer for these pages (theme footer is hidden).
 */
add_action( 'wp_footer', function () {
	if ( ! softkom_traffic_sprint_is_page() ) {
		return;
	}
	echo '<footer class="sks-compact-footer"><div class="sks-footer-inner"><div><strong>Softkom Solutions</strong><span>Business Systems · AI Automation · System Integration</span></div><nav><a href="' . esc_url( home_url( '/services/' ) ) . '">Solutions</a><a href="' . esc_url( home_url( '/assessment/' ) ) . '">Free Assessment</a><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></nav><small>© ' . esc_html( wp_date( 'Y' ) ) . ' Softkom Solutions</small></div></footer>';
}, 1 );

/**
 * Internal linking: ensure every new page links to its sibling traffic pages.
 */
add_filter( 'the_content', function ( $content ) {
	if ( is_admin() || ! is_main_query() || ! in_the_loop() || ! softkom_traffic_sprint_is_page() ) {
		return $content;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$pages = softkom_traffic_sprint_pages();
	$links = '';
	$count = 0;
	foreach ( $pages as $target => $p ) {
		if ( $target === $slug ) {
			continue;
		}
		$links .= '<a href="' . esc_url( home_url( '/' . $target . '/' ) ) . '">' . esc_html( $p['title'] ) . '<span>→</span></a>';
		if ( ++$count >= 3 ) {
			break;
		}
	}
	return $content . '<section class="sks-main sks-links" aria-label="Related topics"><div class="sks-inner"><p class="sks-label">RELATED TOPICS</p><h2>Continue exploring your options</h2><nav class="sks-proof">' . $links . '</nav></div></section>';
}, 82 );
