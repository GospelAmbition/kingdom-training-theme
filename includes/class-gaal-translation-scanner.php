<?php
/**
 * Translation Scanner
 * 
 * Scans all content and identifies missing translations for enabled languages.
 */

if (!class_exists('GAAL_Translation_Scanner')) {
    class GAAL_Translation_Scanner {
        
        /**
         * Supported post types for translation
         * 
         * @var array
         */
        protected $post_types = array('post', 'page', 'article', 'strategy_course', 'tool');
        
        /**
         * Find all English posts missing translations
         * 
         * @param array $filters Optional filters (post_type, language, etc.)
         * @return array Translation gaps
         */
        public function find_gaps($filters = array()) {
            $enabled_languages = get_option('gaal_translation_enabled_languages', array());
            $target_languages = array_diff($enabled_languages, array('en'));
            
            if (empty($target_languages)) {
                return array();
            }
            
            // Build query args
            $args = array(
                'post_type' => isset($filters['post_type']) && !empty($filters['post_type']) ? $filters['post_type'] : $this->post_types,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            );
            
            // Add language filter for Polylang
            if (function_exists('pll_default_language')) {
                $args['lang'] = pll_default_language('slug') ?: 'en';
            }
            
            $english_post_ids = get_posts($args);
            $gaps = array();
            
            foreach ($english_post_ids as $post_id) {
                $translations = function_exists('pll_get_post_translations') 
                    ? pll_get_post_translations($post_id) 
                    : array();
                
                $existing_langs = array_keys($translations);
                $missing_langs = array_diff($target_languages, $existing_langs);
                
                // Apply language filter if specified
                if (!empty($filters['language'])) {
                    $missing_langs = array_intersect($missing_langs, (array) $filters['language']);
                }
                
                if (!empty($missing_langs)) {
                    $post = get_post($post_id);
                    $post_type_obj = get_post_type_object($post->post_type);
                    
                    $gaps[$post_id] = array(
                        'id' => $post_id,
                        'title' => $post->post_title,
                        'post_type' => $post->post_type,
                        'post_type_label' => $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type,
                        'status' => $post->post_status,
                        'edit_link' => get_edit_post_link($post_id, 'raw'),
                        'missing_languages' => array_values($missing_langs),
                        'existing_translations' => $this->get_translation_status($translations),
                        'content_length' => strlen($post->post_content),
                        'estimated_chunks' => max(1, ceil(strlen($post->post_content) / 3000)),
                    );
                }
            }
            
            return $gaps;
        }
        
        /**
         * Get summary statistics
         * 
         * @return array Summary data
         */
        public function get_summary() {
            $gaps = $this->find_gaps();
            $enabled_languages = get_option('gaal_translation_enabled_languages', array());
            $target_languages = array_diff($enabled_languages, array('en'));
            
            $total_posts = count($gaps);
            $total_translations_needed = 0;
            $by_language = array();
            $by_post_type = array();
            
            foreach ($target_languages as $lang) {
                $by_language[$lang] = 0;
            }
            
            foreach ($gaps as $gap) {
                $total_translations_needed += count($gap['missing_languages']);
                
                foreach ($gap['missing_languages'] as $lang) {
                    if (isset($by_language[$lang])) {
                        $by_language[$lang]++;
                    }
                }
                
                $pt = $gap['post_type'];
                $by_post_type[$pt] = isset($by_post_type[$pt]) ? $by_post_type[$pt] + 1 : 1;
            }
            
            return array(
                'posts_needing_translation' => $total_posts,
                'total_translations_needed' => $total_translations_needed,
                'languages_enabled' => count($target_languages),
                'by_language' => $by_language,
                'by_post_type' => $by_post_type,
            );
        }
        
        /**
         * Format translation status for display
         * 
         * @param array $translations Polylang translations array
         * @return array Formatted translation status
         */
        protected function get_translation_status($translations) {
            $status = array();
            foreach ($translations as $lang => $post_id) {
                if ($lang === 'en') continue;
                $post = get_post($post_id);
                if ($post) {
                    $status[$lang] = array(
                        'id' => $post_id,
                        'status' => $post->post_status,
                        'edit_link' => get_edit_post_link($post_id, 'raw'),
                    );
                }
            }
            return $status;
        }
        
        /**
         * Get supported post types
         * 
         * @return array
         */
        public function get_post_types() {
            return $this->post_types;
        }
        
        /**
         * Get post types with labels for display
         * 
         * @return array
         */
        public function get_post_types_with_labels() {
            $result = array();
            foreach ($this->post_types as $post_type) {
                $obj = get_post_type_object($post_type);
                $result[$post_type] = $obj ? $obj->labels->singular_name : $post_type;
            }
            return $result;
        }
        
        /**
         * Find all existing translations (non-English posts that are translations)
         * 
         * @param array $filters Optional filters (post_type, language, status)
         * @return array Existing translations
         */
        public function find_existing_translations($filters = array()) {
            $enabled_languages = get_option('gaal_translation_enabled_languages', array());
            $target_languages = array_diff($enabled_languages, array('en'));
            
            if (empty($target_languages)) {
                return array();
            }
            
            // Apply language filter if specified
            if (!empty($filters['language'])) {
                $target_languages = array_intersect($target_languages, (array) $filters['language']);
            }
            
            $translations = array();
            
            foreach ($target_languages as $lang) {
                // Build query args for this language
                $args = array(
                    'post_type' => isset($filters['post_type']) && !empty($filters['post_type']) ? $filters['post_type'] : $this->post_types,
                    'post_status' => isset($filters['status']) && !empty($filters['status']) ? $filters['status'] : array('publish', 'draft', 'pending'),
                    'posts_per_page' => -1,
                    'lang' => $lang,
                );
                
                $posts = get_posts($args);
                
                foreach ($posts as $post) {
                    // Get the source (English) post
                    $source_post_id = null;
                    $source_post = null;
                    
                    if (function_exists('pll_get_post_translations')) {
                        $post_translations = pll_get_post_translations($post->ID);
                        if (isset($post_translations['en'])) {
                            $source_post_id = $post_translations['en'];
                            $source_post = get_post($source_post_id);
                        }
                    }
                    
                    $post_type_obj = get_post_type_object($post->post_type);
                    
                    // Get translation metadata
                    $translated_at = get_post_meta($post->ID, '_gaal_translated_at', true);
                    $evaluation = get_post_meta($post->ID, '_gaal_evaluation', true);
                    
                    $translations[] = array(
                        'id' => $post->ID,
                        'title' => $post->post_title,
                        'post_type' => $post->post_type,
                        'post_type_label' => $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type,
                        'status' => $post->post_status,
                        'language' => $lang,
                        'edit_link' => get_edit_post_link($post->ID, 'raw'),
                        'view_link' => get_permalink($post->ID),
                        'source_post_id' => $source_post_id,
                        'source_title' => $source_post ? $source_post->post_title : null,
                        'source_edit_link' => $source_post_id ? get_edit_post_link($source_post_id, 'raw') : null,
                        'translated_at' => $translated_at,
                        'modified_date' => $post->post_modified,
                        'content_length' => strlen($post->post_content),
                        'evaluation' => $evaluation ? $evaluation : null,
                    );
                }
            }
            
            // Sort by modified date descending
            usort($translations, function($a, $b) {
                return strtotime($b['modified_date']) - strtotime($a['modified_date']);
            });
            
            return $translations;
        }
        
        /**
         * Get summary of existing translations
         * 
         * @return array Summary data
         */
        public function get_translations_summary() {
            $translations = $this->find_existing_translations();
            
            $by_language = array();
            $by_status = array();
            $by_post_type = array();
            
            foreach ($translations as $t) {
                // By language
                $lang = $t['language'];
                $by_language[$lang] = isset($by_language[$lang]) ? $by_language[$lang] + 1 : 1;
                
                // By status
                $status = $t['status'];
                $by_status[$status] = isset($by_status[$status]) ? $by_status[$status] + 1 : 1;
                
                // By post type
                $pt = $t['post_type'];
                $by_post_type[$pt] = isset($by_post_type[$pt]) ? $by_post_type[$pt] + 1 : 1;
            }
            
            return array(
                'total' => count($translations),
                'by_language' => $by_language,
                'by_status' => $by_status,
                'by_post_type' => $by_post_type,
            );
        }
        
        /**
         * Get all Polylang registered strings with their translations
         * 
         * @param array $filters Optional filters (group, language)
         * @return array Strings with translations
         */
        /**
         * Get Polylang strings using DIRECT DATABASE ACCESS
         * 
         * This method bypasses Polylang's API and caching to read directly from the database.
         * It uses:
         * 1. gaal_get_all_translatable_strings() for registered strings (single source of truth)
         * 2. Direct term meta queries for translations (no caching, no API)
         * 
         * @param array $filters Optional filters (group, etc.)
         * @return array Strings with translations
         */
        public function get_polylang_strings($filters = array()) {
            $enabled_languages = get_option('gaal_translation_enabled_languages', array());
            $target_languages = array_diff($enabled_languages, array('en'));
            
            if (empty($target_languages)) {
                return array();
            }
            
            // Get registered strings from our centralized function (single source of truth)
            if (!function_exists('gaal_get_all_translatable_strings')) {
                return array();
            }
            
            $registered_strings = gaal_get_all_translatable_strings();
            
            if (empty($registered_strings)) {
                return array();
            }
            
            // Get language terms directly from database
            $language_terms = $this->get_language_terms_direct();
            if (empty($language_terms)) {
                return array();
            }
            
            // Build language lookup by slug
            $lang_terms_by_slug = array();
            foreach ($language_terms as $term) {
                $lang_terms_by_slug[$term->slug] = $term;
            }
            
            // Group filter
            $filter_group = isset($filters['group']) ? $filters['group'] : '';
            
            $strings = array();
            
            // Process each registered string
            foreach ($registered_strings as $name => $data) {
                $string_text = isset($data['string']) ? $data['string'] : '';
                $string_group = isset($data['context']) ? $data['context'] : 'Frontend UI';
                $multiline = isset($data['multiline']) ? $data['multiline'] : false;
                
                // Skip empty strings
                if (empty($string_text)) {
                    continue;
                }
                
                // Skip date/time format strings - these are PHP date format codes, not translatable text
                if ($this->is_date_format_string($string_text)) {
                    continue;
                }
                
                // Apply group filter
                if ($filter_group && $string_group !== $filter_group) {
                    continue;
                }
                
                // Get translations for each target language using DIRECT DATABASE ACCESS
                $translations = array();
                $missing_languages = array();
                
                foreach ($target_languages as $lang_slug) {
                    if (!isset($lang_terms_by_slug[$lang_slug])) {
                        $missing_languages[] = $lang_slug;
                        continue;
                    }
                    
                    $term = $lang_terms_by_slug[$lang_slug];
                    $translated = $this->get_string_translation_direct($string_text, $term);
                    
                    if ($translated && $translated !== $string_text && !empty($translated)) {
                        $translations[$lang_slug] = $translated;
                    } else {
                        $missing_languages[] = $lang_slug;
                    }
                }
                
                $strings[] = array(
                    'name' => $name,
                    'string' => $string_text,
                    'group' => $string_group,
                    'multiline' => $multiline,
                    'translations' => $translations,
                    'missing_languages' => $missing_languages,
                    'is_complete' => empty($missing_languages),
                );
            }
            
            return $strings;
        }
        
        /**
         * Get registered Polylang strings
         * 
         * This method reads directly from Polylang's database structure to get ALL registered strings.
         * It combines multiple sources to ensure we get the complete list:
         * 
         * 1. PRIMARY: PLL_Admin_Strings::get_strings() - same method Polylang's admin page uses
         * 2. SUPPLEMENT: Centralized function - ensures our theme strings are included
         * 3. DATABASE: MO options - catches any strings in the database that might be missing
         * 
         * This approach ensures we get ALL strings regardless of registration timing or context.
         * 
         * @return array
         */
        protected function get_registered_pll_strings() {
            $strings = array();
            $seen_strings = array();
            
            // Ensure all string registrations have completed
            // This is critical when called from REST API context where 'init' hook may have fired
            // but string registrations might not be complete. We explicitly trigger registration
            // to ensure all strings are available.
            if (function_exists('gaal_register_ui_strings') && did_action('init')) {
                // Only call if init has fired (safe to call multiple times)
                gaal_register_ui_strings(); // Explicitly register our theme strings
            }
            
            // PRIMARY METHOD: Use PLL_Admin_Strings::get_strings() - this is what Polylang's
            // String Translations page uses, so we get the exact same list
            $pll_strings_loaded = false;
            if (class_exists('PLL_Admin_Strings') && method_exists('PLL_Admin_Strings', 'get_strings')) {
                $pll_strings = PLL_Admin_Strings::get_strings();
                
                if (!empty($pll_strings) && is_array($pll_strings)) {
                    $pll_strings_loaded = true;
                    foreach ($pll_strings as $string_data) {
                        $string_text = isset($string_data['string']) ? $string_data['string'] : '';
                        if (empty($string_text)) {
                            continue;
                        }
                        
                        // Skip date/time format strings - these are PHP date format codes, not translatable text
                        if ($this->is_date_format_string($string_text)) {
                            continue;
                        }
                        
                        // Use string text as key to avoid duplicates
                        $string_key = md5($string_text);
                        if (isset($seen_strings[$string_key])) {
                            continue;
                        }
                        $seen_strings[$string_key] = true;
                        
                        $strings[] = array(
                            'name' => isset($string_data['name']) ? $string_data['name'] : '',
                            'string' => $string_text,
                            'context' => isset($string_data['context']) ? $string_data['context'] : 'Polylang',
                            'multiline' => isset($string_data['multiline']) ? $string_data['multiline'] : false,
                        );
                    }
                }
            }
            
            // SUPPLEMENT METHOD: Always include strings from centralized function
            // This ensures our theme strings are included even if Polylang's method missed them
            // or if they weren't registered yet when PLL_Admin_Strings::get_strings() was called
            if (function_exists('gaal_get_all_translatable_strings')) {
                $all_strings = gaal_get_all_translatable_strings();
                
                foreach ($all_strings as $name => $data) {
                    $string_text = isset($data['string']) ? $data['string'] : '';
                    if (empty($string_text)) {
                        continue;
                    }
                    
                    // Skip date/time format strings
                    if ($this->is_date_format_string($string_text)) {
                        continue;
                    }
                    
                    $string_key = md5($string_text);
                    if (isset($seen_strings[$string_key])) {
                        continue; // Already added from Polylang's registered strings
                    }
                    $seen_strings[$string_key] = true;
                    
                    $strings[] = array(
                        'name' => $name,
                        'string' => $string_text,
                        'context' => isset($data['context']) ? $data['context'] : 'Frontend UI',
                        'multiline' => isset($data['multiline']) ? $data['multiline'] : false,
                    );
                }
            }
            
            // DATABASE METHOD: Get strings from Polylang's MO options (database storage)
            // This catches any strings that have been translated and stored in the database,
            // even if they're not in the registered strings list or weren't found above
            $mo_strings = $this->extract_strings_from_all_mo_options();
            foreach ($mo_strings as $string_data) {
                $string_text = $string_data['string'];
                if (empty($string_text)) {
                    continue;
                }
                
                // Skip date/time format strings
                if ($this->is_date_format_string($string_text)) {
                    continue;
                }
                
                $string_key = md5($string_text);
                if (isset($seen_strings[$string_key])) {
                    continue; // Already added from previous methods
                }
                $seen_strings[$string_key] = true;
                $strings[] = $string_data;
            }
            
            // Sort by group then by string
            usort($strings, function($a, $b) {
                $group_cmp = strcmp($a['context'], $b['context']);
                if ($group_cmp !== 0) {
                    return $group_cmp;
                }
                return strcmp($a['string'], $b['string']);
            });
            
            return $strings;
        }
        
        /**
         * Get the theme's registered strings directly
         * This is a fallback when Polylang's internal storage isn't accessible
         * 
         * Uses the centralized gaal_get_all_translatable_strings() function
         * which is the single source of truth for all translatable strings.
         * 
         * @return array
         */
        protected function get_theme_registered_strings() {
            // Use centralized function - single source of truth
            if (function_exists('gaal_get_all_translatable_strings')) {
                $all_strings = gaal_get_all_translatable_strings();
                $strings = array();
                
                foreach ($all_strings as $name => $data) {
                    $strings[] = array(
                        'name' => $name,
                        'string' => $data['string'],
                        'context' => $data['context'],
                        'multiline' => isset($data['multiline']) ? $data['multiline'] : false,
                    );
                }
                
                return $strings;
            }
            
            // Fallback to hardcoded list if function doesn't exist (shouldn't happen)
            // These match the strings registered in functions.php gaal_register_ui_strings()
            $frontend_ui_strings = array(
                // Navigation
                array('name' => 'nav_home', 'string' => 'Home'),
                array('name' => 'nav_articles', 'string' => 'Articles'),
                array('name' => 'nav_tools', 'string' => 'Tools'),
                array('name' => 'nav_strategy_course', 'string' => 'Strategy Course'),
                array('name' => 'nav_strategy_courses', 'string' => 'Strategy Courses'),
                array('name' => 'nav_newsletter', 'string' => 'Newsletter'),
                array('name' => 'nav_search', 'string' => 'Search'),
                array('name' => 'nav_login', 'string' => 'Login'),
                array('name' => 'nav_menu', 'string' => 'Menu'),
                array('name' => 'nav_about', 'string' => 'About'),
                array('name' => 'nav_enroll_mvp', 'string' => 'Start The MVP Course'),
                array('name' => 'nav_start_mvp', 'string' => 'Start the MVP Course'),
                array('name' => 'nav_subscribe_newsletter', 'string' => 'Subscribe to Newsletter'),
                // UI elements
                array('name' => 'ui_read_more', 'string' => 'Learn more'),
                array('name' => 'ui_view_all', 'string' => 'View all'),
                array('name' => 'ui_browse_all', 'string' => 'Browse all'),
                array('name' => 'ui_back_to', 'string' => 'Back to'),
                array('name' => 'ui_explore', 'string' => 'Explore'),
                array('name' => 'ui_read_articles', 'string' => 'Read Articles'),
                array('name' => 'ui_explore_tools', 'string' => 'Explore Tools'),
                array('name' => 'ui_select_language', 'string' => 'Select Language'),
                array('name' => 'ui_close', 'string' => 'Close'),
                array('name' => 'ui_loading', 'string' => 'Loading...'),
                // Page strings
                array('name' => 'page_latest_articles', 'string' => 'Latest Articles'),
                array('name' => 'page_featured_tools', 'string' => 'Featured Tools'),
                array('name' => 'page_key_information', 'string' => 'Key Information About Media to Disciple Making Movements'),
                array('name' => 'page_mvp_strategy_course', 'string' => 'The MVP: Strategy Course'),
                array('name' => 'page_start_strategy_course', 'string' => 'Start Your Strategy Course'),
                array('name' => 'page_step_curriculum', 'string' => 'The {count}-Step Curriculum:'),
                array('name' => 'page_strategy_course', 'string' => 'Strategy Course'),
                array('name' => 'page_strategy_course_description', 'string' => 'Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program below to develop your complete M2DMM strategy.', 'multiline' => true),
                array('name' => 'page_tools', 'string' => 'Tools'),
                array('name' => 'page_articles', 'string' => 'Articles'),
                array('name' => 'page_newsletter', 'string' => 'Newsletter'),
                array('name' => 'page_about', 'string' => 'About Us'),
                // Messages
                array('name' => 'msg_no_articles', 'string' => 'Articles will appear here once content is added to WordPress.'),
                array('name' => 'msg_no_tools', 'string' => 'Tools will appear here once content is added to WordPress.'),
                array('name' => 'msg_no_content', 'string' => 'No content found.'),
                array('name' => 'msg_discover_supplementary', 'string' => 'Discover supplementary tools and resources to enhance your M2DMM strategy development and practice.'),
                array('name' => 'msg_discover_more', 'string' => 'Discover more articles and resources to deepen your understanding and enhance your M2DMM practice.'),
                // Footer
                array('name' => 'footer_quick_links', 'string' => 'Quick Links'),
                array('name' => 'footer_our_vision', 'string' => 'Our Vision'),
                array('name' => 'footer_subscribe', 'string' => 'Subscribe to Newsletter'),
                array('name' => 'footer_privacy_policy', 'string' => 'Privacy Policy'),
                array('name' => 'footer_all_rights', 'string' => 'All rights reserved.'),
                array('name' => 'footer_mission_statement', 'string' => 'Training disciple makers to use media to accelerate Disciple Making Movements. Equipping practitioners with practical strategies that bridge online engagement with face-to-face discipleship.', 'multiline' => true),
                array('name' => 'footer_scripture_quote', 'string' => 'Of the sons of Issachar, men who understood the times, with knowledge of what Israel should do.'),
                array('name' => 'footer_scripture_citation', 'string' => '— 1 Chronicles 12:32'),
                array('name' => 'footer_technology_paragraph', 'string' => 'We wonder what the Church could accomplish with technology God has given to this generation for the first time in history.'),
                // Newsletter
                array('name' => 'newsletter_subscribe', 'string' => 'Subscribe'),
                array('name' => 'newsletter_email_placeholder', 'string' => 'Enter your email'),
                array('name' => 'newsletter_name_placeholder', 'string' => 'Enter your name'),
                array('name' => 'newsletter_success', 'string' => 'Successfully subscribed!'),
                array('name' => 'newsletter_error', 'string' => 'Failed to subscribe. Please try again.'),
                // Search
                array('name' => 'search_placeholder', 'string' => 'Search...'),
                array('name' => 'search_no_results', 'string' => 'No results found'),
                array('name' => 'search_results', 'string' => 'Search Results'),
                // Breadcrumbs
                array('name' => 'breadcrumb_home', 'string' => 'Home'),
                array('name' => 'breadcrumb_articles', 'string' => 'Articles'),
                array('name' => 'breadcrumb_tools', 'string' => 'Tools'),
                array('name' => 'breadcrumb_strategy_courses', 'string' => 'Strategy Courses'),
                // Hero
                array('name' => 'hero_explore_resources', 'string' => 'Explore Our Resources'),
                array('name' => 'hero_about_us', 'string' => 'About Us'),
                array('name' => 'hero_cta_about_us', 'string' => 'About Us'),
                array('name' => 'hero_cta_explore_resources', 'string' => 'Explore Our Resources'),
                array('name' => 'hero_description', 'string' => 'Accelerate your disciple making with strategic use of media, advertising, and AI tools. Kingdom.Training is a resource for disciple makers to use media to accelerate Disciple Making Movements.', 'multiline' => true),
                array('name' => 'hero_newsletter_title', 'string' => 'Get the newest insights, techniques, and strategies.'),
                // Home page
                array('name' => 'home_mvp_description', 'string' => 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context. Complete your plan in 6-7 hours.', 'multiline' => true),
                array('name' => 'home_newsletter_description', 'string' => 'Field driven tools and articles for disciple makers.'),
                array('name' => 'home_heavenly_economy', 'string' => 'We operate within what we call the "Heavenly Economy"—a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, we reflect God\'s generous nature by offering free training, hands-on coaching, and open-source tools like Disciple.Tools.', 'multiline' => true),
                array('name' => 'home_mission_statement', 'string' => 'Our heart beats with passion for the unreached and least-reached peoples of the world. Every course, article, and tool serves the ultimate vision of seeing Disciple Making Movements catalyzed among people groups where the name of Jesus has never been proclaimed.', 'multiline' => true),
                array('name' => 'home_loading_steps', 'string' => 'Loading course steps...'),
                // Course Strings
                array('name' => 'course_flagship_description', 'string' => 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context.', 'multiline' => true),
                array('name' => 'course_complete_plan', 'string' => 'Complete your plan step by step.'),
                // SEO Strings
                array('name' => 'seo_tools_description', 'string' => 'Essential tools and resources for Media to Disciple Making Movements work. Discover Disciple.Tools—our free, open-source disciple relationship management system—and other practical resources designed specifically for M2DMM practitioners.', 'multiline' => true),
                // Content Strings
                array('name' => 'content_digital_disciple_making', 'string' => 'What is Digital Disciple Making?'),
                array('name' => 'content_heavenly_economy', 'string' => 'The Heavenly Economy'),
                array('name' => 'content_key_information_m2dmm', 'string' => 'Key Information About Media to Disciple Making Movements'),
                array('name' => 'content_m2dmm_definition', 'string' => 'Media to Disciple Making Movements (M2DMM) is a strategic approach that uses targeted media content to identify spiritual seekers online and connect them with face-to-face disciple-makers. The process involves three stages: (1) Media Content - targeted content reaches entire people groups through platforms like Facebook and Google Ads, (2) Digital Filtering - trained responders dialogue with seekers online, identifying persons of peace ready for face-to-face engagement, (3) Face-to-Face Discipleship - multipliers meet seekers in person, guiding them through discovery, obedience, and sharing in reproducing communities.', 'multiline' => true),
                array('name' => 'content_additional_resources', 'string' => 'Additional Course Resources'),
                array('name' => 'content_supplementary_materials', 'string' => 'Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development.', 'multiline' => true),
                array('name' => 'content_categories', 'string' => 'Categories'),
                array('name' => 'content_tags', 'string' => 'Tags'),
                array('name' => 'content_no_tools_found', 'string' => 'No Tools Found'),
                array('name' => 'content_no_tools_try', 'string' => 'Try adjusting your filters or check back later.'),
                array('name' => 'content_no_articles_found', 'string' => 'No Articles Found'),
                array('name' => 'content_no_articles_try', 'string' => 'Try adjusting your filters or check back later.'),
                // KeyInfoSection Terms & Definitions
                array('name' => 'content_m2dmm_term', 'string' => 'What is Media to Disciple Making Movements (M2DMM)?'),
                array('name' => 'content_digital_disciple_making_term', 'string' => 'What is digital disciple making?'),
                array('name' => 'content_digital_disciple_making_definition', 'string' => 'Digital disciple making is the strategic use of all digital means—including social media, online advertising, AI tools, content creation, and digital communication platforms—to find seekers and bring them into relationship with Christ and his church in person. The ambition is to leverage every available digital tool and technique to identify spiritual seekers, engage them meaningfully online, and ultimately connect them with face-to-face discipleship communities where they can grow in their relationship with Jesus and participate in multiplying movements.', 'multiline' => true),
                array('name' => 'content_mvp_course_term', 'string' => 'What is the MVP Strategy Course?'),
                array('name' => 'content_mvp_course_definition', 'string' => 'The MVP (Minimum Viable Product) Strategy Course is a 10-step program that guides you through the core elements needed to craft a Media to Disciple Making Movements strategy for any context. The course helps you develop your complete M2DMM strategy and can be completed in 6-7 hours. It covers topics including media content creation, digital filtering strategies, face-to-face discipleship methods, and movement multiplication principles.', 'multiline' => true),
                array('name' => 'content_ai_discipleship_term', 'string' => 'What is AI for discipleship?'),
                array('name' => 'content_ai_discipleship_definition', 'string' => 'AI for discipleship empowers small teams to have a big impact by leveraging artificial intelligence tools and techniques. Kingdom.Training is bringing new techniques to accelerate small teams to use AI effectively in disciple making. These innovative approaches help teams scale their efforts, automate routine tasks, personalize engagement, and multiply their reach—enabling small groups to accomplish what previously required much larger teams.', 'multiline' => true),
                array('name' => 'content_heavenly_economy_term', 'string' => 'What is the Heavenly Economy?'),
                array('name' => 'content_heavenly_economy_definition', 'string' => 'The Heavenly Economy is a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, reflecting God\'s generous nature by offering free training, hands-on coaching, and open-source tools. This approach enables more people to access resources for disciple making, especially in unreached and least-reached areas.', 'multiline' => true),
                array('name' => 'content_kingdom_training_for_term', 'string' => 'Who is Kingdom.Training for?'),
                array('name' => 'content_kingdom_training_for_definition', 'string' => 'Kingdom.Training is for disciple makers, church planters, missionaries, and ministry leaders who want to use media strategically to accelerate Disciple Making Movements. We particularly focus on equipping those working with unreached and least-reached peoples - people groups where the name of Jesus has never been proclaimed or where there is no indigenous community of believers with adequate numbers and resources to evangelize their own people.', 'multiline' => true),
            );
            
            $strings = array();
            foreach ($frontend_ui_strings as $s) {
                $strings[] = array(
                    'name' => $s['name'],
                    'string' => $s['string'],
                    'context' => 'Frontend UI',
                    'multiline' => isset($s['multiline']) ? $s['multiline'] : false,
                );
            }
            
            // Also add common WordPress strings that Polylang registers
            $wordpress_strings = $this->get_wordpress_registered_strings();
            $strings = array_merge($strings, $wordpress_strings);
            
            return $strings;
        }
        
        /**
         * Get common WordPress strings registered by Polylang
         * These are strings that appear in the WordPress group in Polylang's String Translations
         * 
         * @return array
         */
        protected function get_wordpress_registered_strings() {
            // Common WordPress strings that Polylang registers for translation
            $wp_strings = array(
                // Site info
                array('name' => 'blogname', 'string' => get_bloginfo('name')),
                array('name' => 'blogdescription', 'string' => get_bloginfo('description')),
                // Note: date_format and time_format are excluded - they are PHP date format codes, not translatable text
            );
            
            // Get widget titles if any widgets are active
            $sidebars_widgets = get_option('sidebars_widgets', array());
            if (!empty($sidebars_widgets)) {
                foreach ($sidebars_widgets as $sidebar_id => $widgets) {
                    if (is_array($widgets)) {
                        foreach ($widgets as $widget_id) {
                            // Extract widget base and number
                            if (preg_match('/^(.+)-(\d+)$/', $widget_id, $matches)) {
                                $widget_base = $matches[1];
                                $widget_num = $matches[2];
                                $widget_option = get_option('widget_' . $widget_base);
                                if (!empty($widget_option[$widget_num]['title'])) {
                                    $wp_strings[] = array(
                                        'name' => 'widget_title_' . $widget_id,
                                        'string' => $widget_option[$widget_num]['title'],
                                    );
                                }
                            }
                        }
                    }
                }
            }
            
            // Get menu names
            $menus = wp_get_nav_menus();
            if (!empty($menus)) {
                foreach ($menus as $menu) {
                    if (!empty($menu->name)) {
                        $wp_strings[] = array(
                            'name' => 'menu_' . $menu->term_id,
                            'string' => $menu->name,
                        );
                    }
                }
            }
            
            $strings = array();
            foreach ($wp_strings as $s) {
                if (!empty($s['string'])) {
                    $strings[] = array(
                        'name' => $s['name'],
                        'string' => $s['string'],
                        'context' => 'WordPress',
                        'multiline' => isset($s['multiline']) ? $s['multiline'] : false,
                    );
                }
            }
            
            return $strings;
        }
        
        /**
         * Extract strings from all Polylang MO options
         * This captures any string that has ever been translated
         * 
         * @return array
         */
        protected function extract_strings_from_all_mo_options() {
            $strings = array();
            $seen_strings = array();
            
            // Get all language terms
            $language_terms = get_terms(array(
                'taxonomy' => 'language',
                'hide_empty' => false,
            ));
            
            if (!is_array($language_terms) || is_wp_error($language_terms)) {
                return $strings;
            }
            
            // Get the theme's known strings to help with categorization
            $theme_string_texts = array();
            $theme_strings = $this->get_theme_registered_strings();
            foreach ($theme_strings as $ts) {
                $theme_string_texts[$ts['string']] = true;
            }
            
            // Collect strings from all language MO options
            foreach ($language_terms as $term) {
                $mo_option = get_option('polylang_mo' . $term->term_id, array());
                
                if (!empty($mo_option) && is_array($mo_option)) {
                    foreach ($mo_option as $original => $translated) {
                        // Skip empty strings or already seen strings
                        if (empty($original) || isset($seen_strings[$original])) {
                            continue;
                        }
                        
                        $seen_strings[$original] = true;
                        
                        // Determine the group - if it's a known theme string, use Frontend UI
                        if (isset($theme_string_texts[$original])) {
                            $group = 'Frontend UI';
                        } else {
                            $group = 'WordPress';
                        }
                        
                        $strings[] = array(
                            'name' => sanitize_title(substr($original, 0, 50)),
                            'string' => $original,
                            'context' => $group,
                            'multiline' => (strpos($original, "\n") !== false || strlen($original) > 100),
                        );
                    }
                }
            }
            
            return $strings;
        }
        
        /**
         * Check if a string is a date/time format string (PHP date format codes)
         * These should not be translated as they are format codes, not text
         * 
         * @param string $string
         * @return bool
         */
        protected function is_date_format_string($string) {
            if (empty($string) || strlen($string) > 50) {
                return false; // Too long to be a date format
            }
            
            // Skip if it contains common words (not a date format)
            $common_words = array('the', 'and', 'or', 'to', 'of', 'in', 'on', 'at', 'for', 'with', 'from', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'this', 'that', 'these', 'those', 'what', 'when', 'where', 'who', 'why', 'how', 'about', 'home', 'page', 'menu', 'search', 'login', 'tools', 'articles', 'newsletter', 'subscribe', 'loading', 'close', 'explore', 'view', 'read', 'browse', 'learn', 'more', 'all', 'back', 'select', 'language', 'latest', 'featured', 'key', 'information', 'strategy', 'course', 'start', 'your', 'step', 'curriculum', 'no', 'content', 'found', 'try', 'adjusting', 'filters', 'check', 'later', 'quick', 'links', 'vision', 'privacy', 'policy', 'rights', 'reserved', 'enter', 'email', 'name', 'successfully', 'subscribed', 'failed', 'please', 'again', 'results', 'breadcrumb', 'hero', 'description', 'accelerate', 'disciple', 'making', 'movements', 'media', 'advertising', 'ai', 'innovate', 'make', 'disciples', 'get', 'newest', 'insights', 'techniques', 'strategies', 'flagship', 'guides', 'through', 'core', 'elements', 'needed', 'craft', 'any', 'context', 'complete', 'plan', 'hours', 'field', 'driven', 'operate', 'within', 'call', 'heavenly', 'economy', 'principle', 'challenges', 'broken', 'world', 'teaching', 'more', 'get', 'should', 'keep', 'instead', 'reflect', 'god', 'generous', 'nature', 'offering', 'free', 'training', 'hands', 'coaching', 'open', 'source', 'like', 'disciple', 'tools', 'heart', 'beats', 'passion', 'unreached', 'least', 'reached', 'peoples', 'every', 'serves', 'ultimate', 'seeing', 'catalyzed', 'among', 'people', 'groups', 'where', 'jesus', 'never', 'been', 'proclaimed', 'digital', 'means', 'including', 'social', 'online', 'creation', 'communication', 'platforms', 'find', 'seekers', 'bring', 'them', 'into', 'relationship', 'christ', 'church', 'person', 'ambition', 'leverage', 'every', 'available', 'technique', 'identify', 'spiritual', 'engage', 'meaningfully', 'ultimately', 'connect', 'face', 'discipleship', 'communities', 'grow', 'participate', 'multiplying', 'minimum', 'viable', 'product', 'program', 'helps', 'develop', 'covers', 'topics', 'creation', 'filtering', 'methods', 'multiplication', 'principles', 'empowers', 'small', 'teams', 'big', 'impact', 'leveraging', 'artificial', 'intelligence', 'bringing', 'new', 'effectively', 'innovative', 'approaches', 'help', 'scale', 'efforts', 'automate', 'routine', 'tasks', 'personalize', 'engagement', 'multiply', 'reach', 'enabling', 'accomplish', 'previously', 'required', 'much', 'larger', 'enables', 'access', 'resources', 'especially', 'areas', 'church', 'planters', 'missionaries', 'ministry', 'leaders', 'want', 'use', 'strategically', 'particularly', 'focus', 'equipping', 'working', 'indigenous', 'community', 'believers', 'adequate', 'numbers', 'evangelize', 'own', 'essential', 'discover', 'free', 'relationship', 'management', 'system', 'other', 'practical', 'designed', 'specifically', 'practitioners', 'supplementary', 'materials', 'deepen', 'understanding', 'enhance', 'development', 'practice', 'categories', 'tags', 'adjusting', 'back', 'discover', 'materials', 'resources');
            $lower_string = strtolower($string);
            foreach ($common_words as $word) {
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/', $lower_string)) {
                    return false; // Contains a common word, not a date format
                }
            }
            
            // Check for known date format patterns (exact matches)
            $known_formats = array(
                'F j, Y', 'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y',
                'F j', 'j F', 'M j, Y', 'j M Y', 'Y M j', 'M j Y',
                'g:i a', 'g:i A', 'H:i', 'h:i:s', 'g:i:s a', 'g:i:s A',
            );
            
            $trimmed = trim($string);
            if (in_array($trimmed, $known_formats)) {
                return true; // Exact match to known format
            }
            
            // Check if it's a pattern that looks like a date format
            // Must contain ONLY date format letters and separators, with at least 2 format letters
            $date_format_letters = array('d', 'D', 'j', 'l', 'N', 'S', 'w', 'z', 'W', 'F', 'm', 'M', 'n', 't', 'L', 'o', 'Y', 'y', 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'e', 'I', 'O', 'P', 'T', 'Z', 'c', 'r', 'U');
            $separators = array(' ', ',', '/', '-', ':', '.', '|');
            
            $chars = str_split($string);
            $format_char_count = 0;
            $separator_count = 0;
            $other_char_count = 0;
            
            foreach ($chars as $char) {
                if (in_array($char, $date_format_letters)) {
                    $format_char_count++;
                } elseif (in_array($char, $separators)) {
                    $separator_count++;
                } else {
                    $other_char_count++;
                }
            }
            
            // If it has other characters (not format letters or separators), it's not a date format
            if ($other_char_count > 0) {
                return false;
            }
            
            // Must have at least 2 format letters to be considered a date format
            if ($format_char_count < 2) {
                return false;
            }
            
            // If it's ONLY format letters and separators (no other chars), it's likely a date format
            // But only if it's short (date formats are typically short)
            if ($format_char_count + $separator_count === strlen($string) && strlen($string) <= 20) {
                return true;
            }
            
            return false;
        }
        
        /**
         * Guess the string group based on content patterns
         * 
         * @param string $string
         * @return string
         */
        protected function guess_string_group($string) {
            // Known Frontend UI strings from functions.php registrations
            $frontend_ui_strings = array(
                // Navigation
                'Home', 'Articles', 'Tools', 'Strategy Course', 'Strategy Courses', 
                'Newsletter', 'Search', 'Login', 'Menu', 'About', 'Start The MVP Course',
                'Start the MVP Course', 'Subscribe to Newsletter',
                // UI elements
                'Learn more', 'View all', 'Browse all', 'Back to', 'Explore',
                'Read Articles', 'Explore Tools', 'Select Language', 'Close', 'Loading...',
                // Page titles
                'Latest Articles', 'Featured Tools', 'Key Information About Media to Disciple Making Movements',
                'The MVP: Strategy Course', 'Start Your Strategy Course',
                // Messages
                'Articles will appear here', 'Tools will appear here', 'No content found',
                'Discover supplementary tools', 'Discover more articles',
                // Footer
                'Quick Links', 'Our Vision', 'Subscribe to Newsletter', 'Privacy Policy', 
                'All rights reserved', 'Training disciple makers',
                // Newsletter
                'Subscribe', 'Enter your email', 'Enter your name', 'Successfully subscribed',
                'Failed to subscribe',
                // Search
                'Search...', 'No results found', 'Search Results',
                // Breadcrumbs
                'breadcrumb',
                // Hero
                'Explore Our Resources', 'About Us', 'Accelerate your disciple making',
                'Get the newest insights, techniques, and strategies',
                // Home page
                'Our flagship course', 'Field driven tools', 'Heavenly Economy',
                'Our heart beats', 'Loading course steps',
                // Content strings
                'What is Digital Disciple Making', 'The Heavenly Economy',
                'Key Information About Media to Disciple Making Movements',
                'Media to Disciple Making Movements (M2DMM)',
                // KeyInfoSection Terms & Definitions
                'What is Media to Disciple Making Movements',
                'What is digital disciple making',
                'What is the MVP Strategy Course',
                'What is AI for discipleship',
                'What is the Heavenly Economy',
                'Who is Kingdom.Training for',
                'Digital disciple making is the strategic use',
                'AI for discipleship empowers small teams',
                'Kingdom.Training is for disciple makers',
            );
            
            // Check exact matches first
            foreach ($frontend_ui_strings as $pattern) {
                if ($string === $pattern) {
                    return 'Frontend UI';
                }
            }
            
            // Check partial matches for longer patterns
            foreach ($frontend_ui_strings as $pattern) {
                if (strlen($pattern) > 5 && stripos($string, $pattern) !== false) {
                    return 'Frontend UI';
                }
            }
            
            // WordPress core strings typically include these patterns
            $wordpress_patterns = array(
                'widget', 'sidebar', 'comment', 'post', 'page', 'category', 'tag',
                'archive', 'author', 'date', 'search', 'error', '404',
            );
            
            $string_lower = strtolower($string);
            foreach ($wordpress_patterns as $pattern) {
                if (strpos($string_lower, $pattern) !== false) {
                    return 'WordPress';
                }
            }
            
            // Default to WordPress for unknown short strings, Content for long ones
            if (strlen($string) > 150) {
                return 'Content';
            }
            
            return 'WordPress';
        }
        
        /**
         * Get translation for a specific string in a language
         * 
         * Reads directly from Polylang's MO database storage (wp_options: polylang_mo{term_id})
         * This is the same storage that Polylang's String Translations admin page uses.
         * 
         * @param string $string Original string
         * @param string $lang Language code
         * @return string|null Translated string or null if not found
         */
        /**
         * DEPRECATED: Use get_string_translation_direct() instead
         * 
         * This method is kept for backward compatibility but should not be used
         * for new code. Use get_string_translation_direct() for direct database access.
         */
        protected function get_string_translation($string, $lang) {
            // Get language term directly
            $term = $this->get_language_term_direct($lang);
            
            if (empty($term)) {
                return null;
            }
            
            // Use direct database access method
            return $this->get_string_translation_direct($string, $term);
        }
        
        /**
         * Save a string translation using DIRECT DATABASE ACCESS
         * 
         * Writes directly to term meta '_pll_strings_translations' in wp_termmeta table.
         * Bypasses Polylang's API and caching completely.
         * 
         * Format: array of arrays [[$original, $translation], ...]
         * 
         * @param string $string Original string
         * @param string $translation Translated string
         * @param string $lang Language code (slug)
         * @return bool|WP_Error Success or error
         */
        public function save_string_translation($string, $translation, $lang) {
            if (empty($string)) {
                return new WP_Error('empty_string', 'Original string cannot be empty');
            }
            
            if (empty($lang)) {
                return new WP_Error('empty_language', 'Language code cannot be empty');
            }
            
            // Get language term directly from database
            $term = $this->get_language_term_direct($lang);
            
            if (empty($term)) {
                return new WP_Error('language_not_found', 'Language "' . $lang . '" not found in Polylang');
            }
            
            // Read existing translations directly from term meta (no caching, no API)
            $strings = get_term_meta($term->term_id, '_pll_strings_translations', true);
            
            if (empty($strings) || !is_array($strings)) {
                $strings = array();
            }
            
            // Check if this string already exists and update it, or add new
            $found = false;
            foreach ($strings as $index => $entry) {
                if (is_array($entry) && count($entry) >= 2 && $entry[0] === $string) {
                    // Update existing translation
                    $strings[$index] = wp_slash(array($string, $translation));
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // Add new translation
                $strings[] = wp_slash(array($string, $translation));
            }
            
            // Write directly to term meta (direct database access, no API)
            $result = update_term_meta($term->term_id, '_pll_strings_translations', $strings);
            
            if ($result === false) {
                return new WP_Error('save_failed', 'Failed to save translation to database');
            }
            
            return true;
        }
        
        /**
         * Get unique groups from Polylang strings
         * 
         * @return array
         */
        public function get_string_groups() {
            $strings = $this->get_polylang_strings();
            $groups = array();
            
            foreach ($strings as $s) {
                if (!in_array($s['group'], $groups)) {
                    $groups[] = $s['group'];
                }
            }
            
            sort($groups);
            return $groups;
        }
        
        /**
         * Get strings summary
         * 
         * @return array
         */
        public function get_strings_summary() {
            $strings = $this->get_polylang_strings();
            $enabled_languages = get_option('gaal_translation_enabled_languages', array());
            $target_languages = array_diff($enabled_languages, array('en'));
            
            $total = count($strings);
            $complete = 0;
            $missing_by_language = array();
            
            foreach ($target_languages as $lang) {
                $missing_by_language[$lang] = 0;
            }
            
            foreach ($strings as $s) {
                if ($s['is_complete']) {
                    $complete++;
                }
                foreach ($s['missing_languages'] as $lang) {
                    if (isset($missing_by_language[$lang])) {
                        $missing_by_language[$lang]++;
                    }
                }
            }
            
            return array(
                'total' => $total,
                'complete' => $complete,
                'incomplete' => $total - $complete,
                'missing_by_language' => $missing_by_language,
            );
        }
        
        /**
         * DIRECT DATABASE ACCESS METHODS
         * These methods bypass Polylang's API and caching to read/write directly to the database
         */
        
        /**
         * Get language terms directly from database
         * 
         * @return array Array of language term objects
         */
        protected function get_language_terms_direct() {
            $terms = get_terms(array(
                'taxonomy' => 'language',
                'hide_empty' => false,
            ));
            
            if (is_wp_error($terms) || empty($terms)) {
                return array();
            }
            
            return $terms;
        }
        
        /**
         * Get language term by slug directly from database
         * 
         * @param string $lang_slug Language slug (e.g., 'ar', 'es')
         * @return WP_Term|null Language term object or null if not found
         */
        protected function get_language_term_direct($lang_slug) {
            $terms = get_terms(array(
                'taxonomy' => 'language',
                'slug' => $lang_slug,
                'hide_empty' => false,
            ));
            
            if (is_wp_error($terms) || empty($terms)) {
                return null;
            }
            
            return $terms[0];
        }
        
        /**
         * Get string translation directly from database (term meta)
         * 
         * Reads directly from wp_termmeta table, bypassing Polylang's caching
         * 
         * @param string $string Original string
         * @param WP_Term $language_term Language term object
         * @return string|null Translated string or null if not found
         */
        protected function get_string_translation_direct($string, $language_term) {
            if (empty($string) || empty($language_term) || !isset($language_term->term_id)) {
                return null;
            }
            
            // Read directly from term meta - no caching, no API
            $strings = get_term_meta($language_term->term_id, '_pll_strings_translations', true);
            
            if (empty($strings) || !is_array($strings)) {
                return null;
            }
            
            // Format: array of arrays [[$original, $translation], ...]
            foreach ($strings as $entry) {
                if (is_array($entry) && count($entry) >= 2) {
                    $original = isset($entry[0]) ? $entry[0] : '';
                    $translation = isset($entry[1]) ? $entry[1] : '';
                    
                    // Match the original string (exact match)
                    if ($original === $string && !empty($translation)) {
                        return $translation;
                    }
                }
            }
            
            return null;
        }
        
        /**
         * Get all translations for a language directly from database
         * 
         * @param WP_Term $language_term Language term object
         * @return array Associative array of [original_string => translated_string]
         */
        protected function get_all_translations_for_language_direct($language_term) {
            if (empty($language_term) || !isset($language_term->term_id)) {
                return array();
            }
            
            // Read directly from term meta
            $strings = get_term_meta($language_term->term_id, '_pll_strings_translations', true);
            
            if (empty($strings) || !is_array($strings)) {
                return array();
            }
            
            $translations = array();
            
            // Format: array of arrays [[$original, $translation], ...]
            foreach ($strings as $entry) {
                if (is_array($entry) && count($entry) >= 2) {
                    $original = isset($entry[0]) ? $entry[0] : '';
                    $translation = isset($entry[1]) ? $entry[1] : '';
                    
                    if (!empty($original) && !empty($translation)) {
                        $translations[$original] = $translation;
                    }
                }
            }
            
            return $translations;
        }
    }
}
