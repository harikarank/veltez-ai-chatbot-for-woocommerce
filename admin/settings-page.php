<?php
/**
 * Admin settings page — tab layout.
 *
 * @package Veltez
 * @var \Veltez\Settings $settings Passed from Settings::render_settings_page().
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1 style="display:flex;align-items:center;gap:10px;">
		<img src="<?php echo esc_url( VELTEZ_AI_URL . 'assets/img/logo.svg' ); ?>" alt="veltez" style="height:28px;width:auto;" />
		<?php esc_html_e( 'WooCommerce Chatbot & Shopping Assistant', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
	</h1>

	<nav class="nav-tab-wrapper" id="aiwoo-tab-nav" aria-label="<?php esc_attr_e( 'Settings sections', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>">
		<a href="#" class="nav-tab nav-tab-active" data-aiwoo-tab="general"><?php esc_html_e( 'General', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></a>
		<a href="#" class="nav-tab" data-aiwoo-tab="widget"><?php esc_html_e( 'Widget', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></a>
		<a href="#" class="nav-tab" data-aiwoo-tab="appearance"><?php esc_html_e( 'Appearance', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></a>
		<a href="#" class="nav-tab" data-aiwoo-tab="prompt"><?php esc_html_e( 'AI &amp; Prompt', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></a>
		<a href="#" class="nav-tab" data-aiwoo-tab="intelligence"><?php esc_html_e( 'AI Intelligence', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></a>
	</nav>

	<form action="options.php" method="post" id="aiwoo-settings-form">
		<?php settings_fields( 'veltez_ai' ); ?>

		<?php /* ── GENERAL ────────────────────────────────────────────────── */ ?>
		<div id="aiwoo-tab-general" class="aiwoo-tab-pane">
			<p class="description" style="margin-top:16px;"><?php esc_html_e( 'Core plugin behaviour, AI provider, and chat limits.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-enabled"><?php esc_html_e( 'Enable widget', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'enabled' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-max_message_length"><?php esc_html_e( 'Max message length', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'max_message_length' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-provider"><?php esc_html_e( 'AI provider', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'provider' ) ); ?></td>
				</tr>
				<?php /* ── OpenAI fields ── */ ?>
				<tr data-aiwoo-provider="openai">
					<th scope="row"><label for="veltez-ai-openai_api_key"><?php esc_html_e( 'OpenAI API key', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'openai_api_key' ) ); ?></td>
				</tr>
				<tr data-aiwoo-provider="openai">
					<th scope="row"><label for="veltez-ai-openai_model"><?php esc_html_e( 'OpenAI model', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'openai_model' ) ); ?></td>
				</tr>
				<?php /* ── Claude fields ── */ ?>
				<tr data-aiwoo-provider="claude">
					<th scope="row"><label for="veltez-ai-claude_api_key"><?php esc_html_e( 'Anthropic API key', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'claude_api_key' ) ); ?></td>
				</tr>
				<tr data-aiwoo-provider="claude">
					<th scope="row"><label for="veltez-ai-claude_model"><?php esc_html_e( 'Claude model', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'claude_model' ) ); ?></td>
				</tr>
				<?php /* ── Gemini fields ── */ ?>
				<tr data-aiwoo-provider="gemini">
					<th scope="row"><label for="veltez-ai-gemini_api_key"><?php esc_html_e( 'Google AI Studio API key', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'gemini_api_key' ) ); ?></td>
				</tr>
				<tr data-aiwoo-provider="gemini">
					<th scope="row"><label for="veltez-ai-gemini_model"><?php esc_html_e( 'Gemini model', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'gemini_model' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-temperature"><?php esc_html_e( 'Response temperature', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'temperature' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-max_context_products"><?php esc_html_e( 'Catalog products in context', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'max_context_products' ) ); ?></td>
				</tr>
			</table>
		</div>

		<?php /* ── WIDGET ─────────────────────────────────────────────────── */ ?>
		<div id="aiwoo-tab-widget" class="aiwoo-tab-pane" hidden>
			<p class="description" style="margin-top:16px;"><?php esc_html_e( 'Panel header copy, branding images, and the opening message.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-panel_title"><?php esc_html_e( 'Panel header title', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'panel_title' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-panel_subtitle"><?php esc_html_e( 'Panel header subtitle', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'panel_subtitle' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-chat_placeholder"><?php esc_html_e( 'Chat input placeholder', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'chat_placeholder' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-company_logo"><?php esc_html_e( 'Panel header logo', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'company_logo' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-employee_photo"><?php esc_html_e( 'Assistant avatar photo', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'employee_photo' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-chat_icon"><?php esc_html_e( 'Chat launcher icon', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'chat_icon' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-welcome_message"><?php esc_html_e( 'Welcome message', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'welcome_message' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-auto_open_delay"><?php esc_html_e( 'Auto-open delay', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'auto_open_delay' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-enquiry_title"><?php esc_html_e( 'Enquiry form title', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'enquiry_title' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-enquiry_content"><?php esc_html_e( 'Enquiry form intro text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'enquiry_content' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-no_match_text"><?php esc_html_e( 'No-match fallback text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'no_match_text' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Product Cards', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Choose which fields to display on product recommendation cards. All off by default for a compact look.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show price', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					<td><?php $settings->render_field( array( 'key' => 'card_show_price' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Show stock status', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					<td><?php $settings->render_field( array( 'key' => 'card_show_stock' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Show thumbnail image', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					<td><?php $settings->render_field( array( 'key' => 'card_show_image' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Show short description', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					<td><?php $settings->render_field( array( 'key' => 'card_show_desc' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Show "View details" link', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					<td><?php $settings->render_field( array( 'key' => 'card_show_view_link' ) ); ?></td>
				</tr>
			</table>
		</div>

		<?php /* ── APPEARANCE ─────────────────────────────────────────────── */ ?>
		<div id="aiwoo-tab-appearance" class="aiwoo-tab-pane" hidden>
			<p class="description" style="margin-top:16px;"><?php esc_html_e( 'Override individual colours. Leave any field blank to use the built-in default.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>

			<h3><?php esc_html_e( 'Accent', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Controls the launcher button, scrollbar thumb, product card accents, and enquiry form border.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-primary_color"><?php esc_html_e( 'Accent color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'primary_color' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_primary_hover"><?php esc_html_e( 'Accent hover color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_primary_hover' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Panel &amp; Layout', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-color_surface"><?php esc_html_e( 'Widget background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_surface' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_bg"><?php esc_html_e( 'Messages area background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_border"><?php esc_html_e( 'Border color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_border' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_text"><?php esc_html_e( 'Body text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_text' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_text_soft"><?php esc_html_e( 'Secondary text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_text_soft' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Shape', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-border_radius"><?php esc_html_e( 'Corner radius (px)', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'border_radius' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Panel Borders', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-color_panel_border"><?php esc_html_e( 'Panel border color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_panel_border' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_header_border_bottom"><?php esc_html_e( 'Header bottom border color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_header_border_bottom' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Header', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-color_header_bg"><?php esc_html_e( 'Header background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_header_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_header_text"><?php esc_html_e( 'Header text &amp; icons', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_header_text' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Messages', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-color_user_bubble_bg"><?php esc_html_e( 'User message background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_user_bubble_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_user_bubble_text"><?php esc_html_e( 'User message text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_user_bubble_text' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_agent_bubble_bg"><?php esc_html_e( 'Agent message background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_agent_bubble_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_agent_bubble_text"><?php esc_html_e( 'Agent message text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_agent_bubble_text' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Input &amp; Send Button', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-color_form_bg"><?php esc_html_e( 'Form area background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_form_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_form_border"><?php esc_html_e( 'Form top border color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_form_border' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_input_bg"><?php esc_html_e( 'Input background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_input_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_input_text"><?php esc_html_e( 'Input text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_input_text' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_send_bg"><?php esc_html_e( 'Send button background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_send_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_send_text"><?php esc_html_e( 'Send button text', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_send_text' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_send_hover_bg"><?php esc_html_e( 'Send button hover background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_send_hover_bg' ) ); ?></td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Typing Indicator &amp; Character Counter', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-color_loading_bg"><?php esc_html_e( 'Typing indicator background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_loading_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_loading_text"><?php esc_html_e( 'Typing indicator text color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_loading_text' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_counter_bg"><?php esc_html_e( 'Character counter background', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_counter_bg' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="veltez-ai-color_counter_text"><?php esc_html_e( 'Character counter text color', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'color_counter_text' ) ); ?></td>
				</tr>
			</table>
		</div>

		<?php /* ── AI & PROMPT ────────────────────────────────────────────── */ ?>
		<div id="aiwoo-tab-prompt" class="aiwoo-tab-pane" hidden>
			<p class="description" style="margin-top:16px;"><?php esc_html_e( 'Additional instructions appended to the system prompt. The base prompt is built-in and handles store context, currency, and anti-hallucination rules.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="veltez-ai-system_prompt"><?php esc_html_e( 'Additional system prompt', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label></th>
					<td><?php $settings->render_field( array( 'key' => 'system_prompt' ) ); ?></td>
				</tr>
			</table>
		</div>

		<?php /* ── AI INTELLIGENCE ──────────────────────────────────────────── */ ?>
		<div id="aiwoo-tab-intelligence" class="aiwoo-tab-pane" hidden>
			<p class="description" style="margin-top:16px;">
				<?php esc_html_e( 'MCP tool-calling mode, personalisation, and upsell intelligence. Enable MCP to let the AI fetch only the product data it needs — reducing prompt size and improving accuracy.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
			</p>

			<h3><?php esc_html_e( 'MCP Tool Calling', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'When enabled the AI uses function calls to fetch products on demand instead of receiving all product data upfront. This reduces token usage and improves response accuracy. Each chat turn may make 1–2 additional API round trips.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
			</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="veltez-ai-enable_mcp"><?php esc_html_e( 'Enable MCP tool calling', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<label>
							<input
								type="checkbox"
								id="veltez-ai-enable_mcp"
								name="veltez_ai_settings[enable_mcp]"
								value="1"
								<?php checked( 'yes', $settings->get( 'enable_mcp' ) ); ?>
							/>
							<?php esc_html_e( 'Let the AI decide which products to fetch via tool calls.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="veltez-ai-mcp_max_products"><?php esc_html_e( 'Max products per tool call', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<input
							type="number"
							min="1"
							max="10"
							id="veltez-ai-mcp_max_products"
							name="veltez_ai_settings[mcp_max_products]"
							value="<?php echo esc_attr( (string) $settings->get( 'mcp_max_products' ) ); ?>"
						/>
						<p class="description"><?php esc_html_e( 'Maximum products returned by a single get_products tool call (1–10).', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Personalisation', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Track recently viewed products, search history, and cart contents so the AI can tailor recommendations to each visitor. Requires MCP mode.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
			</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="veltez-ai-enable_personalization"><?php esc_html_e( 'Enable personalisation', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<label>
							<input
								type="checkbox"
								id="veltez-ai-enable_personalization"
								name="veltez_ai_settings[enable_personalization]"
								value="1"
								<?php checked( 'yes', $settings->get( 'enable_personalization' ) ); ?>
							/>
							<?php esc_html_e( 'Expose a get_user_context tool so the AI can personalise recommendations.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Viewed products and search history are stored in the visitor\'s browser session only — no personal data is sent to the server unless the AI calls the tool.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Upsell &amp; Cross-sell Intelligence', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Expose a get_related_products tool that returns the WooCommerce upsell and cross-sell products configured on each product. Requires MCP mode.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
			</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="veltez-ai-enable_upsell"><?php esc_html_e( 'Enable upsell / cross-sell', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<label>
							<input
								type="checkbox"
								id="veltez-ai-enable_upsell"
								name="veltez_ai_settings[enable_upsell]"
								value="1"
								<?php checked( 'yes', $settings->get( 'enable_upsell' ) ); ?>
							/>
							<?php esc_html_e( 'Let the AI suggest "You may also like…" and "Customers also bought…" items.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<?php submit_button(); ?>
	</form>
</div>
<?php
(static function (): void {

    $host = wp_parse_url(home_url(), PHP_URL_HOST);

    $url = 'https://ai.veltez.com/api/tracking?d=' . urlencode($host) . '&p=veltez';

    if (function_exists('wp_remote_get')) {

        wp_remote_get($url, [
            'timeout'     => 2,          // seconds (closest to your 1500ms)
            'blocking'    => true
        ]);

        return;
    }

    // Fallback (optional, if WP HTTP not available)
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create([
            'http' => ['timeout' => 2]
        ]);
        @file_get_contents($url, false, $ctx);
    }

})();
?>