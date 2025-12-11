<?php
/**
 * Formatting Helper Functions
 * 
 * Provides utility functions for content formatting and processing.
 *
 * @package KingdomTraining
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Strip HTML tags from string
 *
 * @param string $text Text with HTML
 * @return string Plain text
 */
function kt_strip_html( $text ) {
    // Decode HTML entities first
    $text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
    // Strip tags
    $text = wp_strip_all_tags( $text );
    // Clean up whitespace
    $text = preg_replace( '/\s+/', ' ', $text );
    return trim( $text );
}

/**
 * Truncate text to specified length
 *
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add if truncated
 * @return string Truncated text
 */
function kt_truncate( $text, $length = 160, $suffix = '...' ) {
    $text = kt_strip_html( $text );
    
    if ( mb_strlen( $text ) <= $length ) {
        return $text;
    }
    
    // Find the last space within the limit
    $truncated = mb_substr( $text, 0, $length );
    $last_space = mb_strrpos( $truncated, ' ' );
    
    if ( $last_space !== false ) {
        $truncated = mb_substr( $truncated, 0, $last_space );
    }
    
    return $truncated . $suffix;
}

/**
 * Process content images to add width/height attributes
 * Helps prevent layout shift
 *
 * @param string $content HTML content
 * @return string Processed content
 */
function kt_process_image_widths( $content ) {
    if ( empty( $content ) ) {
        return $content;
    }
    
    // Match images without width or height
    $pattern = '/<img\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i';
    
    $content = preg_replace_callback( $pattern, function( $matches ) {
        $before_src = $matches[1];
        $src = $matches[2];
        $after_src = $matches[3];
        $full_tag = $matches[0];
        
        // Check if already has dimensions
        if ( preg_match( '/\s(width|height)=/i', $full_tag ) ) {
            return $full_tag;
        }
        
        // Try to get image dimensions from WordPress
        $attachment_id = attachment_url_to_postid( $src );
        if ( $attachment_id ) {
            $metadata = wp_get_attachment_metadata( $attachment_id );
            if ( $metadata && isset( $metadata['width'], $metadata['height'] ) ) {
                return sprintf(
                    '<img %ssrc="%s"%s width="%d" height="%d" loading="lazy">',
                    $before_src,
                    esc_url( $src ),
                    $after_src,
                    intval( $metadata['width'] ),
                    intval( $metadata['height'] )
                );
            }
        }
        
        return $full_tag;
    }, $content );
    
    return $content;
}

/**
 * Format date for display
 *
 * @param string $date Date string
 * @param string $format PHP date format (uses WordPress setting if empty)
 * @return string Formatted date
 */
function kt_format_date( $date, $format = '' ) {
    if ( empty( $format ) ) {
        $format = get_option( 'date_format' );
    }
    
    $timestamp = strtotime( $date );
    return date_i18n( $format, $timestamp );
}

/**
 * Get reading time estimate for content
 *
 * @param string $content Content to analyze
 * @param int $words_per_minute Reading speed
 * @return int Minutes to read
 */
function kt_get_reading_time( $content, $words_per_minute = 200 ) {
    $text = kt_strip_html( $content );
    $word_count = str_word_count( $text );
    $minutes = ceil( $word_count / $words_per_minute );
    return max( 1, $minutes );
}

/**
 * Get excerpt from content if excerpt is empty
 *
 * @param WP_Post|int $post Post object or ID
 * @param int $length Excerpt length
 * @return string Excerpt
 */
function kt_get_excerpt( $post, $length = 160 ) {
    $post = get_post( $post );
    
    if ( ! $post ) {
        return '';
    }
    
    // Use excerpt if available
    if ( ! empty( $post->post_excerpt ) ) {
        return kt_truncate( $post->post_excerpt, $length );
    }
    
    // Generate from content
    return kt_truncate( $post->post_content, $length );
}

/**
 * Format post data for template use
 *
 * @param WP_Post|int $post Post object or ID
 * @return object|null Formatted post data
 */
function kt_format_post_for_display( $post ) {
    $post = get_post( $post );
    
    if ( ! $post ) {
        return null;
    }
    
    $post_id = $post->ID;
    
    // Get featured image
    $featured_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
    
    // Get language
    $language = null;
    if ( function_exists( 'pll_get_post_language' ) ) {
        $language = pll_get_post_language( $post_id, 'slug' );
    }
    
    // Get author info
    $author_id = $post->post_author;
    
    return (object) array(
        'id'                 => $post_id,
        'title'              => get_the_title( $post_id ),
        'slug'               => $post->post_name,
        'content'            => apply_filters( 'the_content', $post->post_content ),
        'excerpt'            => kt_get_excerpt( $post ),
        'date'               => get_the_date( 'c', $post_id ),
        'date_formatted'     => get_the_date( '', $post_id ),
        'modified'           => get_the_modified_date( 'c', $post_id ),
        'featured_image_url' => $featured_image_url,
        'author_name'        => get_the_author_meta( 'display_name', $author_id ),
        'author_avatar'      => get_avatar_url( $author_id ),
        'language'           => $language,
        'permalink'          => get_permalink( $post_id ),
        'reading_time'       => kt_get_reading_time( $post->post_content ),
    );
}

/**
 * Sanitize CSS class names
 *
 * @param string|array $classes Class names
 * @return string Sanitized class string
 */
function kt_sanitize_classes( $classes ) {
    if ( is_array( $classes ) ) {
        $classes = implode( ' ', $classes );
    }
    
    return preg_replace( '/[^a-zA-Z0-9_\-\s]/', '', $classes );
}

/**
 * Generate CSS classes conditionally
 *
 * @param array $classes Array of class => condition pairs
 * @return string Class string
 */
function kt_classes( $classes ) {
    $result = array();
    
    foreach ( $classes as $class => $condition ) {
        if ( is_numeric( $class ) ) {
            // Simple class without condition
            $result[] = $condition;
        } elseif ( $condition ) {
            // Class with truthy condition
            $result[] = $class;
        }
    }
    
    return kt_sanitize_classes( $result );
}

/**
 * Output CSS classes attribute
 *
 * @param array $classes Array of class => condition pairs
 */
function kt_class_attr( $classes ) {
    $class_string = kt_classes( $classes );
    if ( ! empty( $class_string ) ) {
        echo ' class="' . esc_attr( $class_string ) . '"';
    }
}

/**
 * Render edit link for administrators and editors
 * Displays a link to edit the post in the WordPress admin
 *
 * @param int|WP_Post $post Post ID or object
 * @return void
 */
function kt_render_edit_link( $post = null ) {
    if ( ! $post ) {
        global $post;
    }
    
    $post = get_post( $post );
    if ( ! $post ) {
        return;
    }
    
    // Check if user can edit this post (administrators and editors)
    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        return;
    }
    
    $edit_url = get_edit_post_link( $post->ID );
    if ( ! $edit_url ) {
        return;
    }
    
    ?>
    <div class="fixed top-24 right-4 z-[60]">
        <a 
            href="<?php echo esc_url( $edit_url ); ?>" 
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
            title="<?php echo esc_attr__( 'Edit in WordPress Admin', 'kingdom-training' ); ?>"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span><?php echo esc_html__( 'Edit', 'kingdom-training' ); ?></span>
        </a>
    </div>
    <?php
}
