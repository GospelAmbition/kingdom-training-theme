<?php
/**
 * Content Card Template Part
 * 
 * @param array $args {
 *     @type object $post Post object with title, excerpt, permalink, featured_image_url
 *     @type string $type Content type: 'articles', 'tools', 'strategy-course'
 * }
 * 
 * @package KingdomTraining
 */

$post = $args['post'] ?? null;
$type = $args['type'] ?? 'articles';

if ( ! $post ) {
    return;
}

$current_lang = kt_get_current_language();
$permalink = kt_get_language_url( '/' . $type . '/' . $post->slug );
?>

<article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
    <?php if ( ! empty( $post->featured_image_url ) ) : ?>
        <a href="<?php echo esc_url( $permalink ); ?>" class="block aspect-video overflow-hidden">
            <img 
                src="<?php echo esc_url( $post->featured_image_url ); ?>" 
                alt="<?php echo esc_attr( $post->title ); ?>"
                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        </a>
    <?php else : ?>
        <a href="<?php echo esc_url( $permalink ); ?>" class="block aspect-video bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center">
            <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </a>
    <?php endif; ?>
    
    <div class="p-6 flex flex-col flex-grow">
        <div class="flex-grow">
            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                <a href="<?php echo esc_url( $permalink ); ?>" class="hover:text-primary-500 transition-colors">
                    <?php echo esc_html( $post->title ); ?>
                </a>
            </h3>
            
            <?php if ( ! empty( $post->excerpt ) ) : ?>
                <p class="text-gray-600 mb-4 line-clamp-3">
                    <?php echo esc_html( kt_strip_html( $post->excerpt ) ); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-100">
            <a 
                href="<?php echo esc_url( $permalink ); ?>" 
                class="text-primary-500 hover:text-primary-600 font-medium text-sm"
            >
                <?php kt_e( 'ui_read_more' ); ?> →
            </a>
        </div>
    </div>
</article>
