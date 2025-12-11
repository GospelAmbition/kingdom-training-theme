<?php
/**
 * Tool Query Class
 * 
 * Handles all tool-related database queries with Polylang language support.
 *
 * @package KingdomTraining
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KT_Query_Tools {

    /**
     * Get tools with optional filtering
     *
     * @param array $args Query arguments
     * @return array Array of tool objects
     */
    public static function get_tools( $args = array() ) {
        $defaults = array(
            'per_page'   => 10,
            'page'       => 1,
            'orderby'    => 'date',
            'order'      => 'DESC',
            'category'   => null,
            'tag'        => null,
            'lang'       => null,
            'search'     => null,
        );

        $args = wp_parse_args( $args, $defaults );

        $query_args = array(
            'post_type'      => 'tool',
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
                    'taxonomy' => 'tool_category',
                    'field'    => is_numeric( $args['category'] ) ? 'term_id' : 'slug',
                    'terms'    => $args['category'],
                ),
            );
        }

        // Tag filter
        if ( ! empty( $args['tag'] ) ) {
            $query_args['tag_id'] = $args['tag'];
        }

        // Search
        if ( ! empty( $args['search'] ) ) {
            $query_args['s'] = $args['search'];
        }

        // Language filter (Polylang)
        if ( ! empty( $args['lang'] ) && function_exists( 'pll_get_post_language' ) ) {
            $query_args['lang'] = $args['lang'];
        }

        $query = new WP_Query( $query_args );

        $tools = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $tools[] = self::format_tool( get_post() );
            }
            wp_reset_postdata();
        }

        return array(
            'items'       => $tools,
            'total'       => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => $args['page'],
        );
    }

    /**
     * Get a single tool by slug
     *
     * @param string $slug Tool slug
     * @param string|null $lang Language code
     * @return object|null Tool object or null
     */
    public static function get_tool_by_slug( $slug, $lang = null ) {
        $args = array(
            'post_type'      => 'tool',
            'name'           => $slug,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        );

        if ( ! empty( $lang ) && function_exists( 'pll_get_post_language' ) ) {
            $args['lang'] = $lang;
        }

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            $query->the_post();
            $tool = self::format_tool( get_post() );
            wp_reset_postdata();
            return $tool;
        }

        return null;
    }

    /**
     * Get related tools (same category, excluding current)
     *
     * @param int $tool_id Current tool ID
     * @param int $limit Number of related tools
     * @param string|null $lang Language code
     * @return array Array of tool objects
     */
    public static function get_related_tools( $tool_id, $limit = 3, $lang = null ) {
        // Get categories of current tool
        $categories = wp_get_post_terms( $tool_id, 'tool_category', array( 'fields' => 'ids' ) );

        $args = array(
            'post_type'      => 'tool',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'post__not_in'   => array( $tool_id ),
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'tool_category',
                    'field'    => 'term_id',
                    'terms'    => $categories,
                ),
            );
        }

        if ( ! empty( $lang ) && function_exists( 'pll_get_post_language' ) ) {
            $args['lang'] = $lang;
        }

        $query = new WP_Query( $args );

        $tools = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $tools[] = self::format_tool( get_post() );
            }
            wp_reset_postdata();
        }

        return $tools;
    }

    /**
     * Get tool categories
     *
     * @param string|null $lang Language code
     * @return array Array of category objects
     */
    public static function get_categories( $lang = null ) {
        $args = array(
            'taxonomy'   => 'tool_category',
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
     * Format tool data for templates
     *
     * @param WP_Post $post Post object
     * @return object Formatted tool object
     */
    public static function format_tool( $post ) {
        $post_id = $post->ID;

        // Get featured image
        $featured_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
        $featured_image_sizes = self::get_image_sizes( get_post_thumbnail_id( $post_id ) );

        // Get language
        $language = null;
        if ( function_exists( 'pll_get_post_language' ) ) {
            $language = pll_get_post_language( $post_id, 'slug' );
        }

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
