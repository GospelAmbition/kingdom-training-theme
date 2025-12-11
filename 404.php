<?php
/**
 * 404 Template
 * 
 * @package KingdomTraining
 */

get_header();

// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'error_404_title' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => kt_t( 'error_404_description' ),
) );
?>

<section class="py-20 bg-background-50 min-h-[60vh] flex items-center">
    <div class="container-custom">
        <div class="max-w-2xl mx-auto text-center">
            <div class="text-9xl font-bold text-primary-200 mb-4">404</div>
            
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                <?php kt_e( 'error_404_title' ); ?>
            </h1>
            
            <p class="text-xl text-gray-600 mb-8">
                <?php kt_e( 'error_404_description' ); ?>
            </p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a 
                    href="<?php echo esc_url( home_url( '/' ) ); ?>"
                    class="inline-flex items-center justify-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors"
                >
                    ← <?php kt_e( 'nav_home' ); ?>
                </a>
                
                <a 
                    href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>"
                    class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-lg transition-colors"
                >
                    <?php kt_e( 'nav_strategy_courses' ); ?>
                </a>
            </div>
            
            <!-- Quick Links -->
            <div class="mt-12 pt-12 border-t border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4"><?php kt_e( 'error_404_popular_pages' ); ?></h2>
                <div class="flex flex-wrap justify-center gap-4 text-primary-500">
                    <a href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>" class="hover:text-primary-600">
                        <?php kt_e( 'nav_articles' ); ?>
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>" class="hover:text-primary-600">
                        <?php kt_e( 'nav_tools' ); ?>
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>" class="hover:text-primary-600">
                        <?php kt_e( 'nav_newsletter' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
