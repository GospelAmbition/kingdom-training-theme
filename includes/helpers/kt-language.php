<?php
/**
 * Language Helper Functions
 * 
 * Provides wrapper functions for Polylang language handling.
 *
 * @package KingdomTraining
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get current language code
 *
 * @return string|null Language code or null
 */
function kt_get_current_language() {
    if ( function_exists( 'pll_current_language' ) ) {
        return pll_current_language( 'slug' );
    }
    return null;
}

/**
 * Get default language code
 *
 * @return string|null Default language code or null
 */
function kt_get_default_language() {
    if ( function_exists( 'pll_default_language' ) ) {
        return pll_default_language( 'slug' );
    }
    return null;
}

/**
 * Check if current language is RTL
 *
 * @return bool True if RTL, false otherwise
 */
function kt_is_rtl() {
    if ( function_exists( 'pll_current_language' ) ) {
        $current_lang = pll_current_language( 'slug' );
        $rtl_languages = array( 'ar', 'he', 'fa', 'ur', 'yi', 'ps', 'sd' );
        return in_array( $current_lang, $rtl_languages, true );
    }
    return is_rtl();
}

/**
 * Get available languages
 *
 * @return array Array of language data
 */
function kt_get_languages() {
    if ( function_exists( 'pll_the_languages' ) ) {
        return pll_the_languages( array(
            'raw'                => true,
            'hide_if_no_translation' => false,
        ) );
    }
    return array();
}

/**
 * Build language-aware URL
 *
 * @param string $path URL path (e.g., '/articles')
 * @param string|null $lang Language code (null uses current)
 * @return string Full URL with language prefix if needed
 */
function kt_get_language_url( $path, $lang = null ) {
    $current_lang = $lang ?: kt_get_current_language();
    $default_lang = kt_get_default_language();
    
    // Clean the path
    $path = '/' . ltrim( $path, '/' );
    
    // If using default language, don't add prefix
    if ( $current_lang === $default_lang || empty( $current_lang ) ) {
        return home_url( $path );
    }
    
    // Add language prefix
    return home_url( '/' . $current_lang . $path );
}

/**
 * Get language-aware permalink for a post
 *
 * @param int|WP_Post $post Post ID or object
 * @param string|null $lang Target language code
 * @return string Permalink
 */
function kt_get_post_permalink( $post, $lang = null ) {
    $post_id = is_object( $post ) ? $post->ID : $post;
    
    // If specific language requested and Polylang is active
    if ( $lang && function_exists( 'pll_get_post' ) ) {
        $translated_id = pll_get_post( $post_id, $lang );
        if ( $translated_id ) {
            return get_permalink( $translated_id );
        }
    }
    
    return get_permalink( $post_id );
}

/**
 * Get translations of a post
 *
 * @param int|WP_Post $post Post ID or object
 * @return array Array of translations with language code as key
 */
function kt_get_post_translations( $post ) {
    $post_id = is_object( $post ) ? $post->ID : $post;
    
    if ( function_exists( 'pll_get_post_translations' ) ) {
        $translations = pll_get_post_translations( $post_id );
        $result = array();
        
        foreach ( $translations as $lang => $id ) {
            $result[ $lang ] = array(
                'id'        => $id,
                'permalink' => get_permalink( $id ),
                'title'     => get_the_title( $id ),
            );
        }
        
        return $result;
    }
    
    return array();
}

/**
 * Parse language from URL path
 *
 * @param string|null $path URL path (uses current URL if null)
 * @return array Array with 'lang' and 'path_without_lang' keys
 */
function kt_parse_language_from_path( $path = null ) {
    if ( $path === null ) {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
    }
    
    // Remove query string
    $path = strtok( $path, '?' );
    
    // Get available languages
    $available_langs = array();
    if ( function_exists( 'pll_languages_list' ) ) {
        $available_langs = pll_languages_list( array( 'fields' => 'slug' ) );
    }
    
    // Parse path
    $parts = explode( '/', trim( $path, '/' ) );
    
    if ( ! empty( $parts[0] ) && in_array( $parts[0], $available_langs, true ) ) {
        $lang = $parts[0];
        array_shift( $parts );
        $path_without_lang = '/' . implode( '/', $parts );
        
        return array(
            'lang'              => $lang,
            'path_without_lang' => $path_without_lang ?: '/',
        );
    }
    
    return array(
        'lang'              => null,
        'path_without_lang' => $path,
    );
}

/**
 * Get the dir attribute value based on current language
 *
 * @return string 'rtl' or 'ltr'
 */
function kt_get_dir_attribute() {
    return kt_is_rtl() ? 'rtl' : 'ltr';
}

/**
 * Output language selector HTML
 *
 * @param array $args Display arguments
 */
function kt_language_selector( $args = array() ) {
    $defaults = array(
        'show_flags'  => true,
        'show_names'  => true,
        'dropdown'    => false,
        'class'       => 'kt-language-selector',
    );
    
    $args = wp_parse_args( $args, $defaults );
    $languages = kt_get_languages();
    
    if ( empty( $languages ) ) {
        return;
    }
    
    $current_lang = kt_get_current_language();
    
    echo '<div class="' . esc_attr( $args['class'] ) . '">';
    
    if ( $args['dropdown'] ) {
        // Get language code for display (uppercase initials)
        $current_lang_data = null;
        foreach ( $languages as $lang ) {
            if ( $lang['slug'] === $current_lang ) {
                $current_lang_data = $lang;
                break;
            }
        }
        $display_text = $current_lang_data ? strtoupper( substr( $current_lang_data['slug'], 0, 2 ) ) : 'EN';
        
        echo '<div class="kt-language-dropdown-wrapper relative" data-display="' . esc_attr( $display_text ) . '">';
        echo '<select class="kt-language-dropdown" onchange="window.location.href=this.value">';
        foreach ( $languages as $lang ) {
            $selected = ( $lang['slug'] === $current_lang ) ? 'selected' : '';
            printf(
                '<option value="%s" %s>%s</option>',
                esc_url( $lang['url'] ),
                $selected,
                esc_html( $lang['name'] )
            );
        }
        echo '</select>';
        echo '</div>';
    } else {
        echo '<ul class="kt-language-list">';
        foreach ( $languages as $lang ) {
            $active_class = ( $lang['slug'] === $current_lang ) ? 'active' : '';
            printf(
                '<li class="%s"><a href="%s" lang="%s" hreflang="%s">',
                esc_attr( $active_class ),
                esc_url( $lang['url'] ),
                esc_attr( $lang['slug'] ),
                esc_attr( $lang['slug'] )
            );
            
            if ( $args['show_flags'] && ! empty( $lang['flag'] ) ) {
                echo '<span class="flag">' . $lang['flag'] . '</span>';
            }
            
            if ( $args['show_names'] ) {
                echo '<span class="name">' . esc_html( $lang['name'] ) . '</span>';
            }
            
            echo '</a></li>';
        }
        echo '</ul>';
    }
    
    echo '</div>';
}
