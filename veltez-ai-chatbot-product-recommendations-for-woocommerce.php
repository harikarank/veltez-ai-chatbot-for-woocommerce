<?php
/**
 * Plugin Name: veltez - AI Chatbot & Product Recommendations for WooCommerce
 * Plugin URI:  https://ai.veltez.com/
 * Description: AI-powered WooCommerce chatbot and shopping assistant that helps customers find products, get instant answers, and boost sales with smart recommendations.
 * Version:     1.0.1
 * Author:      Harikaran
 * Author URI:  https://harikaran.com/
 * Text Domain: veltez-ai-chatbot-product-recommendations-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Tested up to: 6.9
 * Tested PHP up to: 8.4
 * WC requires at least: 7.8
 * WC tested up to: 10.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

define( 'VELTEZ_AI_VERSION', '1.0.0' );
define( 'VELTEZ_AI_FILE', __FILE__ );
define( 'VELTEZ_AI_PATH', plugin_dir_path( __FILE__ ) );
define( 'VELTEZ_AI_URL', plugin_dir_url( __FILE__ ) );

require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-settings.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-chat-logger.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-ai-error-logger.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-ip-blocker.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-quick-reply-service.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-admin-menu.php';
require_once VELTEZ_AI_PATH . 'includes/woocommerce-handler.php';
require_once VELTEZ_AI_PATH . 'includes/api-handler.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-mcp-tools.php';
require_once VELTEZ_AI_PATH . 'includes/class-aiwoo-assistant-plugin.php';

register_activation_hook(
	VELTEZ_AI_FILE,
	static function () {
		\Veltez\Chat_Logger::create_table();
		\Veltez\Quick_Reply_Service::create_table();
		\Veltez\Quick_Reply_Service::seed_on_activation();
		\Veltez\AI_Error_Logger::create_table();
	}
);

\Veltez\Plugin::instance();
