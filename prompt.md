Use Prefixes for declarations, globals and stored data

ℹ️ Why it matters: Prefixing avoid naming collisions with other themes, plugins, or WordPress core functions.
A prefix is a string placed in front of a name to avoid collisions. It must be at least 4 characters long, feel distinct and unique to the plugin (do not use common words), and be separated by an underscore or dash.
Please check the official WordPress docs on avoiding name collisions.

🔍 Identify not prefixed names: Look for any name that is used in a place where it can create a collision.
Type of element 	Affected elements
Declarations 	Functions, classes, etc (if not under a namespace)
Globals 	Global variables, namespaces, define().
Data storage 	update_option(), set_transient(), update_post_meta(), etc.
WordPress declarations 	add_shortcode(), register_post_type(), add_menu_page(), wp_register_script(), wp_localize_script(), add_action( 'wp_ajax_...' ), etc.

If the defined name for that is not prefixed, that’s a potential issue! 🕵️

🛠 Fix it: Always prefix those names, for example if your plugin is called "veltez – AI Chatbot & Product Recommendations for WooCommerce" then you could use names like these:

    function veltaich_save_post(){ ... }
    class VELTAICH_Admin { ... }
    update_option( 'veltaich_options', $options );
    register_setting( 'veltaich_settings', 'veltaich_user_id', ... );
    define( 'VELTAICH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    global $veltaich_options;
    add_action('wp_ajax_veltaich_save_data', ... );
    namespace veltez\veltezaichatbotproductrecommendationsforwoocommerce;

Our tool has automatically detected these common issues, please check your code as there may be more names needing a prefix:


# Using the common word "ai" as a prefix.
veltez-ai-chatbot-product-recommendations-for-woocommerce.php:23 define('AI_WOO_ASSISTANT_VERSION', '1.0.0');
veltez-ai-chatbot-product-recommendations-for-woocommerce.php:24 define('AI_WOO_ASSISTANT_FILE', __FILE__);
veltez-ai-chatbot-product-recommendations-for-woocommerce.php:25 define('AI_WOO_ASSISTANT_PATH', plugin_dir_path(__FILE__));
veltez-ai-chatbot-product-recommendations-for-woocommerce.php:26 define('AI_WOO_ASSISTANT_URL', plugin_dir_url(__FILE__));
includes/class-aiwoo-assistant-catalog-service.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-chat-logger.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-provider-interface.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-settings.php:120 register_setting('ai_woo_assistant', $this->option_name, array('type' => 'array', 'sanitize_callback' => array($this, 'sanitize_settings'), 'default' => $this->defaults));
includes/class-aiwoo-assistant-settings.php:8 namespace AIWooAssistant
includes/api-handler.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-admin-menu.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-claude-provider.php:9 namespace AIWooAssistant
includes/class-aiwoo-assistant-mcp-tools.php:278 set_transient($cache_key, $result, self::CACHE_TTL);
includes/class-aiwoo-assistant-mcp-tools.php:344 set_transient($cache_key, $result, self::CACHE_TTL);
includes/class-aiwoo-assistant-mcp-tools.php:398 set_transient($cache_key, $result, self::CACHE_TTL);
includes/class-aiwoo-assistant-mcp-tools.php:19 namespace AIWooAssistant
includes/class-aiwoo-assistant-ai-error-logger.php:15 namespace AIWooAssistant
includes/woocommerce-handler.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-gemini-provider.php:9 namespace AIWooAssistant
includes/class-aiwoo-assistant-ip-blocker.php:108 update_option(self::OPTION_KEY, $list, false);
includes/class-aiwoo-assistant-ip-blocker.php:127 update_option(self::OPTION_KEY, $list, false);
includes/class-aiwoo-assistant-ip-blocker.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-quick-reply-service.php:88 update_option(self::SEED_OPTION, '1', true);
includes/class-aiwoo-assistant-quick-reply-service.php:93 update_option(self::SEED_OPTION, '1', true);
includes/class-aiwoo-assistant-quick-reply-service.php:131 update_option(self::SEED_OPTION, '1', true);
includes/class-aiwoo-assistant-quick-reply-service.php:668 set_transient(self::CACHE_KEY, $rules, self::CACHE_TTL);
includes/class-aiwoo-assistant-quick-reply-service.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-ajax-controller.php:331 set_transient($key, $count + 1, 90);
includes/class-aiwoo-assistant-ajax-controller.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-openai-provider.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-plugin.php:69 add_action('wp_ajax_ai_woo_assistant_chat', array($this, 'handle_ajax_chat'));
includes/class-aiwoo-assistant-plugin.php:70 add_action('wp_ajax_nopriv_ai_woo_assistant_chat', array($this, 'handle_ajax_chat'));
includes/class-aiwoo-assistant-plugin.php:71 add_action('wp_ajax_ai_woo_assistant_enquiry', array($this, 'handle_ajax_enquiry'));
includes/class-aiwoo-assistant-plugin.php:72 add_action('wp_ajax_nopriv_ai_woo_assistant_enquiry', array($this, 'handle_ajax_enquiry'));
includes/class-aiwoo-assistant-plugin.php:328 register_post_type('aiwoo_enquiry', array('labels' => array('name' => __('veltez Enquiries', 'veltez-ai-chatbot-product-recommendations-for-woocommerce')), 'public' => false, 'show_ui' => false, 'show_in_menu' => false, 'supports' => array('title', 'editor'), 'capability_type' => 'post', 'exclude_from_search' => true, 'publicly_queryable' => false));
includes/class-aiwoo-assistant-plugin.php:378 wp_localize_script('ai-woo-assistant-widget', 'AIWooAssistant', array('ajaxUrl' => esc_url_raw(admin_url('admin-ajax.php')), 'nonce' => wp_create_nonce('ai_woo_assistant_nonce'), 'actions' => array('chat' => 'ai_woo_assistant_chat', 'enquiry' => 'ai_woo_assistant_enquiry'), 'strings' => array('title' => __('veltez Shopping Assistant', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'companyName' => get_bloginfo('name'), 'subtitle' => __('Ask about products, comparisons, and buying advice.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'placeholder' => '' !== (string) $this->settings->get('chat_placeholder') ? (string) $this->settings->get('chat_placeholder') : __('Ask about products...', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'send' => __('Send', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'open' => __('Open veltez chat assistant', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'close' => __('Close chat assistant', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'typing' => __('veltez is thinking...', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'error' => __('The assistant is temporarily unavailable. Please try again.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'welcome' => $this->settings->get('welcome_message'), 'emptyValidation' => __('Enter a message before sending.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'enquiryIntro' => __('I could not find a strong product match yet. Share your details and our team can help directly.', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'enquiryName' => __('Name', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'enquiryEmail' => __('Email', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'enquiryMessage' => __('Message', 'veltez-ai-chatbot-product-recommendations-for-woocommerce'), 'enquirySubmit' => __('Send enquiry', 'veltez-ai-chatbot-product-recommendations-for-woocommerce')), 'ui' => array('primaryColor' => $this->settings->get('primary_color'), 'iconUrl' => $this->settings->get('chat_icon'), 'companyLogo' => $this->settings->get('company_logo'), 'employeePhoto' => $this->settings->get('employee_photo'), 'faviconUrl' => esc_url(AI_WOO_ASSISTANT_URL . 'assets/img/favicon.svg')), 'storeContext' => array('currencySymbol' => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : get_option('woocommerce_currency', 'USD'), 'pageUrl' => esc_url_raw(home_url(add_query_arg(array(), $GLOBALS['wp']->request ?? ''))), 'product' => $this->get_catalog_service()->get_current_product_context()), 'featureFlags' => array('hasWooCommerce' => class_exists('WooCommerce')), 'widgetStateKey' => 'ai_woo_assistant_widget_state', 'settings' => array('maxMessageLength' => max(10, (int) $this->settings->get('max_message_length')), 'autoOpenDelay' => '' !== (string) $this->settings->get('auto_open_delay') ? (int) $this->settings->get('auto_open_delay') : 0)));
includes/class-aiwoo-assistant-plugin.php:8 namespace AIWooAssistant
includes/class-aiwoo-assistant-chat-service.php:11 namespace AIWooAssistant