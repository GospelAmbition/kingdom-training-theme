<?php
/**
 * Template Name: About Page
 * 
 * @package KingdomTraining
 */

get_header();
?>

<?php kt_render_edit_link(); ?>

<?php
// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'nav_about' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => kt_t( 'footer_mission_statement' ),
    'url'         => kt_get_language_url( '/about' ),
) );
?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'       => kt_t( 'about_header_title' ),
    'description' => kt_t( 'about_header_description' ),
    'bg_class'    => 'bg-gradient-to-r from-primary-700 to-primary-500',
) ); ?>

<!-- Our Vision Section -->
<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"><?php echo esc_html( kt_t( 'about_vision_title' ) ); ?></h2>
            <div class="prose prose-lg max-w-none text-gray-700">
                <p class="text-lg mb-4">
                    <?php echo esc_html( kt_t( 'about_vision_paragraph_1' ) ); ?>
                </p>
                <p class="text-lg">
                    <?php echo esc_html( kt_t( 'about_vision_paragraph_2' ) ); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission Section -->
<section class="py-16 bg-primary-100">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"><?php echo esc_html( kt_t( 'about_mission_title' ) ); ?></h2>
            <div class="prose prose-lg max-w-none text-gray-700">
                <p class="text-lg mb-4">
                    <?php echo esc_html( kt_t( 'about_mission_paragraph_1' ) ); ?>
                </p>
                <p class="text-lg">
                    <?php echo esc_html( kt_t( 'about_mission_paragraph_2' ) ); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How it works Section -->
<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8"><?php echo esc_html( kt_t( 'about_how_works_title' ) ); ?></h2>
            <div class="space-y-8">
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold">
                            1
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo esc_html( kt_t( 'about_step_1_title' ) ); ?></h3>
                        <p class="text-lg text-gray-700">
                            <?php echo esc_html( kt_t( 'about_step_1_description' ) ); ?>
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold">
                            2
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo esc_html( kt_t( 'about_step_2_title' ) ); ?></h3>
                        <p class="text-lg text-gray-700">
                            <?php echo esc_html( kt_t( 'about_step_2_description' ) ); ?>
                        </p>
                    </div>
                </div>
                
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold">
                            3
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo esc_html( kt_t( 'about_step_3_title' ) ); ?></h3>
                        <p class="text-lg text-gray-700">
                            <?php echo esc_html( kt_t( 'about_step_3_description' ) ); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
