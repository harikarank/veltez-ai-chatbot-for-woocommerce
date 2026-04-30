# veltez — Project Memory

**Root:** `D:\Harikaran\Golang\Live\veltez-ai-chatbot-product-recommendations-for-woocommerce`
**Plugin:** veltez - AI Chatbot & Product Recommendations for WooCommerce
**Stack:** PHP 8.0+, WordPress 6.4+, WooCommerce 7.8+, vanilla JS, no build step
**Namespace:** `Veltez`
**Text domain:** `veltez-ai-chatbot-product-recommendations-for-woocommerce`
**Main file:** `veltez-ai-chatbot-product-recommendations-for-woocommerce.php`
**Version:** 1.0.1

## Key Files

| File | Purpose |
|---|---|
| `veltez-ai-chatbot-product-recommendations-for-woocommerce.php` | Entry point, constants, requires, activation hook |
| `includes/class-aiwoo-assistant-plugin.php` | Singleton bootstrap — ALL hook registrations live here |
| `includes/class-aiwoo-assistant-settings.php` | Settings CRUD, sanitization, render_field() |
| `includes/class-aiwoo-assistant-ajax-controller.php` | Chat + enquiry AJAX handlers |
| `includes/class-aiwoo-assistant-chat-service.php` | AI routing (MCP vs legacy), fallback logic |
| `includes/class-aiwoo-assistant-catalog-service.php` | WooCommerce product search via WP_Query |
| `includes/class-aiwoo-assistant-chat-logger.php` | Chat log DB table read/write |
| `includes/class-aiwoo-assistant-ai-error-logger.php` | AI error log DB table read/write |
| `includes/class-aiwoo-assistant-quick-reply-service.php` | Quick reply CRUD, matching engine, admin handlers |
| `includes/class-aiwoo-assistant-ip-blocker.php` | IP blocklist (exact + CIDR, IPv4 + IPv6), admin handlers |
| `includes/class-aiwoo-assistant-admin-menu.php` | All admin page renderers |
| `includes/class-aiwoo-assistant-mcp-tools.php` | Tool definitions + executor for MCP mode |
| `includes/class-aiwoo-assistant-openai-provider.php` | OpenAI Responses API + Chat Completions (tool-calling) |
| `includes/class-aiwoo-assistant-claude-provider.php` | Anthropic Claude Messages API — fully working |
| `includes/class-aiwoo-assistant-gemini-provider.php` | Google Gemini Generative Language API — fully working |
| `includes/class-aiwoo-assistant-provider-interface.php` | Interface: generate_response() + generate_with_tools() |
| `includes/api-handler.php` | make_ai_provider(), call_ai_model() factory helpers |
| `includes/woocommerce-handler.php` | Loads Catalog_Service |
| `admin/chat-history-page.php` | Chat history list template |
| `admin/chat-session-detail-page.php` | Single session view |
| `admin/quick-replies-page.php` | Quick replies list/add/edit |
| `admin/ip-blocklist-page.php` | IP blocklist add/delete UI |
| `admin/top-requests-page.php` | Top requests analytics + save-as-QR forms |
| `admin/ai-errors-page.php` | AI error log table |
| `admin/enquiries-page.php` | Enquiry CPT list |
| `admin/settings-page.php` | Settings page template |
| `templates/chat-widget.php` | Frontend floating chat widget HTML |
| `assets/js/chat.js` | Frontend AJAX, session management, widget UI |
| `assets/js/admin.js` | Admin color picker + media uploader |
| `uninstall.php` | Full cleanup on plugin deletion |

## Constants

`VELTEZ_AI_VERSION`, `VELTEZ_AI_FILE`, `VELTEZ_AI_PATH`, `VELTEZ_AI_URL`

## Conventions

- Every PHP file: `defined( 'ABSPATH' ) || exit;`
- WordPress coding standards (spaces not tabs, snake_case, Yoda conditions)
- No Composer, no npm, no build pipeline
- All admin-post hooks registered in `Plugin::__construct()` via shim methods — NEVER in service __construct()

## Service Instantiation

| Service | Instantiation | Hook registration |
|---|---|---|
| `Settings` | Eager | `admin_init` → register_settings() |
| `IP_Blocker` | Eager | admin_post hooks in Plugin (not __construct) |
| `Chat_Logger` | Lazy | — |
| `AI_Error_Logger` | Lazy | — |
| `Quick_Reply_Service` | Lazy | admin_post hooks in Plugin |
| `Catalog_Service` | Lazy | — |
| `MCP_Tools` | Lazy | — |
| `Chat_Service` | Lazy | — |
| `Ajax_Controller` | Lazy | — |
| `Admin_Menu` | Lazy via admin_menu@1 | register_menus added at priority 10 — safe |

## Admin-Post Actions (all in Plugin::__construct)

| Action | Handler | Service |
|---|---|---|
| `veltez_save_quick_reply` | `handle_admin_post_qr_save` | Quick_Reply_Service::handle_save() |
| `veltez_delete_quick_reply` | `handle_admin_post_qr_delete` | Quick_Reply_Service::handle_delete() |
| `veltez_save_quick_reply_from_ai` | `handle_admin_post_qr_save_from_ai` | Quick_Reply_Service::handle_save_from_ai() |
| `veltez_add_blocked_ip` | `handle_admin_post_ip_add` | IP_Blocker::handle_add() |
| `veltez_delete_blocked_ip` | `handle_admin_post_ip_delete` | IP_Blocker::handle_delete() |

## AJAX Actions

| Action | Visibility | Controller |
|---|---|---|
| `veltez_ai_chat` | public + logged-in | Ajax_Controller::handle_chat() |
| `veltez_ai_enquiry` | public + logged-in | Ajax_Controller::handle_enquiry() |

Security chain: is_enabled → IP block → bot UA → `check_ajax_referer('veltez_ai_nonce')` → rate limit (15/min fixed-window) → message length check (auto-blocks IP) → sanitize

## Database Tables

| Table | Option key | Columns |
|---|---|---|
| `{prefix}veltez_chat_logs` | `veltez_db_version = '1'` | id, session_id, ip_address, customer_name, user_message, ai_response, created_at |
| `{prefix}veltez_quick_replies` | `veltez_qr_db_version = '1'` | id, title, keywords, response, match_type, priority, status, created_at |
| `{prefix}veltez_ai_error_logs` | `veltez_ai_error_log_db_version = '1'` | id, session_id, ip_address, user_message, error_context, error_message, created_at |

All use `$wpdb->get_charset_collate()`. If charset is `utf8` (not `utf8mb4`), emoji cause silent INSERT failures — the `$strip4` closure in each `log()` method handles this.

## Options / Transients

| Key | Type | Notes |
|---|---|---|
| `veltez_ai_settings` | option | Main settings array (group: `veltez_ai`) |
| `veltez_blocked_ips` | option | Array of IP strings, autoload=false, max 500 |
| `veltez_db_version` | option | Chat logs schema version |
| `veltez_qr_db_version` | option | Quick replies schema version |
| `veltez_ai_error_log_db_version` | option | Error log schema version |
| `veltez_qr_seeded` | option | QR defaults seeded flag |
| `veltez_quick_replies_cache` | transient | QR rules cache, TTL 3600s |
| `veltez_rl_{md5(ip\|window)}` | transient | Rate limiter, TTL 90s |

## AI Providers — Current State

All three providers are fully implemented (generate_response + generate_with_tools).

| Provider | Key | Default model | Valid models |
|---|---|---|---|
| OpenAI | `openai_api_key` | `gpt-5.4-mini` | gpt-5.4-mini, gpt-5.4, gpt-4.1-mini |
| Claude | `claude_api_key` | `claude-sonnet-4-6` | claude-opus-4-7, claude-sonnet-4-6, claude-haiku-4-5-20251001 |
| Gemini | `gemini_api_key` | `gemini-2.5-flash` | gemini-2.5-flash, gemini-2.5-pro, gemini-2.5-flash-lite, gemini-2.0-flash-lite, gemini-1.5-pro, gemini-1.5-flash |

**Claude Opus note:** The correct model ID is `claude-opus-4-7` (not `claude-opus-4-6`). Migration alias exists in both Settings::normalize_claude_model() and Claude_Provider::validated_model().

## Quick Reply Cache

1-hour transient (`aiwoo_quick_replies_cache`). Flushed automatically on every insert/update/delete via `flush_cache()`. If rule changes don't appear in chat, delete this transient.

## MCP Mode

Enabled via `enable_mcp = yes` setting. Routes through `Chat_Service::generate_reply_mcp()` which uses `MCP_Tools` to let the AI fetch product data via tool calls instead of injecting the catalog into the prompt. Falls back to catalog search on AI failure.

## WordPress.org Compliance Notes

- **`Requires Plugins: woocommerce`** — added to both `veltez-ai-chatbot-product-recommendations-for-woocommerce.php` and `readme.txt`. WordPress 6.5+ enforces this at activation.
- **Admin notices are scoped** — `maybe_temperature_notice()` only fires on veltez pages. `maybe_warn_if_woocommerce_missing()` only fires on veltez pages or the Plugins list (`screen->id === 'plugins'`). Neither shows site-wide. Both are dismissible.
- **Frontend widget** — no external links, no "Powered by" credits, no attribution. Guideline 10 automated flag was a false positive.
- **Admin bar Settings link** — `admin.php?page=veltez-ai-settings` (fixed stale `ai-woo-assistant` slug after prefix rename).

## wp_enqueue Architecture (post-WP.org review fix)

- **Admin bar CSS** — `Plugin::enqueue_admin_bar_styles()` hooked to both `admin_enqueue_scripts` and `wp_enqueue_scripts`. Uses `wp_add_inline_style('admin-bar', $css)`. No inline `<style>` tag. Only fires when `is_admin_bar_showing() && current_user_can('manage_options')`.
- **Top Requests JS** — Toggle JS injected via `wp_add_inline_script('jquery', $js)` inside `enqueue_admin_assets()` when `$hook === $this->admin_menu->get_top_requests_hook()`. No inline `<script>` in the template.
- `Admin_Menu` exposes `get_top_requests_hook()` alongside `get_settings_hook()` for asset gating.

## Known Design Decisions (do not change without user direction)

- OpenAI model names (`gpt-5.4-mini`, `gpt-5.4`) — non-standard, intentional future-proofing
- `created_at DEFAULT '0000-00-00 00:00:00'` — INSERT always provides explicit value so default never triggers
- `Settings::register_settings_page()` — dead code, never called; menu registered via Admin_Menu
- Rate limiter is **fixed-window** (key rotates every 60s, TTL 90s)
- `REMOTE_ADDR` only for IP detection — no X-Forwarded-For (spoofing prevention)
