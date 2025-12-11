<?php
/**
 * Newsletter CTA Template Part
 * 
 * @param array $args {
 *     @type string  $variant          Display variant: 'banner', 'compact', 'full'
 *     @type string  $title            CTA title
 *     @type string  $description      CTA description
 *     @type bool    $show_email_input Show email input field
 *     @type bool    $white_background Use white background
 * }
 * 
 * @package KingdomTraining
 */

$variant          = $args['variant'] ?? 'banner';
$title            = $args['title'] ?? kt_t( 'newsletter_title' );
$description      = $args['description'] ?? kt_t( 'newsletter_description' );
$show_email_input = $args['show_email_input'] ?? true;
$white_background = $args['white_background'] ?? false;

$bg_class = $white_background ? 'bg-white' : 'bg-primary-50';
?>

<?php if ( $variant === 'banner' ) : ?>
    <div class="container-custom">
        <div class="<?php echo esc_attr( $bg_class ); ?> rounded-lg p-8 text-center" style="opacity: 0.9; mask-image: linear-gradient(to right, transparent 0%, black 20%, black 80%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 20%, black 80%, transparent 100%);">
            <!-- Email Icon -->
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                <?php echo esc_html( $title ); ?>
            </h2>
            
            <?php if ( $description ) : ?>
                <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                    <?php echo esc_html( $description ); ?>
                </p>
            <?php endif; ?>
            
            <?php if ( $show_email_input ) : ?>
                <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto" action="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>">
                    <input 
                        type="email" 
                        name="email"
                        placeholder="<?php echo esc_attr( kt_t( 'newsletter_email_placeholder' ) ); ?>"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors"
                    >
                        <?php kt_e( 'newsletter_subscribe' ); ?>
                    </button>
                </form>
            <?php else : ?>
                <a 
                    href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors"
                >
                    <?php kt_e( 'footer_subscribe' ); ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ( $variant === 'compact' ) : ?>
    <div class="<?php echo esc_attr( $bg_class ); ?> rounded-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-2">
            <?php echo esc_html( $title ); ?>
        </h3>
        
        <?php if ( $description ) : ?>
            <p class="text-gray-600 mb-4 text-sm">
                <?php echo esc_html( $description ); ?>
            </p>
        <?php endif; ?>
        
        <a 
            href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>"
            class="inline-flex items-center text-primary-500 hover:text-primary-600 font-medium text-sm"
        >
            <?php kt_e( 'newsletter_subscribe' ); ?> →
        </a>
    </div>

<?php else : ?>
    <!-- Full variant -->
    <section class="py-16 <?php echo esc_attr( $bg_class ); ?>">
        <div class="container-custom">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    <?php echo esc_html( $title ); ?>
                </h2>
                
                <?php if ( $description ) : ?>
                    <p class="text-gray-600 mb-8">
                        <?php echo esc_html( $description ); ?>
                    </p>
                <?php endif; ?>
                
                <a 
                    href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>"
                    class="inline-flex items-center justify-center px-8 py-4 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors text-lg"
                >
                    <?php kt_e( 'footer_subscribe' ); ?>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>
