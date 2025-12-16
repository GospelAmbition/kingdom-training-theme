<?php
/**
 * Kingdom.Training Theme Functions
 * 
 * Traditional WordPress PHP theme for Media to Disciple Making Movements training.
 * Uses Tailwind CSS, Polylang for multilingual support, and custom post types.
 */

// ============================================================================
// THEME QUERY CLASSES AND HELPERS
// ============================================================================

// Include query classes
require_once get_template_directory() . '/includes/classes/class-kt-query-articles.php';
require_once get_template_directory() . '/includes/classes/class-kt-query-tools.php';
require_once get_template_directory() . '/includes/classes/class-kt-query-courses.php';

// Include helper functions
require_once get_template_directory() . '/includes/helpers/kt-translations.php';
require_once get_template_directory() . '/includes/helpers/kt-language.php';
require_once get_template_directory() . '/includes/helpers/kt-formatting.php';
require_once get_template_directory() . '/includes/helpers/kt-seo.php';

// ============================================================================
// TRANSLATION AUTOMATION FEATURES
// ============================================================================

// Include translation automation classes
// Note: Logger must be loaded first as other classes depend on it
require_once get_template_directory() . '/includes/class-gaal-translation-logger.php';
require_once get_template_directory() . '/includes/admin-translation-settings.php';
require_once get_template_directory() . '/includes/class-gaal-translation-api.php';
require_once get_template_directory() . '/includes/class-gaal-google-translate-api.php';
require_once get_template_directory() . '/includes/class-gaal-llm-api.php';
require_once get_template_directory() . '/includes/class-gaal-translation-job.php';
require_once get_template_directory() . '/includes/class-gaal-content-processor.php';
require_once get_template_directory() . '/includes/class-gaal-translation-engine.php';
require_once get_template_directory() . '/includes/class-gaal-translation-scanner.php';
require_once get_template_directory() . '/includes/class-gaal-batch-translator.php';
require_once get_template_directory() . '/includes/class-gaal-translation-dashboard.php';

// ============================================================================
// PERFORMANCE OPTIMIZATIONS
// ============================================================================

/**
 * Add caching headers for REST API responses
 * This significantly reduces server load for repeated requests
 */
function gaal_add_rest_cache_headers($response, $server, $request) {
    // Only cache GET requests
    if ($request->get_method() !== 'GET') {
        return $response;
    }
    
    // Get the route
    $route = $request->get_route();
    
    // Skip caching for auth endpoints
    if (strpos($route, '/auth/') !== false) {
        return $response;
    }
    
    // Set cache headers for public content (5 minutes for content, 1 hour for translations)
    $cache_time = 300; // 5 minutes default
    
    if (strpos($route, '/translations') !== false) {
        $cache_time = 3600; // 1 hour for translations
    } elseif (strpos($route, '/site-info') !== false) {
        $cache_time = 3600; // 1 hour for site info
    }
    
    $response->header('Cache-Control', 'public, max-age=' . $cache_time);
    $response->header('Vary', 'Accept-Encoding');
    
    return $response;
}
add_filter('rest_post_dispatch', 'gaal_add_rest_cache_headers', 10, 3);

/**
 * Disable unnecessary WordPress features for headless setup
 * This reduces PHP execution time on every request
 */
function gaal_disable_unnecessary_features() {
    // Remove oEmbed discovery links
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    
    // Remove REST API link in header (not needed, API still works)
    remove_action('wp_head', 'rest_output_link_wp_head');
    
    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Remove WordPress version
    remove_action('wp_head', 'wp_generator');
    
    // Remove wlwmanifest link
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
    
    // Remove feed links
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    
    // Disable XML-RPC (security + performance)
    add_filter('xmlrpc_enabled', '__return_false');
    
    // Remove XML-RPC header
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'gaal_disable_unnecessary_features');

/**
 * Disable self-pingbacks (minor performance improvement)
 */
function gaal_disable_self_pingback(&$links) {
    $home = get_option('home');
    foreach ($links as $key => $link) {
        if (strpos($link, $home) !== false) {
            unset($links[$key]);
        }
    }
}
add_action('pre_ping', 'gaal_disable_self_pingback');

/**
 * Limit post revisions to reduce database bloat
 */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 5);
}

/**
 * Optimize heartbeat API (reduces admin AJAX calls)
 */
function gaal_optimize_heartbeat($settings) {
    $settings['interval'] = 60; // 60 seconds instead of 15
    return $settings;
}
add_filter('heartbeat_settings', 'gaal_optimize_heartbeat');

/**
 * Disable heartbeat on frontend (not needed for headless)
 */
function gaal_disable_frontend_heartbeat() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}
add_action('init', 'gaal_disable_frontend_heartbeat', 1);

// Enable REST API CORS
function gaal_enable_cors() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        // Get the origin from the request
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
        
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce, X-Requested-With');
        header('Access-Control-Expose-Headers: X-WP-Nonce');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            status_header(200);
            exit;
        }
        
        return $value;
    });
    
    // Ensure cookies are sent with REST API requests
    add_filter('rest_authentication_errors', function($result, $server = null, $request = null) {
        // Allow our custom auth endpoint to work without requiring authentication
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/gaal/v1/auth/me') !== false) {
            return true; // Allow the request
        }
        
        // Allow Gospel Ambition Web Forms endpoints to work without authentication
        if ($request && method_exists($request, 'get_route')) {
            $route = $request->get_route();
            if (strpos($route, '/go-webform/') === 0) {
                return true; // Allow access
            }
        }
        // Fallback: check REQUEST_URI if route is not available
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/go-webform/') !== false) {
            return true; // Allow the request
        }
        
        return $result;
    }, 20, 3);
}
add_action('rest_api_init', 'gaal_enable_cors');

// Theme Setup
function gaal_theme_setup() {
    // Add theme support for various features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'kingdom-training'),
        'footer' => __('Footer Menu', 'kingdom-training'),
    ));

    // Add excerpt support to pages
    add_post_type_support('page', 'excerpt');
}
add_action('after_setup_theme', 'gaal_theme_setup');

/**
 * Remove WordPress admin bar from frontend
 */
function kt_remove_admin_bar() {
    return false;
}
add_filter('show_admin_bar', 'kt_remove_admin_bar');

// Set permalink structure to "Post name" (/%postname%/)
// This ensures REST API endpoints work correctly
function gaal_set_permalink_structure() {
    // Check if permalink structure is already set to post name
    $current_structure = get_option('permalink_structure');
    
    // Post name structure is '/%postname%/'
    if ($current_structure !== '/%postname%/') {
        // Set permalink structure to post name
        update_option('permalink_structure', '/%postname%/');
        
        // Flush rewrite rules to ensure changes take effect
        flush_rewrite_rules(false);
    }
}
// Run on theme activation (runs once when theme is activated)
add_action('after_switch_theme', 'gaal_set_permalink_structure');
// Run once on admin init to fix existing installations (only if not already set)
// Use a transient to avoid running on every admin page load
add_action('admin_init', function() {
    $transient_key = 'gaal_permalink_structure_set';
    if (!get_transient($transient_key)) {
        gaal_set_permalink_structure();
        // Set transient for 1 hour to avoid repeated checks
        set_transient($transient_key, true, HOUR_IN_SECONDS);
    }
});

/**
 * Modify page permalinks to include /page/ prefix
 * This matches the React router structure for SimplePage component
 * Excludes special pages that have their own routes (privacy, etc.)
 */
function gaal_modify_page_permalink($link, $post_id) {
    $post = get_post($post_id);
    
    // Only modify if this is a page
    if (!$post || $post->post_type !== 'page') {
        return $link;
    }
    
    // Exclude pages that have dedicated React routes
    $excluded_slugs = array('privacy');
    if (in_array($post->post_name, $excluded_slugs)) {
        return $link;
    }
    
    // Get the site URL
    $site_url = home_url();
    
    // Check if this is a Polylang translated page (has language prefix)
    $lang_slug = '';
    if (function_exists('pll_get_post_language')) {
        $post_lang = pll_get_post_language($post_id, 'slug');
        $default_lang = pll_default_language('slug');
        
        // Only add language prefix if it's not the default language
        if ($post_lang && $post_lang !== $default_lang) {
            $lang_slug = $post_lang . '/';
        }
    }
    
    // Build new permalink with /page/ prefix
    $new_link = trailingslashit($site_url) . $lang_slug . 'page/' . $post->post_name . '/';
    
    return $new_link;
}
add_filter('page_link', 'gaal_modify_page_permalink', 10, 2);

// Register Custom Post Types
// Register Custom Post Types and Taxonomies
function gaal_register_custom_post_types() {
    
    // Register Custom Taxonomies
    
    // Strategy Course Category
    register_taxonomy('strategy_course_category', 'strategy_course', array(
        'labels' => array(
            'name' => __('Course Categories', 'kingdom-training'),
            'singular_name' => __('Course Category', 'kingdom-training'),
            'menu_name' => __('Categories', 'kingdom-training'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rest_base' => 'strategy-course-categories',
        'rewrite' => array('slug' => 'strategy-course-category'),
    ));

    // Article Category
    register_taxonomy('article_category', 'article', array(
        'labels' => array(
            'name' => __('Article Categories', 'kingdom-training'),
            'singular_name' => __('Article Category', 'kingdom-training'),
            'menu_name' => __('Categories', 'kingdom-training'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rest_base' => 'article-categories',
        'rewrite' => array('slug' => 'article-category'),
    ));

    // Tool Category
    register_taxonomy('tool_category', 'tool', array(
        'labels' => array(
            'name' => __('Tool Categories', 'kingdom-training'),
            'singular_name' => __('Tool Category', 'kingdom-training'),
            'menu_name' => __('Categories', 'kingdom-training'),
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rest_base' => 'tool-categories',
        'rewrite' => array('slug' => 'tool-category'),
    ));

    // Strategy Course Post Type
    register_post_type('strategy_course', array(
        'labels' => array(
            'name' => __('Strategy Course', 'kingdom-training'),
            'singular_name' => __('Strategy Course', 'kingdom-training'),
            'add_new' => __('Add New Strategy Course', 'kingdom-training'),
            'add_new_item' => __('Add New Strategy Course', 'kingdom-training'),
            'edit_item' => __('Edit Strategy Course', 'kingdom-training'),
            'new_item' => __('New Strategy Course', 'kingdom-training'),
            'view_item' => __('View Strategy Course', 'kingdom-training'),
            'search_items' => __('Search Strategy Courses', 'kingdom-training'),
            'not_found' => __('No strategy courses found', 'kingdom-training'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rest_base' => 'strategy-course',
        'menu_icon' => 'dashicons-book-alt',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'strategy-course'),
    ));

    // Articles Post Type (enhanced from default posts)
    register_post_type('article', array(
        'labels' => array(
            'name' => __('Articles', 'kingdom-training'),
            'singular_name' => __('Article', 'kingdom-training'),
            'add_new' => __('Add New Article', 'kingdom-training'),
            'add_new_item' => __('Add New Article', 'kingdom-training'),
            'edit_item' => __('Edit Article', 'kingdom-training'),
            'new_item' => __('New Article', 'kingdom-training'),
            'view_item' => __('View Article', 'kingdom-training'),
            'search_items' => __('Search Articles', 'kingdom-training'),
            'not_found' => __('No articles found', 'kingdom-training'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rest_base' => 'articles',
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'author', 'revisions'),
        'rewrite' => array('slug' => 'articles'),
        'taxonomies' => array('article_category'),
    ));

    // Tools Post Type
    register_post_type('tool', array(
        'labels' => array(
            'name' => __('Tools', 'kingdom-training'),
            'singular_name' => __('Tool', 'kingdom-training'),
            'add_new' => __('Add New Tool', 'kingdom-training'),
            'add_new_item' => __('Add New Tool', 'kingdom-training'),
            'edit_item' => __('Edit Tool', 'kingdom-training'),
            'new_item' => __('New Tool', 'kingdom-training'),
            'view_item' => __('View Tool', 'kingdom-training'),
            'search_items' => __('Search Tools', 'kingdom-training'),
            'not_found' => __('No tools found', 'kingdom-training'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rest_base' => 'tools',
        'menu_icon' => 'dashicons-admin-tools',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'tools'),
        'taxonomies' => array('tool_category'),
    ));
}
add_action('init', 'gaal_register_custom_post_types');

// Add custom fields to REST API
function gaal_register_custom_fields() {
    // Add featured image URL to REST API
    register_rest_field(
        array('post', 'page', 'strategy_course', 'article', 'tool'),
        'featured_image_url',
        array(
            'get_callback' => function($object) {
                if ($object['featured_media']) {
                    $image = wp_get_attachment_image_src($object['featured_media'], 'full');
                    return $image ? $image[0] : null;
                }
                return null;
            },
            'schema' => array(
                'description' => __('Featured image URL', 'kingdom-training'),
                'type' => 'string',
            ),
        )
    );

    // Add featured image sizes to REST API for responsive images
    register_rest_field(
        array('post', 'page', 'strategy_course', 'article', 'tool'),
        'featured_image_sizes',
        array(
            'get_callback' => function($object) {
                if ($object['featured_media']) {
                    $sizes = array();
                    $image_sizes = array('thumbnail', 'medium', 'medium_large', 'large', 'full');
                    foreach ($image_sizes as $size) {
                        $image = wp_get_attachment_image_src($object['featured_media'], $size);
                        if ($image) {
                            $sizes[$size] = array(
                                'url' => $image[0],
                                'width' => $image[1],
                                'height' => $image[2],
                            );
                        }
                    }
                    return !empty($sizes) ? $sizes : null;
                }
                return null;
            },
            'schema' => array(
                'description' => __('Featured image sizes for responsive images', 'kingdom-training'),
                'type' => 'object',
            ),
        )
    );

    // Add author information to REST API
    register_rest_field(
        array('post', 'article', 'strategy_course', 'tool'),
        'author_info',
        array(
            'get_callback' => function($object) {
                $author_id = $object['author'];
                return array(
                    'name' => get_the_author_meta('display_name', $author_id),
                    'avatar' => get_avatar_url($author_id),
                    'bio' => get_the_author_meta('description', $author_id),
                );
            },
            'schema' => array(
                'description' => __('Author information', 'kingdom-training'),
                'type' => 'object',
            ),
        )
    );

    // Add steps meta field to REST API for strategy courses
    register_rest_field(
        'strategy_course',
        'steps',
        array(
            'get_callback' => function($object) {
                $steps = get_post_meta($object['id'], 'steps', true);
                return $steps ? intval($steps) : null;
            },
            'update_callback' => function($value, $object) {
                if (is_numeric($value) && $value >= 1 && $value <= 20) {
                    return update_post_meta($object->ID, 'steps', intval($value));
                }
                return false;
            },
            'schema' => array(
                'description' => __('Step number (1-20) for ordering strategy course content', 'kingdom-training'),
                'type' => 'integer',
                'context' => array('view', 'edit'),
            ),
        )
    );

    // Add language information to REST API (Polylang integration)
    // Only add if Polylang is active
    // 
    // Polylang Configuration Requirements:
    // 1. Ensure Polylang plugin is installed and activated
    // 2. In Polylang settings, configure URL structure to include language code in URL
    //    (Settings > Languages > URL modifications: "The language is set from content")
    // 3. In Polylang settings, mark custom post types as translatable:
    //    - Settings > Languages > Synchronization: Enable for strategy_course, article, tool
    // 4. Set default language (typically English) in Polylang settings
    // 5. Add languages (e.g., Spanish/es) in Polylang settings
    if (function_exists('pll_get_post_language')) {
        register_rest_field(
            array('post', 'page', 'strategy_course', 'article', 'tool'),
            'language',
            array(
                'get_callback' => function($object) {
                    $lang = pll_get_post_language($object['id'], 'slug');
                    return $lang ? $lang : null;
                },
                'schema' => array(
                    'description' => __('Language code (slug) for this post', 'kingdom-training'),
                    'type' => 'string',
                    'context' => array('view', 'edit'),
                ),
            )
        );

        // Add translations field (alternate language versions)
        register_rest_field(
            array('post', 'page', 'strategy_course', 'article', 'tool'),
            'translations',
            array(
                'get_callback' => function($object) {
                    $translations = pll_get_post_translations($object['id']);
                    if (empty($translations)) {
                        return array();
                    }
                    
                    $translation_data = array();
                    foreach ($translations as $lang_code => $translation_id) {
                        // Skip the current post itself
                        if ($translation_id == $object['id']) {
                            continue;
                        }
                        
                        $translation_post = get_post($translation_id);
                        if ($translation_post && $translation_post->post_status === 'publish') {
                            $translation_data[] = array(
                                'id' => $translation_id,
                                'slug' => $translation_post->post_name,
                                'language' => $lang_code,
                                'link' => get_permalink($translation_id),
                            );
                        }
                    }
                    
                    return $translation_data;
                },
                'schema' => array(
                    'description' => __('Array of alternate language versions of this post', 'kingdom-training'),
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object',
                        'properties' => array(
                            'id' => array('type' => 'integer'),
                            'slug' => array('type' => 'string'),
                            'language' => array('type' => 'string'),
                            'link' => array('type' => 'string', 'format' => 'uri'),
                        ),
                    ),
                    'context' => array('view', 'edit'),
                ),
            )
        );
    }
}
add_action('rest_api_init', 'gaal_register_custom_fields');

/**
 * Translate taxonomy term IDs in REST API queries based on language
 * 
 * When a category ID is provided in the query, this function translates it
 * to the corresponding category ID in the requested language using Polylang.
 * This ensures that category filtering works correctly across languages.
 */
function gaal_rest_api_translate_taxonomy_ids($args, $request) {
    $lang = $request->get_param('lang');
    
    if (empty($lang) || !function_exists('PLL')) {
        return $args;
    }
    
    // Verify the language exists
    $language = PLL()->model->get_language($lang);
    if (!$language) {
        return $args;
    }
    
    // Translate taxonomy term IDs in tax_query
    if (!empty($args['tax_query']) && is_array($args['tax_query'])) {
        foreach ($args['tax_query'] as $key => $tax_query) {
            if (!is_array($tax_query) || empty($tax_query['taxonomy']) || empty($tax_query['terms'])) {
                continue;
            }
            
            $taxonomy = $tax_query['taxonomy'];
            
            // Only translate if this is a translatable taxonomy
            if (!PLL()->model->is_translated_taxonomy($taxonomy)) {
                continue;
            }
            
            $field = isset($tax_query['field']) ? $tax_query['field'] : 'term_id';
            $terms = (array) $tax_query['terms'];
            $translated_terms = array();
            
            foreach ($terms as $term) {
                $translated_term = null;
                
                if ($field === 'term_id') {
                    // Translate term ID: get the translation of this term in the requested language
                    $source_term = get_term($term, $taxonomy);
                    if ($source_term && !is_wp_error($source_term)) {
                        // Get all translations of this term
                        $translations = PLL()->model->term->get_translations($source_term->term_id);
                        if (!empty($translations[$lang])) {
                            $translated_term = $translations[$lang];
                        }
                    }
                } else {
                    // For slug/name fields, look up the term and translate it
                    $source_terms = get_terms(array(
                        'taxonomy' => $taxonomy,
                        $field => $term,
                        'hide_empty' => false,
                        'lang' => '', // Get from any language
                    ));
                    
                    if (!empty($source_terms) && !is_wp_error($source_terms)) {
                        $source_term = reset($source_terms);
                        $translations = PLL()->model->term->get_translations($source_term->term_id);
                        if (!empty($translations[$lang])) {
                            $translated_term_obj = get_term($translations[$lang], $taxonomy);
                            if ($translated_term_obj && !is_wp_error($translated_term_obj)) {
                                $translated_term = $translated_term_obj->$field;
                            }
                        }
                    }
                }
                
                // Use translated term if available, otherwise keep original
                $translated_terms[] = $translated_term ? $translated_term : $term;
            }
            
            $args['tax_query'][$key]['terms'] = $translated_terms;
        }
    }
    
    return $args;
}

/**
 * REST API Language Filter for Custom Post Types
 * 
 * Polylang's REST API integration may not always filter by language parameter
 * for custom post types. This filter ensures the 'lang' query parameter works
 * correctly by modifying the query to use Polylang's taxonomy-based filtering.
 * 
 * When combined with other taxonomy filters (like categories), ensures proper
 * AND relation so both filters are applied correctly.
 */
function gaal_rest_api_language_filter($args, $request) {
    // First, translate any taxonomy term IDs to the requested language
    $args = gaal_rest_api_translate_taxonomy_ids($args, $request);
    
    $lang = $request->get_param('lang');
    
    if (!empty($lang) && function_exists('PLL')) {
        // Verify the language exists using Polylang's model
        $language = PLL()->model->get_language($lang);
        if ($language) {
            // Add tax_query to filter by language
            if (!isset($args['tax_query'])) {
                $args['tax_query'] = array();
            }
            
            // Store existing relation if set
            $existing_relation = isset($args['tax_query']['relation']) ? $args['tax_query']['relation'] : null;
            
            // Count existing taxonomy queries (excluding 'relation' key)
            $existing_queries = array_filter($args['tax_query'], function($item) {
                return is_array($item) && isset($item['taxonomy']);
            });
            
            // Add language filter
            $args['tax_query'][] = array(
                'taxonomy' => 'language',
                'field'    => 'slug',
                'terms'    => $lang,
            );
            
            // If there are multiple taxonomy queries, ensure AND relation
            // This is critical when combining category filters with language filters
            // WordPress REST API processes taxonomy params before this filter runs,
            // so we need to ensure proper relation when combining them
            if (count($existing_queries) > 0) {
                // Rebuild tax_query with relation at the start (WordPress convention)
                $all_queries = array_filter($args['tax_query'], function($item) {
                    return is_array($item) && isset($item['taxonomy']);
                });
                
                // Use AND relation to ensure both category and language filters apply
                $args['tax_query'] = array_merge(
                    array('relation' => 'AND'),
                    array_values($all_queries)
                );
            }
        }
    }
    
    return $args;
}
// Apply to our custom post types
// Note: Translation filter runs at priority 5 (before language filter at 10)
// to ensure taxonomy IDs are translated before language filtering is applied
add_filter('rest_article_query', 'gaal_rest_api_translate_taxonomy_ids', 5, 2);
add_filter('rest_strategy_course_query', 'gaal_rest_api_translate_taxonomy_ids', 5, 2);
add_filter('rest_tool_query', 'gaal_rest_api_translate_taxonomy_ids', 5, 2);
add_filter('rest_post_query', 'gaal_rest_api_translate_taxonomy_ids', 5, 2);
add_filter('rest_page_query', 'gaal_rest_api_translate_taxonomy_ids', 5, 2);

add_filter('rest_article_query', 'gaal_rest_api_language_filter', 10, 2);
add_filter('rest_strategy_course_query', 'gaal_rest_api_language_filter', 10, 2);
add_filter('rest_tool_query', 'gaal_rest_api_language_filter', 10, 2);
add_filter('rest_post_query', 'gaal_rest_api_language_filter', 10, 2);
add_filter('rest_page_query', 'gaal_rest_api_language_filter', 10, 2);

/**
 * Filter taxonomy terms (categories) by language in REST API
 * 
 * Ensures that when fetching categories via REST API with a lang parameter,
 * only categories in that language are returned.
 */
function gaal_rest_api_filter_taxonomy_by_language($args, $request) {
    $lang = $request->get_param('lang');
    
    if (!empty($lang) && function_exists('PLL')) {
        // Verify the language exists
        $language = PLL()->model->get_language($lang);
        if ($language) {
            // Set the language context for get_terms
            // Polylang will automatically filter terms by this language
            $args['lang'] = $lang;
        }
    }
    
    return $args;
}

// Apply language filtering to taxonomy REST API endpoints
// Note: WordPress uses the taxonomy name (not rest_base) for filter hooks: rest_{$taxonomy}_query
add_filter('rest_article_category_query', 'gaal_rest_api_filter_taxonomy_by_language', 10, 2);
add_filter('rest_tool_category_query', 'gaal_rest_api_filter_taxonomy_by_language', 10, 2);
add_filter('rest_strategy_course_category_query', 'gaal_rest_api_filter_taxonomy_by_language', 10, 2);

/**
 * Translation-Aware REST API Slug Lookup
 * 
 * When fetching a post by slug with a language parameter, if no post is found
 * in that language with that slug, this filter will look up the post in any
 * language and return its translation in the requested language.
 * 
 * This allows URLs like /pt/articles/english-slug to show the Portuguese 
 * translation even though the Portuguese post has a different slug.
 */
function gaal_rest_translation_slug_lookup($response, $handler, $request) {
    // Only process REST API collection responses
    if (!($response instanceof WP_REST_Response)) {
        return $response;
    }
    
    $data = $response->get_data();
    
    // Only process if we're returning an empty array (no results)
    if (!is_array($data) || !empty($data)) {
        return $response;
    }
    
    // Check if this is a custom post type query with slug and lang
    $route = $request->get_route();
    $slug = $request->get_param('slug');
    $lang = $request->get_param('lang');
    
    // Must have both slug and lang parameters
    if (empty($slug) || empty($lang)) {
        return $response;
    }
    
    // Check if this is one of our custom post types
    $post_type = null;
    $rest_base = null;
    if (preg_match('#/wp/v2/(articles|strategy-course|tools)#', $route, $matches)) {
        $post_type_map = array(
            'articles' => 'article',
            'strategy-course' => 'strategy_course',
            'tools' => 'tool',
        );
        $rest_base = $matches[1];
        $post_type = $post_type_map[$rest_base] ?? null;
    }
    
    if (!$post_type || !function_exists('pll_get_post')) {
        return $response;
    }
    
    // Look up the post by slug in any language (bypass Polylang filter)
    // Temporarily disable Polylang's filter
    $pll_curlang = null;
    if (function_exists('PLL') && isset(PLL()->curlang)) {
        $pll_curlang = PLL()->curlang;
        PLL()->curlang = null;
    }
    
    $posts = get_posts(array(
        'post_type' => $post_type,
        'name' => $slug,
        'post_status' => 'publish',
        'numberposts' => 1,
        'lang' => '', // Explicitly no language filter
    ));
    
    // Restore Polylang's filter
    if ($pll_curlang !== null && function_exists('PLL')) {
        PLL()->curlang = $pll_curlang;
    }
    
    if (empty($posts)) {
        return $response;
    }
    
    $source_post = $posts[0];
    
    // Get the translation in the requested language
    $translation_id = pll_get_post($source_post->ID, $lang);
    
    if (!$translation_id || $translation_id === $source_post->ID) {
        // If translation_id equals source_post ID, this IS the translated version
        // but maybe slug was different - check if source post matches requested lang
        $source_lang = pll_get_post_language($source_post->ID, 'slug');
        if ($source_lang !== $lang) {
            // No translation exists for this language
            return $response;
        }
        $translation_id = $source_post->ID;
    }
    
    $translation_post = get_post($translation_id);
    
    if (!$translation_post || $translation_post->post_status !== 'publish') {
        return $response;
    }
    
    // Build a REST API response for this post using the appropriate controller
    $controller = new WP_REST_Posts_Controller($post_type);
    $item_response = $controller->prepare_item_for_response($translation_post, $request);
    $item_data = $item_response->get_data();
    
    // Add our custom fields (language and translations)
    if (function_exists('pll_get_post_language')) {
        $item_data['language'] = pll_get_post_language($translation_id, 'slug');
    }
    
    if (function_exists('pll_get_post_translations')) {
        $translations = pll_get_post_translations($translation_id);
        $translation_data = array();
        foreach ($translations as $lang_code => $trans_id) {
            if ($trans_id == $translation_id) continue;
            $trans_post = get_post($trans_id);
            if ($trans_post && $trans_post->post_status === 'publish') {
                $translation_data[] = array(
                    'id' => $trans_id,
                    'slug' => $trans_post->post_name,
                    'language' => $lang_code,
                    'link' => get_permalink($trans_id),
                );
            }
        }
        $item_data['translations'] = $translation_data;
    }
    
    // Return the translation as an array (matching the collection format)
    $response->set_data(array($item_data));
    
    return $response;
}
add_filter('rest_request_after_callbacks', 'gaal_rest_translation_slug_lookup', 10, 3);

// Ensure content is always included in REST API for custom post types
// This ensures the content field is always available in the API response
function gaal_rest_ensure_content($response, $post, $request) {
    // Only modify our custom post types
    $post_types = array('strategy_course', 'article', 'tool');
    
    if (in_array($post->post_type, $post_types)) {
        // Ensure content.rendered is always present and properly formatted
        if (isset($response->data['content'])) {
            // Make sure content.rendered exists and has the actual content
            if (empty($response->data['content']['rendered']) && !empty($post->post_content)) {
                $response->data['content']['rendered'] = apply_filters('the_content', $post->post_content);
            }
        } else {
            // Content field missing entirely, add it
            $response->data['content'] = array(
                'rendered' => apply_filters('the_content', $post->post_content),
                'protected' => false,
            );
        }
        
        // Ensure excerpt.rendered is always present
        if (isset($response->data['excerpt'])) {
            if (empty($response->data['excerpt']['rendered'])) {
                $excerpt = !empty($post->post_excerpt) ? $post->post_excerpt : wp_trim_words($post->post_content, 55);
                $response->data['excerpt']['rendered'] = apply_filters('the_excerpt', $excerpt);
            }
        } else {
            $excerpt = !empty($post->post_excerpt) ? $post->post_excerpt : wp_trim_words($post->post_content, 55);
            $response->data['excerpt'] = array(
                'rendered' => apply_filters('the_excerpt', $excerpt),
                'protected' => false,
            );
        }
    }
    
    return $response;
}
add_filter('rest_prepare_strategy_course', 'gaal_rest_ensure_content', 10, 3);
add_filter('rest_prepare_article', 'gaal_rest_ensure_content', 10, 3);
add_filter('rest_prepare_tool', 'gaal_rest_ensure_content', 10, 3);

// Ensure content field is always included in REST API context
function gaal_rest_include_content_in_context() {
    $post_types = array('strategy_course', 'article', 'tool');
    
    foreach ($post_types as $post_type) {
        $post_type_obj = get_post_type_object($post_type);
        if ($post_type_obj) {
            // Ensure content is in the view context
            add_filter("rest_{$post_type}_collection_params", function($query_params, $post_type_obj) {
                // This ensures content is included in list views
                return $query_params;
            }, 10, 2);
        }
    }
}
add_action('rest_api_init', 'gaal_rest_include_content_in_context', 20);

// Add menu items to REST API
function gaal_register_menu_api() {
    register_rest_route('gaal/v1', '/menus/(?P<location>[a-zA-Z0-9_-]+)', array(
        'methods' => 'GET',
        'callback' => function($request) {
            $location = $request['location'];
            $locations = get_nav_menu_locations();
            
            if (!isset($locations[$location])) {
                return new WP_Error('menu_not_found', 'Menu location not found', array('status' => 404));
            }
            
            $menu_items = wp_get_nav_menu_items($locations[$location]);
            
            if (!$menu_items) {
                return array();
            }
            
            $menu_data = array();
            foreach ($menu_items as $item) {
                $menu_data[] = array(
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                    'parent' => $item->menu_item_parent,
                    'order' => $item->menu_order,
                );
            }
            
            return $menu_data;
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gaal_register_menu_api');

// Add site options to REST API
function gaal_register_site_options_api() {
    register_rest_route('gaal/v1', '/site-info', array(
        'methods' => 'GET',
        'callback' => function() {
            return array(
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'url' => get_bloginfo('url'),
                'logo' => get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : null,
            );
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gaal_register_site_options_api');

// Register Authentication API endpoints
function gaal_register_auth_api() {
    // Login endpoint
    register_rest_route('gaal/v1', '/auth/login', array(
        'methods' => 'POST',
        'callback' => function($request) {
            $username = $request->get_param('username');
            $password = $request->get_param('password');
            
            if (empty($username) || empty($password)) {
                return new WP_Error('missing_credentials', 'Username and password are required', array('status' => 400));
            }
            
            // Attempt to authenticate
            $user = wp_authenticate($username, $password);
            
            if (is_wp_error($user)) {
                return new WP_Error('invalid_credentials', 'Invalid username or password', array('status' => 401));
            }
            
            // Set authentication cookies
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);
            
            // Return user data
            // Get user roles
            $roles = $user->roles;
            
            return array(
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'avatar' => get_avatar_url($user->ID),
                'capabilities' => $user->allcaps,
                'roles' => $roles,
            );
        },
        'permission_callback' => '__return_true',
    ));
    
    // Logout endpoint
    register_rest_route('gaal/v1', '/auth/logout', array(
        'methods' => 'POST',
        'callback' => function($request) {
            wp_logout();
            return array('success' => true, 'message' => 'Logged out successfully');
        },
        'permission_callback' => '__return_true',
    ));
    
    // Get current user endpoint
    register_rest_route('gaal/v1', '/auth/me', array(
        'methods' => 'GET',
        'callback' => function($request) {
            // WordPress should already have loaded the user session
            // Just get the current user ID - WordPress handles cookie authentication automatically
            $user_id = get_current_user_id();
            
            // If no user ID, return null (not an error) so frontend can handle gracefully
            if (!$user_id || $user_id === 0) {
                // Return 200 OK with null to avoid 401 errors
                return new WP_REST_Response(null, 200);
            }
            
            $user = get_userdata($user_id);
            
            if (!$user) {
                return new WP_REST_Response(null, 200);
            }
            
            // Get user roles
            $roles = $user->roles;
            
            return new WP_REST_Response(array(
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'avatar' => get_avatar_url($user->ID),
                'capabilities' => $user->allcaps,
                'roles' => $roles,
            ), 200);
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gaal_register_auth_api');

// Brevo API Integration Functions
/**
 * Get Brevo API key from WordPress options
 */
function gaal_get_brevo_api_key() {
    return get_option('gaal_brevo_api_key', '');
}

/**
 * Get Brevo list ID from WordPress options
 */
function gaal_get_brevo_list_id() {
    $list_id = get_option('gaal_brevo_list_id', '');
    // Convert to integer if it's a valid number
    return is_numeric($list_id) ? intval($list_id) : $list_id;
}

/**
 * Submit contact to Brevo API
 * 
 * @param string $email Contact email address
 * @param string $name Contact name (optional)
 * @return array|WP_Error Response from Brevo API or WP_Error on failure
 */
function gaal_subscribe_to_brevo($email, $name = '') {
    $api_key = gaal_get_brevo_api_key();
    $list_id = gaal_get_brevo_list_id();
    
    // Check if Brevo is configured
    if (empty($api_key)) {
        return new WP_Error('brevo_not_configured', 'Brevo API key is not configured', array('status' => 500));
    }
    
    // Prepare name attributes
    $attributes = array();
    if (!empty($name)) {
        // Split name into first and last name if space exists
        $name_parts = explode(' ', trim($name), 2);
        $attributes['FIRSTNAME'] = $name_parts[0];
        if (isset($name_parts[1])) {
            $attributes['LASTNAME'] = $name_parts[1];
        }
    }
    
    // Prepare request body
    $body = array(
        'email' => $email,
        'updateEnabled' => true, // Update contact if already exists
    );
    
    if (!empty($attributes)) {
        $body['attributes'] = $attributes;
    }
    
    // Add list ID if configured
    if (!empty($list_id)) {
        $body['listIds'] = array($list_id);
    }
    
    // Make API request to Brevo
    $url = 'https://api.brevo.com/v3/contacts';
    $args = array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => 'application/json',
            'api-key' => $api_key,
        ),
        'body' => json_encode($body),
        'timeout' => 15,
    );
    
    $response = wp_remote_request($url, $args);
    
    // Handle errors
    if (is_wp_error($response)) {
        error_log('Brevo API Error: ' . $response->get_error_message());
        return new WP_Error('brevo_api_error', 'Failed to connect to Brevo API: ' . $response->get_error_message(), array('status' => 500));
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $response_data = json_decode($response_body, true);
    
    // Handle successful responses
    if ($response_code === 201 || $response_code === 200 || $response_code === 204) {
        // Success - contact created or updated
        return array(
            'success' => true,
            'code' => $response_code,
            'data' => $response_data,
        );
    }
    
    // Handle error responses
    $error_message = 'Unknown error';
    if (isset($response_data['message'])) {
        $error_message = $response_data['message'];
    } elseif (isset($response_data['error'])) {
        $error_message = is_array($response_data['error']) ? json_encode($response_data['error']) : $response_data['error'];
    }
    
    error_log('Brevo API Error Response: ' . $error_message . ' (Code: ' . $response_code . ')');
    
    // Handle specific error cases
    if ($response_code === 400) {
        return new WP_Error('brevo_invalid_request', 'Invalid request to Brevo: ' . $error_message, array('status' => 400));
    } elseif ($response_code === 401) {
        return new WP_Error('brevo_unauthorized', 'Brevo API key is invalid', array('status' => 401));
    } elseif ($response_code === 404) {
        return new WP_Error('brevo_not_found', 'Brevo resource not found: ' . $error_message, array('status' => 404));
    } else {
        return new WP_Error('brevo_api_error', 'Brevo API error: ' . $error_message, array('status' => $response_code));
    }
}

// Register Brevo Settings in WordPress Admin
function gaal_add_brevo_settings() {
    add_options_page(
        __('Brevo Newsletter Settings', 'kingdom-training'),
        __('Brevo Newsletter', 'kingdom-training'),
        'manage_options',
        'gaal-brevo-settings',
        'gaal_brevo_settings_page'
    );
}
add_action('admin_menu', 'gaal_add_brevo_settings');

/**
 * Render Brevo settings page
 */
function gaal_brevo_settings_page() {
    // Save settings if form was submitted
    if (isset($_POST['gaal_brevo_settings_submit']) && check_admin_referer('gaal_brevo_settings')) {
        $api_key = sanitize_text_field($_POST['gaal_brevo_api_key'] ?? '');
        $list_id = sanitize_text_field($_POST['gaal_brevo_list_id'] ?? '');
        
        update_option('gaal_brevo_api_key', $api_key);
        update_option('gaal_brevo_list_id', $list_id);
        
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'kingdom-training') . '</p></div>';
    }
    
    $api_key = gaal_get_brevo_api_key();
    $list_id = gaal_get_brevo_list_id();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Brevo Newsletter Settings', 'kingdom-training'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('gaal_brevo_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="gaal_brevo_api_key"><?php echo esc_html__('Brevo API Key', 'kingdom-training'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="gaal_brevo_api_key" 
                               name="gaal_brevo_api_key" 
                               value="<?php echo esc_attr($api_key); ?>" 
                               class="regular-text"
                               placeholder="xkeysib-...">
                        <p class="description">
                            <?php echo esc_html__('Enter your Brevo API key. You can find this in your Brevo account under SMTP & API > API keys.', 'kingdom-training'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="gaal_brevo_list_id"><?php echo esc_html__('Brevo List ID', 'kingdom-training'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="gaal_brevo_list_id" 
                               name="gaal_brevo_list_id" 
                               value="<?php echo esc_attr($list_id); ?>" 
                               class="regular-text"
                               placeholder="1">
                        <p class="description">
                            <?php echo esc_html__('Enter the ID of the Brevo list where contacts should be added. Leave empty if you want to add contacts without assigning them to a list.', 'kingdom-training'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'kingdom-training'), 'primary', 'gaal_brevo_settings_submit'); ?>
        </form>
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2><?php echo esc_html__('How to Get Your Brevo API Key', 'kingdom-training'); ?></h2>
            <ol>
                <li><?php echo esc_html__('Log into your Brevo account', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Navigate to Settings > SMTP & API', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Click on the "API keys" tab', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Click "Generate a new API key"', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Name your API key and click "Generate"', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Copy the generated API key and paste it above', 'kingdom-training'); ?></li>
            </ol>
            <h2><?php echo esc_html__('How to Find Your List ID', 'kingdom-training'); ?></h2>
            <ol>
                <li><?php echo esc_html__('Log into your Brevo account', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Navigate to Contacts > Lists', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('Click on the list you want to use', 'kingdom-training'); ?></li>
                <li><?php echo esc_html__('The List ID is shown in the URL or list details', 'kingdom-training'); ?></li>
            </ol>
        </div>
    </div>
    <?php
}

// Register Newsletter Subscription API endpoint
function gaal_register_newsletter_api() {
    // Newsletter subscription endpoint
    register_rest_route('gaal/v1', '/newsletter/subscribe', array(
        'methods' => 'POST',
        'callback' => function($request) {
            $email = sanitize_email($request->get_param('email'));
            $name = sanitize_text_field($request->get_param('name'));
            
            // Validate email
            if (empty($email) || !is_email($email)) {
                return new WP_Error('invalid_email', 'Please provide a valid email address', array('status' => 400));
            }
            
            // Submit to Brevo API
            $brevo_result = gaal_subscribe_to_brevo($email, $name);
            
            // If Brevo is configured and there's an error, log it but continue with local storage
            $brevo_success = false;
            if (!is_wp_error($brevo_result)) {
                $brevo_success = true;
            } else {
                // Log Brevo errors but don't fail the subscription
                // This allows the subscription to work even if Brevo is temporarily unavailable
                error_log('Brevo subscription failed for ' . $email . ': ' . $brevo_result->get_error_message());
            }
            
            // Store subscriber data locally as backup
            $subscribers = get_option('gaal_newsletter_subscribers', array());
            
            // Check if email already exists locally
            $email_exists = false;
            foreach ($subscribers as $index => $subscriber) {
                if (isset($subscriber['email']) && strtolower($subscriber['email']) === strtolower($email)) {
                    // Update existing subscriber
                    $subscribers[$index] = array(
                        'email' => $email,
                        'name' => $name,
                        'subscribed_at' => $subscriber['subscribed_at'] ?? current_time('mysql'),
                        'updated_at' => current_time('mysql'),
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'brevo_synced' => $brevo_success,
                    );
                    $email_exists = true;
                    break;
                }
            }
            
            // Add new subscriber if not exists
            if (!$email_exists) {
                $subscriber_data = array(
                    'email' => $email,
                    'name' => $name,
                    'subscribed_at' => current_time('mysql'),
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'brevo_synced' => $brevo_success,
                );
                $subscribers[] = $subscriber_data;
            }
            
            // Save subscribers list
            update_option('gaal_newsletter_subscribers', $subscribers);
            
            // If Brevo sync failed but we have a configured API key, return error
            // This ensures users know if there's a configuration issue
            if (is_wp_error($brevo_result) && !empty(gaal_get_brevo_api_key())) {
                // Check if it's a configuration error (401) or a temporary issue
                $error_code = $brevo_result->get_error_code();
                if (in_array($error_code, array('brevo_unauthorized', 'brevo_not_configured'))) {
                    // Configuration error - return error to user
                    return new WP_Error(
                        'brevo_config_error',
                        'Newsletter subscription service is not properly configured. Please contact the administrator.',
                        array('status' => 500)
                    );
                }
                // For other errors (network, etc.), still succeed but log
            }
            
            // Return success response
            return array(
                'success' => true,
                'message' => 'Successfully subscribed to newsletter',
                'email' => $email,
                'brevo_synced' => $brevo_success,
            );
        },
        'permission_callback' => '__return_true',
    ));
    
    // Get newsletter subscribers (admin only)
    register_rest_route('gaal/v1', '/newsletter/subscribers', array(
        'methods' => 'GET',
        'callback' => function($request) {
            // Check if user is admin
            if (!current_user_can('manage_options')) {
                return new WP_Error('forbidden', 'You do not have permission to view subscribers', array('status' => 403));
            }
            
            $subscribers = get_option('gaal_newsletter_subscribers', array());
            return array(
                'success' => true,
                'count' => count($subscribers),
                'subscribers' => $subscribers,
            );
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gaal_register_newsletter_api');

// Register shortcode processing endpoint
function gaal_register_shortcode_api() {
    register_rest_route('gaal/v1', '/shortcode/render', array(
        'methods' => 'POST',
        'callback' => function($request) {
            $shortcode = sanitize_text_field($request->get_param('shortcode'));
            
            if (empty($shortcode)) {
                return new WP_Error('missing_shortcode', 'Shortcode is required', array('status' => 400));
            }
            
            // Process the shortcode
            // Note: do_shortcode() processes WordPress shortcodes and returns the rendered HTML
            $rendered = do_shortcode($shortcode);
            
            return array(
                'success' => true,
                'html' => $rendered,
            );
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gaal_register_shortcode_api');

// Add Translation meta box
function gaal_add_translation_meta_box() {
    $post_types = array('strategy_course', 'article', 'tool');
    foreach ($post_types as $post_type) {
        add_meta_box(
            'gaal_translation_meta_box',
            __('Multilingual Translation', 'kingdom-training'),
            'gaal_translation_meta_box_callback',
            $post_type,
            'side',
            'default'
        );
    }
}
// DISABLED: Translation meta box removed from post type sidebars - keeping settings page only
// add_action('add_meta_boxes', 'gaal_add_translation_meta_box');

// Enqueue admin scripts and styles for translation meta box
function gaal_enqueue_translation_admin_assets($hook) {
    // Only load on post edit pages
    if (!in_array($hook, array('post.php', 'post-new.php'))) {
        return;
    }
    
    global $post;
    if (!$post) {
        return;
    }
    
    // Only load for our custom post types
    $post_types = array('strategy_course', 'article', 'tool');
    if (!in_array($post->post_type, $post_types)) {
        return;
    }
    
    wp_enqueue_script(
        'gaal-translation-admin',
        get_template_directory_uri() . '/admin/js/translation-admin.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    wp_enqueue_style(
        'gaal-translation-admin',
        get_template_directory_uri() . '/admin/css/translation-admin.css',
        array(),
        '1.0.0'
    );
    
    // Localize script with data
    wp_localize_script('gaal-translation-admin', 'gaalTranslation', array(
        'apiUrl' => rest_url('gaal/v1/translate/'),
        'nonce' => wp_create_nonce('wp_rest'),
        'postId' => $post->ID,
        'strings' => array(
            'generateAll' => __('Generate All Translations', 'kingdom-training'),
            'translateSingle' => __('Translate', 'kingdom-training'),
            'retranslate' => __('Re-translate', 'kingdom-training'),
            'retranslating' => __('Translating...', 'kingdom-training'),
            'retranslateSuccess' => __('Content re-translated successfully!', 'kingdom-training'),
            'confirmRetranslate' => __('This will overwrite the current title, content, and excerpt with a fresh translation from Google Translate. Continue?', 'kingdom-training'),
            'resume' => __('Resume', 'kingdom-training'),
            'loading' => __('Loading...', 'kingdom-training'),
            'success' => __('Success', 'kingdom-training'),
            'error' => __('Error', 'kingdom-training'),
            'inProgress' => __('In Progress', 'kingdom-training'),
            'completed' => __('Completed', 'kingdom-training'),
            'pending' => __('Pending', 'kingdom-training'),
            'failed' => __('Failed', 'kingdom-training'),
            'copyFromEnglish' => __('Copy Content from English', 'kingdom-training'),
            'copying' => __('Copying...', 'kingdom-training'),
            'copySuccess' => __('Content copied successfully!', 'kingdom-training'),
            'confirmCopy' => __('This will overwrite the current title, content, and excerpt with the English version. Continue?', 'kingdom-training'),
            // Chunked translation progress strings
            'stepInit' => __('Initializing...', 'kingdom-training'),
            'stepTitle' => __('Translating title...', 'kingdom-training'),
            'stepContent' => __('Translating content chunk %d...', 'kingdom-training'),
            'stepExcerpt' => __('Translating excerpt...', 'kingdom-training'),
            'stepFinalize' => __('Finalizing...', 'kingdom-training'),
            'stepProgress' => __('Step %1$d of %2$d: %3$s', 'kingdom-training'),
            'translationComplete' => __('Translation completed!', 'kingdom-training'),
            'translationFailed' => __('Translation failed at step: %s', 'kingdom-training'),
        ),
    ));
}
// DISABLED: Translation admin assets not needed since meta box is disabled
// add_action('admin_enqueue_scripts', 'gaal_enqueue_translation_admin_assets');

// Translation meta box callback
function gaal_translation_meta_box_callback($post) {
    // Get current language
    $current_language = 'en';
    if (function_exists('pll_get_post_language')) {
        $current_language = pll_get_post_language($post->ID, 'slug') ?: 'en';
    }
    
    // Get enabled languages
    $enabled_languages = get_option('gaal_translation_enabled_languages', array());
    
    // Get available languages from Polylang
    $available_languages = array();
    if (function_exists('PLL') && isset(PLL()->model)) {
        // Get full language objects from Polylang
        $languages = PLL()->model->get_languages_list();
        foreach ($languages as $lang) {
            $available_languages[$lang->slug] = $lang->name;
        }
    } elseif (function_exists('pll_languages_list')) {
        // Fallback: get language slugs and retrieve language data
        $language_slugs = pll_languages_list();
        foreach ($language_slugs as $slug) {
            if (function_exists('PLL') && isset(PLL()->model)) {
                $lang = PLL()->model->get_language($slug);
                if ($lang) {
                    $available_languages[$lang->slug] = $lang->name;
                }
            } else {
                // Last resort: use slug as name
                $available_languages[$slug] = strtoupper($slug);
            }
        }
    }
    
    // Get translation status
    $translations = array();
    if (function_exists('pll_get_post_translations')) {
        $post_translations = pll_get_post_translations($post->ID);
        if ($post_translations) {
            foreach ($post_translations as $lang => $trans_id) {
                if ($lang !== $current_language) {
                    $trans_post = get_post($trans_id);
                    $translations[$lang] = array(
                        'post_id' => $trans_id,
                        'status' => $trans_post ? $trans_post->post_status : 'missing',
                        'title' => $trans_post ? $trans_post->post_title : '',
                    );
                }
            }
        }
    }
    
    // Get English source post for non-English posts
    $english_source_post = null;
    $english_source_post_id = null;
    $debug_translations = array(); // Debug info
    $debug_pll_get_post = null; // Debug: result from pll_get_post
    
    if ($current_language !== 'en') {
        // Method 1: Try pll_get_post_translations
        if (function_exists('pll_get_post_translations')) {
            $all_translations = pll_get_post_translations($post->ID);
            $debug_translations = $all_translations;
            if (isset($all_translations['en'])) {
                $english_source_post_id = $all_translations['en'];
                $english_source_post = get_post($english_source_post_id);
            }
        }
        
        // Method 2: If not found, try pll_get_post (direct lookup)
        if (!$english_source_post_id && function_exists('pll_get_post')) {
            $debug_pll_get_post = pll_get_post($post->ID, 'en');
            if ($debug_pll_get_post && $debug_pll_get_post != $post->ID) {
                $english_source_post_id = $debug_pll_get_post;
                $english_source_post = get_post($english_source_post_id);
            }
        }
    }
    
    ?>
    <div class="gaal-translation-meta-box">
        <p>
            <strong><?php echo esc_html__('Current Language:', 'kingdom-training'); ?></strong>
            <?php echo esc_html($available_languages[$current_language] ?? $current_language); ?>
        </p>
        
        <?php // Show "Copy from English" and "Re-translate" sections for non-English posts ?>
        <?php if ($current_language !== 'en'): ?>
            <?php 
            // Get the language name for display
            $current_language_name = isset($available_languages[$current_language]) 
                ? $available_languages[$current_language] 
                : strtoupper($current_language);
            ?>
            <div class="gaal-copy-from-english" style="margin-bottom: 15px; padding: 10px; background: #f0f6fc; border-left: 3px solid #0073aa;">
                <h4 style="margin: 0 0 8px 0; font-size: 13px;"><?php echo esc_html__('Translation from English', 'kingdom-training'); ?></h4>
                <?php if ($english_source_post): ?>
                    <p style="margin: 0 0 8px 0; font-size: 12px;">
                        <strong><?php echo esc_html__('English version:', 'kingdom-training'); ?></strong>
                        <a href="<?php echo esc_url(get_edit_post_link($english_source_post_id)); ?>" target="_blank">
                            <?php echo esc_html($english_source_post->post_title); ?>
                        </a>
                    </p>
                    
                    <?php // Re-translate button ?>
                    <button type="button" class="button button-primary gaal-retranslate-btn" 
                            data-source-id="<?php echo esc_attr($english_source_post_id); ?>"
                            data-target-language="<?php echo esc_attr($current_language); ?>"
                            data-language-name="<?php echo esc_attr($current_language_name); ?>"
                            style="width: 100%; margin-bottom: 8px;">
                        <?php 
                        /* translators: %s: language name */
                        printf(esc_html__('Re-translate into %s', 'kingdom-training'), esc_html($current_language_name)); 
                        ?>
                    </button>
                    <p class="description" style="margin-top: 0; margin-bottom: 10px; font-size: 11px;">
                        <?php echo esc_html__('Get a fresh translation from Google Translate using the English version.', 'kingdom-training'); ?>
                    </p>
                    
                    <?php // Copy from English button ?>
                    <button type="button" class="button gaal-copy-from-english-btn" data-source-id="<?php echo esc_attr($english_source_post_id); ?>" style="width: 100%;">
                        <?php echo esc_html__('Copy Content from English (no translation)', 'kingdom-training'); ?>
                    </button>
                    <p class="description" style="margin-top: 5px; font-size: 11px;">
                        <?php echo esc_html__('This will copy the raw English title, content, and excerpt without translation.', 'kingdom-training'); ?>
                    </p>
                <?php else: ?>
                    <p class="description" style="margin: 0;">
                        <?php echo esc_html__('No English version linked to this post.', 'kingdom-training'); ?>
                    </p>
                    <?php if (current_user_can('manage_options')): ?>
                        <details style="margin-top: 8px; font-size: 11px;">
                            <summary style="cursor: pointer; color: #666;"><?php echo esc_html__('Debug Info', 'kingdom-training'); ?></summary>
                            <div style="margin-top: 5px; padding: 5px; background: #fff; border: 1px solid #ddd; font-family: monospace; font-size: 10px;">
                                <p style="margin: 0 0 5px 0;"><strong>Post ID:</strong> <?php echo esc_html($post->ID); ?></p>
                                <p style="margin: 0 0 5px 0;"><strong>Current Lang:</strong> <?php echo esc_html($current_language); ?></p>
                                <p style="margin: 0 0 5px 0;"><strong>pll_get_post_translations exists:</strong> <?php echo function_exists('pll_get_post_translations') ? 'Yes' : 'No'; ?></p>
                                <p style="margin: 0 0 5px 0;"><strong>pll_get_post_translations result:</strong> 
                                    <?php 
                                    if (empty($debug_translations)) {
                                        echo 'None';
                                    } else {
                                        $trans_info = array();
                                        foreach ($debug_translations as $lang => $trans_id) {
                                            $trans_info[] = $lang . '=' . $trans_id;
                                        }
                                        echo esc_html(implode(', ', $trans_info));
                                    }
                                    ?>
                                </p>
                                <p style="margin: 0 0 5px 0;"><strong>pll_get_post exists:</strong> <?php echo function_exists('pll_get_post') ? 'Yes' : 'No'; ?></p>
                                <p style="margin: 0 0 5px 0;"><strong>pll_get_post(<?php echo $post->ID; ?>, 'en') result:</strong> <?php echo esc_html($debug_pll_get_post !== null ? ($debug_pll_get_post ?: 'false/empty') : 'Not called'); ?></p>
                                <?php 
                                // Also check the translation term directly
                                $translation_term = null;
                                if (function_exists('PLL') && isset(PLL()->model)) {
                                    $translation_term = PLL()->model->post->get_translation_term($post->ID);
                                }
                                ?>
                                <p style="margin: 0;"><strong>Translation term ID:</strong> <?php echo esc_html($translation_term ? $translation_term : 'None'); ?></p>
                            </div>
                        </details>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($enabled_languages)): ?>
            <p class="description">
                <?php echo esc_html__('No languages enabled for translation. Please configure in Settings → Translation Automation.', 'kingdom-training'); ?>
            </p>
        <?php else: ?>
            <div class="gaal-translation-actions">
                <button type="button" class="button button-primary gaal-generate-all" style="width: 100%; margin-bottom: 10px;">
                    <?php echo esc_html__('Generate All Translations', 'kingdom-training'); ?>
                </button>
            </div>
            
            <div class="gaal-translation-status">
                <h4><?php echo esc_html__('Translation Status', 'kingdom-training'); ?></h4>
                <ul class="gaal-translation-languages">
                    <?php foreach ($enabled_languages as $lang): ?>
                        <?php if ($lang === $current_language) continue; ?>
                        <li class="gaal-translation-language" data-language="<?php echo esc_attr($lang); ?>">
                            <strong><?php echo esc_html($available_languages[$lang] ?? $lang); ?>:</strong>
                            <span class="gaal-translation-status-text">
                                <?php if (isset($translations[$lang])): ?>
                                    <?php 
                                    $status = $translations[$lang]['status'];
                                    $status_label = $status === 'publish' ? __('Published', 'kingdom-training') : 
                                                   ($status === 'draft' ? __('Draft', 'kingdom-training') : 
                                                   ($status === 'pending' ? __('Pending', 'kingdom-training') : __('Unknown', 'kingdom-training')));
                                    ?>
                                    <span class="status-<?php echo esc_attr($status); ?>"><?php echo esc_html($status_label); ?></span>
                                    <a href="<?php echo esc_url(get_edit_post_link($translations[$lang]['post_id'])); ?>" target="_blank">
                                        <?php echo esc_html__('Edit', 'kingdom-training'); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="status-missing"><?php echo esc_html__('Not translated', 'kingdom-training'); ?></span>
                                <?php endif; ?>
                            </span>
                            <button type="button" class="button button-small gaal-translate-single" data-language="<?php echo esc_attr($lang); ?>" style="margin-left: 5px;">
                                <?php echo esc_html__('Translate', 'kingdom-training'); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Progress bar for chunked translation -->
            <div class="gaal-progress-container" style="display: none; margin-top: 10px; padding: 10px; background: #f0f0f1; border-radius: 4px;">
                <div class="gaal-progress-bar-wrapper" style="background: #dcdcde; border-radius: 3px; height: 20px; overflow: hidden;">
                    <div class="gaal-progress-bar" style="background: #2271b1; height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                </div>
                <div class="gaal-progress-text" style="margin-top: 8px; font-size: 12px; color: #50575e; text-align: center;">
                    <?php echo esc_html__('Preparing translation...', 'kingdom-training'); ?>
                </div>
            </div>
            
            <div class="gaal-translation-messages" style="margin-top: 10px;"></div>
        <?php endif; ?>
    </div>
    <?php
}

// Add Steps meta box for Strategy Course post type
function gaal_add_steps_meta_box() {
    add_meta_box(
        'steps_meta_box',
        __('Step Number', 'kingdom-training'),
        'gaal_steps_meta_box_callback',
        'strategy_course',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'gaal_add_steps_meta_box');

// Meta box callback function
function gaal_steps_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('gaal_save_steps_meta_box', 'gaal_steps_meta_box_nonce');
    
    // Get current value
    $steps = get_post_meta($post->ID, 'steps', true);
    $steps = $steps ? intval($steps) : '';
    
    // Create dropdown
    echo '<label for="steps_field">' . __('Select step number:', 'kingdom-training') . '</label>';
    echo '<select name="steps_field" id="steps_field" style="width: 100%; margin-top: 5px;">';
    echo '<option value="">' . __('None', 'kingdom-training') . '</option>';
    
    for ($i = 1; $i <= 20; $i++) {
        $selected = ($steps == $i) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($i) . '" ' . $selected . '>' . esc_html($i) . '</option>';
    }
    
    echo '</select>';
    echo '<p class="description">' . __('Select a step number (1-20) to control the order of this course content.', 'kingdom-training') . '</p>';
}

// Save steps meta box data
function gaal_save_steps_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['gaal_steps_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['gaal_steps_meta_box_nonce'], 'gaal_save_steps_meta_box')) {
        return;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if this is a strategy_course post type
    if (get_post_type($post_id) !== 'strategy_course') {
        return;
    }
    
    // Save the meta field
    if (isset($_POST['steps_field'])) {
        $steps = sanitize_text_field($_POST['steps_field']);
        
        // Validate that it's a number between 1 and 20
        if ($steps === '' || (is_numeric($steps) && intval($steps) >= 1 && intval($steps) <= 20)) {
            if ($steps === '') {
                delete_post_meta($post_id, 'steps');
            } else {
                update_post_meta($post_id, 'steps', intval($steps));
            }
        }
    } else {
        // If field is not set, delete the meta
        delete_post_meta($post_id, 'steps');
    }
}
add_action('save_post', 'gaal_save_steps_meta_box');

/**
 * Publishing Notes Meta Box
 * 
 * Adds a meta field for leaving notes about the state of content
 * (e.g., "needs review", "waiting for images", "ready to publish", etc.)
 */

// Add Publishing Notes meta box for Strategy Course, Article, and Tool post types
function gaal_add_publishing_notes_meta_box() {
    $post_types = array('strategy_course', 'article', 'tool');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'publishing_notes_meta_box',
            __('Publishing Notes', 'kingdom-training'),
            'gaal_publishing_notes_meta_box_callback',
            $post_type,
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'gaal_add_publishing_notes_meta_box');

// Publishing Notes meta box callback function
function gaal_publishing_notes_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('gaal_save_publishing_notes', 'gaal_publishing_notes_nonce');
    
    // Get current value
    $notes = get_post_meta($post->ID, 'publishing_notes', true);
    
    ?>
    <label for="publishing_notes_field" class="screen-reader-text">
        <?php esc_html_e('Publishing Notes', 'kingdom-training'); ?>
    </label>
    <textarea 
        name="publishing_notes_field" 
        id="publishing_notes_field" 
        rows="4" 
        style="width: 100%;"
        placeholder="<?php esc_attr_e('Add notes about this content...', 'kingdom-training'); ?>"
    ><?php echo esc_textarea($notes); ?></textarea>
    <p class="description">
        <?php esc_html_e('Leave notes about the state of this content (e.g., "needs review", "waiting for images", "ready to translate").', 'kingdom-training'); ?>
    </p>
    <?php
}

// Save Publishing Notes meta box data
function gaal_save_publishing_notes_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['gaal_publishing_notes_nonce']) || 
        !wp_verify_nonce($_POST['gaal_publishing_notes_nonce'], 'gaal_save_publishing_notes')) {
        return;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if this is one of our target post types
    $post_type = get_post_type($post_id);
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Save the meta field
    if (isset($_POST['publishing_notes_field'])) {
        $notes = sanitize_textarea_field($_POST['publishing_notes_field']);
        
        if (empty($notes)) {
            delete_post_meta($post_id, 'publishing_notes');
        } else {
            update_post_meta($post_id, 'publishing_notes', $notes);
        }
    } else {
        delete_post_meta($post_id, 'publishing_notes');
    }
}
add_action('save_post', 'gaal_save_publishing_notes_meta_box');

/**
 * Content Status Multi-Select Meta Box
 * 
 * Adds a multi-select field for tracking content status needs
 * (e.g., "needs review", "needs image", "needs AI enhancement", etc.)
 */

// Define content status options
function gaal_get_content_status_options() {
    return array(
        'draft_complete' => __('Draft Complete', 'kingdom-training'),
        'needs_ai_enhancement' => __('Needs AI Enhancement', 'kingdom-training'),
        'needs_call_out_text' => __('Needs Call Out Text', 'kingdom-training'),
        'needs_complete_draft' => __('Needs Complete Draft', 'kingdom-training'),
        'needs_image' => __('Needs Image', 'kingdom-training'),
        'needs_image_fix' => __('Needs Image Fix', 'kingdom-training'),
        'needs_link' => __('Needs Link', 'kingdom-training'),
        'needs_review' => __('Needs Review', 'kingdom-training'),
        'needs_scheduled' => __('Needs Scheduled', 'kingdom-training'),
        'needs_screenshots' => __('Needs Screenshots', 'kingdom-training'),
    );
}

// Add Content Status meta box for Strategy Course, Article, and Tool post types
function gaal_add_content_status_meta_box() {
    $post_types = array('strategy_course', 'article', 'tool');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'content_status_meta_box',
            __('Content Status', 'kingdom-training'),
            'gaal_content_status_meta_box_callback',
            $post_type,
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'gaal_add_content_status_meta_box');

// Content Status meta box callback function
function gaal_content_status_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('gaal_save_content_status', 'gaal_content_status_nonce');
    
    // Get current values (stored as array)
    $selected_statuses = get_post_meta($post->ID, 'content_status', true);
    if (!is_array($selected_statuses)) {
        $selected_statuses = array();
    }
    
    // Get available options
    $options = gaal_get_content_status_options();
    
    ?>
    <style>
        .content-status-checkboxes {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 8px;
            background: #fff;
        }
        .content-status-checkboxes label {
            display: block;
            margin-bottom: 6px;
            cursor: pointer;
        }
        .content-status-checkboxes label:last-child {
            margin-bottom: 0;
        }
        .content-status-checkboxes input[type="checkbox"] {
            margin-right: 6px;
        }
    </style>
    <fieldset>
        <legend class="screen-reader-text">
            <?php esc_html_e('Content Status', 'kingdom-training'); ?>
        </legend>
        <div class="content-status-checkboxes">
            <?php foreach ($options as $value => $label) : ?>
                <label>
                    <input 
                        type="checkbox" 
                        name="content_status_field[]" 
                        value="<?php echo esc_attr($value); ?>"
                        <?php checked(in_array($value, $selected_statuses)); ?>
                    />
                    <?php echo esc_html($label); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>
    <p class="description">
        <?php esc_html_e('Select all status items that apply to this content.', 'kingdom-training'); ?>
    </p>
    <?php
}

// Save Content Status meta box data
function gaal_save_content_status_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['gaal_content_status_nonce']) || 
        !wp_verify_nonce($_POST['gaal_content_status_nonce'], 'gaal_save_content_status')) {
        return;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if this is one of our target post types
    $post_type = get_post_type($post_id);
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Get valid options
    $valid_options = array_keys(gaal_get_content_status_options());
    
    // Save the meta field
    if (isset($_POST['content_status_field']) && is_array($_POST['content_status_field'])) {
        // Sanitize and validate each selected value
        $statuses = array_map('sanitize_text_field', $_POST['content_status_field']);
        $statuses = array_filter($statuses, function($status) use ($valid_options) {
            return in_array($status, $valid_options);
        });
        
        if (empty($statuses)) {
            delete_post_meta($post_id, 'content_status');
        } else {
            update_post_meta($post_id, 'content_status', $statuses);
        }
    } else {
        delete_post_meta($post_id, 'content_status');
    }
}
add_action('save_post', 'gaal_save_content_status_meta_box');

// Add Content Status column to Strategy Course, Article, and Tool admin list tables
function gaal_add_content_status_column_strategy_course($columns) {
    // Insert Content Status column after Publishing Notes
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'publishing_notes') {
            $new_columns['content_status'] = __('Content Status', 'kingdom-training');
        }
    }
    // Fallback: add at end if position not found
    if (!isset($new_columns['content_status'])) {
        $new_columns['content_status'] = __('Content Status', 'kingdom-training');
    }
    return $new_columns;
}
add_filter('manage_strategy_course_posts_columns', 'gaal_add_content_status_column_strategy_course', 16);

function gaal_add_content_status_column_article($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'publishing_notes') {
            $new_columns['content_status'] = __('Content Status', 'kingdom-training');
        }
    }
    if (!isset($new_columns['content_status'])) {
        $new_columns['content_status'] = __('Content Status', 'kingdom-training');
    }
    return $new_columns;
}
add_filter('manage_article_posts_columns', 'gaal_add_content_status_column_article', 16);

function gaal_add_content_status_column_tool($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'publishing_notes') {
            $new_columns['content_status'] = __('Content Status', 'kingdom-training');
        }
    }
    if (!isset($new_columns['content_status'])) {
        $new_columns['content_status'] = __('Content Status', 'kingdom-training');
    }
    return $new_columns;
}
add_filter('manage_tool_posts_columns', 'gaal_add_content_status_column_tool', 16);

// Populate Content Status column for Strategy Course
function gaal_populate_content_status_column_strategy_course($column, $post_id) {
    if ($column === 'content_status') {
        $statuses = get_post_meta($post_id, 'content_status', true);
        if (!empty($statuses) && is_array($statuses)) {
            $options = gaal_get_content_status_options();
            $labels = array();
            foreach ($statuses as $status) {
                if (isset($options[$status])) {
                    $labels[] = '<span class="content-status-tag">' . esc_html($options[$status]) . '</span>';
                }
            }
            echo implode(' ', $labels);
        } else {
            echo '<span class="na">—</span>';
        }
    }
}
add_action('manage_strategy_course_posts_custom_column', 'gaal_populate_content_status_column_strategy_course', 10, 2);

// Populate Content Status column for Article
function gaal_populate_content_status_column_article($column, $post_id) {
    if ($column === 'content_status') {
        $statuses = get_post_meta($post_id, 'content_status', true);
        if (!empty($statuses) && is_array($statuses)) {
            $options = gaal_get_content_status_options();
            $labels = array();
            foreach ($statuses as $status) {
                if (isset($options[$status])) {
                    $labels[] = '<span class="content-status-tag">' . esc_html($options[$status]) . '</span>';
                }
            }
            echo implode(' ', $labels);
        } else {
            echo '<span class="na">—</span>';
        }
    }
}
add_action('manage_article_posts_custom_column', 'gaal_populate_content_status_column_article', 10, 2);

// Populate Content Status column for Tool
function gaal_populate_content_status_column_tool($column, $post_id) {
    if ($column === 'content_status') {
        $statuses = get_post_meta($post_id, 'content_status', true);
        if (!empty($statuses) && is_array($statuses)) {
            $options = gaal_get_content_status_options();
            $labels = array();
            foreach ($statuses as $status) {
                if (isset($options[$status])) {
                    $labels[] = '<span class="content-status-tag">' . esc_html($options[$status]) . '</span>';
                }
            }
            echo implode(' ', $labels);
        } else {
            echo '<span class="na">—</span>';
        }
    }
}
add_action('manage_tool_posts_custom_column', 'gaal_populate_content_status_column_tool', 10, 2);

// Add admin styles for Content Status tags
function gaal_content_status_admin_styles() {
    $screen = get_current_screen();
    if ($screen && in_array($screen->id, array('edit-strategy_course', 'edit-article', 'edit-tool'))) {
        ?>
        <style>
            .content-status-tag {
                display: inline-block;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                padding: 2px 6px;
                margin: 1px;
                font-size: 11px;
                line-height: 1.4;
                white-space: nowrap;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'gaal_content_status_admin_styles');

// ============================================================================
// BULK EDIT CONTENT STATUS
// ============================================================================

// Add Content Status field to bulk edit panel
function gaal_add_content_status_bulk_edit($column_name, $post_type) {
    // Only show for our target post types
    $target_post_types = array('strategy_course', 'article', 'tool');
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Only show in the content_status column (WordPress calls this for each column)
    if ($column_name !== 'content_status') {
        return;
    }
    
    // Get available options
    $options = gaal_get_content_status_options();
    
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <div class="inline-edit-group wp-clearfix">
                <label class="inline-edit-status alignleft">
                    <span class="title"><?php esc_html_e('Content Status', 'kingdom-training'); ?></span>
                    <div class="content-status-bulk-edit-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 8px; background: #fff; margin-top: 5px;">
                        <?php foreach ($options as $value => $label) : ?>
                            <label style="display: block; margin-bottom: 6px; cursor: pointer;">
                                <input 
                                    type="checkbox" 
                                    name="content_status_bulk[]" 
                                    value="<?php echo esc_attr($value); ?>"
                                    class="content-status-bulk-checkbox"
                                />
                                <?php echo esc_html($label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="description" style="margin-top: 5px;">
                        <?php esc_html_e('Select statuses to add to selected posts. Leave unchecked to keep existing statuses unchanged.', 'kingdom-training'); ?>
                    </p>
                </label>
            </div>
        </div>
    </fieldset>
    <?php
}
// Hook for bulk edit only (quick edit works differently - shows existing values for single post)
add_action('bulk_edit_custom_box', 'gaal_add_content_status_bulk_edit', 10, 2);

// Save bulk edit changes for Content Status
function gaal_save_bulk_edit_content_status() {
    // Check if this is a bulk edit request
    if (!isset($_REQUEST['bulk_edit']) || !isset($_REQUEST['post'])) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_posts')) {
        return;
    }
    
    // Verify nonce for security (WordPress bulk edit doesn't always have nonce, but we check permissions)
    // Note: WordPress handles bulk edit security, but we add extra checks
    
    // Get the post IDs
    $post_ids = array_map('intval', $_REQUEST['post']);
    
    // Get the post type from the first post (all should be the same type in bulk edit)
    if (empty($post_ids)) {
        return;
    }
    
    $first_post = get_post($post_ids[0]);
    if (!$first_post) {
        return;
    }
    
    $post_type = $first_post->post_type;
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    // Only process our target post types
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Check if content status was provided in bulk edit
    // If not provided, don't change anything (WordPress bulk edit only updates provided fields)
    if (!isset($_REQUEST['content_status_bulk']) || !is_array($_REQUEST['content_status_bulk'])) {
        return;
    }
    
    // Get valid options
    $valid_options = array_keys(gaal_get_content_status_options());
    
    // Sanitize and validate selected statuses
    $new_statuses = array_map('sanitize_text_field', $_REQUEST['content_status_bulk']);
    $new_statuses = array_filter($new_statuses, function($status) use ($valid_options) {
        return in_array($status, $valid_options);
    });
    
    // If no valid statuses selected, don't update
    if (empty($new_statuses)) {
        return;
    }
    
    // Update each post
    foreach ($post_ids as $post_id) {
        // Check if user can edit this post
        if (!current_user_can('edit_post', $post_id)) {
            continue;
        }
        
        // Verify this is the correct post type
        $post = get_post($post_id);
        if (!$post || !in_array($post->post_type, $target_post_types)) {
            continue;
        }
        
        // Get existing statuses
        $existing_statuses = get_post_meta($post_id, 'content_status', true);
        if (!is_array($existing_statuses)) {
            $existing_statuses = array();
        }
        
        // Merge new statuses with existing (avoid duplicates)
        // This is additive - selected statuses are added to existing ones
        $merged_statuses = array_unique(array_merge($existing_statuses, $new_statuses));
        
        // Update the meta
        update_post_meta($post_id, 'content_status', $merged_statuses);
    }
}
add_action('load-edit.php', 'gaal_save_bulk_edit_content_status');

// Add JavaScript for bulk edit functionality
function gaal_content_status_bulk_edit_script() {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, array('edit-strategy_course', 'edit-article', 'edit-tool'))) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Ensure content status field is included when bulk edit form is submitted
        $(document).on('click', '#doaction, #doaction2', function(e) {
            var action = $(this).prev('select').val();
            if (action === 'edit') {
                // WordPress will show the bulk edit form
                // Our field should already be there via the hook
                // Just ensure it's visible when the form appears
                setTimeout(function() {
                    var bulkEditForm = $('#bulk-edit');
                    if (bulkEditForm.length > 0) {
                        // Ensure our fieldset is visible
                        var contentStatusFieldset = bulkEditForm.find('fieldset.inline-edit-col-right');
                        if (contentStatusFieldset.length > 0) {
                            contentStatusFieldset.show();
                        }
                    }
                }, 50);
            }
        });
        
        // Handle form submission to ensure checkboxes are included
        $(document).on('submit', '#bulk-edit', function() {
            // Check if any content status checkboxes are checked
            var checkedBoxes = $(this).find('.content-status-bulk-checkbox:checked');
            if (checkedBoxes.length === 0) {
                // If none checked, remove the field so it doesn't interfere
                $(this).find('input[name="content_status_bulk[]"]').remove();
            }
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'gaal_content_status_bulk_edit_script');

/**
 * Publishing Notes Bulk Edit
 * 
 * Adds bulk edit functionality for Publishing Notes field
 */

// Add Publishing Notes field to bulk edit form
function gaal_add_publishing_notes_bulk_edit($column_name, $post_type) {
    // Only show for our target post types
    $target_post_types = array('strategy_course', 'article', 'tool');
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Only show in the publishing_notes column (WordPress calls this for each column)
    if ($column_name !== 'publishing_notes') {
        return;
    }
    
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <div class="inline-edit-group wp-clearfix">
                <label class="inline-edit-status alignleft">
                    <span class="title"><?php esc_html_e('Publishing Notes', 'kingdom-training'); ?></span>
                    <textarea 
                        name="publishing_notes_bulk" 
                        id="publishing_notes_bulk" 
                        rows="4" 
                        style="width: 100%; margin-top: 5px;"
                        placeholder="<?php esc_attr_e('Enter notes to set for all selected posts...', 'kingdom-training'); ?>"
                    ></textarea>
                    <p class="description" style="margin-top: 5px;">
                        <?php esc_html_e('Enter notes to replace existing notes for all selected posts. Leave empty to keep existing notes unchanged.', 'kingdom-training'); ?>
                    </p>
                </label>
            </div>
        </div>
    </fieldset>
    <?php
}
// Hook for bulk edit only
add_action('bulk_edit_custom_box', 'gaal_add_publishing_notes_bulk_edit', 10, 2);

// Save bulk edit changes for Publishing Notes
function gaal_save_bulk_edit_publishing_notes() {
    // Check if this is a bulk edit request
    if (!isset($_REQUEST['bulk_edit']) || !isset($_REQUEST['post'])) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_posts')) {
        return;
    }
    
    // Get the post IDs
    $post_ids = array_map('intval', $_REQUEST['post']);
    
    // Get the post type from the first post (all should be the same type in bulk edit)
    if (empty($post_ids)) {
        return;
    }
    
    $first_post = get_post($post_ids[0]);
    if (!$first_post) {
        return;
    }
    
    $post_type = $first_post->post_type;
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    // Only process our target post types
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Check if publishing notes was provided in bulk edit
    // If not provided, don't change anything (WordPress bulk edit only updates provided fields)
    if (!isset($_REQUEST['publishing_notes_bulk'])) {
        return;
    }
    
    // Sanitize the notes
    $notes = sanitize_textarea_field($_REQUEST['publishing_notes_bulk']);
    
    // If empty, don't update (keep existing notes unchanged per description)
    // User must explicitly enter text to update
    if (empty(trim($notes))) {
        return;
    }
    
    // Update each post
    foreach ($post_ids as $post_id) {
        // Check if user can edit this post
        if (!current_user_can('edit_post', $post_id)) {
            continue;
        }
        
        // Verify this is the correct post type
        $post = get_post($post_id);
        if (!$post || !in_array($post->post_type, $target_post_types)) {
            continue;
        }
        
        // Update the meta
        update_post_meta($post_id, 'publishing_notes', $notes);
    }
}
add_action('load-edit.php', 'gaal_save_bulk_edit_publishing_notes');

// Add JavaScript for Publishing Notes bulk edit functionality
function gaal_publishing_notes_bulk_edit_script() {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, array('edit-strategy_course', 'edit-article', 'edit-tool'))) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Ensure publishing notes field is included when bulk edit form is submitted
        $(document).on('click', '#doaction, #doaction2', function(e) {
            var action = $(this).prev('select').val();
            if (action === 'edit') {
                // WordPress will show the bulk edit form
                // Our field should already be there via the hook
                // Just ensure it's visible when the form appears
                setTimeout(function() {
                    var bulkEditForm = $('#bulk-edit');
                    if (bulkEditForm.length > 0) {
                        // Ensure our fieldset is visible
                        var publishingNotesFieldset = bulkEditForm.find('fieldset.inline-edit-col-right');
                        if (publishingNotesFieldset.length > 0) {
                            publishingNotesFieldset.show();
                        }
                    }
                }, 50);
            }
        });
        
        // Handle form submission to ensure textarea is included
        $(document).on('submit', '#bulk-edit', function() {
            // The textarea will be included automatically if it exists
            // WordPress includes all form fields in bulk edit submission
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'gaal_publishing_notes_bulk_edit_script');

// Add Publishing Notes column to Strategy Course, Article, and Tool admin list tables
function gaal_add_publishing_notes_column_strategy_course($columns) {
    // Insert Publishing Notes column after Title (or after Featured Image if it exists)
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'featured_image' || ($key === 'title' && !isset($columns['featured_image']))) {
            $new_columns['publishing_notes'] = __('Publishing Notes', 'kingdom-training');
        }
    }
    // Fallback: add at end if position not found
    if (!isset($new_columns['publishing_notes'])) {
        $new_columns['publishing_notes'] = __('Publishing Notes', 'kingdom-training');
    }
    return $new_columns;
}
add_filter('manage_strategy_course_posts_columns', 'gaal_add_publishing_notes_column_strategy_course', 15);

function gaal_add_publishing_notes_column_article($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'featured_image' || ($key === 'title' && !isset($columns['featured_image']))) {
            $new_columns['publishing_notes'] = __('Publishing Notes', 'kingdom-training');
        }
    }
    if (!isset($new_columns['publishing_notes'])) {
        $new_columns['publishing_notes'] = __('Publishing Notes', 'kingdom-training');
    }
    return $new_columns;
}
add_filter('manage_article_posts_columns', 'gaal_add_publishing_notes_column_article', 15);

function gaal_add_publishing_notes_column_tool($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'featured_image' || ($key === 'title' && !isset($columns['featured_image']))) {
            $new_columns['publishing_notes'] = __('Publishing Notes', 'kingdom-training');
        }
    }
    if (!isset($new_columns['publishing_notes'])) {
        $new_columns['publishing_notes'] = __('Publishing Notes', 'kingdom-training');
    }
    return $new_columns;
}
add_filter('manage_tool_posts_columns', 'gaal_add_publishing_notes_column_tool', 15);

// Populate Publishing Notes column for Strategy Course
function gaal_populate_publishing_notes_column_strategy_course($column, $post_id) {
    if ($column === 'publishing_notes') {
        $notes = get_post_meta($post_id, 'publishing_notes', true);
        if ($notes) {
            // Truncate long notes for display
            $display_notes = wp_trim_words($notes, 15, '...');
            echo '<span class="publishing-notes" title="' . esc_attr($notes) . '" style="color: #666; font-size: 12px;">' . esc_html($display_notes) . '</span>';
        } else {
            echo '<span style="color: #ccc;">—</span>';
        }
    }
}
add_action('manage_strategy_course_posts_custom_column', 'gaal_populate_publishing_notes_column_strategy_course', 10, 2);

// Populate Publishing Notes column for Article
function gaal_populate_publishing_notes_column_article($column, $post_id) {
    if ($column === 'publishing_notes') {
        $notes = get_post_meta($post_id, 'publishing_notes', true);
        if ($notes) {
            $display_notes = wp_trim_words($notes, 15, '...');
            echo '<span class="publishing-notes" title="' . esc_attr($notes) . '" style="color: #666; font-size: 12px;">' . esc_html($display_notes) . '</span>';
        } else {
            echo '<span style="color: #ccc;">—</span>';
        }
    }
}
add_action('manage_article_posts_custom_column', 'gaal_populate_publishing_notes_column_article', 10, 2);

// Populate Publishing Notes column for Tool
function gaal_populate_publishing_notes_column_tool($column, $post_id) {
    if ($column === 'publishing_notes') {
        $notes = get_post_meta($post_id, 'publishing_notes', true);
        if ($notes) {
            $display_notes = wp_trim_words($notes, 15, '...');
            echo '<span class="publishing-notes" title="' . esc_attr($notes) . '" style="color: #666; font-size: 12px;">' . esc_html($display_notes) . '</span>';
        } else {
            echo '<span style="color: #ccc;">—</span>';
        }
    }
}
add_action('manage_tool_posts_custom_column', 'gaal_populate_publishing_notes_column_tool', 10, 2);

// Add Steps column to Strategy Course admin list table
function gaal_add_steps_column($columns) {
    // Insert Steps column after Title
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['steps'] = __('Step', 'kingdom-training');
        }
    }
    // If title column wasn't found, add steps at the beginning
    if (!isset($new_columns['steps'])) {
        $new_columns = array_merge(array('steps' => __('Step', 'kingdom-training')), $columns);
    }
    return $new_columns;
}
add_filter('manage_strategy_course_posts_columns', 'gaal_add_steps_column');

// Populate Steps column with step number
function gaal_populate_steps_column($column, $post_id) {
    if ($column === 'steps') {
        $steps = get_post_meta($post_id, 'steps', true);
        if ($steps) {
            echo '<strong>' . esc_html(intval($steps)) . '</strong>';
        } else {
            echo '<span style="color: #999;">—</span>';
        }
    }
}
add_action('manage_strategy_course_posts_custom_column', 'gaal_populate_steps_column', 10, 2);

// Make Steps column sortable
function gaal_make_steps_column_sortable($columns) {
    $columns['steps'] = 'steps';
    return $columns;
}
add_filter('manage_edit-strategy_course_sortable_columns', 'gaal_make_steps_column_sortable');

// Handle sorting by steps meta field
function gaal_sort_posts_by_steps($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('orderby') === 'steps') {
        $query->set('meta_key', 'steps');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'gaal_sort_posts_by_steps');

// Add Featured Image column to Strategy Course admin list table
function gaal_add_featured_image_column_strategy_course($columns) {
    // Insert Featured Image column after Title
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['featured_image'] = __('Featured Image', 'kingdom-training');
        }
    }
    // If title column wasn't found, add featured_image at the beginning
    if (!isset($new_columns['featured_image'])) {
        $new_columns = array_merge(array('featured_image' => __('Featured Image', 'kingdom-training')), $columns);
    }
    return $new_columns;
}
add_filter('manage_strategy_course_posts_columns', 'gaal_add_featured_image_column_strategy_course');

// Populate Featured Image column for Strategy Course
function gaal_populate_featured_image_column_strategy_course($column, $post_id) {
    if ($column === 'featured_image') {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {
            echo get_the_post_thumbnail($post_id, array(60, 60), array('style' => 'max-width: 60px; height: auto;'));
        } else {
            echo '<span style="color: #999;">—</span>';
        }
    }
}
add_action('manage_strategy_course_posts_custom_column', 'gaal_populate_featured_image_column_strategy_course', 10, 2);

// Add Featured Image column to Tool admin list table
function gaal_add_featured_image_column_tool($columns) {
    // Insert Featured Image column after Title
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['featured_image'] = __('Featured Image', 'kingdom-training');
        }
    }
    // If title column wasn't found, add featured_image at the beginning
    if (!isset($new_columns['featured_image'])) {
        $new_columns = array_merge(array('featured_image' => __('Featured Image', 'kingdom-training')), $columns);
    }
    return $new_columns;
}
add_filter('manage_tool_posts_columns', 'gaal_add_featured_image_column_tool');

// Populate Featured Image column for Tool
function gaal_populate_featured_image_column_tool($column, $post_id) {
    if ($column === 'featured_image') {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {
            echo get_the_post_thumbnail($post_id, array(60, 60), array('style' => 'max-width: 60px; height: auto;'));
        } else {
            echo '<span style="color: #999;">—</span>';
        }
    }
}
add_action('manage_tool_posts_custom_column', 'gaal_populate_featured_image_column_tool', 10, 2);

// Add Featured Image column to Article admin list table
function gaal_add_featured_image_column_article($columns) {
    // Insert Featured Image column after Title
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['featured_image'] = __('Featured Image', 'kingdom-training');
        }
    }
    // If title column wasn't found, add featured_image at the beginning
    if (!isset($new_columns['featured_image'])) {
        $new_columns = array_merge(array('featured_image' => __('Featured Image', 'kingdom-training')), $columns);
    }
    return $new_columns;
}
add_filter('manage_article_posts_columns', 'gaal_add_featured_image_column_article');

// Populate Featured Image column for Article
function gaal_populate_featured_image_column_article($column, $post_id) {
    if ($column === 'featured_image') {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {
            echo get_the_post_thumbnail($post_id, array(60, 60), array('style' => 'max-width: 60px; height: auto;'));
        } else {
            echo '<span style="color: #999;">—</span>';
        }
    }
}
add_action('manage_article_posts_custom_column', 'gaal_populate_featured_image_column_article', 10, 2);

// Remove Tags and Author columns from Article admin list table
function gaal_remove_article_columns($columns) {
    unset($columns['tags']);
    unset($columns['author']);
    return $columns;
}
add_filter('manage_article_posts_columns', 'gaal_remove_article_columns', 5);

// Remove Tags and Author columns from Tool admin list table
function gaal_remove_tool_columns($columns) {
    unset($columns['tags']);
    unset($columns['author']);
    return $columns;
}
add_filter('manage_tool_posts_columns', 'gaal_remove_tool_columns', 5);

/**
 * Add Language Filter Dropdown to Strategy Course, Article, and Tool Admin Tables
 * 
 * This adds a dropdown filter at the top of the posts table (next to category/date filters)
 * that allows filtering posts by Polylang language.
 */
function gaal_add_language_filter_dropdown($post_type) {
    // Only add to our custom post types
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Check if Polylang is active
    if (!function_exists('PLL') || !isset(PLL()->model)) {
        return;
    }
    
    // Get all languages from Polylang
    $languages = PLL()->model->get_languages_list();
    
    if (empty($languages)) {
        return;
    }
    
    // Get currently selected language filter
    $selected = isset($_GET['language_filter']) ? sanitize_text_field($_GET['language_filter']) : '';
    
    ?>
    <select name="language_filter" id="language_filter">
        <option value=""><?php esc_html_e('All Languages', 'kingdom-training'); ?></option>
        <?php foreach ($languages as $lang) : ?>
            <option value="<?php echo esc_attr($lang->slug); ?>" <?php selected($selected, $lang->slug); ?>>
                <?php echo esc_html($lang->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'gaal_add_language_filter_dropdown');

/**
 * Filter posts by language in admin when language_filter is selected
 */
function gaal_filter_posts_by_language($query) {
    global $pagenow;
    
    // Only apply in admin on edit.php page
    if (!is_admin() || $pagenow !== 'edit.php') {
        return;
    }
    
    // Only apply to main query
    if (!$query->is_main_query()) {
        return;
    }
    
    // Only apply to our custom post types
    $post_type = $query->get('post_type');
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Check if language filter is set
    if (empty($_GET['language_filter'])) {
        return;
    }
    
    $lang_slug = sanitize_text_field($_GET['language_filter']);
    
    // Verify the language exists in Polylang
    if (!function_exists('PLL') || !isset(PLL()->model)) {
        return;
    }
    
    $lang = PLL()->model->get_language($lang_slug);
    if (!$lang) {
        return;
    }
    
    // Add tax_query to filter by language
    $tax_query = $query->get('tax_query');
    if (!is_array($tax_query)) {
        $tax_query = array();
    }
    
    $tax_query[] = array(
        'taxonomy' => 'language',
        'field'    => 'slug',
        'terms'    => $lang_slug,
    );
    
    $query->set('tax_query', $tax_query);
}
add_action('pre_get_posts', 'gaal_filter_posts_by_language');

/**
 * Add Content Status Filter Dropdown to Strategy Course, Article, and Tool Admin Tables
 * 
 * This adds a dropdown filter at the top of the posts table (next to category/date filters)
 * that allows filtering posts by content status.
 */
function gaal_add_content_status_filter_dropdown($post_type) {
    // Only add to our custom post types
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Get content status options
    $options = gaal_get_content_status_options();
    
    if (empty($options)) {
        return;
    }
    
    // Get currently selected content status filter
    $selected = isset($_GET['content_status_filter']) ? sanitize_text_field($_GET['content_status_filter']) : '';
    
    ?>
    <select name="content_status_filter" id="content_status_filter">
        <option value=""><?php esc_html_e('All Content Statuses', 'kingdom-training'); ?></option>
        <?php foreach ($options as $value => $label) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($selected, $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'gaal_add_content_status_filter_dropdown');

/**
 * Filter posts by content status in admin when content_status_filter is selected
 */
function gaal_filter_posts_by_content_status($query) {
    global $pagenow;
    
    // Only apply in admin on edit.php page
    if (!is_admin() || $pagenow !== 'edit.php') {
        return;
    }
    
    // Only apply to main query
    if (!$query->is_main_query()) {
        return;
    }
    
    // Only apply to our custom post types
    $post_type = $query->get('post_type');
    $target_post_types = array('strategy_course', 'article', 'tool');
    
    if (!in_array($post_type, $target_post_types)) {
        return;
    }
    
    // Check if content status filter is set
    if (empty($_GET['content_status_filter'])) {
        return;
    }
    
    $status_value = sanitize_text_field($_GET['content_status_filter']);
    
    // Verify the status value is valid
    $valid_options = array_keys(gaal_get_content_status_options());
    if (!in_array($status_value, $valid_options)) {
        return;
    }
    
    // Add meta_query to filter by content status
    // Since content_status is stored as a serialized array, we need to search within it
    $meta_query = $query->get('meta_query');
    if (!is_array($meta_query)) {
        $meta_query = array();
    }
    
    // Use LIKE to search for the status value within the serialized array
    // WordPress serializes arrays as: a:1:{i:0;s:13:"status_value";}
    // We'll search for the status value with quotes to match the serialized format more precisely
    $meta_query[] = array(
        'key'     => 'content_status',
        'value'   => '"' . $status_value . '"',
        'compare' => 'LIKE',
    );
    
    $query->set('meta_query', $meta_query);
}
add_action('pre_get_posts', 'gaal_filter_posts_by_content_status');

// Decode HTML entities in article excerpts and content for admin list table
function gaal_decode_article_excerpt_admin($excerpt, $post) {
    // Only apply in admin area and for article post type
    if (is_admin() && isset($post) && $post->post_type === 'article') {
        // Decode HTML entities
        $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return $excerpt;
}
add_filter('get_the_excerpt', 'gaal_decode_article_excerpt_admin', 10, 2);

// Decode HTML entities when excerpt is displayed in admin
function gaal_decode_article_excerpt_display($excerpt) {
    // Only apply in admin area
    if (!is_admin()) {
        return $excerpt;
    }
    
    // Check if we're on the article list page
    global $typenow;
    if ($typenow === 'article') {
        // Decode HTML entities
        $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    return $excerpt;
}
add_filter('the_excerpt', 'gaal_decode_article_excerpt_display', 20);

// Disable the theme customizer (not needed for headless)
function gaal_remove_customizer() {
    global $wp_customize;
    if (isset($wp_customize)) {
        remove_action('after_setup_theme', array($wp_customize, 'setup_theme'));
    }
}
add_action('after_setup_theme', 'gaal_remove_customizer', 100);

// Clean up unnecessary WordPress features for headless setup
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

// Show the WordPress admin bar for logged-in users
// The admin bar will automatically appear for users with appropriate permissions

// ============================================================================
// THEME ASSET ENQUEUING
// ============================================================================

/**
 * Enqueue theme styles and scripts
 */
function kt_enqueue_assets() {
    // Enqueue Tailwind CSS
    $css_path = get_template_directory() . '/css/theme.css';
    $css_version = file_exists($css_path) ? filemtime($css_path) : '1.0.0';
    wp_enqueue_style(
        'kt-theme-style',
        get_template_directory_uri() . '/css/theme.css',
        array(),
        $css_version
    );
    
    // Enqueue Google Fonts (Inter)
    wp_enqueue_style(
        'kt-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );
    
    // Enqueue GenMap Background Animation CSS (only on front page)
    if (is_front_page()) {
        $genmap_css_path = get_template_directory() . '/css/genmap-background.css';
        $genmap_css_version = file_exists($genmap_css_path) ? filemtime($genmap_css_path) : '1.0.0';
        wp_enqueue_style(
            'kt-genmap-background',
            get_template_directory_uri() . '/css/genmap-background.css',
            array(),
            $genmap_css_version
        );
    }
    
    // Enqueue Neural Network Background Animation CSS (on strategy course pages, archive, and front page)
    if (is_singular('strategy_course') || is_post_type_archive('strategy_course') || is_front_page()) {
        $neuralnet_css_path = get_template_directory() . '/css/neuralnet-background.css';
        $neuralnet_css_version = file_exists($neuralnet_css_path) ? filemtime($neuralnet_css_path) : '1.0.0';
        wp_enqueue_style(
            'kt-neuralnet-background',
            get_template_directory_uri() . '/css/neuralnet-background.css',
            array(),
            $neuralnet_css_version
        );
    }
    
    // Enqueue Ideas Background Animation CSS (on article archive page)
    if (is_post_type_archive('article')) {
        $ideas_css_path = get_template_directory() . '/css/ideas-background.css';
        $ideas_css_version = file_exists($ideas_css_path) ? filemtime($ideas_css_path) : '1.0.0';
        wp_enqueue_style(
            'kt-ideas-background',
            get_template_directory_uri() . '/css/ideas-background.css',
            array(),
            $ideas_css_version
        );
    }
    
    // Enqueue LLM Background Animation CSS (on tool pages and archive)
    if (is_singular('tool') || is_post_type_archive('tool')) {
        $llm_css_path = get_template_directory() . '/css/llm-background.css';
        $llm_css_version = file_exists($llm_css_path) ? filemtime($llm_css_path) : '1.0.0';
        wp_enqueue_style(
            'kt-llm-background',
            get_template_directory_uri() . '/css/llm-background.css',
            array(),
            $llm_css_version
        );
    }
    
    // Enqueue JavaScript files
    $js_files = array(
        'mobile-menu'       => '/js/mobile-menu.js',
        'search-modal'      => '/js/search-modal.js',
        'language-selector' => '/js/language-selector.js',
    );
    
    // Enqueue GenMap Background Animation JS (only on front page)
    if (is_front_page()) {
        $js_files['genmap-background'] = '/js/genmap-background.js';
    }
    
    // Enqueue Neural Network Background Animation JS (on strategy course pages, archive, and front page)
    if (is_singular('strategy_course') || is_post_type_archive('strategy_course') || is_front_page()) {
        $js_files['neuralnet-background'] = '/js/neuralnet-background.js';
    }
    
    // Enqueue additional JS files for strategy course pages and archive
    if (is_singular('strategy_course') || is_post_type_archive('strategy_course')) {
        $js_files['roadmap-parallax'] = '/js/roadmap-parallax.js';
        $js_files['course-progress'] = '/js/course-progress.js';
    }
    
    // Enqueue Ideas Background Animation JS (on article archive page)
    if (is_post_type_archive('article')) {
        $js_files['ideas-background'] = '/js/ideas-background.js';
    }
    
    // Enqueue LLM Background Animation JS (on tool pages and archive)
    if (is_singular('tool') || is_post_type_archive('tool')) {
        $js_files['llm-background'] = '/js/llm-background.js';
    }
    
    foreach ($js_files as $handle => $path) {
        $full_path = get_template_directory() . $path;
        if (file_exists($full_path)) {
            wp_enqueue_script(
                'kt-' . $handle,
                get_template_directory_uri() . $path,
                array(),
                filemtime($full_path),
                true // Load in footer
            );
        }
    }
    
    // Localize scripts with translation strings if needed
    wp_localize_script('kt-search-modal', 'ktTranslations', array(
        'searchPlaceholder' => kt_t('search_placeholder_courses_tools'),
        'noResults'         => kt_t('search_no_results'),
        'loading'           => kt_t('ui_loading'),
        'searchStartTyping' => kt_t('search_start_typing'),
        'currentLanguage'  => kt_get_current_language(), // Add current language for search filtering
    ));
    
    // Localize genmap background animation with translated prompts (only on front page)
    if (is_front_page()) {
        wp_localize_script('kt-genmap-background', 'ktGenMapPrompts', array(
            kt_t('hero_prompt_1'),
            kt_t('hero_prompt_2'),
            kt_t('hero_prompt_3'),
            kt_t('hero_prompt_4'),
            kt_t('hero_prompt_5'),
            kt_t('hero_prompt_6'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'kt_enqueue_assets');

/**
 * Add preload hints for critical resources
 */
function kt_add_preload_hints() {
    // Preload Google Fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'kt_add_preload_hints', 1);

// ============================================================================
// LEGACY REACT FRONTEND (DISABLED)
// ============================================================================

/**
 * LEGACY: Serve React/Vite static files from theme directory
 * This function has been disabled as the theme now uses PHP templates.
 * Kept for reference during migration.
 * 
 * @deprecated 2.0.0 Use PHP templates instead
 */
function kingdom_training_serve_frontend() {
    // DISABLED: Theme now uses PHP templates instead of React
    return;
    // CRITICAL: Check for REST API requests FIRST, before any other processing
    // Get the raw REQUEST_URI to check
    $raw_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    
    // Multiple checks to ensure we catch REST API requests
    // Check if this looks like a REST API request
    $is_rest_api = false;
    
    // Method 1: Check REQUEST_URI directly
    if (strpos($raw_uri, '/wp-json') !== false) {
        $is_rest_api = true;
    }
    
    // Method 2: Check WordPress REST API constant
    if (defined('REST_REQUEST') && REST_REQUEST) {
        $is_rest_api = true;
    }
    
    // Method 3: Use WordPress's REST API detection function if available
    if (function_exists('rest_is_rest_api_request') && rest_is_rest_api_request()) {
        $is_rest_api = true;
    }
    
    // Method 4: Check using WordPress's URL prefix function
    if (function_exists('rest_get_url_prefix')) {
        $rest_prefix = rest_get_url_prefix();
        if ($rest_prefix && strpos($raw_uri, '/' . $rest_prefix) !== false) {
            $is_rest_api = true;
        }
    }
    
    if ($is_rest_api) {
        // This is a REST API request - exit immediately and let WordPress handle it
        return;
    }
    
    // Now safely parse the URI for frontend serving
    $request_uri_full = $raw_uri;
    $request_uri_path = parse_url($request_uri_full, PHP_URL_PATH);
    
    // Don't interfere with admin, REST API, or AJAX requests
    if (is_admin() || defined('DOING_AJAX') || wp_doing_ajax()) {
        return;
    }

    // Don't interfere with login, registration, etc.
    if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
        return;
    }

    $theme_dir = get_template_directory();
    $dist_dir = $theme_dir . '/dist';
    
    // Check if dist directory exists (Vite build output)
    if (!is_dir($dist_dir)) {
        return; // Fall back to default WordPress template
    }

    // Process the request URI - use the path we already extracted
    $request_uri = trim($request_uri_path, '/');
    $home_path = parse_url(home_url(), PHP_URL_PATH);
    if ($home_path && $home_path !== '/') {
        $home_path = trim($home_path, '/');
        if (strpos($request_uri, $home_path) === 0) {
            $request_uri = substr($request_uri, strlen($home_path));
            $request_uri = trim($request_uri, '/');
        }
    }

    // Check if it's a request for files in the dist directory
    // This includes /assets/... files and root-level files like /kt-logo-header.webp
    // Check both the processed URI and the original path
    $has_extension = pathinfo($request_uri, PATHINFO_EXTENSION);
    $original_has_extension = pathinfo($request_uri_path, PATHINFO_EXTENSION);
    
    if ($has_extension || $original_has_extension) {
        // It's a static asset request (JS, CSS, images, etc.)
        // Normalize the path - ensure no double slashes
        $normalized_uri = ltrim($request_uri, '/');
        $normalized_original = ltrim($request_uri_path, '/');
        
        // Try multiple possible paths to find the file
        $possible_paths = array(
            $dist_dir . '/' . $normalized_uri,  // Processed URI
            $dist_dir . '/' . $normalized_original,  // Original path normalized
            $dist_dir . '/' . $request_uri_path,  // Original path as-is
            $dist_dir . '/' . ltrim($request_uri_path, '/'),  // Original path without leading slash
        );
        
        // Also try with the original REQUEST_URI directly (before any parsing)
        if (isset($_SERVER['REQUEST_URI'])) {
            $raw_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $raw_normalized = ltrim($raw_path, '/');
            $possible_paths[] = $dist_dir . '/' . $raw_normalized;
            $possible_paths[] = $dist_dir . '/' . $raw_path;
        }
        
        // Remove duplicates and empty paths
        $possible_paths = array_filter(array_unique($possible_paths));
        
        $file_path = null;
        foreach ($possible_paths as $path) {
            // Normalize path separators
            $path = str_replace('\\', '/', $path);
            if (file_exists($path) && is_file($path)) {
                $file_path = $path;
                break;
            }
        }
        
        if ($file_path) {
            // Set proper content type based on file extension
            // Use whichever extension was found
            $extension = strtolower($has_extension ? $has_extension : $original_has_extension);
            $mime_types = array(
                'js' => 'application/javascript',
                'css' => 'text/css',
                'json' => 'application/json',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'eot' => 'application/vnd.ms-fontobject',
            );
            
            $mime_type = isset($mime_types[$extension]) 
                ? $mime_types[$extension] 
                : mime_content_type($file_path);
            
            if ($mime_type) {
                header('Content-Type: ' . $mime_type);
            }
            
            // Enhanced cache headers for static assets (1 year cache)
            // Use immutable for versioned assets (files with hash in name like main-abc123.js)
            $is_versioned = preg_match('/[a-f0-9]{8,}/i', basename($file_path));
            $cache_directive = $is_versioned 
                ? 'public, max-age=31536000, immutable' 
                : 'public, max-age=31536000';
            
            header('Cache-Control: ' . $cache_directive);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            
            // Add ETag for cache validation (based on file modification time and size)
            $filemtime = filemtime($file_path);
            $filesize = filesize($file_path);
            $etag = md5($file_path . $filemtime . $filesize);
            header('ETag: "' . $etag . '"');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $filemtime) . ' GMT');
            
            // Handle conditional requests (304 Not Modified)
            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $if_modified_since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
                if ($if_modified_since >= $filemtime) {
                    header('HTTP/1.1 304 Not Modified');
                    exit;
                }
            }
            
            readfile($file_path);
            exit;
        }
    }
    
    // It's a route - serve index.html for client-side routing
    // React Router will handle the routing on the client side
    $file_path = $dist_dir . '/index.html';
    if (file_exists($file_path) && is_file($file_path)) {
        // Add performance headers
        header('Content-Type: text/html; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        
        // Cache HTML for shorter duration (5 minutes) since it may change
        // But still cacheable to improve repeat visit performance
        header('Cache-Control: public, max-age=300, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 300) . ' GMT');
        
        // Add ETag for HTML file
        $filemtime = filemtime($file_path);
        $filesize = filesize($file_path);
        $etag = md5($file_path . $filemtime . $filesize);
        header('ETag: "' . $etag . '"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $filemtime) . ' GMT');
        
        // Handle conditional requests (304 Not Modified)
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $if_modified_since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($if_modified_since >= $filemtime) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }
        
        // Enable compression if not already handled by server
        if (!headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') !== false) {
                ob_start('ob_gzhandler');
            }
        }
        
        $content = file_get_contents($file_path);
        
        // Get theme URI for asset paths
        $theme_uri = get_template_directory_uri() . '/dist';
        
        // Replace absolute asset paths with theme-relative paths FIRST
        // Handle href="/assets/..." and src="/assets/..." (most common case)
        $content = preg_replace('/(href|src)=["\']\/(assets\/[^"\']+)["\']/', '$1="' . $theme_uri . '/$2"', $content);
        
        // Handle other files in dist directory (like /kt-logo-header.webp, /vite.svg, /robots.txt, etc.)
        // Only replace if the file exists in the dist directory
        $content = preg_replace_callback(
            '/(href|src)=["\']\/([^"\']+\.[a-zA-Z0-9]+)["\']/',
            function($matches) use ($dist_dir, $theme_uri) {
                // Skip assets/ paths as they're already handled above
                if (strpos($matches[2], 'assets/') === 0) {
                    return $matches[0];
                }
                
                $file_path = $dist_dir . '/' . $matches[2];
                // Only replace if file exists in dist directory and is not a WordPress path
                if (file_exists($file_path) && strpos($matches[2], 'wp-') !== 0 && strpos($matches[2], 'wp/') !== 0) {
                    return $matches[1] . '="' . $theme_uri . '/' . $matches[2] . '"';
                }
                return $matches[0]; // Keep original if file doesn't exist or is a WordPress path
            },
            $content
        );
        
        // Add preload hints for critical resources AFTER path replacement (improves FCP/LCP)
        $preload_hints = '';
        
        // Preload the main CSS file (critical for rendering) - now using already-replaced paths
        if (preg_match('/href="([^"]*main[^"]*\.css)"/', $content, $css_match)) {
            // The path is already replaced, so use it directly
            $preload_hints .= '<link rel="preload" href="' . esc_attr($css_match[1]) . '" as="style">' . "\n    ";
        }
        
        // Preload Google Fonts connection
        $preload_hints .= '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n    ";
        $preload_hints .= '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' . "\n    ";
        
        // Preload WordPress API connection
        $preload_hints .= '<link rel="preconnect" href="' . esc_url(home_url()) . '">' . "\n    ";
        
        // Insert preload hints after charset meta tag
        $content = str_replace('<meta charset="UTF-8" />', '<meta charset="UTF-8" />' . "\n    " . $preload_hints, $content);
        $content = str_replace('<meta charset="UTF-8">', '<meta charset="UTF-8">' . "\n    " . $preload_hints, $content);
        
        // =====================================================================
        // INJECT INITIAL DATA FOR FAST HOMEPAGE LOAD
        // =====================================================================
        // Detect language from URL path (e.g., /es/articles -> 'es')
        $initial_data_lang = null;
        if (function_exists('pll_languages_list')) {
            $available_langs = pll_languages_list();
            $uri_segments = explode('/', trim($request_uri, '/'));
            $first_segment = !empty($uri_segments[0]) ? $uri_segments[0] : '';
            if (in_array($first_segment, $available_langs)) {
                $initial_data_lang = $first_segment;
            }
        }
        
        // Get initial data and inject into HTML
        $initial_data = kingdom_training_get_initial_data($initial_data_lang);
        $initial_data_json = json_encode($initial_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        $initial_data_script = '<script id="__INITIAL_DATA__">window.__INITIAL_DATA__ = ' . $initial_data_json . ';</script>';
        
        // Insert before closing </head> tag
        $content = str_replace('</head>', $initial_data_script . "\n</head>", $content);
        
        echo $content;
        exit;
    }
}
// LEGACY: Hook disabled - theme now uses PHP templates
// Use a high priority to run before other template_redirect hooks
// add_action('template_redirect', 'kingdom_training_serve_frontend', 1);

// ============================================================================
// INITIAL DATA FOR FRONTEND (PHP-INJECTED FOR FAST LOAD)
// ============================================================================

/**
 * Format a WordPress post to match REST API structure
 * Used for injecting initial data into the frontend
 */
function kingdom_training_format_post_for_frontend($post) {
    $post_id = $post->ID;
    $author_id = $post->post_author;
    
    // Get featured image data
    $featured_media = get_post_thumbnail_id($post_id);
    $featured_image_url = null;
    $featured_image_sizes = null;
    
    if ($featured_media) {
        $image = wp_get_attachment_image_src($featured_media, 'full');
        $featured_image_url = $image ? $image[0] : null;
        
        $sizes = array();
        $image_sizes = array('thumbnail', 'medium', 'medium_large', 'large', 'full');
        foreach ($image_sizes as $size) {
            $img = wp_get_attachment_image_src($featured_media, $size);
            if ($img) {
                $sizes[$size] = array(
                    'url' => $img[0],
                    'width' => $img[1],
                    'height' => $img[2],
                );
            }
        }
        $featured_image_sizes = !empty($sizes) ? $sizes : null;
    }
    
    // Get language if Polylang is active
    $language = null;
    if (function_exists('pll_get_post_language')) {
        $lang = pll_get_post_language($post_id, 'slug');
        $language = $lang ?: null;
    }
    
    // Get steps meta for strategy courses
    $steps = null;
    if ($post->post_type === 'strategy_course') {
        $steps_meta = get_post_meta($post_id, 'steps', true);
        $steps = $steps_meta ? intval($steps_meta) : null;
    }
    
    return array(
        'id' => $post_id,
        'date' => get_the_date('c', $post_id),
        'modified' => get_the_modified_date('c', $post_id),
        'slug' => $post->post_name,
        'status' => $post->post_status,
        'type' => $post->post_type,
        'title' => array(
            'rendered' => get_the_title($post_id),
        ),
        'content' => array(
            'rendered' => apply_filters('the_content', $post->post_content),
        ),
        'excerpt' => array(
            'rendered' => get_the_excerpt($post_id),
        ),
        'author' => intval($author_id),
        'featured_media' => $featured_media ? intval($featured_media) : 0,
        'featured_image_url' => $featured_image_url,
        'featured_image_sizes' => $featured_image_sizes,
        'author_info' => array(
            'name' => get_the_author_meta('display_name', $author_id),
            'avatar' => get_avatar_url($author_id),
            'bio' => get_the_author_meta('description', $author_id),
        ),
        'steps' => $steps,
        'language' => $language,
    );
}

/**
 * Get initial data for frontend homepage
 * This data is injected into the HTML to avoid client-side API calls
 * 
 * @param string|null $lang Language code (e.g., 'es', 'fr') or null for default
 * @return array Initial data matching frontend expected structure
 */
function kingdom_training_get_initial_data($lang = null) {
    // Get default language
    $default_lang = function_exists('pll_default_language') ? pll_default_language() : null;
    
    // -------------------------------------------------------------------------
    // Get Course Steps (MVP Curriculum)
    // -------------------------------------------------------------------------
    $course_args = array(
        'post_type' => 'strategy_course',
        'posts_per_page' => 20,
        'post_status' => 'publish',
        'meta_key' => 'steps',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'steps',
                'value' => array(1, 20),
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN',
            ),
        ),
    );
    
    // Add language filter if Polylang is active
    if (function_exists('pll_get_post_language')) {
        if ($lang) {
            $course_args['lang'] = $lang;
        } elseif ($default_lang) {
            $course_args['lang'] = $default_lang;
        }
    }
    
    $course_posts = get_posts($course_args);
    $course_steps = array_map('kingdom_training_format_post_for_frontend', $course_posts);
    
    // -------------------------------------------------------------------------
    // Get Featured Articles (3 most recent)
    // -------------------------------------------------------------------------
    $article_args = array(
        'post_type' => 'article',
        'posts_per_page' => 3,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    if (function_exists('pll_get_post_language')) {
        if ($lang) {
            $article_args['lang'] = $lang;
        } elseif ($default_lang) {
            $article_args['lang'] = $default_lang;
        }
    }
    
    $article_posts = get_posts($article_args);
    $articles = array_map('kingdom_training_format_post_for_frontend', $article_posts);
    
    // -------------------------------------------------------------------------
    // Get Featured Tools (3 most recent)
    // -------------------------------------------------------------------------
    $tool_args = array(
        'post_type' => 'tool',
        'posts_per_page' => 3,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    if (function_exists('pll_get_post_language')) {
        if ($lang) {
            $tool_args['lang'] = $lang;
        } elseif ($default_lang) {
            $tool_args['lang'] = $default_lang;
        }
    }
    
    $tool_posts = get_posts($tool_args);
    $tools = array_map('kingdom_training_format_post_for_frontend', $tool_posts);
    
    return array(
        'courseSteps' => $course_steps,
        'articles' => $articles,
        'tools' => $tools,
        'lang' => $lang,
        'defaultLang' => $default_lang,
    );
}

// ============================================================================
// TRANSLATION SYSTEM FOR FRONTEND UI STRINGS
// ============================================================================

/**
 * Get all translatable UI strings
 * 
 * This is the SINGLE SOURCE OF TRUTH for all translatable strings.
 * Both Polylang registration and the Auto Translate dashboard use this function.
 * 
 * Format: array(
 *     'string_key' => array(
 *         'string' => 'The actual string text',
 *         'context' => 'Frontend UI', // or 'WordPress', etc.
 *         'multiline' => false, // true for long strings
 *     ),
 *     ...
 * )
 * 
 * @return array All translatable strings
 */
function gaal_get_all_translatable_strings() {
    return array(
        // Navigation Menu Strings
        'nav_home' => array('string' => 'Home', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_articles' => array('string' => 'Articles', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_tools' => array('string' => 'Tools', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_strategy_course' => array('string' => 'Strategy Course', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_strategy_courses' => array('string' => 'Strategy Courses', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_newsletter' => array('string' => 'Newsletter', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_search' => array('string' => 'Search', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_login' => array('string' => 'Login', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_menu' => array('string' => 'Menu', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_about' => array('string' => 'About', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_enroll_mvp' => array('string' => 'Enroll in The MVP Course', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_start_mvp' => array('string' => 'Start the MVP Course', 'context' => 'Frontend UI', 'multiline' => false),
        'nav_subscribe_newsletter' => array('string' => 'Subscribe to Newsletter', 'context' => 'Frontend UI', 'multiline' => false),

        // Common UI Strings
        'ui_read_more' => array('string' => 'Learn more', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_view_all' => array('string' => 'View all', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_browse_all' => array('string' => 'Browse all', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_back_to' => array('string' => 'Back to', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_explore' => array('string' => 'Explore', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_read_articles' => array('string' => 'Read Articles', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_explore_tools' => array('string' => 'Explore Tools', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_select_language' => array('string' => 'Select Language', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_close' => array('string' => 'Close', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_loading' => array('string' => 'Loading...', 'context' => 'Frontend UI', 'multiline' => false),
        'ui_searching' => array('string' => 'Searching...', 'context' => 'Frontend UI', 'multiline' => false),

        // Page Headers and Titles
        'page_latest_articles' => array('string' => 'Latest Articles', 'context' => 'Frontend UI', 'multiline' => false),
        'page_featured_tools' => array('string' => 'Featured Tools', 'context' => 'Frontend UI', 'multiline' => false),
        'page_key_information' => array('string' => 'Key Information About Media to Disciple Making Movements', 'context' => 'Frontend UI', 'multiline' => false),
        'page_mvp_strategy_course' => array('string' => 'The MVP: Strategy Course', 'context' => 'Frontend UI', 'multiline' => false),
        'page_start_strategy_course' => array('string' => 'Start The Strategy Course', 'context' => 'Frontend UI', 'multiline' => false),
        'page_step_curriculum' => array('string' => 'The {count}-Step Curriculum:', 'context' => 'Frontend UI', 'multiline' => false),
        'page_strategy_course' => array('string' => 'Strategy Course', 'context' => 'Frontend UI', 'multiline' => false),
        'page_strategy_course_description' => array('string' => 'Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program below to develop your complete M2DMM strategy.', 'context' => 'Frontend UI', 'multiline' => true),
        'page_tools' => array('string' => 'Tools', 'context' => 'Frontend UI', 'multiline' => false),
        'page_articles' => array('string' => 'Articles', 'context' => 'Frontend UI', 'multiline' => false),
        'page_newsletter' => array('string' => 'Newsletter', 'context' => 'Frontend UI', 'multiline' => false),
        'page_about' => array('string' => 'About Us', 'context' => 'Frontend UI', 'multiline' => false),

        // Content Messages
        'msg_no_articles' => array('string' => 'Articles will appear here once content is added to WordPress.', 'context' => 'Frontend UI', 'multiline' => false),
        'msg_no_tools' => array('string' => 'Tools will appear here once content is added to WordPress.', 'context' => 'Frontend UI', 'multiline' => false),
        'msg_no_content' => array('string' => 'No content found.', 'context' => 'Frontend UI', 'multiline' => false),
        'msg_discover_supplementary' => array('string' => 'Discover supplementary tools and resources to enhance your M2DMM strategy development and practice.', 'context' => 'Frontend UI', 'multiline' => false),
        'msg_discover_more' => array('string' => 'Discover more articles and resources to deepen your understanding and enhance your M2DMM practice.', 'context' => 'Frontend UI', 'multiline' => false),

        // Footer Strings
        'footer_quick_links' => array('string' => 'Quick Links', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_our_vision' => array('string' => 'Our Vision', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_subscribe' => array('string' => 'Subscribe to Newsletter', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_privacy_policy' => array('string' => 'Privacy Policy', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_all_rights' => array('string' => 'All rights reserved.', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_mission_statement' => array('string' => 'Training disciple makers to use media to accelerate Disciple Making Movements. Equipping practitioners with practical strategies that bridge online engagement with face-to-face discipleship.', 'context' => 'Frontend UI', 'multiline' => true),
        'footer_scripture_quote' => array('string' => 'And this gospel of the kingdom will be preached in the whole world as a testimony to all nations, and then the end will come.', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_scripture_citation' => array('string' => '— Matthew 24:14', 'context' => 'Frontend UI', 'multiline' => false),
        'footer_technology_paragraph' => array('string' => 'We leverage technology to accelerate disciple making movements worldwide.', 'context' => 'Frontend UI', 'multiline' => false),

        // Newsletter Strings
        'newsletter_title' => array('string' => 'Subscribe to Our Newsletter', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_description' => array('string' => 'Get the latest articles, tools, and resources delivered to your inbox.', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_page_header_description' => array('string' => 'Stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements.', 'context' => 'Frontend UI', 'multiline' => true),
        'newsletter_form_description' => array('string' => 'Get the latest training resources, articles, and insights delivered directly to your inbox. Join our community of disciple makers committed to using media strategically for Kingdom impact.', 'context' => 'Frontend UI', 'multiline' => true),
        'newsletter_what_to_expect' => array('string' => 'What to Expect', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_expect_item_1' => array('string' => 'Latest articles and insights on Media to Disciple Making Movements', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_expect_item_2' => array('string' => 'Practical tools and strategies for disciple makers', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_expect_item_3' => array('string' => 'Stories from the field and testimonies of impact', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_privacy_statement' => array('string' => 'We respect your privacy. Unsubscribe at any time.', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_privacy_link_text' => array('string' => 'Learn more about our privacy policy', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_subscribe' => array('string' => 'Subscribe', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_email_placeholder' => array('string' => 'Enter your email', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_name_placeholder' => array('string' => 'Enter your name', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_success' => array('string' => 'Successfully subscribed!', 'context' => 'Frontend UI', 'multiline' => false),
        'newsletter_error' => array('string' => 'Failed to subscribe. Please try again.', 'context' => 'Frontend UI', 'multiline' => false),

        // Search Strings
        'search_placeholder' => array('string' => 'Search...', 'context' => 'Frontend UI', 'multiline' => false),
        'search_no_results' => array('string' => 'No results found', 'context' => 'Frontend UI', 'multiline' => false),
        'search_results' => array('string' => 'Search Results', 'context' => 'Frontend UI', 'multiline' => false),
        'search_placeholder_courses_tools' => array('string' => 'Search strategy courses and tools...', 'context' => 'Frontend UI', 'multiline' => false),
        'search_no_results_try' => array('string' => 'Try a different search term', 'context' => 'Frontend UI', 'multiline' => false),
        'search_start_typing' => array('string' => 'Start typing to search...', 'context' => 'Frontend UI', 'multiline' => false),
        'search_start_typing_desc' => array('string' => 'Search strategy courses and tools', 'context' => 'Frontend UI', 'multiline' => false),
        'search_close' => array('string' => 'Close search', 'context' => 'Frontend UI', 'multiline' => false),
        'search_strategy_course' => array('string' => 'Strategy Course', 'context' => 'Frontend UI', 'multiline' => false),
        'search_tool' => array('string' => 'Tool', 'context' => 'Frontend UI', 'multiline' => false),

        // Breadcrumb Strings
        'breadcrumb_home' => array('string' => 'Home', 'context' => 'Frontend UI', 'multiline' => false),
        'breadcrumb_articles' => array('string' => 'Articles', 'context' => 'Frontend UI', 'multiline' => false),
        'breadcrumb_tools' => array('string' => 'Tools', 'context' => 'Frontend UI', 'multiline' => false),
        'breadcrumb_strategy_courses' => array('string' => 'Strategy Courses', 'context' => 'Frontend UI', 'multiline' => false),

        // Hero Section Strings
        'hero_explore_resources' => array('string' => 'Explore Our Resources', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_about_us' => array('string' => 'About Us', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_cta_about_us' => array('string' => 'About Us', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_cta_explore_resources' => array('string' => 'Explore Our Resources', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_description' => array('string' => 'Accelerate your disciple making with strategic use of media, advertising, and AI tools. Kingdom.Training is a resource for disciple makers to use media to accelerate Disciple Making Movements.', 'context' => 'Frontend UI', 'multiline' => true),
        'hero_subtitle_media_ai' => array('string' => 'Media, Advertising, and AI', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_title_innovate' => array('string' => 'Innovate → Accelerate → Make Disciples', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_title_innovate_word' => array('string' => 'Innovate', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_title_accelerate_word' => array('string' => 'Accelerate', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_title_make_disciples_word' => array('string' => 'Make Disciples', 'context' => 'Frontend UI', 'multiline' => false),
        'hero_newsletter_title' => array('string' => 'Stay Connected', 'context' => 'Frontend UI', 'multiline' => false),

        // Homepage Content Strings (longer text chunks)
        'home_mvp_description' => array('string' => 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context. Complete your plan in 6-7 hours.', 'context' => 'Frontend UI', 'multiline' => true),
        'home_newsletter_description' => array('string' => 'Field driven tools and articles for disciple makers.', 'context' => 'Frontend UI', 'multiline' => false),
        'home_heavenly_economy' => array('string' => 'We operate within what we call the "Heavenly Economy"—a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, we reflect God\'s generous nature by offering free training, hands-on coaching, and open-source tools like Disciple.Tools.', 'context' => 'Frontend UI', 'multiline' => true),
        'home_mission_statement' => array('string' => 'Our heart beats with passion for the unreached and least-reached peoples of the world. Every course, article, and tool serves the ultimate vision of seeing Disciple Making Movements catalyzed among people groups where the name of Jesus has never been proclaimed.', 'context' => 'Frontend UI', 'multiline' => true),
        'home_loading_steps' => array('string' => 'Loading course steps...', 'context' => 'Frontend UI', 'multiline' => false),
        
        // Course Strings
        'course_flagship_description' => array('string' => 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context.', 'context' => 'Frontend UI', 'multiline' => true),
        'course_complete_plan' => array('string' => 'Complete your plan step by step.', 'context' => 'Frontend UI', 'multiline' => false),

        // Content Strings
        'content_digital_disciple_making' => array('string' => 'Digital Disciple-Making', 'context' => 'Frontend UI', 'multiline' => false),
        'content_heavenly_economy' => array('string' => 'The Heavenly Economy', 'context' => 'Frontend UI', 'multiline' => false),
        'content_key_information_m2dmm' => array('string' => 'Key Information About Media to Disciple Making Movements', 'context' => 'Frontend UI', 'multiline' => false),
        'content_m2dmm_definition' => array('string' => 'Media to Disciple Making Movements (M2DMM) is a strategic approach that uses targeted media content to identify spiritual seekers online and connect them with face-to-face disciple-makers. The process involves three stages: (1) Media Content - targeted content reaches entire people groups through platforms like Facebook and Google Ads, (2) Digital Filtering - trained responders dialogue with seekers online, identifying persons of peace ready for face-to-face engagement, (3) Face-to-Face Discipleship - multipliers meet seekers in person, guiding them through discovery, obedience, and sharing in reproducing communities.', 'context' => 'Frontend UI', 'multiline' => true),
        'content_additional_resources' => array('string' => 'Additional Course Resources', 'context' => 'Frontend UI', 'multiline' => false),
        'content_supplementary_materials' => array('string' => 'Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development.', 'context' => 'Frontend UI', 'multiline' => true),
        'content_categories' => array('string' => 'Categories', 'context' => 'Frontend UI', 'multiline' => false),
        'content_tags' => array('string' => 'Tags', 'context' => 'Frontend UI', 'multiline' => false),
        'content_no_tools_found' => array('string' => 'No Tools Found', 'context' => 'Frontend UI', 'multiline' => false),
        'content_no_tools_try' => array('string' => 'Try adjusting your filters or check back later.', 'context' => 'Frontend UI', 'multiline' => false),
        'content_no_articles_found' => array('string' => 'No Articles Found', 'context' => 'Frontend UI', 'multiline' => false),
        'content_no_articles_try' => array('string' => 'Try adjusting your filters or check back later.', 'context' => 'Frontend UI', 'multiline' => false),
        
        // KeyInfoSection Terms & Definitions
        'content_m2dmm_term' => array('string' => 'What is Media to Disciple Making Movements (M2DMM)?', 'context' => 'Frontend UI', 'multiline' => false),
        'content_digital_disciple_making_term' => array('string' => 'What is digital disciple making?', 'context' => 'Frontend UI', 'multiline' => false),
        'content_digital_disciple_making_definition' => array('string' => 'Digital disciple making is the strategic use of all digital means—including social media, online advertising, AI tools, content creation, and digital communication platforms—to find seekers and bring them into relationship with Christ and his church in person. The ambition is to leverage every available digital tool and technique to identify spiritual seekers, engage them meaningfully online, and ultimately connect them with face-to-face discipleship communities where they can grow in their relationship with Jesus and participate in multiplying movements.', 'context' => 'Frontend UI', 'multiline' => true),
        'content_mvp_course_term' => array('string' => 'What is the MVP Strategy Course?', 'context' => 'Frontend UI', 'multiline' => false),
        'content_mvp_course_definition' => array('string' => 'The MVP (Minimum Viable Product) Strategy Course is a 10-step program that guides you through the core elements needed to craft a Media to Disciple Making Movements strategy for any context. The course helps you develop your complete M2DMM strategy and can be completed in 6-7 hours. It covers topics including media content creation, digital filtering strategies, face-to-face discipleship methods, and movement multiplication principles.', 'context' => 'Frontend UI', 'multiline' => true),
        'content_ai_discipleship_term' => array('string' => 'What is AI for discipleship?', 'context' => 'Frontend UI', 'multiline' => false),
        'content_ai_discipleship_definition' => array('string' => 'AI for discipleship empowers small teams to have a big impact by leveraging artificial intelligence tools and techniques. Kingdom.Training is bringing new techniques to accelerate small teams to use AI effectively in disciple making. These innovative approaches help teams scale their efforts, automate routine tasks, personalize engagement, and multiply their reach—enabling small groups to accomplish what previously required much larger teams.', 'context' => 'Frontend UI', 'multiline' => true),
        'content_heavenly_economy_term' => array('string' => 'What is the Heavenly Economy?', 'context' => 'Frontend UI', 'multiline' => false),
        'content_heavenly_economy_definition' => array('string' => 'The Heavenly Economy is a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, reflecting God\'s generous nature by offering free training, hands-on coaching, and open-source tools. This approach enables more people to access resources for disciple making, especially in unreached and least-reached areas.', 'context' => 'Frontend UI', 'multiline' => true),
        'content_kingdom_training_for_term' => array('string' => 'Who is Kingdom.Training for?', 'context' => 'Frontend UI', 'multiline' => false),
        'content_kingdom_training_for_definition' => array('string' => 'Kingdom.Training is for disciple makers, church planters, missionaries, and ministry leaders who want to use media strategically to accelerate Disciple Making Movements. We particularly focus on equipping those working with unreached and least-reached peoples - people groups where the name of Jesus has never been proclaimed or where there is no indigenous community of believers with adequate numbers and resources to evangelize their own people.', 'context' => 'Frontend UI', 'multiline' => true),
        
        // SEO Strings
        'seo_home_description' => array('string' => 'Training disciple makers to use media to accelerate Disciple Making Movements. Learn practical strategies that bridge online engagement with face-to-face discipleship. Start your M2DMM strategy course today.', 'context' => 'Frontend UI', 'multiline' => true),
        'seo_articles_description' => array('string' => 'Practical guidance, best practices, and real-world insights from the Media to Disciple Making Movements community. Learn from practitioners implementing M2DMM strategies around the world.', 'context' => 'Frontend UI', 'multiline' => true),
        'seo_tools_description' => array('string' => 'Essential tools and resources for Media to Disciple Making Movements work. Discover Disciple.Tools—our free, open-source disciple relationship management system—and other practical resources designed specifically for M2DMM practitioners.', 'context' => 'Frontend UI', 'multiline' => true),
        'seo_login_description' => array('string' => 'Login to Kingdom.Training to access your account and WordPress admin dashboard.', 'context' => 'Frontend UI', 'multiline' => false),
        'seo_newsletter_description' => array('string' => 'Subscribe to Kingdom.Training newsletter and stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements. Get practical insights delivered to your inbox.', 'context' => 'Frontend UI', 'multiline' => true),
        'seo_privacy_description' => array('string' => 'Privacy Policy for Kingdom.Training. Learn how we collect, use, and protect your personal information when you use our website and services.', 'context' => 'Frontend UI', 'multiline' => false),
        'seo_about_description' => array('string' => 'Kingdom.Training focuses on practical training for Media to Disciple Making Movements (M2DMM). We are field workers with a heart for the unreached and least-reached peoples, equipping disciple makers with strategic media tools.', 'context' => 'Frontend UI', 'multiline' => false),
        
        // Error Strings
        'error_404_title' => array('string' => 'Page Not Found', 'context' => 'Frontend UI', 'multiline' => false),
        'error_404_description' => array('string' => 'The page you are looking for might have been removed or is temporarily unavailable.', 'context' => 'Frontend UI', 'multiline' => false),
        'error_404_popular_pages' => array('string' => 'Popular Pages', 'context' => 'Frontend UI', 'multiline' => false),
        
        // Privacy Policy Page Strings
        'privacy_title' => array('string' => 'Privacy Policy', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_description' => array('string' => 'How we collect, use, and protect your information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_last_updated' => array('string' => 'Last updated:', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_introduction_title' => array('string' => 'Introduction', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_introduction_paragraph1' => array('string' => 'Kingdom.Training ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_introduction_paragraph2' => array('string' => 'By using our website, you consent to the data practices described in this policy. If you do not agree with the practices described in this policy, please do not use our website.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_information_collect_title' => array('string' => 'Information We Collect', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_title' => array('string' => 'Personal Information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_intro' => array('string' => 'We may collect personal information that you voluntarily provide to us when you:', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_subscribe' => array('string' => 'Subscribe to our newsletter', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_register' => array('string' => 'Register for courses or training', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_contact' => array('string' => 'Contact us through our website', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_surveys' => array('string' => 'Participate in surveys or feedback forms', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_account' => array('string' => 'Create an account or profile', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_may_include' => array('string' => 'This information may include:', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_name_contact' => array('string' => 'Name and contact information (email address, phone number)', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_mailing' => array('string' => 'Mailing address', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_organization' => array('string' => 'Organization or ministry affiliation', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_info_other' => array('string' => 'Any other information you choose to provide', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_title' => array('string' => 'Automatically Collected Information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_intro' => array('string' => 'When you visit our website, we may automatically collect certain information about your device and usage, including:', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_auto_info_ip' => array('string' => 'IP address', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_auto_info_browser' => array('string' => 'Browser type and version', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_auto_info_os' => array('string' => 'Operating system', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_auto_info_pages' => array('string' => 'Pages visited and time spent on pages', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_auto_info_referring' => array('string' => 'Referring website addresses', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_auto_info_datetime' => array('string' => 'Date and time of access', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_title' => array('string' => 'How We Use Your Information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_intro' => array('string' => 'We use the information we collect for various purposes, including:', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_use_provide' => array('string' => 'To provide, maintain, and improve our services', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_use_newsletters' => array('string' => 'To send you newsletters, updates, and communications about our training resources', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_use_respond' => array('string' => 'To respond to your inquiries and provide customer support', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_use_process' => array('string' => 'To process registrations and manage your account', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_use_analyze' => array('string' => 'To analyze website usage and trends to improve user experience', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_use_detect' => array('string' => 'To detect, prevent, and address technical issues and security threats', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_use_comply' => array('string' => 'To comply with legal obligations', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_sharing_title' => array('string' => 'Information Sharing and Disclosure', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_sharing_intro' => array('string' => 'We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_service_providers' => array('string' => 'Service Providers: We may share information with trusted third-party service providers who assist us in operating our website, conducting our business, or serving our users, as long as they agree to keep this information confidential.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_legal' => array('string' => 'Legal Requirements: We may disclose your information if required by law or in response to valid requests by public authorities.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_protection' => array('string' => 'Protection of Rights: We may share information when we believe release is appropriate to protect our rights, property, or safety, or that of our users or others.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_business' => array('string' => 'Business Transfers: In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_security_title' => array('string' => 'Data Security', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_security_content' => array('string' => 'We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_cookies_title' => array('string' => 'Cookies and Tracking Technologies', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_cookies_content' => array('string' => 'We use cookies and similar tracking technologies to track activity on our website and store certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_third_party_title' => array('string' => 'Third-Party Links', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_third_party_content' => array('string' => 'Our website may contain links to third-party websites that are not operated by us. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party websites. We encourage you to review the privacy policy of every site you visit.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_rights_title' => array('string' => 'Your Privacy Rights', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_intro' => array('string' => 'Depending on your location, you may have certain rights regarding your personal information, including:', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_rights_access' => array('string' => 'Access: The right to request access to your personal information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_correction' => array('string' => 'Correction: The right to request correction of inaccurate or incomplete information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_deletion' => array('string' => 'Deletion: The right to request deletion of your personal information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_objection' => array('string' => 'Objection: The right to object to processing of your personal information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_portability' => array('string' => 'Data Portability: The right to request transfer of your data to another service', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_withdraw' => array('string' => 'Withdraw Consent: The right to withdraw consent where processing is based on consent', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_contact' => array('string' => 'To exercise these rights, please contact us using the information provided in the "Contact Us" section below.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_children_title' => array('string' => 'Children\'s Privacy', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_children_content' => array('string' => 'Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us, and we will delete such information from our systems.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_changes_title' => array('string' => 'Changes to This Privacy Policy', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_changes_content' => array('string' => 'We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. You are advised to review this Privacy Policy periodically for any changes.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_contact_title' => array('string' => 'Contact Us', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_contact_intro' => array('string' => 'If you have any questions about this Privacy Policy or our data practices, please contact us:', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_contact_organization' => array('string' => 'Kingdom.Training', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_contact_email' => array('string' => 'Email:', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_contact_website' => array('string' => 'Website:', 'context' => 'Frontend UI', 'multiline' => false),
        
        // Privacy Policy Page Strings (matching page-privacy.php template keys)
        'privacy_header_description' => array('string' => 'How we collect, use, and protect your information.', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_introduction_paragraph_1' => array('string' => 'Kingdom.Training ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website at ai.kingdom.training.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_introduction_paragraph_2' => array('string' => 'By using our website, you consent to the data practices described in this policy. If you do not agree with the practices described in this policy, please do not use our website.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_personal_information_item_1' => array('string' => 'Subscribe to our newsletter', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_item_2' => array('string' => 'Register for courses or training', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_item_3' => array('string' => 'Contact us through our website', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_item_4' => array('string' => 'Participate in surveys or feedback forms', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_item_5' => array('string' => 'Create an account or profile', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_list_2_intro' => array('string' => 'This information may include:', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_list_2_item_1' => array('string' => 'Name and contact information (email address, phone number)', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_list_2_item_2' => array('string' => 'Mailing address', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_list_2_item_3' => array('string' => 'Organization or ministry affiliation', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_personal_information_list_2_item_4' => array('string' => 'Any other information you choose to provide', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_item_1' => array('string' => 'IP address', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_item_2' => array('string' => 'Browser type and version', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_item_3' => array('string' => 'Operating system', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_item_4' => array('string' => 'Pages visited and time spent on page', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_item_5' => array('string' => 'Referring website address', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_automatically_collected_item_6' => array('string' => 'Date and time of access', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_1' => array('string' => 'To provide, maintain, and improve our services', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_2' => array('string' => 'To send you newsletters, updates, and communications about our training resources', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_3' => array('string' => 'To respond to your inquiries and provide customer support', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_4' => array('string' => 'To process registrations and manage your account', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_5' => array('string' => 'To analyze website usage and trends to improve user experience', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_6' => array('string' => 'To detect, prevent, and address technical issues and security threats', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_how_use_item_7' => array('string' => 'To comply with legal obligations', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_sharing_item_1' => array('string' => 'Service Providers: We may share information with trusted third-party service providers who assist us in operating our website, conducting our business, or serving our users, as long as they agree to keep this information confidential.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_item_2' => array('string' => 'Legal Requirements: We may disclose your information if required by law or in response to valid requests by public authorities.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_item_3' => array('string' => 'Protection of Rights: We may share information when we believe release is appropriate to protect our rights, property, or safety, or that of our users or others.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_sharing_item_4' => array('string' => 'Business Transfers: In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_data_security_text' => array('string' => 'We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_cookies_text' => array('string' => 'We use cookies and similar tracking technologies to track activity on our website and store certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_third_party_text' => array('string' => 'Our website may contain links to third-party websites that are not operated by us. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_rights_item_1' => array('string' => 'Access: The right to request access to your personal information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_item_2' => array('string' => 'Correction: The right to request correction of inaccurate or incomplete information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_item_3' => array('string' => 'Deletion: The right to request deletion of your personal information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_item_4' => array('string' => 'Objection: The right to object to processing of your personal information', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_item_5' => array('string' => 'Data Portability: The right to request transfer of your data to another service', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_rights_item_6' => array('string' => 'Withdraw Consent: The right to withdraw consent where processing is based on consent', 'context' => 'Frontend UI', 'multiline' => false),
        'privacy_children_text' => array('string' => 'Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us so we can delete such information.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_changes_text' => array('string' => 'We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. You are advised to review this Privacy Policy periodically for any changes.', 'context' => 'Frontend UI', 'multiline' => true),
        'privacy_contact_org' => array('string' => 'Kingdom.Training', 'context' => 'Frontend UI', 'multiline' => false),
        
        // About Page Strings
        'about_seo_title' => array('string' => 'About Us', 'context' => 'Frontend UI', 'multiline' => false),
        'about_page_title' => array('string' => 'About Kingdom.Training', 'context' => 'Frontend UI', 'multiline' => false),
        'about_page_description' => array('string' => 'Training disciple makers to use media strategically for Disciple Making Movements', 'context' => 'Frontend UI', 'multiline' => false),
        'about_vision_title' => array('string' => 'Our Vision', 'context' => 'Frontend UI', 'multiline' => false),
        'about_vision_paragraph1' => array('string' => 'Kingdom.Training focuses on practical training for Media to Disciple Making Movements (M2DMM). We are field workers with a heart for the unreached and least-reached peoples of the world, and our passion is to equip disciple makers with strategic media tools that bridge online engagement with face-to-face discipleship.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_vision_paragraph2' => array('string' => 'We operate within what we call the "Heavenly Economy"—a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, we reflect God\'s generous nature by offering free training, hands-on coaching, and open-source tools.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_mission_title' => array('string' => 'Our Mission', 'context' => 'Frontend UI', 'multiline' => false),
        'about_mission_paragraph1' => array('string' => 'Like the men of Issachar who understood the times, we equip disciple makers to use media strategically—identifying spiritual seekers online and connecting them with face-to-face disciplers who help them discover, obey, and share all that Jesus taught.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_mission_paragraph2' => array('string' => 'We wonder what the Church could accomplish with technology God has given to this generation for the first time in history.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_how_works_title' => array('string' => 'How it works', 'context' => 'Frontend UI', 'multiline' => false),
        'about_media_content_title' => array('string' => '1. Media Content', 'context' => 'Frontend UI', 'multiline' => false),
        'about_media_content_description' => array('string' => 'Targeted content reaches entire people groups through platforms like Facebook and Google Ads. This is the wide end of the funnel, introducing masses of people to the gospel message.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_digital_filtering_title' => array('string' => '2. Digital Filtering', 'context' => 'Frontend UI', 'multiline' => false),
        'about_digital_filtering_description' => array('string' => 'Trained responders dialogue with seekers online, identifying persons of peace ready for face-to-face engagement. This filters out disinterested individuals and focuses on genuine seekers.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_face_to_face_title' => array('string' => '3. Face-to-Face Discipleship', 'context' => 'Frontend UI', 'multiline' => false),
        'about_face_to_face_description' => array('string' => 'Multipliers meet seekers in person, guiding them through discovery, obedience, and sharing in reproducing communities. This is where true disciple making happens.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_foundation_title' => array('string' => 'Our Foundation', 'context' => 'Frontend UI', 'multiline' => false),
        'about_foundation_scripture1' => array('string' => 'Of the sons of Issachar, men who understood the times, with knowledge of what Israel should do.', 'context' => 'Frontend UI', 'multiline' => false),
        'about_foundation_scripture1_citation' => array('string' => '— 1 Chronicles 12:32', 'context' => 'Frontend UI', 'multiline' => false),
        'about_foundation_scripture2' => array('string' => 'Go and make disciples of all nations, baptizing them in the name of the Father and of the Son and of the Holy Spirit, and teaching them to obey everything I have commanded you. And surely I am with you always, to the very end of the age.', 'context' => 'Frontend UI', 'multiline' => true),
        'about_foundation_scripture2_citation' => array('string' => '— Matthew 28:19-20', 'context' => 'Frontend UI', 'multiline' => false),
        
        // Pagination Strings
        'pagination_previous' => array('string' => '← Previous', 'context' => 'Frontend UI', 'multiline' => false),
        'pagination_next' => array('string' => 'Next →', 'context' => 'Frontend UI', 'multiline' => false),
        'pagination_page_of' => array('string' => 'Page {current} of {total}', 'context' => 'Frontend UI', 'multiline' => false),
        
        // Hero Animation Prompts (GenMap Background)
        'hero_prompt_1' => array('string' => 'Create a discipleship training video that sparks movements in unreached communities...', 'context' => 'Hero Animation', 'multiline' => false),
        'hero_prompt_2' => array('string' => 'Generate an engaging video teaching biblical principles for multiplying disciples...', 'context' => 'Hero Animation', 'multiline' => false),
        'hero_prompt_3' => array('string' => 'Produce a testimony video showing how media catalyzes church planting movements...', 'context' => 'Hero Animation', 'multiline' => false),
        'hero_prompt_4' => array('string' => 'Make an interactive video equipping believers to share the Gospel through digital tools...', 'context' => 'Hero Animation', 'multiline' => false),
        'hero_prompt_5' => array('string' => 'Create a training series on facilitating discovery Bible studies in oral cultures...', 'context' => 'Hero Animation', 'multiline' => false),
        'hero_prompt_6' => array('string' => 'Generate content showing how one faithful disciple can multiply into thousands...', 'context' => 'Hero Animation', 'multiline' => false),
    );
}

/**
 * Register all UI strings for translation with Polylang
 * These strings are used in the React frontend and can be translated
 * via WordPress Admin > Languages > String translations
 * 
 * Uses the centralized gaal_get_all_translatable_strings() function
 */
function gaal_register_ui_strings() {
    if (!function_exists('pll_register_string')) {
        return; // Polylang not active
    }

    $strings = gaal_get_all_translatable_strings();
    
    foreach ($strings as $name => $data) {
        pll_register_string(
            $name,
            $data['string'],
            $data['context'],
            isset($data['multiline']) ? $data['multiline'] : false
        );
    }
}
add_action('init', 'gaal_register_ui_strings');

/**
 * REST API endpoint for frontend translations
 * Returns all registered UI strings translated to the specified language
 * 
 * Usage: GET /wp-json/gaal/v1/translations?lang=en
 */
function gaal_register_translations_api() {
    register_rest_route('gaal/v1', '/translations', array(
        'methods' => 'GET',
        'callback' => function($request) {
            if (!function_exists('pll_translate_string')) {
                return new WP_Error('polylang_not_active', 'Polylang is not active', array('status' => 500));
            }

            $lang = $request->get_param('lang');
            
            // If no language specified, try to get current language
            if (empty($lang) && function_exists('pll_current_language')) {
                $lang = pll_current_language('slug');
            }
            
            // If still no language, get default
            if (empty($lang) && function_exists('pll_default_language')) {
                $lang = pll_default_language('slug');
            }

            // Fallback to 'en' if still no language
            if (empty($lang)) {
                $lang = 'en';
            }

            // Get all registered strings from centralized source and translate them
            // Uses the same source as Polylang registration
            $all_strings = gaal_get_all_translatable_strings();
            $strings = array();
            
            foreach ($all_strings as $key => $data) {
                $strings[$key] = pll_translate_string($data['string'], $lang);
            }

            return array(
                'success' => true,
                'language' => $lang,
                'translations' => $strings,
            );
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gaal_register_translations_api');

// ============================================================================
// TRANSLATION AUTOMATION REST API ENDPOINTS
// ============================================================================

/**
 * Register translation automation REST API endpoints
 */
function gaal_register_translation_api() {
    // Generate all translations endpoint
    register_rest_route('gaal/v1', '/translate/generate-all', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_generate_all_translations',
        'permission_callback' => 'gaal_check_translation_permissions',
        'args' => array(
            'post_id' => array(
                'required' => true,
                'type' => 'integer',
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
            ),
        ),
    ));
    
    // Simple test endpoint - always returns success
    register_rest_route('gaal/v1', '/translate/test', array(
        'methods' => array('GET', 'POST'),
        'callback' => function($request) {
            return new WP_REST_Response(array(
                'success' => true,
                'message' => 'Test endpoint working',
                'method' => $request->get_method(),
                'params' => $request->get_params(),
                'body_params' => $request->get_body_params(),
                'json_params' => $request->get_json_params(),
                'body' => $request->get_body(),
            ), 200);
        },
        'permission_callback' => '__return_true', // No auth required for testing
    ));
    
    // Test Google Translate API configuration (public for debugging)
    register_rest_route('gaal/v1', '/translate/test-google', array(
        'methods' => 'GET',
        'callback' => function($request) {
            $google_api_key = get_option('gaal_translation_google_api_key', '');
            
            if (empty($google_api_key)) {
                return new WP_REST_Response(array(
                    'success' => false,
                    'error' => 'Google Translate API key is not configured',
                    'api_key_status' => 'empty',
                    'hint' => 'Set the API key in WordPress Admin > Settings > Translation Automation',
                ), 200);
            }
            
            // Test the API
            try {
                $google_translate = new GAAL_Google_Translate_API($google_api_key);
                $result = $google_translate->test_connection();
                
                if (is_wp_error($result)) {
                    return new WP_REST_Response(array(
                        'success' => false,
                        'error' => $result->get_error_message(),
                        'error_code' => $result->get_error_code(),
                        'error_data' => $result->get_error_data(),
                        'api_key_preview' => substr($google_api_key, 0, 8) . '...',
                        'api_key_length' => strlen($google_api_key),
                    ), 200);
                }
                
                return new WP_REST_Response(array(
                    'success' => true,
                    'message' => 'Google Translate API is working',
                    'test_result' => $result,
                ), 200);
            } catch (Exception $e) {
                return new WP_REST_Response(array(
                    'success' => false,
                    'error' => 'Exception: ' . $e->getMessage(),
                    'api_key_preview' => substr($google_api_key, 0, 8) . '...',
                ), 200);
            }
        },
        'permission_callback' => '__return_true', // Public for debugging
    ));
    
    // Translate single language endpoint
    register_rest_route('gaal/v1', '/translate/single', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_translate_single',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));
    
    // Re-translate endpoint
    register_rest_route('gaal/v1', '/translate/retranslate', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_retranslate',
        'permission_callback' => 'gaal_check_translation_permissions',
        'args' => array(
            'post_id' => array(
                'required' => true,
                'type' => 'integer',
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
            ),
            'target_language' => array(
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
    
    // Get translation status endpoint
    register_rest_route('gaal/v1', '/translate/status/(?P<post_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'gaal_api_get_translation_status',
        'permission_callback' => 'gaal_check_translation_permissions',
        'args' => array(
            'post_id' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ),
        ),
    ));
    
    // Resume job endpoint
    register_rest_route('gaal/v1', '/translate/resume', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_resume_job',
        'permission_callback' => 'gaal_check_translation_permissions',
    ));
    
    // Copy content from English endpoint
    register_rest_route('gaal/v1', '/translate/copy-from-english', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_copy_from_english',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
        'args' => array(
            'target_post_id' => array(
                'required' => true,
                'type' => 'integer',
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
            ),
            'source_post_id' => array(
                'required' => true,
                'type' => 'integer',
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
            ),
        ),
    ));
    
    // Chunked translation endpoint - handles translation in steps to avoid timeout
    register_rest_route('gaal/v1', '/translate/chunked', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_translate_chunked',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
        'args' => array(
            'source_post_id' => array(
                'required' => true,
                'type' => 'integer',
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
            ),
            'target_language' => array(
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'step' => array(
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'job_id' => array(
                'required' => false,
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
            'target_post_id' => array(
                'required' => false,
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
        ),
    ));
    
    // =========================================================================
    // AUTO TRANSLATE DASHBOARD ENDPOINTS
    // =========================================================================
    
    // Scan for translation gaps
    register_rest_route('gaal/v1', '/translate/scan', array(
        'methods' => 'GET',
        'callback' => 'gaal_api_scan_translation_gaps',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'args' => array(
            'post_type' => array(
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'language' => array(
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
    
    // Create draft translations
    register_rest_route('gaal/v1', '/translate/create-drafts', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_create_translation_drafts',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
}

/**
 * Scan for translation gaps
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response
 */
function gaal_api_scan_translation_gaps($request) {
    $scanner = new GAAL_Translation_Scanner();
    
    $filters = array();
    if ($request->get_param('post_type')) {
        $filters['post_type'] = $request->get_param('post_type');
    }
    if ($request->get_param('language')) {
        $filters['language'] = $request->get_param('language');
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'gaps' => $scanner->find_gaps($filters),
        'summary' => $scanner->get_summary(),
    ));
}

/**
 * Create draft translations
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_create_translation_drafts($request) {
    $items = $request->get_param('items');
    
    if (empty($items)) {
        return new WP_Error('no_items', __('No items provided', 'kingdom-training'), array('status' => 400));
    }
    
    $batch_translator = new GAAL_Batch_Translator();
    $results = $batch_translator->create_drafts($items);
    
    // Count results
    $created = 0;
    $existed = 0;
    $errors = 0;
    
    foreach ($results as $post_results) {
        if (isset($post_results['error'])) {
            $errors++;
            continue;
        }
        foreach ($post_results as $lang_result) {
            if (isset($lang_result['status'])) {
                switch ($lang_result['status']) {
                    case 'created': $created++; break;
                    case 'exists': $existed++; break;
                    case 'error': $errors++; break;
                }
            }
        }
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'results' => $results,
        'summary' => array(
            'created' => $created,
            'existed' => $existed,
            'errors' => $errors,
        ),
    ));
}

/**
 * Get existing translations
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response
 */
function gaal_api_get_existing_translations($request) {
    $scanner = new GAAL_Translation_Scanner();
    
    $filters = array();
    if ($request->get_param('post_type')) {
        $filters['post_type'] = $request->get_param('post_type');
    }
    if ($request->get_param('language')) {
        $filters['language'] = $request->get_param('language');
    }
    if ($request->get_param('status')) {
        $filters['status'] = $request->get_param('status');
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'translations' => $scanner->find_existing_translations($filters),
        'summary' => $scanner->get_translations_summary(),
    ));
}

/**
 * LLM evaluate a translation
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_llm_evaluate($request) {
    $post_id = $request->get_param('post_id');
    
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error('post_not_found', __('Post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Get source post
    $source_post_id = null;
    if (function_exists('pll_get_post_translations')) {
        $translations = pll_get_post_translations($post_id);
        if (isset($translations['en'])) {
            $source_post_id = $translations['en'];
        }
    }
    
    if (!$source_post_id) {
        return new WP_Error('source_not_found', __('English source post not found', 'kingdom-training'), array('status' => 404));
    }
    
    $source_post = get_post($source_post_id);
    if (!$source_post) {
        return new WP_Error('source_not_found', __('English source post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Get target language
    $target_language = 'unknown';
    if (function_exists('pll_get_post_language')) {
        $target_language = pll_get_post_language($post_id, 'slug');
    }
    
    // Initialize LLM API
    $llm_endpoint = get_option('gaal_translation_llm_endpoint', '');
    $llm_api_key = get_option('gaal_translation_llm_api_key', '');
    $llm_model = get_option('gaal_translation_llm_model', 'gpt-4');
    $llm_provider = get_option('gaal_translation_llm_provider', 'custom');
    
    $llm = new GAAL_LLM_API($llm_endpoint, $llm_api_key, $llm_model, $llm_provider);
    
    if (!$llm->is_configured()) {
        return new WP_Error('llm_not_configured', __('LLM API is not configured. Please configure it in Translation Settings.', 'kingdom-training'), array('status' => 400));
    }
    
    // Evaluate title
    $title_evaluation = $llm->evaluate_translation($source_post->post_title, $post->post_title, $target_language);
    
    // Evaluate content (use excerpt or first part of content to avoid token limits)
    $source_content = wp_strip_all_tags($source_post->post_content);
    $translated_content = wp_strip_all_tags($post->post_content);
    
    // Limit content for evaluation
    if (strlen($source_content) > 2000) {
        $source_content = substr($source_content, 0, 2000) . '...';
    }
    if (strlen($translated_content) > 2000) {
        $translated_content = substr($translated_content, 0, 2000) . '...';
    }
    
    $content_evaluation = $llm->evaluate_translation($source_content, $translated_content, $target_language);
    
    if (is_wp_error($content_evaluation)) {
        return $content_evaluation;
    }
    
    // Combine evaluations
    $title_score = is_wp_error($title_evaluation) ? 0 : (isset($title_evaluation['score']) ? $title_evaluation['score'] : 75);
    $content_score = isset($content_evaluation['score']) ? $content_evaluation['score'] : 75;
    $overall_score = round(($title_score * 0.2) + ($content_score * 0.8));
    
    $evaluation = array(
        'score' => $overall_score,
        'title_score' => $title_score,
        'content_score' => $content_score,
        'feedback' => isset($content_evaluation['feedback']) ? $content_evaluation['feedback'] : '',
        'evaluated_at' => current_time('mysql'),
    );
    
    // Save evaluation to post meta
    update_post_meta($post_id, '_gaal_evaluation', $evaluation);
    
    GAAL_Translation_Logger::info('LLM evaluation completed', array(
        'post_id' => $post_id,
        'score' => $overall_score,
    ));
    
    return rest_ensure_response(array(
        'success' => true,
        'evaluation' => $evaluation,
    ));
}

/**
 * LLM improve a translation
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_llm_improve($request) {
    $post_id = $request->get_param('post_id');
    
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error('post_not_found', __('Post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Get source post
    $source_post_id = null;
    if (function_exists('pll_get_post_translations')) {
        $translations = pll_get_post_translations($post_id);
        if (isset($translations['en'])) {
            $source_post_id = $translations['en'];
        }
    }
    
    if (!$source_post_id) {
        return new WP_Error('source_not_found', __('English source post not found', 'kingdom-training'), array('status' => 404));
    }
    
    $source_post = get_post($source_post_id);
    if (!$source_post) {
        return new WP_Error('source_not_found', __('English source post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Get target language
    $target_language = 'unknown';
    if (function_exists('pll_get_post_language')) {
        $target_language = pll_get_post_language($post_id, 'slug');
    }
    
    // Initialize LLM API
    $llm_endpoint = get_option('gaal_translation_llm_endpoint', '');
    $llm_api_key = get_option('gaal_translation_llm_api_key', '');
    $llm_model = get_option('gaal_translation_llm_model', 'gpt-4');
    $llm_provider = get_option('gaal_translation_llm_provider', 'custom');
    
    $llm = new GAAL_LLM_API($llm_endpoint, $llm_api_key, $llm_model, $llm_provider);
    
    if (!$llm->is_configured()) {
        return new WP_Error('llm_not_configured', __('LLM API is not configured. Please configure it in Translation Settings.', 'kingdom-training'), array('status' => 400));
    }
    
    // Improve title
    $improved_title = $llm->improve_translation($source_post->post_title, $post->post_title, $target_language);
    if (is_wp_error($improved_title)) {
        $improved_title = $post->post_title; // Keep original on error
    }
    
    // Improve content (chunk if necessary)
    $source_content = $source_post->post_content;
    $translated_content = $post->post_content;
    
    // For large content, we'd need to chunk - for now, limit and warn
    if (strlen($translated_content) > 8000) {
        return new WP_Error('content_too_large', __('Content is too large for LLM improvement. Please use re-translation instead.', 'kingdom-training'), array('status' => 400));
    }
    
    $improved_content = $llm->improve_translation($source_content, $translated_content, $target_language);
    if (is_wp_error($improved_content)) {
        return $improved_content;
    }
    
    // Update the post
    $update_result = wp_update_post(array(
        'ID' => $post_id,
        'post_title' => $improved_title,
        'post_content' => $improved_content,
    ));
    
    if (is_wp_error($update_result)) {
        return $update_result;
    }
    
    // Update meta
    update_post_meta($post_id, '_gaal_llm_improved_at', current_time('mysql'));
    delete_post_meta($post_id, '_gaal_evaluation'); // Clear old evaluation
    
    GAAL_Translation_Logger::info('LLM improvement applied', array(
        'post_id' => $post_id,
        'target_language' => $target_language,
    ));
    
    return rest_ensure_response(array(
        'success' => true,
        'message' => __('Translation improved successfully', 'kingdom-training'),
        'post_id' => $post_id,
    ));
}

// Register new translation dashboard endpoints
function gaal_register_translation_dashboard_api() {
    // Get existing translations
    register_rest_route('gaal/v1', '/translate/existing', array(
        'methods' => 'GET',
        'callback' => 'gaal_api_get_existing_translations',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'args' => array(
            'post_type' => array(
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'language' => array(
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'status' => array(
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
    
    // LLM evaluate translation
    register_rest_route('gaal/v1', '/translate/llm-evaluate', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_llm_evaluate',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'args' => array(
            'post_id' => array(
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
        ),
    ));
    
    // LLM improve translation
    register_rest_route('gaal/v1', '/translate/llm-improve', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_llm_improve',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'args' => array(
            'post_id' => array(
                'required' => true,
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
        ),
    ));
    
    // =========================================================================
    // POLYLANG STRINGS ENDPOINTS
    // =========================================================================
    
    // Get Polylang strings
    register_rest_route('gaal/v1', '/strings', array(
        'methods' => 'GET',
        'callback' => 'gaal_api_get_strings',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
        'args' => array(
            'group' => array(
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
    
    // Save string translation
    register_rest_route('gaal/v1', '/strings/save', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_save_string',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
    
    // Translate string with Google Translate
    register_rest_route('gaal/v1', '/strings/translate', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_translate_string',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
    
    // Bulk translate strings
    register_rest_route('gaal/v1', '/strings/translate-bulk', array(
        'methods' => 'POST',
        'callback' => 'gaal_api_translate_strings_bulk',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
}

/**
 * Get Polylang strings
 */
function gaal_api_get_strings($request) {
    $scanner = new GAAL_Translation_Scanner();
    
    $filters = array();
    if ($request->get_param('group')) {
        $filters['group'] = $request->get_param('group');
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'strings' => $scanner->get_polylang_strings($filters),
        'groups' => $scanner->get_string_groups(),
        'summary' => $scanner->get_strings_summary(),
    ));
}

/**
 * Save a string translation
 */
function gaal_api_save_string($request) {
    $string = $request->get_param('string');
    $translation = $request->get_param('translation');
    $language = $request->get_param('language');
    
    if (empty($string) || empty($language)) {
        return new WP_Error('missing_params', __('Missing required parameters', 'kingdom-training'), array('status' => 400));
    }
    
    $scanner = new GAAL_Translation_Scanner();
    $result = $scanner->save_string_translation($string, $translation, $language);
    
    if (is_wp_error($result)) {
        return new WP_Error(
            $result->get_error_code(),
            $result->get_error_message(),
            array('status' => 500)
        );
    }
    
    if ($result === true) {
        return rest_ensure_response(array(
            'success' => true,
            'message' => __('Translation saved', 'kingdom-training'),
        ));
    }
    
    return new WP_Error('save_failed', __('Failed to save translation', 'kingdom-training'), array('status' => 500));
}

/**
 * Translate a string using Google Translate
 */
function gaal_api_translate_string($request) {
    $string = $request->get_param('string');
    $language = $request->get_param('language');
    $save = $request->get_param('save');
    
    if (empty($string) || empty($language)) {
        return new WP_Error('missing_params', __('Missing required parameters', 'kingdom-training'), array('status' => 400));
    }
    
    // Use the translation engine
    $engine = new GAAL_Translation_Engine();
    $translated = $engine->translate_text($string, $language, 'en');
    
    if (is_wp_error($translated)) {
        return $translated;
    }
    
    // Save if requested
    $saved = false;
    if ($save) {
        $scanner = new GAAL_Translation_Scanner();
        $save_result = $scanner->save_string_translation($string, $translated, $language);
        $saved = ($save_result === true);
        
        // Log any save errors but don't fail the request
        if (is_wp_error($save_result)) {
            GAAL_Translation_Logger::warning('Failed to save string translation', array(
                'error' => $save_result->get_error_message(),
                'string' => substr($string, 0, 50),
                'language' => $language,
            ));
        }
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'original' => $string,
        'translation' => $translated,
        'language' => $language,
        'saved' => $saved,
    ));
}

/**
 * Bulk translate strings
 */
function gaal_api_translate_strings_bulk($request) {
    $strings = $request->get_param('strings'); // Array of strings
    $language = $request->get_param('language');
    $save = $request->get_param('save');
    
    if (empty($strings) || empty($language)) {
        return new WP_Error('missing_params', __('Missing required parameters', 'kingdom-training'), array('status' => 400));
    }
    
    $engine = new GAAL_Translation_Engine();
    $scanner = new GAAL_Translation_Scanner();
    
    $results = array();
    $success_count = 0;
    $error_count = 0;
    
    foreach ($strings as $string) {
        $translated = $engine->translate_text($string, $language, 'en');
        
        if (is_wp_error($translated)) {
            $results[] = array(
                'string' => $string,
                'error' => $translated->get_error_message(),
            );
            $error_count++;
        } else {
            if ($save) {
                $scanner->save_string_translation($string, $translated, $language);
            }
            $results[] = array(
                'string' => $string,
                'translation' => $translated,
            );
            $success_count++;
        }
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'results' => $results,
        'summary' => array(
            'total' => count($strings),
            'success' => $success_count,
            'errors' => $error_count,
        ),
    ));
}

add_action('rest_api_init', 'gaal_register_translation_dashboard_api');

add_action('rest_api_init', 'gaal_register_translation_api');

/**
 * Check if user has permission to use translation features
 * 
 * @param WP_REST_Request $request Request object
 * @return bool|WP_Error
 */
function gaal_check_translation_permissions($request) {
    // Temporarily allow all requests for debugging
    return true;
    
    // Original permission checks (commented out for debugging):
    /*
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return new WP_Error('rest_forbidden', __('You must be logged in to use this endpoint.', 'kingdom-training'), array('status' => 401));
    }
    
    // Check user capability
    if (!current_user_can('edit_posts')) {
        return new WP_Error('rest_forbidden', __('You do not have permission to use translation features.', 'kingdom-training'), array('status' => 403));
    }
    
    return true;
    */
}

/**
 * Generate all translations for a post
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_generate_all_translations($request) {
    // Get parameters from request body (JSON) or query string
    $post_id = $request->get_param('post_id');
    
    // Log for debugging
    GAAL_Translation_Logger::debug('Generate all translations request', array(
        'post_id' => $post_id,
        'method' => $request->get_method(),
        'headers' => $request->get_headers(),
        'body' => $request->get_body(),
        'json_params' => $request->get_json_params(),
    ));
    
    if (empty($post_id)) {
        GAAL_Translation_Logger::error('Missing post_id in translation request');
        return new WP_Error('missing_post_id', __('Post ID is required', 'kingdom-training'), array('status' => 400));
    }
    
    // Verify post exists
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error('post_not_found', __('Post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Get enabled languages
    $enabled_languages = get_option('gaal_translation_enabled_languages', array());
    if (empty($enabled_languages)) {
        return new WP_Error('no_languages', __('No languages enabled for translation', 'kingdom-training'), array('status' => 400));
    }
    
    // Check if Google Translate API is configured
    $google_api_key = get_option('gaal_translation_google_api_key', '');
    if (empty($google_api_key)) {
        GAAL_Translation_Logger::error('Google Translate API key not configured');
        return new WP_Error(
            'api_not_configured',
            __('Google Translate API key is not configured. Please configure it in Translation Settings.', 'kingdom-training'),
            array('status' => 400)
        );
    }
    
    // Create translation job
    $job = new GAAL_Translation_Job();
    $job_id = GAAL_Translation_Job::create($post_id, $enabled_languages);
    
    if (is_wp_error($job_id)) {
        return $job_id;
    }
    
    $job = new GAAL_Translation_Job($job_id);
    $job->set_status(GAAL_Translation_Job::STATUS_IN_PROGRESS);
    
    // Start background processing
    $engine = new GAAL_Translation_Engine();
    $result = $engine->translate_all_languages($post_id, $job);
    
    if (is_wp_error($result)) {
        $error_data = $result->get_error_data();
        $error_message = $result->get_error_message();
        
        // Include detailed error information if available
        if (isset($error_data['errors']) && is_array($error_data['errors'])) {
            $error_message .= ': ' . implode(', ', $error_data['errors']);
        }
        
        GAAL_Translation_Logger::error('Translation job failed', array(
            'post_id' => $post_id,
            'error_code' => $result->get_error_code(),
            'error_message' => $error_message,
            'error_data' => $error_data,
        ));
        
        $job->fail($error_message);
        
        return new WP_Error(
            $result->get_error_code(),
            $error_message,
            array('status' => 500, 'errors' => isset($error_data['errors']) ? $error_data['errors'] : array())
        );
    }
    
    // Check if there were any errors in the result
    if (isset($result['errors']) && !empty($result['errors']) && empty($result['translations'])) {
        $error_message = __('All translations failed', 'kingdom-training');
        if (is_array($result['errors'])) {
            $error_message .= ': ' . implode(', ', array_values($result['errors']));
        }
        
        GAAL_Translation_Logger::error('All translations failed', array(
            'post_id' => $post_id,
            'errors' => $result['errors'],
        ));
        
        $job->fail($error_message);
        
        return new WP_Error(
            'translation_failed',
            $error_message,
            array('status' => 500, 'errors' => $result['errors'])
        );
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'message' => __('Translation job completed', 'kingdom-training'),
        'result' => $result,
    ), 200);
}

/**
 * Translate to a single language
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_translate_single($request) {
    // DEBUG STEP 1
    return new WP_REST_Response(array('debug' => 'step1', 'success' => true), 200);
    
    // Get parameters
    $post_id = $request->get_param('post_id');
    $target_language = $request->get_param('target_language');
    
    // DEBUG STEP 2
    // return new WP_REST_Response(array('debug' => 'step2', 'post_id' => $post_id, 'lang' => $target_language, 'success' => true), 200);
    
    // Sanitize
    $post_id = absint($post_id);
    $target_language = sanitize_text_field($target_language);
    
    // Validate
    if (empty($post_id)) {
        return new WP_Error('missing_post_id', 'Post ID is required', array('status' => 400));
    }
    
    if (empty($target_language)) {
        return new WP_Error('missing_target_language', 'Target language is required', array('status' => 400));
    }
    
    // DEBUG STEP 3
    // return new WP_REST_Response(array('debug' => 'step3', 'post_id' => $post_id, 'lang' => $target_language, 'success' => true), 200);
    
    // Verify post exists
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error('post_not_found', 'Post not found', array('status' => 404));
    }
    
    // DEBUG STEP 4
    // return new WP_REST_Response(array('debug' => 'step4', 'post_title' => $post->post_title, 'success' => true), 200);
    
    // Perform translation
    try {
        // DEBUG STEP 5
        // return new WP_REST_Response(array('debug' => 'step5', 'about_to_create_engine' => true, 'success' => true), 200);
        
        $engine = new GAAL_Translation_Engine();
        
        // DEBUG STEP 6
        // return new WP_REST_Response(array('debug' => 'step6', 'engine_created' => true, 'success' => true), 200);
        
        $translated_post_id = $engine->translate_post($post_id, $target_language);
        
        if (is_wp_error($translated_post_id)) {
            return $translated_post_id;
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'translated_post_id' => $translated_post_id,
            'target_language' => $target_language,
            'message' => 'Translation completed',
        ), 200);
        
    } catch (Exception $e) {
        return new WP_Error('translation_error', $e->getMessage(), array('status' => 500));
    } catch (Error $e) {
        // Catch PHP 7+ fatal errors
        return new WP_Error('fatal_error', 'Fatal error: ' . $e->getMessage(), array('status' => 500));
    }
}

/**
 * Copy content from English source post to target post
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_copy_from_english($request) {
    $target_post_id = $request->get_param('target_post_id');
    $source_post_id = $request->get_param('source_post_id');
    
    // Validate target post
    $target_post = get_post($target_post_id);
    if (!$target_post) {
        return new WP_Error('target_not_found', __('Target post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Validate source post
    $source_post = get_post($source_post_id);
    if (!$source_post) {
        return new WP_Error('source_not_found', __('English source post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Verify source post is English
    if (function_exists('pll_get_post_language')) {
        $source_language = pll_get_post_language($source_post_id, 'slug');
        if ($source_language !== 'en') {
            return new WP_Error('not_english', __('Source post is not in English', 'kingdom-training'), array('status' => 400));
        }
    }
    
    // Verify posts are linked translations
    if (function_exists('pll_get_post_translations')) {
        $translations = pll_get_post_translations($source_post_id);
        $target_language = function_exists('pll_get_post_language') ? pll_get_post_language($target_post_id, 'slug') : null;
        
        if (!$target_language || !isset($translations[$target_language]) || $translations[$target_language] != $target_post_id) {
            return new WP_Error('not_linked', __('Posts are not linked as translations', 'kingdom-training'), array('status' => 400));
        }
    }
    
    // Copy content from source to target
    $update_data = array(
        'ID' => $target_post_id,
        'post_title' => $source_post->post_title,
        'post_content' => $source_post->post_content,
        'post_excerpt' => $source_post->post_excerpt,
    );
    
    $result = wp_update_post($update_data, true);
    
    if (is_wp_error($result)) {
        return new WP_Error('update_failed', $result->get_error_message(), array('status' => 500));
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'message' => __('Content copied from English successfully', 'kingdom-training'),
        'source_post_id' => $source_post_id,
        'target_post_id' => $target_post_id,
        'copied' => array(
            'title' => $source_post->post_title,
            'content_length' => strlen($source_post->post_content),
            'excerpt_length' => strlen($source_post->post_excerpt),
        ),
    ), 200);
}

/**
 * Chunked translation API handler
 * 
 * Handles translation in steps to avoid PHP timeout:
 * - init: Create job, chunk content, return chunk count
 * - title: Translate title only
 * - content_0..N: Translate content chunk N
 * - excerpt: Translate excerpt
 * - finalize: Assemble chunks, create/update post, link in Polylang
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_translate_chunked($request) {
    $source_post_id = $request->get_param('source_post_id');
    $target_language = $request->get_param('target_language');
    $step = $request->get_param('step');
    $job_id = $request->get_param('job_id');
    $target_post_id = $request->get_param('target_post_id');
    
    GAAL_Translation_Logger::debug('Chunked translation request', array(
        'source_post_id' => $source_post_id,
        'target_language' => $target_language,
        'step' => $step,
        'job_id' => $job_id,
        'target_post_id' => $target_post_id,
    ));
    
    // Validate source post
    $source_post = get_post($source_post_id);
    if (!$source_post) {
        return new WP_Error('post_not_found', __('Source post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Initialize translation engine
    $engine = new GAAL_Translation_Engine();
    
    // Handle different steps
    switch ($step) {
        case 'init':
            return gaal_chunked_translate_init($source_post, $target_language, $engine, $target_post_id);
            
        case 'title':
            return gaal_chunked_translate_title($job_id, $target_language, $engine);
            
        case 'excerpt':
            return gaal_chunked_translate_excerpt($job_id, $target_language, $engine);
            
        case 'finalize':
            return gaal_chunked_translate_finalize($job_id, $source_post, $target_language, $engine);
            
        default:
            // Handle content_N steps
            if (preg_match('/^content_(\d+)$/', $step, $matches)) {
                $chunk_index = intval($matches[1]);
                return gaal_chunked_translate_content($job_id, $chunk_index, $target_language, $engine);
            }
            
            return new WP_Error('invalid_step', __('Invalid translation step', 'kingdom-training'), array('status' => 400));
    }
}

/**
 * Initialize chunked translation job
 * 
 * @param WP_Post $source_post Source post object
 * @param string $target_language Target language code
 * @param GAAL_Translation_Engine $engine Translation engine
 * @param int|null $target_post_id Optional existing post ID to update instead of creating new
 */
function gaal_chunked_translate_init($source_post, $target_language, $engine, $target_post_id = null) {
    // Get source content
    $content_processor = new GAAL_Content_Processor();
    $content = $content_processor->extract_translatable_content($source_post->ID);
    
    // Chunk the content (approximately 3000 chars per chunk, split on paragraph boundaries)
    $chunks = gaal_chunk_content($content['content'], 3000);
    
    // Create a translation job to store progress
    $job_id = GAAL_Translation_Job::create($source_post->ID, array($target_language));
    
    if (is_wp_error($job_id)) {
        return $job_id;
    }
    
    $job = new GAAL_Translation_Job($job_id);
    $job->set_status(GAAL_Translation_Job::STATUS_IN_PROGRESS);
    
    // Store chunks and metadata in job
    $job->set_chunks($chunks);
    $job->set_meta('title', $content['title']);
    $job->set_meta('excerpt', $content['excerpt']);
    $job->set_meta('target_language', $target_language);
    $job->set_meta('source_post_id', $source_post->ID);
    $job->set_meta('source_post_type', $source_post->post_type);
    $job->set_meta('source_post_author', $source_post->post_author);
    
    // Store target post ID if provided (for updating existing drafts)
    if ($target_post_id) {
        $job->set_meta('target_post_id', intval($target_post_id));
    }
    
    // Calculate total steps: init(done) + title + content chunks + excerpt + finalize
    $total_steps = 1 + 1 + count($chunks) + 1 + 1;
    $job->set_meta('total_steps', $total_steps);
    $job->set_meta('current_step', 1);
    
    GAAL_Translation_Logger::info('Chunked translation initialized', array(
        'job_id' => $job_id,
        'source_post_id' => $source_post->ID,
        'target_language' => $target_language,
        'target_post_id' => $target_post_id,
        'chunk_count' => count($chunks),
        'total_steps' => $total_steps,
    ));
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'chunk_count' => count($chunks),
        'total_steps' => $total_steps,
        'current_step' => 1,
        'next_step' => 'title',
        'message' => sprintf(__('Translation job created with %d content chunks', 'kingdom-training'), count($chunks)),
    ), 200);
}

/**
 * Translate title in chunked translation
 */
function gaal_chunked_translate_title($job_id, $target_language, $engine) {
    $job = new GAAL_Translation_Job($job_id);
    
    if (!$job->get_id()) {
        return new WP_Error('job_not_found', __('Translation job not found', 'kingdom-training'), array('status' => 404));
    }
    
    $title = $job->get_meta('title');
    $source_language = 'en';
    
    // Translate title
    $translated_title = $engine->translate_text($title, $target_language, $source_language);
    
    if (is_wp_error($translated_title)) {
        GAAL_Translation_Logger::error('Failed to translate title', array(
            'job_id' => $job_id,
            'error' => $translated_title->get_error_message(),
        ));
        return $translated_title;
    }
    
    // Store translated title
    $job->set_translated_meta('title', $translated_title);
    $current_step = $job->get_meta('current_step') + 1;
    $job->set_meta('current_step', $current_step);
    
    $chunks = $job->get_chunks();
    $next_step = count($chunks) > 0 ? 'content_0' : 'excerpt';
    
    GAAL_Translation_Logger::debug('Title translated', array(
        'job_id' => $job_id,
        'current_step' => $current_step,
    ));
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'current_step' => $current_step,
        'total_steps' => $job->get_meta('total_steps'),
        'next_step' => $next_step,
        'message' => __('Title translated', 'kingdom-training'),
    ), 200);
}

/**
 * Translate content chunk in chunked translation
 */
function gaal_chunked_translate_content($job_id, $chunk_index, $target_language, $engine) {
    $job = new GAAL_Translation_Job($job_id);
    
    if (!$job->get_id()) {
        return new WP_Error('job_not_found', __('Translation job not found', 'kingdom-training'), array('status' => 404));
    }
    
    $chunks = $job->get_chunks();
    
    if (!isset($chunks[$chunk_index])) {
        return new WP_Error('chunk_not_found', __('Content chunk not found', 'kingdom-training'), array('status' => 404));
    }
    
    $chunk = $chunks[$chunk_index];
    $source_language = 'en';
    
    // Translate chunk
    $translated_chunk = $engine->translate_text($chunk, $target_language, $source_language);
    
    if (is_wp_error($translated_chunk)) {
        GAAL_Translation_Logger::error('Failed to translate content chunk', array(
            'job_id' => $job_id,
            'chunk_index' => $chunk_index,
            'error' => $translated_chunk->get_error_message(),
        ));
        return $translated_chunk;
    }
    
    // Store translated chunk
    $job->set_translated_chunk($chunk_index, $translated_chunk);
    $current_step = $job->get_meta('current_step') + 1;
    $job->set_meta('current_step', $current_step);
    
    // Determine next step
    $next_chunk_index = $chunk_index + 1;
    if ($next_chunk_index < count($chunks)) {
        $next_step = 'content_' . $next_chunk_index;
    } else {
        $next_step = 'excerpt';
    }
    
    GAAL_Translation_Logger::debug('Content chunk translated', array(
        'job_id' => $job_id,
        'chunk_index' => $chunk_index,
        'current_step' => $current_step,
    ));
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'chunk_index' => $chunk_index,
        'current_step' => $current_step,
        'total_steps' => $job->get_meta('total_steps'),
        'next_step' => $next_step,
        'message' => sprintf(__('Content chunk %d translated', 'kingdom-training'), $chunk_index + 1),
    ), 200);
}

/**
 * Translate excerpt in chunked translation
 */
function gaal_chunked_translate_excerpt($job_id, $target_language, $engine) {
    $job = new GAAL_Translation_Job($job_id);
    
    if (!$job->get_id()) {
        return new WP_Error('job_not_found', __('Translation job not found', 'kingdom-training'), array('status' => 404));
    }
    
    $excerpt = $job->get_meta('excerpt');
    $source_language = 'en';
    
    // Translate excerpt (if not empty)
    $translated_excerpt = '';
    if (!empty($excerpt)) {
        $translated_excerpt = $engine->translate_text($excerpt, $target_language, $source_language);
        
        if (is_wp_error($translated_excerpt)) {
            GAAL_Translation_Logger::warning('Failed to translate excerpt, continuing without', array(
                'job_id' => $job_id,
                'error' => $translated_excerpt->get_error_message(),
            ));
            $translated_excerpt = ''; // Continue without excerpt
        }
    }
    
    // Store translated excerpt
    $job->set_translated_meta('excerpt', $translated_excerpt);
    $current_step = $job->get_meta('current_step') + 1;
    $job->set_meta('current_step', $current_step);
    
    GAAL_Translation_Logger::debug('Excerpt translated', array(
        'job_id' => $job_id,
        'current_step' => $current_step,
    ));
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'current_step' => $current_step,
        'total_steps' => $job->get_meta('total_steps'),
        'next_step' => 'finalize',
        'message' => __('Excerpt translated', 'kingdom-training'),
    ), 200);
}

/**
 * Finalize chunked translation - assemble and save post
 */
function gaal_chunked_translate_finalize($job_id, $source_post, $target_language, $engine) {
    $job = new GAAL_Translation_Job($job_id);
    
    if (!$job->get_id()) {
        return new WP_Error('job_not_found', __('Translation job not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Get all translated parts
    $translated_title = $job->get_translated_meta('title');
    $translated_excerpt = $job->get_translated_meta('excerpt');
    $translated_chunks = $job->get_all_translated_chunks();
    
    // Reassemble content from chunks
    $translated_content = implode("\n\n", $translated_chunks);
    
    // Get source language
    $source_language = 'en';
    if (function_exists('pll_get_post_language')) {
        $source_language = pll_get_post_language($source_post->ID, 'slug') ?: 'en';
    }
    
    // Check if we have a target post ID from the job (for updating existing drafts)
    $target_post_id = $job->get_meta('target_post_id');
    
    // Check if translation already exists in Polylang
    $existing_translation = null;
    if (!$target_post_id && function_exists('pll_get_post_translations')) {
        $translations = pll_get_post_translations($source_post->ID);
        if (isset($translations[$target_language])) {
            $existing_translation = get_post($translations[$target_language]);
        }
    }
    
    // Prepare post data
    $default_status = get_option('gaal_translation_default_status', 'draft');
    $post_data = array(
        'post_title' => $translated_title,
        'post_content' => $translated_content,
        'post_excerpt' => $translated_excerpt,
        'post_status' => $default_status,
        'post_type' => $source_post->post_type,
        'post_author' => $source_post->post_author,
    );
    
    // Create or update translated post
    if ($target_post_id) {
        // Update existing draft (from batch creation)
        $post_data['ID'] = $target_post_id;
        $translated_post_id = wp_update_post($post_data);
        
        // Clear the "needs translation" flag
        if (!is_wp_error($translated_post_id)) {
            delete_post_meta($translated_post_id, '_gaal_needs_translation');
            update_post_meta($translated_post_id, '_gaal_translated_at', current_time('mysql'));
        }
    } elseif ($existing_translation) {
        $post_data['ID'] = $existing_translation->ID;
        $translated_post_id = wp_update_post($post_data);
    } else {
        $translated_post_id = wp_insert_post($post_data);
    }
    
    if (is_wp_error($translated_post_id)) {
        GAAL_Translation_Logger::error('Failed to create/update translated post', array(
            'job_id' => $job_id,
            'error' => $translated_post_id->get_error_message(),
        ));
        $job->fail($translated_post_id->get_error_message());
        return $translated_post_id;
    }
    
    // Set language in Polylang (only needed if not using target_post_id which already has language set)
    if (!$target_post_id && function_exists('pll_set_post_language')) {
        pll_set_post_language($translated_post_id, $target_language);
    }
    
    // Link translations in Polylang (only if not already linked via target_post_id)
    if (!$target_post_id && function_exists('pll_save_post_translations')) {
        $translations = array();
        if (function_exists('pll_get_post_translations')) {
            $existing_translations = pll_get_post_translations($source_post->ID);
            $translations = $existing_translations ?: array();
        }
        $translations[$source_language] = $source_post->ID;
        $translations[$target_language] = $translated_post_id;
        pll_save_post_translations($translations);
    }
    
    // Copy featured image if available (check if not already set)
    $thumbnail_id = get_post_thumbnail_id($source_post->ID);
    if ($thumbnail_id && !get_post_thumbnail_id($translated_post_id)) {
        set_post_thumbnail($translated_post_id, $thumbnail_id);
    }
    
    // Mark job as completed
    $job->complete();
    $current_step = $job->get_meta('current_step') + 1;
    $job->set_meta('current_step', $current_step);
    
    GAAL_Translation_Logger::info('Chunked translation completed', array(
        'job_id' => $job_id,
        'source_post_id' => $source_post->ID,
        'translated_post_id' => $translated_post_id,
        'target_post_id_used' => $target_post_id ? true : false,
        'target_language' => $target_language,
    ));
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'translated_post_id' => $translated_post_id,
        'current_step' => $current_step,
        'total_steps' => $job->get_meta('total_steps'),
        'next_step' => null,
        'message' => __('Translation completed successfully', 'kingdom-training'),
        'edit_url' => get_edit_post_link($translated_post_id, 'raw'),
    ), 200);
}

/**
 * Split content into chunks by paragraph boundaries
 * 
 * @param string $content HTML content to chunk
 * @param int $max_chars Maximum characters per chunk (approximate)
 * @return array Array of content chunks
 */
function gaal_chunk_content($content, $max_chars = 3000) {
    if (empty($content)) {
        return array();
    }
    
    // If content is small enough, return as single chunk
    if (strlen($content) <= $max_chars) {
        return array($content);
    }
    
    $chunks = array();
    $current_chunk = '';
    
    // Split by paragraph tags first
    // Handle both </p> and double newlines as paragraph boundaries
    $paragraphs = preg_split('/(<\/p>\s*|[\r\n]{2,})/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);
        if (empty($paragraph)) {
            continue;
        }
        
        // If adding this paragraph would exceed max_chars, start a new chunk
        if (strlen($current_chunk) + strlen($paragraph) > $max_chars && !empty($current_chunk)) {
            $chunks[] = trim($current_chunk);
            $current_chunk = '';
        }
        
        $current_chunk .= $paragraph . ' ';
        
        // If this single paragraph is larger than max_chars, it becomes its own chunk
        if (strlen($current_chunk) >= $max_chars) {
            $chunks[] = trim($current_chunk);
            $current_chunk = '';
        }
    }
    
    // Don't forget the last chunk
    if (!empty(trim($current_chunk))) {
        $chunks[] = trim($current_chunk);
    }
    
    // If we ended up with no chunks (edge case), return original content as single chunk
    if (empty($chunks)) {
        return array($content);
    }
    
    return $chunks;
}

/**
 * Re-translate an existing post
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_retranslate($request) {
    $post_id = $request->get_param('post_id');
    $target_language = $request->get_param('target_language');
    
    if (empty($post_id)) {
        return new WP_Error('missing_post_id', __('Post ID is required', 'kingdom-training'), array('status' => 400));
    }
    
    // Verify post exists
    $post = get_post($post_id);
    if (!$post) {
        return new WP_Error('post_not_found', __('Post not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Perform re-translation
    $engine = new GAAL_Translation_Engine();
    $translated_post_id = $engine->retranslate_post($post_id, $target_language);
    
    if (is_wp_error($translated_post_id)) {
        return $translated_post_id;
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'translated_post_id' => $translated_post_id,
        'message' => __('Re-translation completed', 'kingdom-training'),
    ), 200);
}

/**
 * Get translation status for a post
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_get_translation_status($request) {
    $post_id = $request->get_param('post_id');
    
    // Get available languages
    $enabled_languages = get_option('gaal_translation_enabled_languages', array());
    
    // Get source language
    $source_language = 'en';
    if (function_exists('pll_get_post_language')) {
        $source_language = pll_get_post_language($post_id, 'slug') ?: 'en';
    }
    
    // Get translations
    $translations = array();
    if (function_exists('pll_get_post_translations')) {
        $post_translations = pll_get_post_translations($post_id);
        if ($post_translations) {
            foreach ($post_translations as $lang => $trans_id) {
                if ($lang !== $source_language) {
                    $trans_post = get_post($trans_id);
                    $translations[$lang] = array(
                        'post_id' => $trans_id,
                        'status' => $trans_post ? $trans_post->post_status : 'missing',
                        'title' => $trans_post ? $trans_post->post_title : '',
                    );
                }
            }
        }
    }
    
    // Build status for each enabled language
    $status = array();
    foreach ($enabled_languages as $lang) {
        if ($lang === $source_language) {
            continue;
        }
        
        $status[$lang] = array(
            'language' => $lang,
            'exists' => isset($translations[$lang]),
            'post_id' => isset($translations[$lang]) ? $translations[$lang]['post_id'] : null,
            'post_status' => isset($translations[$lang]) ? $translations[$lang]['status'] : null,
        );
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'source_post_id' => $post_id,
        'source_language' => $source_language,
        'translations' => $status,
    ), 200);
}

/**
 * Resume an interrupted translation job
 * 
 * @param WP_REST_Request $request Request object
 * @return WP_REST_Response|WP_Error
 */
function gaal_api_resume_job($request) {
    $job_id = $request->get_param('job_id');
    
    if (empty($job_id)) {
        return new WP_Error('missing_job_id', __('Job ID is required', 'kingdom-training'), array('status' => 400));
    }
    
    $job = new GAAL_Translation_Job($job_id);
    
    if (!$job->get_id()) {
        return new WP_Error('job_not_found', __('Translation job not found', 'kingdom-training'), array('status' => 404));
    }
    
    // Resume job
    if (!$job->resume()) {
        return new WP_Error('cannot_resume', __('Job cannot be resumed', 'kingdom-training'), array('status' => 400));
    }
    
    // Get remaining languages
    $remaining_languages = $job->get_remaining_languages();
    $source_post_id = $job->get_source_post_id();
    
    if (empty($remaining_languages) || empty($source_post_id)) {
        return new WP_Error('nothing_to_resume', __('No remaining languages to translate', 'kingdom-training'), array('status' => 400));
    }
    
    // Continue translation
    $engine = new GAAL_Translation_Engine();
    $result = $engine->translate_all_languages($source_post_id, $job);
    
    if (is_wp_error($result)) {
        return $result;
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'job_id' => $job_id,
        'message' => __('Job resumed', 'kingdom-training'),
        'result' => $result,
    ), 200);
}

// AJAX handlers for background processing
function gaal_ajax_process_translation() {
    check_ajax_referer('gaal_translation_nonce', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => __('Permission denied', 'kingdom-training')));
    }
    
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
    
    if (empty($job_id)) {
        wp_send_json_error(array('message' => __('Job ID is required', 'kingdom-training')));
    }
    
    $job = new GAAL_Translation_Job($job_id);
    
    if (!$job->get_id()) {
        wp_send_json_error(array('message' => __('Job not found', 'kingdom-training')));
    }
    
    // Get remaining languages
    $remaining_languages = $job->get_remaining_languages();
    
    if (empty($remaining_languages)) {
        $job->complete();
        wp_send_json_success(array(
            'message' => __('All translations completed', 'kingdom-training'),
            'completed' => true,
        ));
    }
    
    // Process next language
    $source_post_id = $job->get_source_post_id();
    $next_language = array_shift($remaining_languages);
    
    $job->update_language_progress($next_language, 'in_progress');
    
    $engine = new GAAL_Translation_Engine();
    $result = $engine->translate_post($source_post_id, $next_language);
    
    if (is_wp_error($result)) {
        $job->update_language_progress($next_language, 'failed', $result->get_error_message());
        wp_send_json_error(array(
            'message' => $result->get_error_message(),
            'language' => $next_language,
        ));
    }
    
    $job->update_language_progress($next_language, 'completed');
    
    // Check if job is complete
    $remaining = $job->get_remaining_languages();
    if (empty($remaining)) {
        $job->complete();
    }
    
    wp_send_json_success(array(
        'message' => sprintf(__('Translation completed for %s', 'kingdom-training'), $next_language),
        'language' => $next_language,
        'completed' => empty($remaining),
        'progress' => $job->get_progress(),
    ));
}
add_action('wp_ajax_gaal_process_translation', 'gaal_ajax_process_translation');

/**
 * AJAX handler for testing API connections
 */
function gaal_ajax_test_api_connection() {
    check_ajax_referer('gaal_test_api_connection', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied', 'kingdom-training')));
    }
    
    $api_type = isset($_POST['api_type']) ? sanitize_text_field($_POST['api_type']) : '';
    
    if (empty($api_type)) {
        wp_send_json_error(array('message' => __('API type is required', 'kingdom-training')));
    }
    
    if ($api_type === 'google_translate') {
        // Test Google Translate API
        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API key is required', 'kingdom-training')));
        }
        
        // Create Google Translate API instance
        $google_translate = new GAAL_Google_Translate_API($api_key);
        
        // Test with a simple translation
        $test_text = 'Hello';
        $result = $google_translate->translate($test_text, 'es', 'en');
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'message' => __('Google Translate API connection successful', 'kingdom-training'),
            'test_translation' => $result,
        ));
        
    } elseif ($api_type === 'llm') {
        // Test LLM API
        $endpoint = isset($_POST['endpoint']) ? esc_url_raw($_POST['endpoint']) : '';
        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        $model = isset($_POST['model']) ? sanitize_text_field($_POST['model']) : '';
        
        if (empty($endpoint)) {
            wp_send_json_error(array('message' => __('Endpoint URL is required', 'kingdom-training')));
        }
        
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API key is required', 'kingdom-training')));
        }
        
        if (empty($model)) {
            wp_send_json_error(array('message' => __('Model name is required', 'kingdom-training')));
        }
        
        // Detect provider from endpoint
        $provider = 'custom';
        if (strpos($endpoint, 'openai.com') !== false) {
            $provider = 'openai';
        } elseif (strpos($endpoint, 'anthropic.com') !== false) {
            $provider = 'anthropic';
        } elseif (strpos($endpoint, 'generativelanguage.googleapis.com') !== false) {
            $provider = 'gemini';
        } elseif (strpos($endpoint, 'openrouter.ai') !== false) {
            $provider = 'openrouter';
        }
        
        // Create LLM API instance
        $llm_api = new GAAL_LLM_API($endpoint, $api_key, $model, $provider);
        
        // Test connection
        $result = $llm_api->test_connection();
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'message' => __('LLM API connection successful', 'kingdom-training'),
            'test_response' => substr($result, 0, 100), // First 100 chars of response
        ));
    } else {
        wp_send_json_error(array('message' => __('Invalid API type', 'kingdom-training')));
    }
}
add_action('wp_ajax_gaal_test_api_connection', 'gaal_ajax_test_api_connection');

// ============================================================================
// LOGIN PAGE ROUTING WITH LANGUAGE SUPPORT
// ============================================================================

/**
 * Create a virtual page object for route handling
 * 
 * @param string $slug Page slug
 * @param string $title Page title
 * @param string $template Template name to use
 * @return WP_Post Virtual page object
 */
function kt_create_virtual_page( $slug, $title, $template = null ) {
    // Create a virtual post object
    $page_data = array(
        'ID'                    => -1, // Virtual page ID
        'post_title'            => $title,
        'post_name'             => $slug,
        'post_content'          => '',
        'post_excerpt'          => '',
        'post_status'           => 'publish',
        'post_type'             => 'page',
        'post_author'           => 1,
        'post_date'             => current_time( 'mysql' ),
        'post_date_gmt'         => current_time( 'mysql', 1 ),
        'post_modified'         => current_time( 'mysql' ),
        'post_modified_gmt'     => current_time( 'mysql', 1 ),
        'post_parent'           => 0,
        'post_content_filtered' => '',
        'post_mime_type'        => '',
        'comment_status'        => 'closed',
        'ping_status'           => 'closed',
        'comment_count'         => 0,
        'menu_order'            => 0,
        'filter'                => 'raw',
    );
    
    // Convert to object
    $page = (object) $page_data;
    
    // Set template if provided
    if ( $template ) {
        $page->page_template = $template;
    }
    
    // Return as WP_Post object
    return new WP_Post( $page );
}

/**
 * Handle /login route with language prefixes (e.g., /es/login, /fr/login)
 * This ensures the login page works correctly in all languages
 */
function kt_handle_login_route() {
    // Only handle frontend requests
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }
    
    // Get the current request URI
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $request_path = parse_url( $request_uri, PHP_URL_PATH );
    
    // Remove leading/trailing slashes and normalize
    $request_path = trim( $request_path, '/' );
    
    // Parse language from path
    $parsed = kt_parse_language_from_path( '/' . $request_path );
    $lang = $parsed['lang'];
    $path_without_lang = trim( $parsed['path_without_lang'], '/' );
    
    // Check if this is a login route
    if ( $path_without_lang === 'login' ) {
        // Set Polylang language if a language prefix was found
        if ( $lang && function_exists( 'PLL' ) ) {
            $polylang = PLL();
            $language = $polylang->model->get_language( $lang );
            if ( $language ) {
                $polylang->curlang = $language;
                // Force Polylang to recognize the language
                $_REQUEST['lang'] = $lang;
            }
        }
        
        // Find the login page
        // First, try to find a page with slug "login" in the current language
        $login_page = null;
        
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language( 'slug' );
            // Try to get the login page in the current language
            $login_page = get_page_by_path( 'login' );
            
            // If Polylang is active, try to get the translated version
            if ( $login_page && function_exists( 'pll_get_post' ) ) {
                // Check if we need the translated version
                $page_lang = pll_get_post_language( $login_page->ID, 'slug' );
                if ( $current_lang && $page_lang !== $current_lang ) {
                    $translated_id = pll_get_post( $login_page->ID, $current_lang );
                    if ( $translated_id ) {
                        $login_page = get_post( $translated_id );
                    }
                }
            }
        } else {
            // Fallback: find any page with slug "login"
            $login_page = get_page_by_path( 'login' );
        }
        
        // If no page found by slug, try to find any page using the "Login Page" template
        if ( ! $login_page ) {
            $pages = get_pages( array(
                'meta_key'   => '_wp_page_template',
                'meta_value' => 'page-login.php',
                'number'     => 1,
            ) );
            
            if ( ! empty( $pages ) ) {
                $login_page = $pages[0];
                
                // If Polylang is active, try to get the translated version
                if ( function_exists( 'pll_get_post' ) && $lang ) {
                    $translated_id = pll_get_post( $login_page->ID, $lang );
                    if ( $translated_id ) {
                        $login_page = get_post( $translated_id );
                    }
                }
            }
        }
        
        // If still no page found, create a virtual page
        if ( ! $login_page ) {
            $login_page = kt_create_virtual_page( 'login', kt_t( 'nav_login' ), 'page-login.php' );
        }
        
        // Set up the query
        global $wp_query, $post;
        
        // Set up the query to use the login page
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_404 = false;
        $wp_query->queried_object = $login_page;
        $wp_query->queried_object_id = $login_page->ID;
        $wp_query->posts = array( $login_page );
        $wp_query->post_count = 1;
        $wp_query->found_posts = 1;
        $wp_query->max_num_pages = 1;
        
        // Set the global post object
        $post = $login_page;
        setup_postdata( $post );
        
        // Set the page template
        $template = locate_template( 'page-login.php' );
        if ( $template ) {
            add_filter( 'template_include', function() use ( $template ) {
                return $template;
            }, 999 );
        }
    }
}
add_action( 'template_redirect', 'kt_handle_login_route', 1 );

/**
 * Handle /newsletter route with language prefixes (e.g., /es/newsletter, /fr/newsletter)
 * This ensures the newsletter page works correctly in all languages
 */
function kt_handle_newsletter_route() {
    // Only handle frontend requests
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }
    
    // Get the current request URI
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $request_path = parse_url( $request_uri, PHP_URL_PATH );
    
    // Remove leading/trailing slashes and normalize
    $request_path = trim( $request_path, '/' );
    
    // Parse language from path
    $parsed = kt_parse_language_from_path( '/' . $request_path );
    $lang = $parsed['lang'];
    $path_without_lang = trim( $parsed['path_without_lang'], '/' );
    
    // Check if this is a newsletter route
    if ( $path_without_lang === 'newsletter' ) {
        // Set Polylang language if a language prefix was found
        if ( $lang && function_exists( 'PLL' ) ) {
            $polylang = PLL();
            $language = $polylang->model->get_language( $lang );
            if ( $language ) {
                $polylang->curlang = $language;
                // Force Polylang to recognize the language
                $_REQUEST['lang'] = $lang;
            }
        }
        
        // Find the newsletter page
        // First, try to find a page with slug "newsletter" in the current language
        $newsletter_page = null;
        
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language( 'slug' );
            // Try to get the newsletter page in the current language
            $newsletter_page = get_page_by_path( 'newsletter' );
            
            // If Polylang is active, try to get the translated version
            if ( $newsletter_page && function_exists( 'pll_get_post' ) ) {
                // Check if we need the translated version
                $page_lang = pll_get_post_language( $newsletter_page->ID, 'slug' );
                if ( $current_lang && $page_lang !== $current_lang ) {
                    $translated_id = pll_get_post( $newsletter_page->ID, $current_lang );
                    if ( $translated_id ) {
                        $newsletter_page = get_post( $translated_id );
                    }
                }
            }
        } else {
            // Fallback: find any page with slug "newsletter"
            $newsletter_page = get_page_by_path( 'newsletter' );
        }
        
        // If no page found by slug, try to find any page using the "Newsletter Page" template
        if ( ! $newsletter_page ) {
            $pages = get_pages( array(
                'meta_key'   => '_wp_page_template',
                'meta_value' => 'page-newsletter.php',
                'number'     => 1,
            ) );
            
            if ( ! empty( $pages ) ) {
                $newsletter_page = $pages[0];
                
                // If Polylang is active, try to get the translated version
                if ( function_exists( 'pll_get_post' ) && $lang ) {
                    $translated_id = pll_get_post( $newsletter_page->ID, $lang );
                    if ( $translated_id ) {
                        $newsletter_page = get_post( $translated_id );
                    }
                }
            }
        }
        
        // If still no page found, create a virtual page
        if ( ! $newsletter_page ) {
            $newsletter_page = kt_create_virtual_page( 'newsletter', kt_t( 'nav_newsletter' ), 'page-newsletter.php' );
        }
        
        // Set up the query
        global $wp_query, $post;
        
        // Set up the query to use the newsletter page
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_404 = false;
        $wp_query->queried_object = $newsletter_page;
        $wp_query->queried_object_id = $newsletter_page->ID;
        $wp_query->posts = array( $newsletter_page );
        $wp_query->post_count = 1;
        $wp_query->found_posts = 1;
        $wp_query->max_num_pages = 1;
        
        // Set the global post object
        $post = $newsletter_page;
        setup_postdata( $post );
        
        // Set the page template
        $template = locate_template( 'page-newsletter.php' );
        if ( $template ) {
            add_filter( 'template_include', function() use ( $template ) {
                return $template;
            }, 999 );
        }
    }
}
add_action( 'template_redirect', 'kt_handle_newsletter_route', 1 );

/**
 * Handle /privacy route with language prefixes (e.g., /es/privacy, /fr/privacy)
 * This ensures the privacy page works correctly in all languages
 */
function kt_handle_privacy_route() {
    // Only handle frontend requests
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }
    
    // Get the current request URI
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $request_path = parse_url( $request_uri, PHP_URL_PATH );
    
    // Remove leading/trailing slashes and normalize
    $request_path = trim( $request_path, '/' );
    
    // Parse language from path
    $parsed = kt_parse_language_from_path( '/' . $request_path );
    $lang = $parsed['lang'];
    $path_without_lang = trim( $parsed['path_without_lang'], '/' );
    
    // Check if this is a privacy route
    if ( $path_without_lang === 'privacy' ) {
        // Set Polylang language if a language prefix was found
        if ( $lang && function_exists( 'PLL' ) ) {
            $polylang = PLL();
            $language = $polylang->model->get_language( $lang );
            if ( $language ) {
                $polylang->curlang = $language;
                // Force Polylang to recognize the language
                $_REQUEST['lang'] = $lang;
            }
        }
        
        // Find the privacy page
        // First, try to find a page with slug "privacy" in the current language
        $privacy_page = null;
        
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language( 'slug' );
            // Try to get the privacy page in the current language
            $privacy_page = get_page_by_path( 'privacy' );
            
            // If Polylang is active, try to get the translated version
            if ( $privacy_page && function_exists( 'pll_get_post' ) ) {
                // Check if we need the translated version
                $page_lang = pll_get_post_language( $privacy_page->ID, 'slug' );
                if ( $current_lang && $page_lang !== $current_lang ) {
                    $translated_id = pll_get_post( $privacy_page->ID, $current_lang );
                    if ( $translated_id ) {
                        $privacy_page = get_post( $translated_id );
                    }
                }
            }
        } else {
            // Fallback: find any page with slug "privacy"
            $privacy_page = get_page_by_path( 'privacy' );
        }
        
        // If no page found by slug, try to find any page using the "Privacy Page" template
        if ( ! $privacy_page ) {
            $pages = get_pages( array(
                'meta_key'   => '_wp_page_template',
                'meta_value' => 'page-privacy.php',
                'number'     => 1,
            ) );
            
            if ( ! empty( $pages ) ) {
                $privacy_page = $pages[0];
                
                // If Polylang is active, try to get the translated version
                if ( function_exists( 'pll_get_post' ) && $lang ) {
                    $translated_id = pll_get_post( $privacy_page->ID, $lang );
                    if ( $translated_id ) {
                        $privacy_page = get_post( $translated_id );
                    }
                }
            }
        }
        
        // If still no page found, create a virtual page
        if ( ! $privacy_page ) {
            $privacy_page = kt_create_virtual_page( 'privacy', kt_t( 'footer_privacy_policy' ), 'page-privacy.php' );
        }
        
        // Set up the query
        global $wp_query, $post;
        
        // Set up the query to use the privacy page
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_404 = false;
        $wp_query->queried_object = $privacy_page;
        $wp_query->queried_object_id = $privacy_page->ID;
        $wp_query->posts = array( $privacy_page );
        $wp_query->post_count = 1;
        $wp_query->found_posts = 1;
        $wp_query->max_num_pages = 1;
        
        // Set the global post object
        $post = $privacy_page;
        setup_postdata( $post );
        
        // Set the page template
        $template = locate_template( 'page-privacy.php' );
        if ( $template ) {
            add_filter( 'template_include', function() use ( $template ) {
                return $template;
            }, 999 );
        }
    }
}
add_action( 'template_redirect', 'kt_handle_privacy_route', 1 );

/**
 * Handle /about route with language prefixes (e.g., /es/about, /fr/about)
 * This ensures the about page works correctly in all languages
 */
function kt_handle_about_route() {
    // Only handle frontend requests
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }
    
    // Get the current request URI
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $request_path = parse_url( $request_uri, PHP_URL_PATH );
    
    // Remove leading/trailing slashes and normalize
    $request_path = trim( $request_path, '/' );
    
    // Parse language from path
    $parsed = kt_parse_language_from_path( '/' . $request_path );
    $lang = $parsed['lang'];
    $path_without_lang = trim( $parsed['path_without_lang'], '/' );
    
    // Check if this is an about route
    if ( $path_without_lang === 'about' ) {
        // Set Polylang language if a language prefix was found
        if ( $lang && function_exists( 'PLL' ) ) {
            $polylang = PLL();
            $language = $polylang->model->get_language( $lang );
            if ( $language ) {
                $polylang->curlang = $language;
                // Force Polylang to recognize the language
                $_REQUEST['lang'] = $lang;
            }
        }
        
        // Find the about page
        // First, try to find a page with slug "about" in the current language
        $about_page = null;
        
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language( 'slug' );
            // Try to get the about page in the current language
            $about_page = get_page_by_path( 'about' );
            
            // If Polylang is active, try to get the translated version
            if ( $about_page && function_exists( 'pll_get_post' ) ) {
                // Check if we need the translated version
                $page_lang = pll_get_post_language( $about_page->ID, 'slug' );
                if ( $current_lang && $page_lang !== $current_lang ) {
                    $translated_id = pll_get_post( $about_page->ID, $current_lang );
                    if ( $translated_id ) {
                        $about_page = get_post( $translated_id );
                    }
                }
            }
        } else {
            // Fallback: find any page with slug "about"
            $about_page = get_page_by_path( 'about' );
        }
        
        // If no page found by slug, try to find any page using an "About Page" template
        if ( ! $about_page ) {
            $pages = get_pages( array(
                'meta_key'   => '_wp_page_template',
                'meta_value' => 'page-about.php',
                'number'     => 1,
            ) );
            
            if ( ! empty( $pages ) ) {
                $about_page = $pages[0];
                
                // If Polylang is active, try to get the translated version
                if ( function_exists( 'pll_get_post' ) && $lang ) {
                    $translated_id = pll_get_post( $about_page->ID, $lang );
                    if ( $translated_id ) {
                        $about_page = get_post( $translated_id );
                    }
                }
            }
        }
        
        // If still no page found, create a virtual page
        if ( ! $about_page ) {
            $about_page = kt_create_virtual_page( 'about', kt_t( 'nav_about' ), 'page-about.php' );
        }
        
        // Set up the query
        global $wp_query, $post;
        
        // Set up the query to use the about page
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_404 = false;
        $wp_query->queried_object = $about_page;
        $wp_query->queried_object_id = $about_page->ID;
        $wp_query->posts = array( $about_page );
        $wp_query->post_count = 1;
        $wp_query->found_posts = 1;
        $wp_query->max_num_pages = 1;
        
        // Set the global post object
        $post = $about_page;
        setup_postdata( $post );
        
        // Set the page template (use page-about.php if it exists, otherwise default to page.php)
        $template = locate_template( array( 'page-about.php', 'page.php' ) );
        if ( $template ) {
            add_filter( 'template_include', function() use ( $template ) {
                return $template;
            }, 999 );
        }
    }
}
add_action( 'template_redirect', 'kt_handle_about_route', 1 );

// ============================================================================
// WP-CLI COMMANDS
// ============================================================================

/**
 * Register WP-CLI command for testing Google Translate API
 */
if (defined('WP_CLI') && WP_CLI) {
    require_once get_template_directory() . '/cli/test-google-translate.php';
}


