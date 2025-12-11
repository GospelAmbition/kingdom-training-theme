<?php
/**
 * Key Info Section Template Part
 * 
 * Used for Answer Engine Optimization (AEO)
 * 
 * @param array $args {
 *     @type string $title Section title
 *     @type array  $items Array of term/definition pairs
 * }
 * 
 * @package KingdomTraining
 */

$title = $args['title'] ?? '';
$items = $args['items'] ?? array();

if ( empty( $items ) ) {
    return;
}
?>

<section class="py-16 bg-gray-50">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto">
            <?php if ( $title ) : ?>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8 text-center">
                    <?php echo esc_html( $title ); ?>
                </h2>
            <?php endif; ?>
            
            <dl class="space-y-6">
                <?php foreach ( $items as $item ) : ?>
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <dt class="text-lg font-semibold text-primary-600 mb-2">
                            <?php echo esc_html( $item['term'] ); ?>
                        </dt>
                        <dd class="text-gray-600 leading-relaxed">
                            <?php echo esc_html( $item['definition'] ); ?>
                        </dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </div>
</section>
