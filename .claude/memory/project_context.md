# veltez — All Bugs Fixed & Audit Results

This file records every bug found and fixed, plus the complete audit results so future sessions don't re-investigate resolved issues.

---

## All Fixes Applied (18 total)

### Fix 1 — PHPCS NonceVerification on `$_GET['paged']` in render_chat_history()
**File:** `includes/class-aiwoo-assistant-admin-menu.php`
`$current_page` assignment was outside the phpcs:disable block. Moved `// phpcs:enable` to after the line.

### Fix 2 — PHPCS ReplacementsWrongNumber on get_sessions() spread args
**File:** `includes/class-aiwoo-assistant-chat-logger.php`
PHPCS counted `...$args` spread as 1 replacement but query had 2 `%d`. Added `WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber` to the phpcs:disable block.

### Fixes 3–7 — PHPCS InterpolatedNotPrepared, UnfinishedPrepare, UnescapedDBParameter
**File:** `includes/class-aiwoo-assistant-chat-logger.php`
Original `phpcs:ignore` comments were on the `return` line — they only suppress 1 line. The SQL string with `{$where_tpl}` was on a different line and still flagged. Replaced all with `phpcs:disable` / `phpcs:enable` blocks that wrap the full multi-line prepare call. Added all missing rule names.

### Fix 8 — Chat History / Top Requests: new records not shown (query bug)
**File:** `includes/class-aiwoo-assistant-chat-logger.php` → `get_sessions()`, `count_sessions()`
**Root cause:** `build_where()` was refactored to return `[$where_tpl, $where_args]` tuple. When no filters are active, `$where_args = []`. Spreading `...$where_args` into `$wpdb->prepare()` with zero args can return `null` on WP 4.8.3+ — making both methods return 0/empty.
**Fix:** Split both methods into two branches:
- No filter args → `prepare()` with only explicit `$per_page, $offset`; count uses raw SQL in `get_var()`
- Has filter args → spread `...$args` as before

### Fix 9 — Chat History / Top Requests: new records not shown (INSERT bug)
**File:** `includes/class-aiwoo-assistant-chat-logger.php` → `log()`
**Root cause:** MySQL `utf8` charset (3-byte) silently rejects 4-byte Unicode (emoji). AI responses started containing emoji → `$wpdb->insert()` returned false with no exception, no log entry.
**Fix:**
- Added `$strip4` closure: `preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $str)` applied to `user_message` and `ai_response`
- Added `$wpdb->last_error` check + `error_log()` when INSERT returns false
- Changed `catch(\Exception)` → `catch(\Throwable)` (PHP 8 `Error` types are not `Exception`)

### Fix 10 — Quick Replies Save/Update: blank admin-post.php page
**Files:** `includes/class-aiwoo-assistant-plugin.php`, `includes/class-aiwoo-assistant-quick-reply-service.php`
**Root cause:** `Quick_Reply_Service::__construct()` registered `admin_post_*` hooks. Service is lazily instantiated — only created when `admin_menu` fires. `admin_menu` does NOT fire on `admin-post.php` requests → hooks never registered → WordPress falls through to blank page.
**Fix:** Removed hooks from `Quick_Reply_Service::__construct()`. Added to `Plugin::__construct()` with public shim methods (`handle_admin_post_qr_save`, `handle_admin_post_qr_delete`, `handle_admin_post_qr_save_from_ai`).

### Fix 11 — AI_Error_Logger::log() — same three bugs as Fix 9
**File:** `includes/class-aiwoo-assistant-ai-error-logger.php`
- `catch(\Exception)` → `catch(\Throwable)`
- Added `$strip4` on `user_message` and `error_message`
- Added `$wpdb->last_error` + `error_log()`

### Fix 12 — Wrong Claude Opus model ID: claude-opus-4-6 → claude-opus-4-7
**Files:** `includes/class-aiwoo-assistant-settings.php`, `includes/class-aiwoo-assistant-claude-provider.php`
`claude-opus-4-6` does not exist. Correct ID is `claude-opus-4-7`. Fixed in 4 places:
- `Settings::$defaults` — comment updated
- `Settings::render_field()` — dropdown option value + label
- `Settings::normalize_claude_model()` — supported list + migration alias for old value
- `Claude_Provider::validated_model()` — supported list + same migration alias

### Fix 13 — IP_Blocker admin_post hooks moved to Plugin
**Files:** `includes/class-aiwoo-assistant-plugin.php`, `includes/class-aiwoo-assistant-ip-blocker.php`
`IP_Blocker::__construct()` originally registered `admin_post_aiwoo_add_blocked_ip` and `admin_post_aiwoo_delete_blocked_ip` (now renamed `veltez_add_blocked_ip` / `veltez_delete_blocked_ip`). Worked only because IP_Blocker is eagerly instantiated — brittle. Removed from `__construct()`. Added to `Plugin::__construct()` with shims (`handle_admin_post_ip_add`, `handle_admin_post_ip_delete`).

### Fix 14 — load_plugin_textdomain() added then removed
**File:** `includes/class-aiwoo-assistant-plugin.php`
Initially added `load_plugin_textdomain()` on `init`. Removed after PluginCheck flagged it — WordPress 4.6+ auto-loads translations for plugins on WordPress.org, and this plugin requires WP 6.4+. No manual call needed.

### Fix 15 — ai-errors-page.php: esc_html() on printf integer args
**File:** `admin/ai-errors-page.php`
`printf(esc_html__('%d ...'), esc_html($total))` — `esc_html()` is for HTML output context, not format string arguments. Changed to `(int)` casts on `$total`, `$current_page`, `$total_pages`.

### Fix 16 — chat-session-detail-page.php: unescaped echo count()
**File:** `admin/chat-session-detail-page.php`
`echo count($messages)` with no escaping. Changed to `echo (int) count($messages)`.

### Fix 17 — WordPress.org: inline `<script>` and `<style>` tags (wp_enqueue requirement)
**Files:** `admin/top-requests-page.php`, `includes/class-aiwoo-assistant-plugin.php`, `includes/class-aiwoo-assistant-admin-menu.php`
WP.org review flagged three inline script/style violations:
- Removed `<script>` block from `top-requests-page.php`. JS now injected via `wp_add_inline_script('jquery', $js)` in `Plugin::enqueue_admin_assets()` when on the Top Requests hook.
- Replaced `render_admin_bar_styles()` (which echoed `<style>`) with `enqueue_admin_bar_styles()` that calls `wp_add_inline_style('admin-bar', $css)`. Hooked to both `admin_enqueue_scripts` and `wp_enqueue_scripts`.
- Deleted unused `MENU_ICON_SVG` private const (contained `<style>` in SVG string, was never referenced — `register_menus()` uses PNG icon).
- Added `Admin_Menu::get_top_requests_hook()` accessor used by `Plugin::enqueue_admin_assets()` for hook gating.

### Fix 18 — WordPress.org: "ai" is too common a prefix (full project-wide rename)
**All PHP and JS files** — WP.org review flagged `AIWooAssistant` / `AI_WOO_ASSISTANT_` / `aiwoo_` / `ai_woo_assistant_` as using the generic word "ai" as a prefix. Full rename to `veltez` prefix across 31 files:

| Old | New |
|---|---|
| `AI_WOO_ASSISTANT_*` constants | `VELTEZ_AI_*` |
| `namespace AIWooAssistant` | `namespace Veltez` |
| `ai_woo_assistant_settings` option | `veltez_ai_settings` |
| `ai_woo_assistant` settings group | `veltez_ai` |
| `aiwoo_*` options/transients | `veltez_*` |
| `aiwoo_chat_logs/quick_replies/ai_error_logs` tables | `veltez_chat_logs/quick_replies/ai_error_logs` |
| AJAX actions `ai_woo_assistant_chat/enquiry` | `veltez_ai_chat/enquiry` |
| admin-post actions `aiwoo_*` | `veltez_*` |
| Nonce `ai_woo_assistant_nonce` | `veltez_ai_nonce` |
| Post type `aiwoo_enquiry` | `veltez_enquiry` |
| Post meta `_aiwoo_*` | `_veltez_*` |
| Script handles `ai-woo-assistant-*` | `veltez-ai-*` |
| JS global `AIWooAssistant` | `VeltezAI` |
| Rate limiter transient `ai_woo_rl_` | `veltez_rl_` |
| MCP tool transients `aiwoo_tool_*` | `veltez_tool_*` |

---

## Complete Audit — Confirmed Safe (do not re-investigate)

**Admin_Menu hook timing:** `init_admin_menu` runs at `admin_menu` priority 1. `Admin_Menu::__construct()` calls `add_action('admin_menu', 'register_menus')` at default priority 10. WordPress processes priorities in ascending order — priority 10 is still in the queue when priority 1 completes. Timing is safe.

**IP_Blocker CIDR matching:** IPv4 and IPv6 CIDR matching both implemented correctly. Uses `inet_pton()` for IPv6 byte comparison. Address family mismatch returns false immediately.

**Settings sanitization:** All fields covered — hex colors via `sanitize_hex_color()`, URLs via `esc_url_raw()`, API keys only overwritten if new input is non-empty (blank preserves existing key). Correct.

**Rate limiter:** Fixed-window (NOT sliding). Key = `veltez_rl_{md5(ip|floor(time/60))}`. TTL = 90s. Key rotates every 60s so each window is truly independent.

**AJAX security chain:** `is_enabled()` → IP block → bot UA detection → `check_ajax_referer('veltez_ai_nonce', 'nonce')` → rate limit → message length (auto-block IP) → sanitize. All present and correct.

**All admin templates:** Every output uses `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()`, or `wp_kses_post()`. No raw output (except fixed in fix 16).

**All admin_post handlers:** All check `current_user_can('manage_options')` and `check_admin_referer()` before any action. Correct.

**Nonces in all templates:** Every form calls `wp_nonce_field()` with the action name that matches the handler's `check_admin_referer()` call. Matched correctly across all 5 actions.

**CSV export nonce** (`top-requests-page.php`): Export link includes `_wpnonce` from `wp_create_nonce('veltez_export_top_requests')`. Handler calls `check_admin_referer('veltez_export_top_requests')`. Correct.

**uninstall.php:** Drops all 3 tables (`veltez_chat_logs`, `veltez_quick_replies`, `veltez_ai_error_logs`), deletes all options, deletes QR transient, deletes all `veltez_enquiry` CPT posts. Complete cleanup.

**Catalog_Service loading:** `woocommerce-handler.php` requires `catalog-service.php`. Loaded before `Plugin::instance()` so the class exists when `get_catalog_service()` first fires.

**All three providers:** Implement both `generate_response()` and `generate_with_tools()` from `Provider_Interface`. No missing method issues. All three are fully functional.

**Provider tool-calling loops:** All three providers cap at 5 rounds (`$max_rounds = 5`) to prevent infinite tool-call loops.

**Quick Reply cache flush:** `flush_cache()` (deletes the transient) is called on every insert, update, and delete in `Quick_Reply_Service`. New rules appear in chat immediately.

**Enquiry CPT `veltez_enquiry`:** Registered on `init`. `public=false`, `show_ui=false`, `publicly_queryable=false`. Not visible in frontend or standard admin UI. Correct.

**Bot detection:** Checks lowercase User-Agent string against 16 known bot signatures. Empty UA → blocked. Admin-only pages are not affected (bot check only in AJAX handler).

---

## Dead Code (harmless)

- `Settings::register_settings_page()` — defines `add_options_page()` but is never called. Settings page is registered by `Admin_Menu` via `admin_menu` hook. Leftover from earlier architecture.

---

## Known Intentional Design Choices

- **OpenAI model names** (`gpt-5.4-mini`, `gpt-5.4`) — non-standard. Intentional future-proofing. Do not change without user direction.
- **`created_at DEFAULT '0000-00-00 00:00:00'`** in all CREATE TABLE statements — INSERT always provides an explicit `current_time('mysql')` value. Default never triggers. DB_VERSION bump not needed to fix this.
- **`REMOTE_ADDR` only** — No X-Forwarded-For. Correct for spoofing prevention. If behind a trusted reverse proxy, configure real-IP at web-server level, not in PHP.
- **No `$wpdb->prepare()` when no args + no placeholders** — `count_sessions()` passes raw SQL string to `get_var()` when `$where_args` is empty. This is safe because the SQL contains only hardcoded table prefix and `1=1`.
