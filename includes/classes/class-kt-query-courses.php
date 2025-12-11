<?php
/**
 * Strategy Course Query Class
 * 
 * Handles all strategy course database queries with Polylang language support.
 *
 * @package KingdomTraining
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KT_Query_Courses {

    /**
     * Get courses with optional filtering
     *
     * @param array $args Query arguments
     * @return array Array of course objects
     */
    public static function get_courses( $args = array() ) {
        $defaults = array(
            'per_page'     => 10,
            'page'         => 1,
            'orderby'      => 'date',
            'order'        => 'DESC',
            'category'     => null,
            'lang'         => null,
            'search'       => null,
            'exclude_steps' => false,
        );

        $args = wp_parse_args( $args, $defaults );

        $query_args = array(
            'post_type'      => 'strategy_course',
            'posts_per_page' => $args['per_page'],
            'paged'          => $args['page'],
            'orderby'        => $args['orderby'],
            'order'          => $args['order'],
            'post_status'    => 'publish',
        );

        // Category filter
        if ( ! empty( $args['category'] ) ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'strategy_course_category',
                    'field'    => is_numeric( $args['category'] ) ? 'term_id' : 'slug',
                    'terms'    => $args['category'],
                ),
            );
        }

        // Search
        if ( ! empty( $args['search'] ) ) {
            $query_args['s'] = $args['search'];
        }

        // Exclude courses with step numbers
        if ( ! empty( $args['exclude_steps'] ) ) {
            $query_args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key'     => 'steps',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => 'steps',
                    'value'   => '',
                    'compare' => '=',
                ),
            );
        }

        // Language filter (Polylang)
        if ( function_exists( 'pll_get_post_language' ) ) {
            if ( ! empty( $args['lang'] ) ) {
                $query_args['lang'] = $args['lang'];
            } elseif ( function_exists( 'pll_default_language' ) ) {
                // Use default language if none specified
                $query_args['lang'] = pll_default_language();
            }
        }

        $query = new WP_Query( $query_args );

        $courses = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $courses[] = self::format_course( get_post() );
            }
            wp_reset_postdata();
        }

        return array(
            'items'       => $courses,
            'total'       => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => $args['page'],
        );
    }

    /**
     * Get ordered course steps (MVP Curriculum)
     * Courses with 'steps' meta field ordered by step number
     *
     * @param string|null $lang Language code
     * @return array Array of course objects ordered by step
     */
    public static function get_ordered_steps( $lang = null ) {
        $args = array(
            'post_type'      => 'strategy_course',
            'posts_per_page' => 20,
            'post_status'    => 'publish',
            'meta_key'       => 'steps',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'     => 'steps',
                    'value'   => array( 1, 20 ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                ),
            ),
        );

        // Language filter (Polylang)
        if ( ! empty( $lang ) && function_exists( 'pll_get_post_language' ) ) {
            $args['lang'] = $lang;
        } elseif ( function_exists( 'pll_default_language' ) ) {
            // Use default language if none specified
            $args['lang'] = pll_default_language();
        }

        $query = new WP_Query( $args );

        $courses = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $courses[] = self::format_course( get_post() );
            }
            wp_reset_postdata();
        }

        return $courses;
    }

    /**
     * Get a single course by slug
     *
     * @param string $slug Course slug
     * @param string|null $lang Language code
     * @return object|null Course object or null
     */
    public static function get_course_by_slug( $slug, $lang = null ) {
        $args = array(
            'post_type'      => 'strategy_course',
            'name'           => $slug,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        );

        // Language filter (Polylang)
        if ( function_exists( 'pll_get_post_language' ) ) {
            if ( ! empty( $lang ) ) {
                $args['lang'] = $lang;
            } elseif ( function_exists( 'pll_default_language' ) ) {
                // Use default language if none specified
                $args['lang'] = pll_default_language();
            }
        }

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            $query->the_post();
            $course = self::format_course( get_post() );
            wp_reset_postdata();
            return $course;
        }

        return null;
    }

    /**
     * Get next and previous course steps
     *
     * @param int $current_step Current step number
     * @param string|null $lang Language code
     * @return array Array with 'prev' and 'next' course objects
     */
    public static function get_adjacent_steps( $current_step, $lang = null ) {
        $all_steps = self::get_ordered_steps( $lang );
        
        $prev = null;
        $next = null;
        
        foreach ( $all_steps as $index => $course ) {
            if ( $course->steps === $current_step ) {
                if ( $index > 0 ) {
                    $prev = $all_steps[ $index - 1 ];
                }
                if ( $index < count( $all_steps ) - 1 ) {
                    $next = $all_steps[ $index + 1 ];
                }
                break;
            }
        }

        return array(
            'prev' => $prev,
            'next' => $next,
        );
    }

    /**
     * Get additional resources (courses without numbered steps)
     *
     * @param string|null $lang Language code
     * @param int $limit Number of courses to return
     * @return array Array of course objects
     */
    public static function get_additional_resources( $lang = null, $limit = 10 ) {
        $args = array(
            'post_type'      => 'strategy_course',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                    'key'     => 'steps',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => 'steps',
                    'value'   => '',
                    'compare' => '=',
                ),
            ),
        );

        // Language filter (Polylang)
        if ( function_exists( 'pll_get_post_language' ) ) {
            if ( ! empty( $lang ) ) {
                $args['lang'] = $lang;
            } elseif ( function_exists( 'pll_default_language' ) ) {
                // Use default language if none specified
                $args['lang'] = pll_default_language();
            }
        }

        $query = new WP_Query( $args );

        $courses = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $courses[] = self::format_course( get_post() );
            }
            wp_reset_postdata();
        }

        return $courses;
    }

    /**
     * Get course categories
     *
     * @param string|null $lang Language code
     * @return array Array of category objects
     */
    public static function get_categories( $lang = null ) {
        $args = array(
            'taxonomy'   => 'strategy_course_category',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        );

        if ( ! empty( $lang ) && function_exists( 'pll_get_term_language' ) ) {
            $args['lang'] = $lang;
        }

        $terms = get_terms( $args );

        if ( is_wp_error( $terms ) ) {
            return array();
        }

        return array_map( function( $term ) {
            return array(
                'id'    => $term->term_id,
                'name'  => $term->name,
                'slug'  => $term->slug,
                'count' => $term->count,
            );
        }, $terms );
    }

    /**
     * Format course data for templates
     *
     * @param WP_Post $post Post object
     * @return object Formatted course object
     */
    public static function format_course( $post ) {
        $post_id = $post->ID;

        // Get featured image
        $featured_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
        $featured_image_sizes = self::get_image_sizes( get_post_thumbnail_id( $post_id ) );

        // Get language
        $language = null;
        if ( function_exists( 'pll_get_post_language' ) ) {
            $language = pll_get_post_language( $post_id, 'slug' );
        }

        // Get steps meta
        $steps = get_post_meta( $post_id, 'steps', true );
        $steps = $steps ? intval( $steps ) : null;

        // Get author info
        $author_id = $post->post_author;

        return (object) array(
            'id'                   => $post_id,
            'title'                => get_the_title( $post_id ),
            'slug'                 => $post->post_name,
            'content'              => apply_filters( 'the_content', $post->post_content ),
            'excerpt'              => get_the_excerpt( $post_id ),
            'date'                 => get_the_date( 'c', $post_id ),
            'date_formatted'       => get_the_date( '', $post_id ),
            'modified'             => get_the_modified_date( 'c', $post_id ),
            'featured_image_url'   => $featured_image_url,
            'featured_image_sizes' => $featured_image_sizes,
            'author_name'          => get_the_author_meta( 'display_name', $author_id ),
            'author_avatar'        => get_avatar_url( $author_id ),
            'author_bio'           => get_the_author_meta( 'description', $author_id ),
            'language'             => $language,
            'steps'                => $steps,
            'permalink'            => get_permalink( $post_id ),
        );
    }

    /**
     * Get image sizes for a given attachment
     *
     * @param int $attachment_id Attachment ID
     * @return array|null Array of image sizes or null
     */
    private static function get_image_sizes( $attachment_id ) {
        if ( ! $attachment_id ) {
            return null;
        }

        $sizes = array();
        $image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' );

        foreach ( $image_sizes as $size ) {
            $img = wp_get_attachment_image_src( $attachment_id, $size );
            if ( $img ) {
                $sizes[ $size ] = array(
                    'url'    => $img[0],
                    'width'  => $img[1],
                    'height' => $img[2],
                );
            }
        }

        return ! empty( $sizes ) ? $sizes : null;
    }
}
