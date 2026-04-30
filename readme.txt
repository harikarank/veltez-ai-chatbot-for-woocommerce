=== veltez - AI Chatbot & Product Recommendations for WooCommerce ===

Contributors: veltez
Tags: woocommerce, ai chatbot, product recommendations, ecommerce, customer support
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 8.0
Requires Plugins: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered WooCommerce chatbot that helps customers find products, get instant answers, and increase conversions.

== Description ==

veltez is a powerful WooCommerce AI chatbot and shopping assistant designed to improve customer experience and boost sales.

It allows customers to chat naturally, discover products faster, and receive intelligent recommendations in real time.

The plugin integrates directly with your WooCommerce store and uses AI providers like OpenAI, Claude (Anthropic), and Google Gemini to generate accurate, context-aware responses.

= AI-Powered Shopping Assistant =

* Natural language chat interface
* Smart product recommendations based on user queries
* Context-aware responses using store data
* Multi-provider support: OpenAI, Claude, Gemini

= WooCommerce Integration =

* Real-time product search from your store
* Displays product cards inside chat responses
* Uses actual product data (no hallucinations)
* Supports upsell and cross-sell recommendations

= Two AI Modes =

* Legacy mode: keyword-based product injection into AI prompt
* MCP mode: AI fetches products dynamically via tool-calling (more efficient and accurate)

= Chat Widget =

* Floating chat button with responsive UI
* Mobile-friendly full-screen experience
* Customizable branding (logo, colors, welcome message)
* Built-in enquiry form when no products match

= Quick Replies (No AI Cost) =

* Rule-based responses for FAQs
* Priority-based keyword matching
* Reduces API usage and improves response speed

= Admin Dashboard =

* Chat history with session tracking
* Enquiry management
* IP blocklist (supports CIDR ranges)
* Top customer requests analytics
* AI error logging

= Security & Performance =

* Rate limiting (15 requests/min per IP)
* Bot detection (User-Agent filtering)
* Nonce verification for AJAX
* Input sanitization
* Honeypot protection for enquiry forms

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/veltez-ai-chatbot-product-recommendations-for-woocommerce/`, or install via the WordPress admin panel.
2. Activate the plugin through the "Plugins" menu.
3. Go to **veltez → Settings**.
4. Enter your AI provider API key.
5. Enable the widget and configure settings.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes. WooCommerce is required for product recommendations.

= Which AI providers are supported? =

* OpenAI
* Claude (Anthropic)
* Google Gemini

= Are API keys exposed to users? =

No. API keys are stored securely on the server and never exposed to the frontend.

= Does the plugin store personal data? =

* Chat messages are stored for analytics
* Enquiries are saved in WordPress
* No sensitive payment data is stored

= What happens if AI fails? =

The plugin automatically falls back to:

* Product search results
* Or an enquiry form if no products are found

= Can I customize the chatbot? =

Yes. You can customize:

* Colors
* Branding
* Welcome message
* AI behavior (system prompt)

== Third-Party Services ==

This plugin connects to external AI providers to generate responses. User messages and relevant product context are sent to the selected provider and processed according to their privacy policies.

= OpenAI =

* API endpoint: https://api.openai.com
* Privacy Policy: https://openai.com/privacy

= Anthropic (Claude) =

* API endpoint: https://api.anthropic.com
* Privacy Policy: https://www.anthropic.com/privacy

= Google Gemini =

* API endpoint: https://generativelanguage.googleapis.com
* Privacy Policy: https://policies.google.com/privacy

== Screenshots ==

1. Chat widget on frontend
2. Product recommendations inside chat
3. Admin settings panel
4. Chat history dashboard
5. Quick replies configuration

== Changelog ==

= 1.0.1 =

* Initial release
* Multi-provider AI support (OpenAI, Claude, Gemini)
* MCP tool-calling mode
* WooCommerce product integration
* Admin dashboard with analytics
* Quick Replies engine
* IP blocklist and rate limiting

== Upgrade Notice ==

= 1.0.1 =

Initial release of veltez.

== Links ==

* Documentation: https://ai.veltez.com/documentation/
* Terms of Service: https://ai.veltez.com/terms/
* Privacy Policy: https://ai.veltez.com/privacy/
