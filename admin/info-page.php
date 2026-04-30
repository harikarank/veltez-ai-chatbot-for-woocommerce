<?php
/**
 * veltez — Information / Documentation page.
 *
 * @package Veltez
 */

defined( 'ABSPATH' ) || exit;
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables scoped via require inside render methods
?>
<div class="wrap">
	<h1 style="display:flex;align-items:center;gap:10px;">
		<img src="<?php echo esc_url( VELTEZ_AI_URL . 'assets/img/logo.svg' ); ?>" alt="veltez" style="height:28px;width:auto;" />
		<?php esc_html_e( 'Plugin Guide', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
	</h1>
	<p style="color:#6b7280;margin-top:4px;"><?php esc_html_e( 'Everything you need to know to set up and get the most from veltez.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>

	<?php
	$sections = array(
		array(
			'id'    => 'overview',
			'title' => __( 'What is veltez?', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '💬',
			'body'  => array(
				__( 'veltez is a WooCommerce-native AI shopping assistant widget. It sits in the corner of your store, helping customers find products, compare options, and get instant answers — all powered by your choice of AI provider (OpenAI, Claude, or Gemini).', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'Conversations are stored in your database so you can review them under <strong>Chat History</strong>. No data is sent anywhere except to the AI provider you configure.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'providers',
			'title' => __( 'AI Providers', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '🤖',
			'body'  => array(
				__( 'Go to <strong>Settings → General</strong> and select your preferred AI provider. Then enter your API key for that provider.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
			'table' => array(
				'headers' => array( __( 'Provider', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ), __( 'Models', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ), __( 'Best for', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
				'rows'    => array(
					array( 'OpenAI', 'gpt-5.4-mini, gpt-5.4, gpt-4.1-mini', __( 'Fast, cost-effective responses', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
					array( 'Claude (Anthropic)', 'Sonnet 4.6, Opus 4.6, Haiku 4.5', __( 'Best for MCP tool-calling mode', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
					array( 'Gemini (Google)', 'Gemini 2.5 Flash, 2.5 Pro, 1.5 series', __( 'Competitive pricing, multilingual', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
				),
			),
		),
		array(
			'id'    => 'widget',
			'title' => __( 'Widget Settings', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '🎨',
			'body'  => array(
				__( 'Configure the chat widget appearance under <strong>Settings → Widget</strong> and <strong>Settings → Appearance</strong>.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
			'list'  => array(
				__( '<strong>Panel title / subtitle</strong> — The text shown at the top of the chat panel. Defaults to your site name.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Company logo</strong> — Shown in the panel header. Leave blank to use the built-in veltez logo.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Chat launcher icon</strong> — The icon on the floating button. Leave blank to use the built-in favicon.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Welcome message</strong> — The first message shown when the panel opens.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Accent color</strong> — Controls the launcher button, scrollbar, card accents, and send button.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Corner radius</strong> — 0 = sharp corners, 24 = fully rounded. Applies to the panel, bubbles, and enquiry form.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Form top border color</strong> — The line separating the input area from the messages.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'product-cards',
			'title' => __( 'Product Cards', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '🛍️',
			'body'  => array(
				__( 'When the AI recommends products, they are displayed as cards. Go to <strong>Settings → Widget → Product Cards</strong> to control what each card shows.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
			'list'  => array(
				__( '<strong>Show price</strong> — Display the formatted WooCommerce price.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Show stock status</strong> — Show "In stock", "Out of stock", or "On backorder".', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Show thumbnail image</strong> — Display the product\'s featured image thumbnail.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Show short description</strong> — Show a trimmed excerpt of the product\'s short description.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>Show "View details" link</strong> — Adds an explicit link below the card content.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>No-match fallback text</strong> — The message shown when no products match the customer\'s query.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'mcp',
			'title' => __( 'MCP Tool-Calling Mode', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '⚡',
			'body'  => array(
				__( 'Enable under <strong>Settings → AI Intelligence → MCP Tool Calling</strong>.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'In MCP mode the AI decides which products to fetch via tool calls instead of receiving all product data upfront. This reduces token usage and improves accuracy — but requires 1–2 extra API round trips per response.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
			'list'  => array(
				__( '<strong>get_products</strong> — Search the catalog by keyword.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>get_product_details</strong> — Fetch full details for a specific product by ID.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>get_related_products</strong> — Return WooCommerce upsells and cross-sells (requires "Enable upsell / cross-sell").', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( '<strong>get_user_context</strong> — Expose viewed products, search history, and cart for personalisation (requires "Enable personalisation").', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
			'note'  => __( 'MCP mode works best with Claude (Anthropic) models due to their native tool-calling support.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
		),
		array(
			'id'    => 'quick-replies',
			'title' => __( 'Quick Replies', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '⚡',
			'body'  => array(
				__( 'Quick Replies let you define rule-based keyword matches that bypass the AI entirely. Great for FAQs like store hours, return policy, or shipping info.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'Go to <strong>veltez → Quick Replies</strong> to add, edit, or delete rules. Match types: <strong>exact</strong> (full message match), <strong>contains</strong> (keyword anywhere), <strong>prefix</strong> (message starts with keyword).', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'ip-blocklist',
			'title' => __( 'IP Blocklist', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '🛡️',
			'body'  => array(
				__( 'Block individual IPs or CIDR ranges from using the chat widget. Go to <strong>veltez → IP Blocklist</strong>.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'IPs that send messages exceeding the configured max length are automatically blocked to protect your token budget.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'chat-history',
			'title' => __( 'Chat History & Logs', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '📋',
			'body'  => array(
				__( 'Every conversation is logged in the database. Browse sessions under <strong>veltez → Chat History</strong>. Filter by IP, customer name, or date range.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'Customer names are backfilled automatically when a visitor submits the enquiry form during their session.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'AI failures (provider errors or fallbacks) are recorded separately under <strong>veltez → AI Error Log</strong> — visible to admins only, never shown to users.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'enquiries',
			'title' => __( 'Enquiries', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '📧',
			'body'  => array(
				__( 'When no products match a customer\'s query, the enquiry form appears. Submissions are stored as private posts under <strong>veltez → Enquiries</strong> and also emailed to your site admin address.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'A honeypot field silently rejects bot submissions without any disruption to real users.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
		array(
			'id'    => 'system-prompt',
			'title' => __( 'Customising the AI Behaviour', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'icon'  => '✏️',
			'body'  => array(
				__( 'Go to <strong>Settings → AI &amp; Prompt</strong> to add custom instructions that are prepended to every conversation. Use this to define the AI\'s tone, restrict topics, or add store-specific rules.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				__( 'The built-in base prompt already handles: store name, currency, anti-hallucination rules, and product-recommendation format. You only need to add instructions that go beyond these defaults.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			),
		),
	);
	?>

	<div style="max-width:860px;margin-top:24px;">
		<?php foreach ( $sections as $section ) : ?>
			<div style="background:#fff;border:1px solid #e5e7eb;border-radius:6px;padding:24px 28px;margin-bottom:16px;">
				<h2 style="margin:0 0 12px;font-size:16px;display:flex;align-items:center;gap:8px;">
					<span aria-hidden="true" style="font-size:18px;"><?php echo esc_html( $section['icon'] ); ?></span>
					<?php echo esc_html( $section['title'] ); ?>
				</h2>

				<?php foreach ( $section['body'] as $para ) : ?>
					<p style="color:#374151;line-height:1.7;margin:0 0 10px;"><?php echo wp_kses( $para, array( 'strong' => array() ) ); ?></p>
				<?php endforeach; ?>

				<?php if ( ! empty( $section['list'] ) ) : ?>
					<ul style="margin:8px 0 0 18px;color:#374151;line-height:1.7;">
						<?php foreach ( $section['list'] as $item ) : ?>
							<li style="margin-bottom:4px;"><?php echo wp_kses( $item, array( 'strong' => array() ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( ! empty( $section['table'] ) ) : ?>
					<table class="wp-list-table widefat fixed striped" style="margin-top:12px;">
						<thead>
							<tr>
								<?php foreach ( $section['table']['headers'] as $h ) : ?>
									<th><?php echo esc_html( $h ); ?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $section['table']['rows'] as $row ) : ?>
								<tr>
									<?php foreach ( $row as $cell ) : ?>
										<td><?php echo esc_html( $cell ); ?></td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<?php if ( ! empty( $section['note'] ) ) : ?>
					<p style="margin-top:12px;padding:10px 14px;background:#eff6ff;border-left:3px solid #3b82f6;border-radius:3px;color:#1e40af;font-size:13px;line-height:1.6;">
						<?php echo esc_html( $section['note'] ); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
