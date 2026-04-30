<?php
/**
 * Uninstall routine — runs when the plugin is deleted from the WordPress admin.
 *
 * @package Veltez
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Remove plugin settings.
delete_option( 'veltez_ai_settings' );

// Remove IP blocklist.
delete_option( 'veltez_blocked_ips' );

// Remove DB version and seed flags.
delete_option( 'veltez_db_version' );
delete_option( 'veltez_qr_db_version' );
delete_option( 'veltez_qr_seeded' );
delete_option( 'veltez_ai_error_log_db_version' );

// Remove quick reply transient cache.
delete_transient( 'veltez_quick_replies_cache' );

global $wpdb;

// Drop chat log table.
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'veltez_chat_logs`' );

// Drop quick replies table.
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'veltez_quick_replies`' );

// Drop AI error log table.
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'veltez_ai_error_logs`' );

// Remove all stored enquiry posts and their meta.
$veltez_enquiry_ids = get_posts(
	array(
		'post_type'      => 'veltez_enquiry',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $veltez_enquiry_ids as $post_id ) {
	wp_delete_post( (int) $post_id, true );
}
