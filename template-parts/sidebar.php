<?php
/**
 * Sidebar Template Part
 * 
 * @param array $args {
 *     @type array  $categories   Array of category objects
 *     @type array  $tags         Array of tag objects
 *     @type string $base_path    Base URL path for filters
 *     @type string $current_cat  Current category filter
 *     @type int    $current_tag  Current tag filter
 * }
 * 
 * @package KingdomTraining
 */

$categories   = $args['categories'] ?? array();
$tags         = $args['tags'] ?? array();
$base_path    = $args['base_path'] ?? '';
$current_cat  = $args['current_cat'] ?? '';
$current_tag  = $args['current_tag'] ?? 0;
?>

<aside class="space-y-8">
    <!-- Categories -->
    <?php if ( ! empty( $categories ) ) : ?>
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <?php kt_e( 'sidebar_categories' ); ?>
            </h3>
            <ul class="space-y-2">
                <li>
                    <a 
                        href="<?php echo esc_url( $base_path ); ?>" 
                        class="flex items-center justify-between py-2 px-3 rounded-lg transition-colors <?php echo empty( $current_cat ) ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50'; ?>"
                    >
                        <span><?php kt_e( 'sidebar_all_categories' ); ?></span>
                    </a>
                </li>
                <?php foreach ( $categories as $cat ) : ?>
                    <li>
                        <a 
                            href="<?php echo esc_url( add_query_arg( 'category', $cat['slug'], $base_path ) ); ?>" 
                            class="flex items-center justify-between py-2 px-3 rounded-lg transition-colors <?php echo $current_cat === $cat['slug'] ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50'; ?>"
                        >
                            <span><?php echo esc_html( $cat['name'] ); ?></span>
                            <span class="text-sm text-gray-400"><?php echo esc_html( $cat['count'] ); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Tags -->
    <?php if ( ! empty( $tags ) ) : ?>
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <?php kt_e( 'sidebar_tags' ); ?>
            </h3>
            <div class="flex flex-wrap gap-2">
                <?php foreach ( $tags as $tag ) : ?>
                    <?php if ( ! is_wp_error( $tag ) ) : ?>
                        <a 
                            href="<?php echo esc_url( add_query_arg( 'tag', $tag->term_id, $base_path ) ); ?>" 
                            class="px-3 py-1 text-sm rounded-full transition-colors <?php echo $current_tag === $tag->term_id ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>"
                        >
                            <?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Newsletter CTA -->
    <?php get_template_part( 'template-parts/newsletter-cta', null, array(
        'variant'     => 'compact',
        'title'       => kt_t( 'hero_newsletter_title' ),
        'description' => kt_t( 'newsletter_description' ),
    ) ); ?>
</aside>
