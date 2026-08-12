=== WP AI OS ===
Contributors: wp-ai-os
Tags: artificial intelligence, ai, seo, geo, aeo, schema, rag, woocommerce
Requires at least: 6.4
Requires PHP: 8.0
Stable tag: 0.9.0
License: GPLv2 or later

AI Readiness, GEO, AEO, RAG and automation infrastructure for WordPress.

== Description ==
WP AI OS helps WordPress sites become easier for search engines, answer engines and AI systems to understand and use.

Core features:
* AI Readiness scanning
* robots.txt and sitemap checks
* llms.txt publishing
* Schema.org JSON-LD
* AEO content analysis
* AI content assistance
* local WordPress knowledge base
* grounded RAG answers with sources
* safe allowlisted AI agents
* optional scheduled automation
* WooCommerce Product schema
* provider-independent AI configuration
* license management foundation

The base readiness features work without an external AI API.

== Installation ==
1. Upload the wp-ai-os folder to /wp-content/plugins/.
2. Activate WP AI OS from Plugins.
3. Open WP AI OS in the WordPress admin.
4. Run the first AI Readiness scan.
5. Configure an AI provider only when AI generation features are required.

== Security ==
Privileged REST endpoints require WordPress authentication and the manage_options capability. API keys are never returned by the REST settings endpoint.

== Privacy ==
AI requests are sent only when an administrator explicitly invokes an AI-powered feature and has configured an external provider. The readiness scanner itself does not require an external AI provider.

== Changelog ==
= 0.9.0 =
* Commercial foundation and license management.
* Marketplace-ready readme and uninstall cleanup.

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
