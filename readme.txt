=== WP AI OS ===
Contributors: wp-ai-os
Tags: artificial intelligence, ai, seo, geo, aeo, schema, rag, woocommerce
Requires at least: 6.4
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later

AI Readiness, GEO, AEO, RAG, Agents and WooCommerce AI infrastructure for WordPress.

== Description ==
WP AI OS is a WordPress AI optimization platform. It combines technical AI readiness, structured data, answer-focused content analysis, provider-backed AI assistance, a local knowledge base, grounded RAG and safe automation in one plugin.

== Features ==
* AI Readiness scoring and recommendations
* HTTPS, robots.txt, XML sitemap, llms.txt, Schema, REST API, content and AI crawler checks
* Automatic llms.txt publishing
* Organization, WebSite, Article and WebPage JSON-LD
* WooCommerce Product JSON-LD when WooCommerce is active
* AEO content analyzer
* AI content assistant for optimization, FAQs, metadata and outlines
* Local post/page knowledge base
* Grounded RAG answers with source URLs
* Allowlisted AI agent tasks
* Optional scheduled readiness/indexing automation
* OpenAI-compatible provider architecture
* Admin settings and API-key masking
* License-management foundation
* RTL/translation-ready WordPress architecture

The base readiness functionality does not require an external AI provider.

== Installation ==
1. Upload the wp-ai-os folder to /wp-content/plugins/.
2. Activate WP AI OS from Plugins.
3. Open WP AI OS in the WordPress admin.
4. Run the first AI Readiness scan.
5. Configure an AI provider when AI generation or RAG features are required.

== Security ==
Privileged REST endpoints require WordPress authentication and the manage_options capability. API keys are never returned by the REST settings endpoint. AI agents are allowlisted and scheduled automation is disabled unless explicitly enabled through the provided filter.

== Privacy ==
The readiness scanner works locally. AI-powered features send content to the configured external provider only when invoked. Site owners should review the privacy policy of their selected provider.

== Changelog ==
= 1.0.0 =
* Production-oriented commercial release candidate.
* Polished admin dashboard.
* Marketplace packaging workflow.
* Complete feature stack from readiness through RAG and agents.
* WooCommerce structured data.
* License foundation and uninstall cleanup.

= 0.9.0 =
* Commercial foundation and license management.

= 0.8.0 =
* WooCommerce Product structured data.

= 0.7.0 =
* Safe agent task runner and optional scheduler.

= 0.6.0 =
* Local knowledge base and RAG engine.

= 0.5.0 =
* AI content assistant.

= 0.4.0 =
* Schema and AEO foundation.

= 0.3.0 =
* AI provider and llms.txt foundation.

= 0.2.1 =
* Secure readiness REST API and explicit scanning.
