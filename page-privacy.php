<?php
/**
 * Template Name: Privacy Page
 * 
 * @package KingdomTraining
 */

get_header();
?>

<?php kt_render_edit_link(); ?>

<?php
// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'footer_privacy_policy' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => 'Privacy policy for Kingdom.Training website.',
    'url'         => kt_get_language_url( '/privacy' ),
) );
?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'       => kt_t( 'footer_privacy_policy' ),
    'description' => kt_t( 'privacy_header_description' ),
    'bg_class'    => 'bg-gradient-to-r from-gray-700 to-gray-600',
) ); ?>

<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="wp-content prose prose-lg max-w-none">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; else : ?>
                <div class="wp-content prose prose-lg max-w-none">
                    <p class="text-gray-600 mb-8"><?php echo esc_html( kt_t( 'privacy_last_updated' ) ); ?></p>
                    
                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_introduction_title' ) ); ?></h2>
                        <p class="mb-4"><?php echo esc_html( kt_t( 'privacy_introduction_paragraph_1' ) ); ?></p>
                        <p><?php echo esc_html( kt_t( 'privacy_introduction_paragraph_2' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_information_collect_title' ) ); ?></h2>
                        
                        <h3 class="text-xl font-semibold mt-6 mb-3"><?php echo esc_html( kt_t( 'privacy_personal_information_title' ) ); ?></h3>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_personal_information_intro' ) ); ?></p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_item_1' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_item_2' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_item_3' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_item_4' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_item_5' ) ); ?></li>
                        </ul>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_personal_information_list_2_intro' ) ); ?></p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_list_2_item_1' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_list_2_item_2' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_list_2_item_3' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_personal_information_list_2_item_4' ) ); ?></li>
                        </ul>

                        <h3 class="text-xl font-semibold mt-6 mb-3"><?php echo esc_html( kt_t( 'privacy_automatically_collected_title' ) ); ?></h3>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_automatically_collected_intro' ) ); ?></p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li><?php echo esc_html( kt_t( 'privacy_automatically_collected_item_1' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_automatically_collected_item_2' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_automatically_collected_item_3' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_automatically_collected_item_4' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_automatically_collected_item_5' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_automatically_collected_item_6' ) ); ?></li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_how_use_title' ) ); ?></h2>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_how_use_intro' ) ); ?></p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_1' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_2' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_3' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_4' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_5' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_6' ) ); ?></li>
                            <li><?php echo esc_html( kt_t( 'privacy_how_use_item_7' ) ); ?></li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_sharing_title' ) ); ?></h2>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_sharing_intro' ) ); ?></p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li><?php echo wp_kses_post( kt_t( 'privacy_sharing_item_1' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_sharing_item_2' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_sharing_item_3' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_sharing_item_4' ) ); ?></li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_data_security_title' ) ); ?></h2>
                        <p><?php echo esc_html( kt_t( 'privacy_data_security_text' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_cookies_title' ) ); ?></h2>
                        <p><?php echo esc_html( kt_t( 'privacy_cookies_text' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_third_party_title' ) ); ?></h2>
                        <p><?php echo esc_html( kt_t( 'privacy_third_party_text' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_rights_title' ) ); ?></h2>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_rights_intro' ) ); ?></p>
                        <ul class="list-disc pl-6 mb-4 space-y-2">
                            <li><?php echo wp_kses_post( kt_t( 'privacy_rights_item_1' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_rights_item_2' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_rights_item_3' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_rights_item_4' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_rights_item_5' ) ); ?></li>
                            <li><?php echo wp_kses_post( kt_t( 'privacy_rights_item_6' ) ); ?></li>
                        </ul>
                        <p><?php echo esc_html( kt_t( 'privacy_rights_contact' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_children_title' ) ); ?></h2>
                        <p><?php echo esc_html( kt_t( 'privacy_children_text' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_changes_title' ) ); ?></h2>
                        <p><?php echo esc_html( kt_t( 'privacy_changes_text' ) ); ?></p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4"><?php echo esc_html( kt_t( 'privacy_contact_title' ) ); ?></h2>
                        <p class="mb-3"><?php echo esc_html( kt_t( 'privacy_contact_intro' ) ); ?></p>
                        <p class="mb-2"><strong><?php echo esc_html( kt_t( 'privacy_contact_org' ) ); ?></strong></p>
                        <p class="mb-2"><?php echo esc_html( kt_t( 'privacy_contact_email' ) ); ?> <a href="mailto:info@kingdom.training" class="text-blue-600 hover:underline">info@kingdom.training</a></p>
                        <p><?php echo esc_html( kt_t( 'privacy_contact_website' ) ); ?> <a href="https://ai.kingdom.training" class="text-blue-600 hover:underline">ai.kingdom.training</a></p>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
