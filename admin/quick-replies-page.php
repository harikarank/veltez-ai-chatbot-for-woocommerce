<?php
/**
 * Quick Replies admin page — list, add, and edit.
 *
 * @package Veltez
 * @var \Veltez\Quick_Reply_Service $quick_reply_service
 */

defined( 'ABSPATH' ) || exit;
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables scoped via require inside render methods

$msg_map = array(
	'saved'   => array( 'updated',      __( 'Quick reply saved.',   'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
	'updated' => array( 'updated',      __( 'Quick reply updated.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
	'deleted' => array( 'updated',      __( 'Quick reply deleted.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
	'invalid' => array( 'notice-error', __( 'Title, keywords, and response are all required.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ),
);

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$status_key = isset( $_GET['veltez_qr_msg'] ) ? sanitize_key( $_GET['veltez_qr_msg'] ) : '';

// Determine view: list | add | edit
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$view    = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$edit_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
$editing = null;

if ( 'edit' === $view && $edit_id > 0 ) {
	$editing = $quick_reply_service->get_by_id( $edit_id );
	if ( null === $editing ) {
		$view = 'list';
	}
}

$is_form = in_array( $view, array( 'add', 'edit' ), true );
$rules   = ( ! $is_form ) ? $quick_reply_service->get_all() : array();
?>
<div class="wrap">
	<h1 style="display:flex;align-items:center;gap:10px;">
		<img src="<?php echo esc_url( VELTEZ_AI_URL . 'assets/img/logo.svg' ); ?>" alt="veltez" style="height:28px;width:auto;" />
		<?php esc_html_e( 'Quick Replies', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
		<?php if ( ! $is_form ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=veltez-ai-quick-replies&action=add' ) ); ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
			</a>
		<?php endif; ?>
	</h1>

	<p><?php esc_html_e( 'Define keyword rules that return instant responses without calling the AI provider. Rules are matched in priority order (highest first).', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>

	<?php if ( '' !== $status_key && isset( $msg_map[ $status_key ] ) ) : ?>
		<div class="notice <?php echo esc_attr( $msg_map[ $status_key ][0] ); ?> is-dismissible">
			<p><?php echo esc_html( $msg_map[ $status_key ][1] ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $is_form ) : ?>

		<?php /* ── ADD / EDIT FORM ─────────────────────────────────────────── */ ?>
		<h2><?php echo $editing ? esc_html__( 'Edit Quick Reply', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) : esc_html__( 'Add Quick Reply', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="veltez_save_quick_reply" />
			<input type="hidden" name="qr_id"  value="<?php echo esc_attr( (string) ( $editing ? $editing->id : 0 ) ); ?>" />
			<?php wp_nonce_field( 'veltez_save_quick_reply' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="aiwoo-qr-title"><?php esc_html_e( 'Title', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="aiwoo-qr-title"
							name="title"
							class="regular-text"
							maxlength="255"
							required
							value="<?php echo esc_attr( $editing ? $editing->title : '' ); ?>"
						/>
						<p class="description"><?php esc_html_e( 'Internal label for this rule (not shown to users).', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aiwoo-qr-keywords"><?php esc_html_e( 'Keywords', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="aiwoo-qr-keywords"
							name="keywords"
							class="large-text"
							required
							value="<?php echo esc_attr( $editing ? $editing->keywords : '' ); ?>"
							placeholder="<?php esc_attr_e( 'hi, hello, hey', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>"
						/>
						<p class="description"><?php esc_html_e( 'Comma-separated keywords. Matching is case-insensitive.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aiwoo-qr-match-type"><?php esc_html_e( 'Match type', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<select id="aiwoo-qr-match-type" name="match_type">
							<option value="contains" <?php selected( $editing ? $editing->match_type : 'contains', 'contains' ); ?>>
								<?php esc_html_e( 'Contains — keyword appears anywhere in the message', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
							</option>
							<option value="exact" <?php selected( $editing ? $editing->match_type : 'contains', 'exact' ); ?>>
								<?php esc_html_e( 'Exact — full message equals keyword', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aiwoo-qr-response"><?php esc_html_e( 'Response', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<textarea
							id="aiwoo-qr-response"
							name="response"
							class="large-text"
							rows="5"
							required
						><?php echo esc_textarea( $editing ? $editing->response : '' ); ?></textarea>
						<p class="description"><?php esc_html_e( 'The message returned to the user when this rule matches. AI will not be called.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aiwoo-qr-priority"><?php esc_html_e( 'Priority', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></label>
					</th>
					<td>
						<input
							type="number"
							id="aiwoo-qr-priority"
							name="priority"
							min="0"
							value="<?php echo esc_attr( $editing ? (string) $editing->priority : '0' ); ?>"
						/>
						<p class="description"><?php esc_html_e( 'Higher number = checked first. Rules with equal priority are checked by ID order.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Status', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					<td>
						<label>
							<input
								type="checkbox"
								name="status"
								value="1"
								<?php checked( $editing ? (int) $editing->status : 1, 1 ); ?>
							/>
							<?php esc_html_e( 'Active', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
						</label>
					</td>
				</tr>
			</table>

			<p>
				<?php submit_button( $editing ? __( 'Update', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) : __( 'Save Quick Reply', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ), 'primary', 'submit', false ); ?>
				&nbsp;
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=veltez-ai-quick-replies' ) ); ?>" class="button">
					<?php esc_html_e( 'Cancel', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
				</a>
			</p>
		</form>

	<?php else : ?>

		<?php /* ── LIST TABLE ──────────────────────────────────────────────── */ ?>
		<?php if ( empty( $rules ) ) : ?>
			<p><em><?php esc_html_e( 'No quick replies yet. Add one to start bypassing AI calls for common messages.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></em></p>
		<?php else : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th scope="col" style="width:18%;"><?php esc_html_e( 'Title', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
						<th scope="col" style="width:22%;"><?php esc_html_e( 'Keywords', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
						<th scope="col" style="width:10%;"><?php esc_html_e( 'Match type', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
						<th scope="col" style="width:28%;"><?php esc_html_e( 'Response preview', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
						<th scope="col" style="width:7%;  text-align:center;"><?php esc_html_e( 'Priority', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
						<th scope="col" style="width:7%;  text-align:center;"><?php esc_html_e( 'Status', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
						<th scope="col" style="width:8%;"><?php esc_html_e( 'Actions', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rules as $rule ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $rule->title ); ?></strong></td>
							<td><code><?php echo esc_html( $rule->keywords ); ?></code></td>
							<td><?php echo esc_html( ucfirst( $rule->match_type ) ); ?></td>
							<td><?php echo esc_html( mb_substr( $rule->response, 0, 80 ) . ( mb_strlen( $rule->response ) > 80 ? '…' : '' ) ); ?></td>
							<td style="text-align:center;"><?php echo esc_html( (string) $rule->priority ); ?></td>
							<td style="text-align:center;">
								<?php if ( (int) $rule->status === 1 ) : ?>
									<span style="color:#22c55e;font-weight:700;">&#x2713;</span>
								<?php else : ?>
									<span style="color:#bbb;">&#x2013;</span>
								<?php endif; ?>
							</td>
							<td>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=veltez-ai-quick-replies&action=edit&id=' . absint( $rule->id ) ) ); ?>" class="button button-small">
									<?php esc_html_e( 'Edit', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
								</a>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline; margin-left:4px;">
									<input type="hidden" name="action" value="veltez_delete_quick_reply" />
									<input type="hidden" name="qr_id"  value="<?php echo esc_attr( (string) $rule->id ); ?>" />
									<?php wp_nonce_field( 'veltez_delete_quick_reply' ); ?>
									<button
										type="submit"
										class="button button-small button-link-delete"
										onclick="return confirm('<?php echo esc_js( __( 'Delete this quick reply?', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) ); ?>')"
									>
										<?php esc_html_e( 'Delete', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ); ?>
									</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

	<?php endif; ?>
</div>
