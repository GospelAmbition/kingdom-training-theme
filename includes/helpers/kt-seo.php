<?php
/**
 * SEO Helper Functions
 * 
 * Provides functions for SEO meta tags and structured data.
 *
 * @package KingdomTraining
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render SEO meta tags
 *
 * @param array $args SEO parameters
 */
function kt_render_seo_meta( $args = array() ) {
    $defaults = array(
        'title'          => get_bloginfo( 'name' ),
        'description'    => get_bloginfo( 'description' ),
        'keywords'       => '',
        'image'          => '',
        'url'            => '',
        'type'           => 'website',
        'author'         => '',
        'published_time' => '',
        'modified_time'  => '',
        'robots'         => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
        'noindex'        => false,
        'nofollow'       => false,
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    // Get the full URL
    $url = $args['url'] ?: ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Site name
    $site_name = get_bloginfo( 'name' );
    
    // Default image if not provided
    if ( empty( $args['image'] ) ) {
        $args['image'] = get_template_directory_uri() . '/dist/kt-logo-header.webp';
    }
    
    // Build robots meta
    $robots_content = array();
    if ( $args['noindex'] ) {
        $robots_content[] = 'noindex';
    } else {
        $robots_content[] = 'index';
    }
    if ( $args['nofollow'] ) {
        $robots_content[] = 'nofollow';
    } else {
        $robots_content[] = 'follow';
    }
    $robots_content[] = 'max-image-preview:large';
    $robots_content[] = 'max-snippet:-1';
    $robots_content[] = 'max-video-preview:-1';
    
    // Output meta tags
    ?>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo esc_attr( $args['description'] ); ?>">
    <?php if ( ! empty( $args['keywords'] ) ) : ?>
    <meta name="keywords" content="<?php echo esc_attr( $args['keywords'] ); ?>">
    <?php endif; ?>
    <?php if ( ! empty( $args['author'] ) ) : ?>
    <meta name="author" content="<?php echo esc_attr( $args['author'] ); ?>">
    <?php endif; ?>
    <meta name="robots" content="<?php echo esc_attr( implode( ', ', $robots_content ) ); ?>">
    <meta name="googlebot" content="<?php echo esc_attr( implode( ', ', $robots_content ) ); ?>">
    <meta name="bingbot" content="<?php echo esc_attr( implode( ', ', $robots_content ) ); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo esc_attr( $args['type'] ); ?>">
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>">
    <meta property="og:title" content="<?php echo esc_attr( $args['title'] ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $args['description'] ); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>">
    <meta property="og:locale" content="<?php echo esc_attr( kt_get_og_locale() ); ?>">
    <?php if ( ! empty( $args['image'] ) ) : ?>
    <meta property="og:image" content="<?php echo esc_url( $args['image'] ); ?>">
    <meta property="og:image:secure_url" content="<?php echo esc_url( $args['image'] ); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/webp">
    <?php endif; ?>
    <?php if ( $args['type'] === 'article' && ! empty( $args['published_time'] ) ) : ?>
    <meta property="article:published_time" content="<?php echo esc_attr( $args['published_time'] ); ?>">
    <?php endif; ?>
    <?php if ( $args['type'] === 'article' && ! empty( $args['modified_time'] ) ) : ?>
    <meta property="article:modified_time" content="<?php echo esc_attr( $args['modified_time'] ); ?>">
    <?php endif; ?>
    <?php if ( $args['type'] === 'article' && ! empty( $args['author'] ) ) : ?>
    <meta property="article:author" content="<?php echo esc_attr( $args['author'] ); ?>">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo esc_url( $url ); ?>">
    <meta name="twitter:title" content="<?php echo esc_attr( $args['title'] ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( $args['description'] ); ?>">
    <?php if ( ! empty( $args['image'] ) ) : ?>
    <meta name="twitter:image" content="<?php echo esc_url( $args['image'] ); ?>">
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo esc_url( $url ); ?>">
    
    <?php
    // Output hreflang tags for multilingual support
    kt_render_hreflang_tags( $url );
}

/**
 * Get Open Graph locale code
 *
 * @return string Locale code
 */
function kt_get_og_locale() {
    $current_lang = kt_get_current_language();
    if ( ! $current_lang ) {
        return 'en_US';
    }
    
    // Map common language codes to Open Graph locale codes
    $locale_map = array(
        'en' => 'en_US',
        'es' => 'es_ES',
        'fr' => 'fr_FR',
        'de' => 'de_DE',
        'pt' => 'pt_PT',
        'it' => 'it_IT',
        'nl' => 'nl_NL',
        'pl' => 'pl_PL',
        'ru' => 'ru_RU',
        'zh' => 'zh_CN',
        'ja' => 'ja_JP',
        'ko' => 'ko_KR',
        'ar' => 'ar_AR',
        'hi' => 'hi_IN',
    );
    
    $base_lang = substr( $current_lang, 0, 2 );
    return isset( $locale_map[ $base_lang ] ) ? $locale_map[ $base_lang ] : $current_lang . '_' . strtoupper( $current_lang );
}

/**
 * Render hreflang tags for multilingual SEO
 *
 * @param string $current_url Current page URL
 */
function kt_render_hreflang_tags( $current_url = '' ) {
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return;
    }
    
    $languages = kt_get_languages();
    if ( empty( $languages ) || count( $languages ) <= 1 ) {
        return;
    }
    
    // Get current post/page ID if available
    $post_id = get_queried_object_id();
    $current_lang = kt_get_current_language();
    
    foreach ( $languages as $lang ) {
        $lang_code = $lang['slug'];
        $lang_url = $current_url;
        
        // If we have a post ID, try to get the translated version
        if ( $post_id && function_exists( 'pll_get_post' ) ) {
            $translated_id = pll_get_post( $post_id, $lang_code );
            if ( $translated_id ) {
                $lang_url = get_permalink( $translated_id );
            } else {
                // Use the language URL from Polylang
                $lang_url = $lang['url'];
            }
        } else {
            // For archive pages or pages without translations, use Polylang's URL
            $lang_url = $lang['url'];
        }
        
        // Output hreflang tag
        printf(
            '<link rel="alternate" hreflang="%s" href="%s">' . "\n",
            esc_attr( $lang_code ),
            esc_url( $lang_url )
        );
    }
    
    // Add x-default hreflang pointing to default language
    $default_lang = kt_get_default_language();
    if ( $default_lang ) {
        foreach ( $languages as $lang ) {
            if ( $lang['slug'] === $default_lang ) {
                printf(
                    '<link rel="alternate" hreflang="x-default" href="%s">' . "\n",
                    esc_url( $lang['url'] )
                );
                break;
            }
        }
    }
}

/**
 * Render JSON-LD structured data
 *
 * @param array $data Structured data configuration
 */
function kt_render_structured_data( $data = array() ) {
    $output = array();
    
    // Organization schema
    if ( isset( $data['organization'] ) ) {
        $org = $data['organization'];
        $org_schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $org['name'] ?? get_bloginfo( 'name' ),
            'url'      => $org['url'] ?? home_url(),
            'description' => $org['description'] ?? get_bloginfo( 'description' ),
        );
        
        // Handle logo (can be string or ImageObject array)
        if ( isset( $org['logo'] ) ) {
            if ( is_array( $org['logo'] ) && isset( $org['logo']['@type'] ) ) {
                $org_schema['logo'] = $org['logo'];
            } else {
                $org_schema['logo'] = $org['logo'];
            }
        }
        
        // Add sameAs if provided
        if ( ! empty( $org['sameAs'] ) && is_array( $org['sameAs'] ) ) {
            $org_schema['sameAs'] = $org['sameAs'];
        }
        
        // Add contactPoint if provided
        if ( ! empty( $org['contactPoint'] ) && is_array( $org['contactPoint'] ) ) {
            $org_schema['contactPoint'] = $org['contactPoint'];
        }
        
        $output[] = $org_schema;
    }
    
    // Website schema
    if ( isset( $data['website'] ) ) {
        $site = $data['website'];
        $site_schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => $site['name'] ?? get_bloginfo( 'name' ),
            'url'      => $site['url'] ?? home_url(),
            'description' => $site['description'] ?? get_bloginfo( 'description' ),
        );
        
        // Add inLanguage if provided
        if ( ! empty( $site['inLanguage'] ) ) {
            $site_schema['inLanguage'] = $site['inLanguage'];
        }
        
        // Add potentialAction (SearchAction)
        if ( ! empty( $site['potentialAction'] ) && is_array( $site['potentialAction'] ) ) {
            $site_schema['potentialAction'] = $site['potentialAction'];
        } else {
            // Default search action
            $site_schema['potentialAction'] = array(
                '@type'       => 'SearchAction',
                'target'      => array(
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => home_url( '/?s={search_term_string}' ),
                ),
                'query-input' => 'required name=search_term_string',
            );
        }
        
        $output[] = $site_schema;
    }
    
    // Article schema
    if ( isset( $data['article'] ) ) {
        $article = $data['article'];
        $output[] = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $article['title'] ?? '',
            'description'   => $article['description'] ?? '',
            'image'         => $article['image'] ?? '',
            'datePublished' => $article['datePublished'] ?? '',
            'dateModified'  => $article['dateModified'] ?? '',
            'author'        => array(
                '@type' => 'Person',
                'name'  => $article['author'] ?? '',
            ),
            'publisher'     => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
                'logo'  => array(
                    '@type' => 'ImageObject',
                    'url'   => $article['publisherLogo'] ?? '',
                ),
            ),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id'   => $article['url'] ?? '',
            ),
        );
    }
    
    // Course schema
    if ( isset( $data['course'] ) ) {
        $course = $data['course'];
        $output[] = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Course',
            'name'        => $course['name'] ?? '',
            'description' => $course['description'] ?? '',
            'provider'    => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
                'url'   => home_url(),
            ),
        );
    }
    
    // FAQ schema
    if ( isset( $data['faq'] ) && ! empty( $data['faq']['questions'] ) ) {
        $faq_items = array();
        foreach ( $data['faq']['questions'] as $item ) {
            $faq_items[] = array(
                '@type'          => 'Question',
                'name'           => $item['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $item['answer'],
                ),
            );
        }
        
        $output[] = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $faq_items,
        );
    }
    
    // Breadcrumb schema
    if ( isset( $data['breadcrumb'] ) && ! empty( $data['breadcrumb']['items'] ) ) {
        $breadcrumb_items = array();
        foreach ( $data['breadcrumb']['items'] as $index => $item ) {
            $breadcrumb_items[] = array(
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $item['name'],
                'item'     => $item['url'],
            );
        }
        
        $output[] = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $breadcrumb_items,
        );
    }
    
    // SoftwareApplication schema (for tools)
    if ( isset( $data['software'] ) ) {
        $software = $data['software'];
        $output[] = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'SoftwareApplication',
            'name'        => $software['name'] ?? '',
            'description' => $software['description'] ?? '',
            'applicationCategory' => $software['category'] ?? 'WebApplication',
            'operatingSystem' => $software['operatingSystem'] ?? 'Any',
            'offers'      => array(
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
            ),
            'provider'    => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
                'url'   => home_url(),
            ),
        );
    }
    
    // EducationalOrganization schema
    if ( isset( $data['educationalOrganization'] ) ) {
        $edu = $data['educationalOrganization'];
        $output[] = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'EducationalOrganization',
            'name'        => $edu['name'] ?? get_bloginfo( 'name' ),
            'url'         => $edu['url'] ?? home_url(),
            'description' => $edu['description'] ?? get_bloginfo( 'description' ),
        );
    }
    
    // CollectionPage schema (for archives)
    if ( isset( $data['collectionPage'] ) ) {
        $collection = $data['collectionPage'];
        $output[] = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => $collection['name'] ?? '',
            'description' => $collection['description'] ?? '',
            'url'         => $collection['url'] ?? '',
        );
    }
    
    // Output the structured data
    if ( ! empty( $output ) ) {
        foreach ( $output as $schema ) {
            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
            echo "\n" . '</script>' . "\n";
        }
    }
}

/**
 * Generate breadcrumb data for current page
 *
 * @return array Breadcrumb items
 */
function kt_get_breadcrumb_data() {
    $items = array(
        array(
            'name' => kt_t( 'breadcrumb_home' ),
            'url'  => home_url( '/' ),
        ),
    );
    
    if ( is_singular( 'article' ) ) {
        $items[] = array(
            'name' => kt_t( 'breadcrumb_articles' ),
            'url'  => kt_get_language_url( '/articles' ),
        );
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
    } elseif ( is_singular( 'tool' ) ) {
        $items[] = array(
            'name' => kt_t( 'breadcrumb_tools' ),
            'url'  => kt_get_language_url( '/tools' ),
        );
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
    } elseif ( is_singular( 'strategy_course' ) ) {
        $items[] = array(
            'name' => kt_t( 'breadcrumb_strategy_courses' ),
            'url'  => kt_get_language_url( '/strategy-course' ),
        );
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
    } elseif ( is_post_type_archive( 'article' ) ) {
        $items[] = array(
            'name' => kt_t( 'breadcrumb_articles' ),
            'url'  => kt_get_language_url( '/articles' ),
        );
    } elseif ( is_post_type_archive( 'tool' ) ) {
        $items[] = array(
            'name' => kt_t( 'breadcrumb_tools' ),
            'url'  => kt_get_language_url( '/tools' ),
        );
    } elseif ( is_post_type_archive( 'strategy_course' ) ) {
        $items[] = array(
            'name' => kt_t( 'breadcrumb_strategy_courses' ),
            'url'  => kt_get_language_url( '/strategy-course' ),
        );
    } elseif ( is_page() ) {
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
    }
    
    return $items;
}

/**
 * Render breadcrumb HTML
 *
 * @param array $args Display arguments
 */
function kt_render_breadcrumbs( $args = array() ) {
    $defaults = array(
        'separator' => ' / ',
        'class'     => 'kt-breadcrumbs',
    );
    
    $args = wp_parse_args( $args, $defaults );
    $items = kt_get_breadcrumb_data();
    
    if ( count( $items ) <= 1 ) {
        return;
    }
    
    echo '<nav class="' . esc_attr( $args['class'] ) . '" aria-label="Breadcrumb">';
    echo '<ol class="flex items-center text-sm text-gray-500">';
    
    $last_index = count( $items ) - 1;
    foreach ( $items as $index => $item ) {
        $is_last = ( $index === $last_index );
        
        echo '<li class="flex items-center">';
        
        if ( $is_last ) {
            echo '<span class="text-gray-700" aria-current="page">' . esc_html( $item['name'] ) . '</span>';
        } else {
            echo '<a href="' . esc_url( $item['url'] ) . '" class="hover:text-primary-500">' . esc_html( $item['name'] ) . '</a>';
            echo '<span class="mx-2">' . esc_html( $args['separator'] ) . '</span>';
        }
        
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}

/**
 * Add default SEO meta tags to wp_head
 * This ensures all pages have basic SEO even if kt_render_seo_meta() isn't called
 */
function kt_add_default_seo_meta() {
    // Only add if we're not in admin and no SEO meta has been explicitly set
    if ( is_admin() ) {
        return;
    }
    
    // Check if we're on a page that already has SEO meta (they call kt_render_seo_meta)
    // We'll add basic defaults that can be overridden
    
    $title = wp_get_document_title();
    $description = get_bloginfo( 'description' );
    $url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $site_name = get_bloginfo( 'name' );
    $image = get_template_directory_uri() . '/dist/kt-logo-header.webp';
    
    // Get page-specific description if available
    if ( is_singular() ) {
        $post = get_queried_object();
        if ( ! empty( $post->post_excerpt ) ) {
            $description = wp_strip_all_tags( $post->post_excerpt );
        } elseif ( ! empty( $post->post_content ) ) {
            $description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );
        }
        
        // Try to get featured image
        $featured_image = get_the_post_thumbnail_url( $post->ID, 'large' );
        if ( $featured_image ) {
            $image = $featured_image;
        }
    }
    
    // Ensure description is not empty
    if ( empty( $description ) ) {
        $description = 'Kingdom.Training - Media to Disciple Making Movements training, resources, and tools for accelerating the Great Commission.';
    }
    
    // Truncate description to 160 characters
    $description = wp_trim_words( $description, 25, '' );
    if ( strlen( $description ) > 160 ) {
        $description = substr( $description, 0, 157 ) . '...';
    }
    
    // Output basic meta tags
    echo '<!-- Default SEO Meta Tags -->' . "\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";
    
    // Open Graph
    echo '<meta property="og:type" content="' . esc_attr( is_singular() ? 'article' : 'website' ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr( kt_get_og_locale() ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    echo '<meta property="og:image:secure_url" content="' . esc_url( $image ) . '">' . "\n";
    
    // Twitter
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    
    // Canonical
    echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
    
    // Hreflang tags
    kt_render_hreflang_tags( $url );
}
add_action( 'wp_head', 'kt_add_default_seo_meta', 1 );
