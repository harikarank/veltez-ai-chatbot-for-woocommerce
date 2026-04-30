<?php
/**
 * Top-level admin menu and sub-page controller.
 *
 * @package Veltez
 */

namespace Veltez;

defined( 'ABSPATH' ) || exit;

final class Admin_Menu {

	/** @var Settings */
	private $settings;

	/** @var Chat_Logger */
	private $chat_logger;

	/** @var IP_Blocker */
	private $ip_blocker;

	/** @var Quick_Reply_Service */
	private $quick_reply_service;

	/** @var AI_Error_Logger */
	private $ai_error_logger;

	/** Hook suffixes returned by add_submenu_page — used for asset enqueueing. */
	private $hook_chat_history   = '';
	private $hook_enquiries       = '';
	private $hook_ip_blocklist    = '';
	private $hook_quick_replies   = '';
	private $hook_top_requests    = '';
	private $hook_ai_errors       = '';
	private $hook_info            = '';
	private $hook_settings        = '';

	public function __construct( Settings $settings, Chat_Logger $chat_logger, IP_Blocker $ip_blocker, Quick_Reply_Service $quick_reply_service, AI_Error_Logger $ai_error_logger ) {
		$this->settings            = $settings;
		$this->chat_logger         = $chat_logger;
		$this->ip_blocker          = $ip_blocker;
		$this->quick_reply_service = $quick_reply_service;
		$this->ai_error_logger     = $ai_error_logger;

		add_action( 'admin_menu', array( $this, 'register_menus' ) );
		// Note: admin bar hooks moved to Plugin class so the admin bar works
		// without forcing this (and its service dependencies) to instantiate.
	}

	// -------------------------------------------------------------------------
	// Menu registration
	// -------------------------------------------------------------------------

	public function register_menus() {
		$menu_icon = VELTEZ_AI_URL . 'assets/img/menu-icon-white.png';

		add_menu_page(
			__( 'veltez', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'veltez', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai',
			array( $this, 'render_chat_history' ),
			$menu_icon,
			58
		);

		// First sub-menu replaces the auto-duplicate top-level entry.
		$this->hook_chat_history = (string) add_submenu_page(
			'veltez-ai',
			__( 'Chat History', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'Chat History', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai',
			array( $this, 'render_chat_history' )
		);

		$this->hook_enquiries = (string) add_submenu_page(
			'veltez-ai',
			__( 'Enquiries', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'Enquiries', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-enquiries',
			array( $this, 'render_enquiries' )
		);

		$this->hook_ip_blocklist = (string) add_submenu_page(
			'veltez-ai',
			__( 'IP Blocklist', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'IP Blocklist', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-ip-blocklist',
			array( $this, 'render_ip_blocklist' )
		);

		$this->hook_quick_replies = (string) add_submenu_page(
			'veltez-ai',
			__( 'Quick Replies', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'Quick Replies', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-quick-replies',
			array( $this, 'render_quick_replies' )
		);

		$this->hook_top_requests = (string) add_submenu_page(
			'veltez-ai',
			__( 'Top Requests', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'Top Requests', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-top-requests',
			array( $this, 'render_top_requests' )
		);

		$this->hook_ai_errors = (string) add_submenu_page(
			'veltez-ai',
			__( 'AI Error Log', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'AI Error Log', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-errors',
			array( $this, 'render_ai_errors' )
		);

		$this->hook_info = (string) add_submenu_page(
			'veltez-ai',
			__( 'Plugin Guide', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'Plugin Guide', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-info',
			array( $this, 'render_info' )
		);

		$this->hook_settings = (string) add_submenu_page(
			'veltez-ai',
			__( 'veltez Settings', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			__( 'Settings', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ),
			'manage_options',
			'veltez-ai-settings',
			array( $this->settings, 'render_settings_page' )
		);
	}

	// -------------------------------------------------------------------------
	// Hook suffix accessors (used by Plugin for asset enqueueing)
	// -------------------------------------------------------------------------

	public function get_settings_hook() {
		return $this->hook_settings;
	}

	public function get_top_requests_hook() {
		return $this->hook_top_requests;
	}

	public function get_all_hooks() {
		return array( $this->hook_chat_history, $this->hook_enquiries, $this->hook_ip_blocklist, $this->hook_quick_replies, $this->hook_top_requests, $this->hook_ai_errors, $this->hook_info, $this->hook_settings );
	}

	// -------------------------------------------------------------------------
	// Page renderers
	// -------------------------------------------------------------------------

	public function render_chat_history() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}

		// Single-session detail view.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only admin display, protected by current_user_can
		$session_id = isset( $_GET['session'] ) ? sanitize_text_field( wp_unslash( $_GET['session'] ) ) : '';
		if ( '' !== $session_id ) {
			$messages = $this->chat_logger->get_session_messages( $session_id );
			$back_url = admin_url( 'admin.php?page=veltez-ai' );
			require VELTEZ_AI_PATH . 'admin/chat-session-detail-page.php';
			return;
		}

		// List view with filters.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only admin display, protected by current_user_can
		$filters = array(
			'ip'        => isset( $_GET['filter_ip'] )        ? sanitize_text_field( wp_unslash( $_GET['filter_ip'] ) )        : '',
			'name'      => isset( $_GET['filter_name'] )      ? sanitize_text_field( wp_unslash( $_GET['filter_name'] ) )      : '',
			'date_from' => isset( $_GET['filter_date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_date_from'] ) ) : '',
			'date_to'   => isset( $_GET['filter_date_to'] )   ? sanitize_text_field( wp_unslash( $_GET['filter_date_to'] ) )   : '',
		);

		$per_page     = 20;
		$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		// phpcs:enable
		$offset       = ( $current_page - 1 ) * $per_page;
		$total        = $this->chat_logger->count_sessions( $filters );
		$sessions     = $this->chat_logger->get_sessions( $filters, $per_page, $offset );

		require VELTEZ_AI_PATH . 'admin/chat-history-page.php';
	}

	public function render_ip_blocklist() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}

		$ip_blocker = $this->ip_blocker;
		require VELTEZ_AI_PATH . 'admin/ip-blocklist-page.php';
	}

	public function render_top_requests() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}

		global $wpdb;

		// ── Filters ───────────────────────────────────────────────────────────
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$search       = isset( $_GET['search'] )      ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
		$filter_type  = isset( $_GET['filter_type'] ) ? sanitize_key( $_GET['filter_type'] )                 : 'all';
		$filter_date  = isset( $_GET['filter_date'] ) ? sanitize_key( $_GET['filter_date'] )                 : 'all';
		$current_page = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
		$export_csv   = isset( $_GET['export'] ) && 'csv' === $_GET['export'];
		// phpcs:enable

		$per_page = 20;

		// ── CSV export — validate nonce then stream and exit ──────────────────
		if ( $export_csv ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			check_admin_referer( 'veltez_export_top_requests' );
			$this->export_top_requests_csv( $search, $filter_date );
			exit;
		}

		// ── Fetch aggregated rows (date + search filters applied inline) ──────────
		// Date conditions are hardcoded strings — no user input. Search uses prepare().
		$all_rows = $this->query_top_requests( $wpdb, $filter_date, $search, 500 );

		// ── Response-type detection ───────────────────────────────────────────
		$qr_response_set = $this->quick_reply_service->get_response_set();

		if ( 'quick_reply' === $filter_type ) {
			$all_rows = array_values( array_filter(
				$all_rows,
				static function ( $r ) use ( $qr_response_set ) {
					return isset( $qr_response_set[ trim( $r->last_response ) ] );
				}
			) );
		} elseif ( 'ai' === $filter_type ) {
			$all_rows = array_values( array_filter(
				$all_rows,
				static function ( $r ) use ( $qr_response_set ) {
					return ! isset( $qr_response_set[ trim( $r->last_response ) ] );
				}
			) );
		}

		$total_rows = count( $all_rows );
		$offset     = ( $current_page - 1 ) * $per_page;
		$rows       = array_slice( $all_rows, $offset, $per_page );

		$quick_reply_service = $this->quick_reply_service;

		require VELTEZ_AI_PATH . 'admin/top-requests-page.php';
	}

	/**
	 * Execute the top-requests aggregate query.
	 *
	 * All date conditions are hardcoded SQL strings (no user input).
	 * Search uses $wpdb->prepare() so the full returned string is "prepared".
	 *
	 * @param \wpdb  $wpdb
	 * @param string $filter_date '7' | '30' | anything else = all time.
	 * @param string $search      Sanitised search term (empty = no filter).
	 * @param int    $limit       Maximum rows to return.
	 * @return array
	 */
	private function query_top_requests( $wpdb, $filter_date, $search, $limit ) {
		$select = 'SELECT LOWER(TRIM(user_message)) AS query, COUNT(*) AS total, MAX(ai_response) AS last_response'
			. " FROM `{$wpdb->prefix}veltez_chat_logs`";

		if ( '' !== $search ) {
			// Combine date filter + search using prepare() so the result is fully prepared.
			if ( '7' === $filter_date ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"{$select} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
						. ' GROUP BY LOWER(TRIM(user_message))'
						. ' HAVING LOWER(TRIM(user_message)) LIKE %s'
						. ' ORDER BY total DESC LIMIT ' . absint( $limit ),
						'%' . $wpdb->esc_like( strtolower( $search ) ) . '%'
					)
				);
			} elseif ( '30' === $filter_date ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"{$select} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
						. ' GROUP BY LOWER(TRIM(user_message))'
						. ' HAVING LOWER(TRIM(user_message)) LIKE %s'
						. ' ORDER BY total DESC LIMIT ' . absint( $limit ),
						'%' . $wpdb->esc_like( strtolower( $search ) ) . '%'
					)
				);
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"{$select} GROUP BY LOWER(TRIM(user_message))"
						. ' HAVING LOWER(TRIM(user_message)) LIKE %s'
						. ' ORDER BY total DESC LIMIT ' . absint( $limit ),
						'%' . $wpdb->esc_like( strtolower( $search ) ) . '%'
					)
				);
			}
		} else {
			// No search — all conditions are hardcoded, no prepare() needed.
			if ( '7' === $filter_date ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$rows = $wpdb->get_results( "{$select} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY LOWER(TRIM(user_message)) ORDER BY total DESC LIMIT " . absint( $limit ) );
			} elseif ( '30' === $filter_date ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$rows = $wpdb->get_results( "{$select} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY LOWER(TRIM(user_message)) ORDER BY total DESC LIMIT " . absint( $limit ) );
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$rows = $wpdb->get_results( "{$select} GROUP BY LOWER(TRIM(user_message)) ORDER BY total DESC LIMIT " . absint( $limit ) );
			}
		}

		return is_array( $rows ) ? $rows : array();
	}

	/** Stream a CSV of all top-request rows and exit. */
	private function export_top_requests_csv( $search, $filter_date ) {
		global $wpdb;

		$rows = $this->query_top_requests( $wpdb, $filter_date, $search, 5000 );

		$filename = 'veltez-top-requests-' . gmdate( 'Y-m-d' ) . '.csv';

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$lines   = array();
		$lines[] = $this->csv_line( array( 'Query', 'Count', 'Response Preview' ) );

		foreach ( $rows as $row ) {
			$lines[] = $this->csv_line( array(
				$this->sanitize_csv_cell( $row->query ),
				(int) $row->total,
				$this->sanitize_csv_cell( mb_substr( $row->last_response, 0, 200 ) ),
			) );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw CSV bytes, not HTML
		echo implode( "\r\n", $lines ) . "\r\n";
	}

	/**
	 * Format one CSV row as a quoted, comma-separated string.
	 *
	 * @param array $fields Field values (mixed types accepted).
	 * @return string
	 */
	private function csv_line( array $fields ) {
		$parts = array();
		foreach ( $fields as $field ) {
			$parts[] = '"' . str_replace( '"', '""', (string) $field ) . '"';
		}
		return implode( ',', $parts );
	}

	/**
	 * Prevent CSV formula injection (a.k.a. CSV injection / formula injection).
	 * Spreadsheet software treats cells starting with =, +, -, @ as formulas.
	 * Prefixing with a tab neutralises the cell without altering visible content.
	 *
	 * @param string $value Raw cell value.
	 * @return string Safe cell value.
	 */
	private function sanitize_csv_cell( $value ) {
		$value = (string) $value;
		if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			$value = "\t" . $value;
		}
		return $value;
	}

	public function render_quick_replies() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}

		$quick_reply_service = $this->quick_reply_service;
		require VELTEZ_AI_PATH . 'admin/quick-replies-page.php';
	}

	public function render_info() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}
		require VELTEZ_AI_PATH . 'admin/info-page.php';
	}

	public function render_ai_errors() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}

		$per_page     = 30;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only admin display, protected by current_user_can
		$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		$offset       = ( $current_page - 1 ) * $per_page;
		$total        = $this->ai_error_logger->count_errors();
		$errors       = $this->ai_error_logger->get_errors( $per_page, $offset );

		require VELTEZ_AI_PATH . 'admin/ai-errors-page.php';
	}

	public function render_enquiries() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce' ) );
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only admin display, protected by current_user_can
		$filter_name  = isset( $_GET['filter_name'] )  ? sanitize_text_field( wp_unslash( $_GET['filter_name'] ) )    : '';
		$filter_email = isset( $_GET['filter_email'] )  ? sanitize_email( wp_unslash( $_GET['filter_email'] ?? '' ) )  : '';
		$filter_date  = isset( $_GET['filter_date'] )   ? sanitize_text_field( wp_unslash( $_GET['filter_date'] ) )    : '';
		$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		// phpcs:enable

		$per_page = 20;

		$query_args = array(
			'post_type'      => 'veltez_enquiry',
			'post_status'    => 'private',
			'posts_per_page' => $per_page,
			'paged'          => $current_page,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$meta_conditions = array();

		if ( '' !== $filter_name ) {
			$meta_conditions[] = array(
				'key'     => '_veltez_name',
				'value'   => $filter_name,
				'compare' => 'LIKE',
			);
		}

		if ( '' !== $filter_email ) {
			$meta_conditions[] = array(
				'key'     => '_veltez_email',
				'value'   => $filter_email,
				'compare' => 'LIKE',
			);
		}

		if ( ! empty( $meta_conditions ) ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- admin-only, low volume
			$query_args['meta_query'] = array_merge(
				array( 'relation' => 'AND' ),
				$meta_conditions
			);
		}

		if ( '' !== $filter_date && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $filter_date ) ) {
			$query_args['date_query'] = array(
				array(
					'year'  => (int) substr( $filter_date, 0, 4 ),
					'month' => (int) substr( $filter_date, 5, 2 ),
					'day'   => (int) substr( $filter_date, 8, 2 ),
				),
			);
		}

		$query     = new \WP_Query( $query_args );
		$enquiries = $query->posts;
		$total     = $query->found_posts;

		require VELTEZ_AI_PATH . 'admin/enquiries-page.php';
	}
}
