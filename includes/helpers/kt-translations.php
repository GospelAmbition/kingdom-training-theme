<?php
/**
 * Translation Helper Functions
 * 
 * Provides wrapper functions for Polylang translations with fallback support.
 *
 * @package KingdomTraining
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get translated string
 * Wrapper around Polylang's pll__() with fallback to default translations
 *
 * @param string $key Translation key
 * @param string $fallback Optional fallback string
 * @return string Translated string
 */
function kt_t( $key, $fallback = '' ) {
    // Try Polylang string translation first
    if ( function_exists( 'pll__' ) ) {
        // Get the default string from our translation system
        $default = kt_get_default_translation( $key );
        if ( $default ) {
            $translated = pll__( $default );
            // Only return if different from default (meaning it was translated)
            if ( $translated !== $default || pll_current_language() === pll_default_language() ) {
                return $translated;
            }
        }
    }

    // Fall back to our stored translations
    $translations = kt_get_translations_for_current_language();
    if ( isset( $translations[ $key ] ) && ! empty( $translations[ $key ] ) ) {
        return $translations[ $key ];
    }

    // Fall back to default English translation
    $default = kt_get_default_translation( $key );
    if ( $default ) {
        return $default;
    }

    // Final fallback
    return $fallback ?: $key;
}

/**
 * Get translated string with placeholder replacement
 *
 * @param string $key Translation key
 * @param array $replacements Key-value pairs for replacement
 * @return string Translated string with replacements
 */
function kt_t_replace( $key, $replacements = array() ) {
    $text = kt_t( $key );
    
    foreach ( $replacements as $placeholder => $value ) {
        $text = str_replace( '{' . $placeholder . '}', $value, $text );
    }
    
    return $text;
}

/**
 * Echo translated string
 *
 * @param string $key Translation key
 * @param string $fallback Optional fallback string
 */
function kt_e( $key, $fallback = '' ) {
    echo esc_html( kt_t( $key, $fallback ) );
}

/**
 * Echo translated string with HTML allowed
 *
 * @param string $key Translation key
 * @param string $fallback Optional fallback string
 */
function kt_e_html( $key, $fallback = '' ) {
    echo wp_kses_post( kt_t( $key, $fallback ) );
}

/**
 * Get default translation for a key
 *
 * @param string $key Translation key
 * @return string|null Default translation or null
 */
function kt_get_default_translation( $key ) {
    $defaults = kt_get_default_translations();
    return isset( $defaults[ $key ] ) ? $defaults[ $key ] : null;
}

/**
 * Get translations for current language
 * Uses existing translation system from functions.php
 *
 * @return array Translations array
 */
function kt_get_translations_for_current_language() {
    static $translations = null;
    
    if ( $translations !== null ) {
        return $translations;
    }

    $lang = kt_get_current_language();
    
    // Use the existing gaal_get_translations function if available
    if ( function_exists( 'gaal_get_translations' ) ) {
        $translations = gaal_get_translations( $lang );
        return $translations;
    }

    // Fallback to defaults
    $translations = kt_get_default_translations();
    return $translations;
}

/**
 * Get all default English translations
 *
 * @return array Default translations
 */
function kt_get_default_translations() {
    return array(
        // Navigation
        'nav_home'             => 'Home',
        'nav_articles'         => 'Articles',
        'nav_tools'            => 'Tools',
        'nav_strategy_course'  => 'Strategy Course',
        'nav_strategy_courses' => 'Strategy Courses',
        'nav_newsletter'       => 'Newsletter',
        'nav_search'           => 'Search',
        'nav_login'            => 'Login',
        'nav_menu'             => 'Menu',
        'nav_about'            => 'About',
        'nav_contact'          => 'Contact',
        'nav_enroll_mvp'       => 'Start The MVP Course',
        'nav_start_mvp'        => 'Start the MVP Course',
        
        // UI Elements
        'ui_read_more'      => 'Learn more',
        'ui_view_all'       => 'View all',
        'ui_browse_all'     => 'Browse all',
        'ui_back_to'        => 'Back to',
        'ui_close'          => 'Close',
        'ui_toggle_menu'    => 'Toggle menu',
        'ui_loading'        => 'Loading...',
        'ui_read_articles'  => 'Read Articles',
        'ui_explore_tools'  => 'Explore Tools',
        
        // Page Titles
        'page_home'               => 'Home',
        'page_articles'           => 'Articles',
        'page_tools'              => 'Tools',
        'page_strategy_courses'   => 'Strategy Courses',
        'page_mvp_strategy_course' => 'The MVP Strategy Course',
        'page_latest_articles'    => 'Latest Articles',
        'page_featured_tools'     => 'Featured Tools',
        'page_step_curriculum'    => '{count}-Step Curriculum',
        'page_start_strategy_course' => 'Start The Strategy Course',
        
        // Hero Section
        'hero_subtitle_media_ai'    => 'Media, Advertising, and AI',
        'hero_title_innovate_word'  => 'Innovate',
        'hero_title_accelerate_word' => 'Accelerate',
        'hero_title_make_disciples_word' => 'Make Disciples',
        'hero_description'          => 'Accelerate your disciple making with strategic use of media, advertising, and AI tools. Kingdom.Training is a resource for disciple makers to use media to accelerate Disciple Making Movements.',
        'hero_newsletter_title'     => 'Stay Connected',
        
        // Messages
        'msg_no_articles'    => 'Articles will appear here once content is added to WordPress.',
        'msg_no_tools'       => 'Tools will appear here once content is added to WordPress.',
        'msg_no_courses'     => 'Strategy courses will appear here once content is added.',
        
        // Content
        'content_no_articles_found' => 'No articles found',
        'content_no_articles_try'   => 'Try adjusting your filters or check back later for new articles.',
        'content_no_tools_found'    => 'No tools found',
        'content_no_tools_try'      => 'Try adjusting your filters or check back later for new tools.',
        'content_digital_disciple_making' => 'Digital Disciple-Making',
        'content_heavenly_economy'  => 'The Heavenly Economy',
        'content_key_information_m2dmm' => 'Key Information About M2DMM',
        
        // Home Page
        'home_mvp_description'     => 'Learn how to use media strategically in evangelism and discipleship. A step-by-step process for reaching the unreached.',
        'home_loading_steps'       => 'Loading course steps...',
        'home_heavenly_economy'    => 'We operate within what we call the "Heavenly Economy"—a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, we reflect God\'s generous nature by offering free training, hands-on coaching, and open-source tools like Disciple.Tools.',
        'home_mission_statement'   => 'Our heart beats with passion for the unreached and least-reached peoples of the world. Every course, article, and tool serves the ultimate vision of seeing Disciple Making Movements catalyzed among people groups where the name of Jesus has never been proclaimed.',
        'home_newsletter_description' => 'Join thousands of disciple makers receiving weekly insights on using media for Kingdom impact.',
        
        // Newsletter
        'newsletter_title'         => 'Subscribe to Our Newsletter',
        'newsletter_description'   => 'Get the latest articles, tools, and resources delivered to your inbox.',
        'newsletter_email_placeholder' => 'Enter your email',
        'newsletter_subscribe'     => 'Subscribe',
        'newsletter_success'       => 'Thank you for subscribing!',
        'newsletter_error'         => 'Something went wrong. Please try again.',
        
        // Footer
        'footer_kingdom_training'  => 'Kingdom.Training',
        'footer_mission_statement' => 'Training disciple makers to use media to accelerate Disciple Making Movements. Equipping practitioners with practical strategies that bridge online engagement with face-to-face discipleship.',
        'footer_quick_links'       => 'Quick Links',
        'footer_our_vision'        => 'Our Vision',
        'footer_scripture_quote'   => 'And this gospel of the kingdom will be preached in the whole world as a testimony to all nations, and then the end will come.',
        'footer_scripture_citation' => '— Matthew 24:14',
        'footer_technology_paragraph' => 'We leverage technology to accelerate disciple making movements worldwide.',
        'footer_subscribe'         => 'Subscribe to Newsletter',
        'footer_privacy_policy'    => 'Privacy Policy',
        'footer_all_rights'        => 'All rights reserved.',
        
        // Search
        'search_placeholder_courses_tools' => 'Search strategy courses and tools...',
        'search_no_results'        => 'No results found',
        'search_start_typing'      => 'Start typing to search',
        'search_start_typing_desc' => 'Search strategy courses and tools',
        
        // Breadcrumbs
        'breadcrumb_home'          => 'Home',
        'breadcrumb_articles'      => 'Articles',
        'breadcrumb_tools'         => 'Tools',
        'breadcrumb_strategy_courses' => 'Strategy Courses',
        
        // Sidebar
        'sidebar_categories'       => 'Categories',
        'sidebar_tags'             => 'Tags',
        'sidebar_all_categories'   => 'All Categories',
        'sidebar_all_tags'         => 'All Tags',
        
        // SEO
        'seo_home_description'     => 'Kingdom.Training - Media, Advertising, and AI to accelerate disciple making movements.',
        'seo_articles_description' => 'Articles and insights on media strategies for disciple making movements.',
        'seo_tools_description'    => 'Tools and resources for effective digital discipleship and evangelism.',
        'seo_courses_description'  => 'Strategy courses for using media in evangelism and discipleship.',
        
        // Errors
        'error_404_title'          => 'Page Not Found',
        'error_404_description'    => 'The page you are looking for might have been removed or is temporarily unavailable.',
        'error_article_not_found'  => 'Article Not Found',
        'error_article_not_found_desc' => 'The article you are looking for could not be found.',
        'error_tool_not_found'     => 'Tool Not Found',
        'error_tool_not_found_desc' => 'The tool you are looking for could not be found.',
        'error_course_not_found'   => 'Course Not Found',
        'error_course_not_found_desc' => 'The strategy course you are looking for could not be found.',
        
        // Video
        'video_kingdom_training_title' => 'Kingdom.Training Introduction',
        
        // Newsletter CTA (Front Page)
        'newsletter_cta_title' => 'Get the newest insights, techniques, and strategies.',
        'newsletter_cta_description' => 'Field driven tools and articles for disciple makers.',
        
        // MVP Course Section
        'mvp_course_title' => 'The MVP: Strategy Course',
        'mvp_course_description' => 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context. Complete your plan in 6-7 hours.',
        'mvp_course_start_step' => 'Start this step >',
        
        // Archive Pages
        'archive_strategy_course_title' => 'Strategy Course',
        'archive_strategy_course_description' => 'Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program below to develop your complete M2DMM strategy.',
        'archive_articles_title' => 'Articles',
        'archive_articles_description' => 'Practical guidance, best practices, and real-world insights from the Media to Disciple Making Movements community. Learn from practitioners implementing M2DMM strategies around the world.',
        'archive_tools_title' => 'Tools',
        'archive_tools_description' => 'Essential tools and resources for Media to Disciple Making Movements work. Discover Disciple.Tools—our free, open-source disciple relationship management system—and other practical resources designed specifically for M2DMM practitioners.',
        'archive_additional_resources_title' => 'Additional Course Resources',
        'archive_additional_resources_description' => 'Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development.',
        
        // Pagination
        'pagination_previous' => '← Previous',
        'pagination_next' => 'Next →',
        'pagination_page_of' => 'Page {current} of {total}',
        
        // 404 Page
        'error_404_popular_pages' => 'Popular Pages',
        
        // Footer SEO Content
        'footer_mission_fallback' => 'Media to Disciple Making Movements training, resources, and tools for accelerating the Great Commission.',
        'footer_contact_type' => 'Customer Service',
        'footer_key_definitions' => 'Key Definitions',
        'footer_site_content_overview' => 'Site Content Overview',
        'footer_site_content_description' => 'Kingdom.Training provides comprehensive training in Media to Disciple Making Movements (M2DMM), including strategy courses, articles, and practical tools for digital evangelism and church planting among unreached peoples.',
        'footer_content_types' => 'Content Types',
        'footer_content_type_courses' => 'Strategy Courses: Step-by-step training in M2DMM methodology',
        'footer_content_type_articles' => 'Articles: In-depth resources on disciple making movements, media strategy, and digital evangelism',
        'footer_content_type_tools' => 'Tools: Practical applications and resources for implementing M2DMM strategies',
        'footer_key_topics' => 'Key Topics',
        
        // Definitions (for footer SEO)
        'definition_m2dmm' => 'A strategy that uses media and technology to accelerate disciple making movements by identifying persons of peace and establishing reproducible patterns of discipleship.',
        'definition_dmm' => 'Rapidly multiplying networks of disciples and churches that spread from community to community.',
        'definition_persons_of_peace' => 'Individuals who are open to the gospel and can serve as a bridge to their community, as described in Luke 10.',
        'definition_heavenly_economy' => 'The principle of freely giving what we have freely received, sharing resources for Kingdom impact.',
        'definition_unreached_peoples' => 'People groups without adequate access to the gospel message or indigenous Christian communities.',
        
        // Key Information Section (FAQ)
        'key_info_title' => 'Key Information About Media to Disciple Making Movements',
        'key_info_m2dmm_term' => 'What is Media to Disciple Making Movements (M2DMM)?',
        'key_info_m2dmm_definition' => 'Media to Disciple Making Movements (M2DMM) is a strategic approach that uses targeted media content to identify spiritual seekers online and connect them with face-to-face disciple-makers. The process involves three stages: (1) Media Content - targeted content reaches entire people groups through platforms like Facebook and Google Ads, (2) Digital Filtering - trained responders dialogue with seekers online, identifying persons of peace ready for face-to-face engagement, (3) Face-to-Face Discipleship - multipliers meet seekers in person, guiding them through discovery, obedience, and sharing in reproducing communities.',
        'key_info_digital_term' => 'What is digital disciple making?',
        'key_info_digital_definition' => 'Digital disciple making is the strategic use of all digital means—including social media, online advertising, AI tools, content creation, and digital communication platforms—to find seekers and bring them into relationship with Christ and his church in person. The ambition is to leverage every available digital tool and technique to identify spiritual seekers, engage them meaningfully online, and ultimately connect them with face-to-face discipleship communities where they can grow in their relationship with Jesus and participate in multiplying movements.',
        'key_info_mvp_term' => 'What is the MVP Strategy Course?',
        'key_info_mvp_definition' => 'The MVP (Minimum Viable Product) Strategy Course is a 10-step program that guides you through the core elements needed to craft a Media to Disciple Making Movements strategy for any context. The course helps you develop your complete M2DMM strategy and can be completed in 6-7 hours. It covers topics including media content creation, digital filtering strategies, face-to-face discipleship methods, and movement multiplication principles.',
        'key_info_ai_term' => 'What is AI for discipleship?',
        'key_info_ai_definition' => 'AI for discipleship empowers small teams to have a big impact by leveraging artificial intelligence tools and techniques. Kingdom.Training is bringing new techniques to accelerate small teams to use AI effectively in disciple making. These innovative approaches help teams scale their efforts, automate routine tasks, personalize engagement, and multiply their reach—enabling small groups to accomplish what previously required much larger teams.',
        'key_info_heavenly_economy_term' => 'What is the Heavenly Economy?',
        'key_info_heavenly_economy_definition' => 'The Heavenly Economy is a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, reflecting God\'s generous nature by offering free training, hands-on coaching, and open-source tools. This approach enables more people to access resources for disciple making, especially in unreached and least-reached areas.',
        'key_info_who_term' => 'Who is Kingdom.Training for?',
        'key_info_who_definition' => 'Kingdom.Training is for disciple makers, church planters, missionaries, and ministry leaders who want to use media strategically to accelerate Disciple Making Movements. We particularly focus on equipping those working with unreached and least-reached peoples - people groups where the name of Jesus has never been proclaimed or where there is no indigenous community of believers with adequate numbers and resources to evangelize their own people.',
        
        // Content Messages
        'content_no_courses_try' => 'Check back later for new strategy courses.',
        
        // Single Post Navigation
        'single_previous_step' => 'Previous Step',
        'single_next_step' => 'Next Step',
        'single_related_articles' => 'Related Articles',
        'single_related_tools' => 'Related Tools',
        
        // Privacy Policy Page
        'privacy_header_description' => 'How we collect, use, and protect your information.',
        'privacy_last_updated' => 'Last updated: December 10, 2025',
        'privacy_introduction_title' => 'Introduction',
        'privacy_introduction_paragraph_1' => 'Kingdom.Training ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website at ai.kingdom.training.',
        'privacy_introduction_paragraph_2' => 'By using our website, you consent to the data practices described in this policy. If you do not agree with the practices described in this policy, please do not use our website.',
        'privacy_information_collect_title' => 'Information We Collect',
        'privacy_personal_information_title' => 'Personal Information',
        'privacy_personal_information_intro' => 'We may collect personal information that you voluntarily provide to us when you:',
        'privacy_personal_information_item_1' => 'Subscribe to our newsletter',
        'privacy_personal_information_item_2' => 'Register for courses or training',
        'privacy_personal_information_item_3' => 'Contact us through our website',
        'privacy_personal_information_item_4' => 'Participate in surveys or feedback forms',
        'privacy_personal_information_item_5' => 'Create an account or profile',
        'privacy_personal_information_list_2_intro' => 'This information may include:',
        'privacy_personal_information_list_2_item_1' => 'Name and contact information (email address, phone number)',
        'privacy_personal_information_list_2_item_2' => 'Mailing address',
        'privacy_personal_information_list_2_item_3' => 'Organization or ministry affiliation',
        'privacy_personal_information_list_2_item_4' => 'Any other information you choose to provide',
        'privacy_automatically_collected_title' => 'Automatically Collected Information',
        'privacy_automatically_collected_intro' => 'When you visit our website, we may automatically collect certain information about your device and usage, including:',
        'privacy_automatically_collected_item_1' => 'IP address',
        'privacy_automatically_collected_item_2' => 'Browser type and version',
        'privacy_automatically_collected_item_3' => 'Operating system',
        'privacy_automatically_collected_item_4' => 'Pages visited and time spent on page',
        'privacy_automatically_collected_item_5' => 'Referring website address',
        'privacy_automatically_collected_item_6' => 'Date and time of access',
        'privacy_how_use_title' => 'How We Use Your Information',
        'privacy_how_use_intro' => 'We use the information we collect for various purposes, including:',
        'privacy_how_use_item_1' => 'To provide, maintain, and improve our services',
        'privacy_how_use_item_2' => 'To send you newsletters, updates, and communications about our training resources',
        'privacy_how_use_item_3' => 'To respond to your inquiries and provide customer support',
        'privacy_how_use_item_4' => 'To process registrations and manage your account',
        'privacy_how_use_item_5' => 'To analyze website usage and trends to improve user experience',
        'privacy_how_use_item_6' => 'To detect, prevent, and address technical issues and security threats',
        'privacy_how_use_item_7' => 'To comply with legal obligations',
        'privacy_sharing_title' => 'Information Sharing and Disclosure',
        'privacy_sharing_intro' => 'We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:',
        'privacy_sharing_item_1' => 'Service Providers: We may share information with trusted third-party service providers who assist us in operating our website, conducting our business, or serving our users, as long as they agree to keep this information confidential.',
        'privacy_sharing_item_2' => 'Legal Requirements: We may disclose your information if required by law or in response to valid requests by public authorities.',
        'privacy_sharing_item_3' => 'Protection of Rights: We may share information when we believe release is appropriate to protect our rights, property, or safety, or that of our users or others.',
        'privacy_sharing_item_4' => 'Business Transfers: In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.',
        'privacy_data_security_title' => 'Data Security',
        'privacy_data_security_text' => 'We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.',
        'privacy_cookies_title' => 'Cookies and Tracking Technologies',
        'privacy_cookies_text' => 'We use cookies and similar tracking technologies to track activity on our website and store certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.',
        'privacy_third_party_title' => 'Third-Party Links',
        'privacy_third_party_text' => 'Our website may contain links to third-party websites that are not operated by us. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.',
        'privacy_rights_title' => 'Your Privacy Rights',
        'privacy_rights_intro' => 'Depending on your location, you may have certain rights regarding your personal information, including:',
        'privacy_rights_item_1' => 'Access: The right to request access to your personal information',
        'privacy_rights_item_2' => 'Correction: The right to request correction of inaccurate or incomplete information',
        'privacy_rights_item_3' => 'Deletion: The right to request deletion of your personal information',
        'privacy_rights_item_4' => 'Objection: The right to object to processing of your personal information',
        'privacy_rights_item_5' => 'Data Portability: The right to request transfer of your data to another service',
        'privacy_rights_item_6' => 'Withdraw Consent: The right to withdraw consent where processing is based on consent',
        'privacy_rights_contact' => 'To exercise these rights, please contact us using the information provided in the "Contact Us" section below.',
        'privacy_children_title' => 'Children\'s Privacy',
        'privacy_children_text' => 'Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us so we can delete such information.',
        'privacy_changes_title' => 'Changes to This Privacy Policy',
        'privacy_changes_text' => 'We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. You are advised to review this Privacy Policy periodically for any changes.',
        'privacy_contact_title' => 'Contact Us',
        'privacy_contact_intro' => 'If you have any questions about this Privacy Policy or our data practices, please contact us:',
        'privacy_contact_org' => 'Kingdom.Training',
        'privacy_contact_email' => 'Email:',
        'privacy_contact_website' => 'Website:',
        
        // About Page
        'about_header_title' => 'About Kingdom.Training',
        'about_header_description' => 'Training disciple makers to use media strategically for Disciple Making Movements',
        'about_vision_title' => 'Our Vision',
        'about_vision_paragraph_1' => 'Kingdom.Training focuses on practical training for Media to Disciple Making Movements (M2DMM). We are field workers with a heart for the unreached and least-reached peoples of the world, and our passion is to equip disciple makers with strategic media tools that bridge online engagement with face-to-face discipleship.',
        'about_vision_paragraph_2' => 'We operate within what we call the "Heavenly Economy"—a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, we reflect God\'s generous nature by offering free training, hands-on coaching, and open-source tools.',
        'about_mission_title' => 'Our Mission',
        'about_mission_paragraph_1' => 'Like the men of Issachar who understood the times, we equip disciple makers to use media strategically—identifying spiritual seekers online and connecting them with face-to-face disciplers who help them discover, obey, and share all that Jesus taught.',
        'about_mission_paragraph_2' => 'We wonder what the Church could accomplish with technology God has given to this generation for the first time in history.',
        'about_how_works_title' => 'How it works',
        'about_step_1_title' => 'Media Content',
        'about_step_1_description' => 'Targeted content reaches entire people groups through platforms like Facebook and Google Ads. This is the wide end of the funnel, introducing masses of people to the gospel message.',
        'about_step_2_title' => 'Digital Filtering',
        'about_step_2_description' => 'Trained responders dialogue with seekers online, identifying persons of peace ready for face-to-face engagement. This filters out disinterested individuals and focuses on genuine seekers.',
        'about_step_3_title' => 'Face-to-Face Discipleship',
        'about_step_3_description' => 'Multipliers meet seekers in person, guiding them through discovery, obedience, and sharing in reproducing communities. This is where true disciple making happens.',
        
        // Hero Animation Prompts (GenMap Background)
        'hero_prompt_1' => 'Create a discipleship training video that sparks movements in unreached communities...',
        'hero_prompt_2' => 'Generate an engaging video teaching biblical principles for multiplying disciples...',
        'hero_prompt_3' => 'Produce a testimony video showing how media catalyzes church planting movements...',
        'hero_prompt_4' => 'Make an interactive video equipping believers to share the Gospel through digital tools...',
        'hero_prompt_5' => 'Create a training series on facilitating discovery Bible studies in oral cultures...',
        'hero_prompt_6' => 'Generate content showing how one faithful disciple can multiply into thousands...',
    );
}
