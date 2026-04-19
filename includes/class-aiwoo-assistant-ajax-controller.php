<?php
/**
 * AJAX controller for chat and enquiry requests.
 *
 * @package AIWooAssistant
 */

namespace AIWooAssistant;

defined( 'ABSPATH' ) || exit;

final class Ajax_Controller {
	private $settings;

	private $chat_service;

	private $chat_logger;

	private $ip_blocker;

	private $ai_error_logger;

	public function __construct( Settings $settings, Chat_Service $chat_service, Chat_Logger $chat_logger, IP_Blocker $ip_blocker, AI_Error_Logger $ai_error_logger ) {
		$this->settings         = $settings;
		$this->chat_service     = $chat_service;
		$this->chat_logger      = $chat_logger;
		$this->ip_blocker       = $ip_blocker;
		$this->ai_error_logger  = $ai_error_logger;

		// AJAX hooks are registered by Plugin so this controller can be
		// instantiated lazily (only on the first AJAX call).
	}

	public function handle_chat() {
		if ( ! $this->settings->is_enabled() ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The assistant is disabled.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				403
			);
		}

		if ( $this->ip_blocker->is_blocked( IP_Blocker::get_visitor_ip() ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Not available.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				403
			);
		}

		if ( $this->is_bot_request() ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Not available.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				403
			);
		}

		check_ajax_referer( 'ai_woo_assistant_nonce', 'nonce' );

		if ( ! $this->rate_limit_ok() ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Too many requests. Please wait a moment and try again.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				429
			);
		}

		$session_id  = isset( $_POST['session_id'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['session_id'] ) ), 0, 64 ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below via sanitize_textarea_field after length check
		$raw_message      = isset( $_POST['message'] )     ? wp_unslash( $_POST['message'] )     : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON payload; decoded and type-checked below
		$raw_history      = isset( $_POST['history'] )     ? wp_unslash( $_POST['history'] )     : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON payload; decoded and type-checked below
		$raw_page_context = isset( $_POST['pageContext'] ) ? wp_unslash( $_POST['pageContext'] ) : '';

		// Reject oversized history / page-context payloads.
		// pageContext limit raised to 6000 to accommodate viewedProducts + searchHistory fields.
		if ( strlen( (string) $raw_history ) > 8000 || strlen( (string) $raw_page_context ) > 6000 ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Your message is too large. Please shorten it and try again.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				413
			);
		}

		// Enforce configurable message length limit.
		// A message exceeding the limit is characteristic of automated/bot traffic —
		// auto-block the IP to protect token budget.
		$max_message_length = max( 10, (int) $this->settings->get( 'max_message_length' ) );
		if ( strlen( (string) $raw_message ) > $max_message_length ) {
			$visitor_ip = IP_Blocker::get_visitor_ip();
			if ( '' !== $visitor_ip ) {
				$this->ip_blocker->add( $visitor_ip ); // silently no-ops on duplicates / limit reached
			}
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Not available.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				413
			);
		}

		$message      = sanitize_textarea_field( $raw_message );
		$history      = '' !== $raw_history ? json_decode( $raw_history, true ) : array();
		$page_context = '' !== $raw_page_context ? json_decode( $raw_page_context, true ) : array();

		if ( '' === trim( $message ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Message cannot be empty.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				400
			);
		}

		if ( ! is_array( $history ) ) {
			$history = array();
		}

		if ( ! is_array( $page_context ) ) {
			$page_context = array();
		}

		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		try {
			$reply = $this->chat_service->generate_reply( $message, $history, $page_context, $session_id, $ip_address );
			$this->chat_logger->log( $session_id, $ip_address, $message, $reply['message'] ?? '' );
			wp_send_json_success( $reply );
		} catch ( \Exception $exception ) {
			// Log full details server-side only; never expose raw exception messages to the frontend.
			$this->ai_error_logger->log( $session_id, $ip_address, $message, 'ajax', $exception->getMessage() );
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The assistant is temporarily unavailable. Please try again.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				500
			);
		}
	}

	public function handle_enquiry() {
		if ( ! $this->settings->is_enabled() ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The assistant is disabled.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				403
			);
		}

		if ( $this->ip_blocker->is_blocked( IP_Blocker::get_visitor_ip() ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Not available.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				403
			);
		}

		check_ajax_referer( 'ai_woo_assistant_nonce', 'nonce' );

		// Rate-limit enquiry submissions the same as chat messages.
		if ( ! $this->rate_limit_ok() ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Too many requests. Please wait a moment and try again.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				429
			);
		}

		// Honeypot — bots typically populate every field including hidden ones.
		// Legitimate users never see or fill this field.
		if ( isset( $_POST['aiwoo_hp'] ) && '' !== $_POST['aiwoo_hp'] ) {
			// Silently succeed without storing or emailing anything.
			wp_send_json_success(
				array(
					'message' => esc_html__( 'Thanks. Your enquiry has been sent and saved. Our team can follow up by email.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				)
			);
		}

		$session_id = isset( $_POST['session_id'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['session_id'] ) ), 0, 64 ) : '';
		$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		if ( '' === $name || '' === $message || ! is_email( $email ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Please enter a valid name, email, and message.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				400
			);
		}

		$admin_email = get_option( 'admin_email' );
		/* translators: %s: customer name */
		$subject     = sprintf( __( 'New veltez enquiry from %s', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ), $name );
		$body        = sprintf(
			"Name: %s\nPhone: %s\nEmail: %s\n\nMessage:\n%s",
			$name,
			'' !== trim( $phone ) ? $phone : __( 'Not provided', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			$email,
			$message
		);
		$headers     = array( 'Reply-To: ' . $name . ' <' . $email . '>' );
		$stored      = $this->store_enquiry( $name, $phone, $email, $message, $session_id, $ip_address );

		// Backfill customer name in chat logs for this session.
		if ( '' !== $session_id && '' !== $name ) {
			$this->chat_logger->backfill_customer_name( $session_id, $name );
		}

		$sent = wp_mail( $admin_email, $subject, $body, $headers );

		if ( ! $sent && ! $stored ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Your enquiry could not be saved or sent right now. Please try again later.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
				),
				500
			);
		}

		if ( $sent && $stored ) {
			$response_message = esc_html__( 'Thanks. Your enquiry has been sent and saved. Our team can follow up by email.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' );
		} elseif ( $sent ) {
			$response_message = esc_html__( 'Thanks. Your enquiry has been emailed to our team.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' );
		} else {
			$response_message = esc_html__( 'Thanks. Your enquiry has been saved and our team can review it shortly.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' );
		}

		wp_send_json_success(
			array(
				'message' => $response_message,
			)
		);
	}

	private function store_enquiry( $name, $phone, $email, $message, $session_id = '', $ip_address = '' ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'aiwoo_enquiry',
				'post_status'  => 'private',
				'post_title'   => sprintf( '%s - %s', $name, current_time( 'mysql' ) ),
				'post_content' => $message,
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return false;
		}

		update_post_meta( $post_id, '_aiwoo_name', $name );
		update_post_meta( $post_id, '_aiwoo_phone', $phone );
		update_post_meta( $post_id, '_aiwoo_email', $email );

		if ( '' !== $session_id ) {
			update_post_meta( $post_id, '_aiwoo_session_id', $session_id );
		}

		if ( '' !== $ip_address ) {
			update_post_meta( $post_id, '_aiwoo_ip', $ip_address );
		}

		return true;
	}

	private function is_bot_request() {
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';

		if ( '' === $user_agent ) {
			return true;
		}

		$bot_signatures = array(
			'bot',
			'crawler',
			'spider',
			'slurp',
			'curl/',
			'wget/',
			'python-',
			'python/',
			'scrapy',
			'httpclient',
			'go-http-client',
			'java/',
			'ruby/',
			'perl/',
			'libwww',
			'okhttp',
			'apache-httpclient',
		);

		foreach ( $bot_signatures as $signature ) {
			if ( str_contains( $user_agent, $signature ) ) {
				return true;
			}
		}

		return false;
	}

	private function rate_limit_ok() {
		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

		// Fixed-window rate limiter: the key rotates every 60 seconds so each
		// window is truly independent. The old sliding-window approach reset the
		// TTL on every request, allowing sustained bursts just under the limit.
		$window = (int) floor( time() / 60 );
		$key    = 'ai_woo_rl_' . md5( $ip_address . '|' . $window );
		$count  = (int) get_transient( $key );

		if ( $count >= 15 ) {
			return false;
		}

		// TTL of 90 s covers the window boundary without accumulating stale keys.
		set_transient( $key, $count + 1, 90 );

		return true;
	}
}
