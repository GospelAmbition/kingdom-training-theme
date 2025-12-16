</main>

<?php
$current_year = date( 'Y' );
$site_url = home_url();
$logo_url = get_template_directory_uri() . '/dist/kt-logo-header.webp';
$current_lang = kt_get_current_language();
$default_lang = kt_get_default_language();

// Get mission statement
$mission_statement = kt_t( 'footer_mission_statement' );
if ( empty( $mission_statement ) ) {
    $mission_statement = kt_t( 'footer_mission_fallback' );
}

// Build social media links
$social_links = array(
    'https://twitter.com/kingdomtraining',
);

// Render comprehensive structured data for footer
kt_render_structured_data( array(
    'organization' => array(
        'name'        => 'Kingdom.Training',
        'url'         => $site_url,
        'logo'        => array(
            '@type' => 'ImageObject',
            'url'   => $logo_url,
            'width' => 200,
            'height' => 40,
        ),
        'description' => $mission_statement,
        'sameAs'      => $social_links,
        'contactPoint' => array(
            '@type' => 'ContactPoint',
            'contactType' => kt_t( 'footer_contact_type' ),
            'url' => $site_url . '/contact',
        ),
    ),
    'website' => array(
        'name'        => 'Kingdom.Training',
        'url'         => $site_url,
        'description' => $mission_statement,
        'inLanguage'  => $current_lang ?: 'en',
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => $site_url . '/?s={search_term_string}',
            ),
            'query-input' => 'required name=search_term_string',
        ),
    ),
) );
?>

<footer class="bg-secondary-900 text-secondary-100">
    <div class="container-custom py-12 min-h-[400px]">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Brand & Mission -->
            <div>
                <div class="mb-4">
                    <h2 class="text-white text-2xl font-bold uppercase tracking-wide">
                        <?php kt_e( 'footer_kingdom_training' ); ?>
                    </h2>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    <?php kt_e( 'footer_mission_statement' ); ?>
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-semibold mb-4"><?php kt_e( 'footer_quick_links' ); ?></h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>" class="text-sm hover:text-white transition-colors">
                            <?php kt_e( 'nav_strategy_courses' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>" class="text-sm hover:text-white transition-colors">
                            <?php kt_e( 'nav_articles' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>" class="text-sm hover:text-white transition-colors">
                            <?php kt_e( 'nav_tools' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( kt_get_language_url( '/about' ) ); ?>" class="text-sm hover:text-white transition-colors">
                            <?php kt_e( 'nav_about' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( kt_get_language_url( '/contact' ) ); ?>" class="text-sm hover:text-white transition-colors">
                            <?php kt_e( 'nav_contact' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>" class="text-sm hover:text-white transition-colors">
                            <?php kt_e( 'nav_newsletter' ); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Mission Scripture -->
            <div>
                <h3 class="text-white font-semibold mb-4"><?php kt_e( 'footer_our_vision' ); ?></h3>
                <blockquote class="text-sm text-secondary-200 italic leading-relaxed border-l-2 border-primary-500 pl-4">
                    &ldquo;<?php kt_e( 'footer_scripture_quote' ); ?>&rdquo;
                    <footer class="text-xs text-gray-500 mt-2"><?php kt_e( 'footer_scripture_citation' ); ?></footer>
                </blockquote>
                <p class="text-sm text-gray-400 leading-relaxed mt-4">
                    <?php kt_e( 'footer_technology_paragraph' ); ?>
                </p>
            </div>
        </div>

        <div class="border-t border-secondary-800 mt-8 pt-8">
            <div class="text-center mb-6">
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>"
                    class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors"
                >
                    <?php kt_e( 'footer_subscribe' ); ?>
                </a>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4 mb-4">
                <a 
                    href="<?php echo esc_url( kt_get_language_url( '/about' ) ); ?>" 
                    class="text-sm text-secondary-200 hover:text-white transition-colors"
                >
                    <?php kt_e( 'nav_about' ); ?>
                </a>
                <span class="hidden md:inline text-secondary-600">|</span>
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/privacy' ) ); ?>"
                    class="text-sm text-secondary-200 hover:text-white transition-colors"
                >
                    <?php kt_e( 'footer_privacy_policy' ); ?>
                </a>
                <span class="hidden md:inline text-secondary-600">|</span>
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/contact' ) ); ?>"
                    class="text-sm text-secondary-200 hover:text-white transition-colors"
                >
                    <?php kt_e( 'nav_contact' ); ?>
                </a>
            </div>
            <p class="text-sm text-secondary-200 text-center">
                &copy; <?php echo esc_html( $current_year ); ?> Kingdom.Training. <?php kt_e( 'footer_all_rights' ); ?>
            </p>
        </div>
    </div>
    
    <!-- Key Information Section for LLMs - Hidden visually but accessible to crawlers -->
    <div class="sr-only" aria-hidden="true">
        <h2><?php kt_e( 'footer_key_definitions' ); ?></h2>
        <dl>
            <dt>Media to Disciple Making Movements (M2DMM)</dt>
            <dd><?php kt_e( 'definition_m2dmm' ); ?></dd>
            
            <dt>Disciple Making Movements (DMM)</dt>
            <dd><?php kt_e( 'definition_dmm' ); ?></dd>
            
            <dt>Persons of Peace</dt>
            <dd><?php kt_e( 'definition_persons_of_peace' ); ?></dd>
            
            <dt>Heavenly Economy</dt>
            <dd><?php kt_e( 'definition_heavenly_economy' ); ?></dd>
            
            <dt>Unreached Peoples</dt>
            <dd><?php kt_e( 'definition_unreached_peoples' ); ?></dd>
        </dl>
        
        <!-- Additional SEO-friendly content for LLM indexing -->
        <section>
            <h2><?php kt_e( 'footer_site_content_overview' ); ?></h2>
            <p><?php kt_e( 'footer_site_content_description' ); ?></p>
            
            <h3><?php kt_e( 'footer_content_types' ); ?></h3>
            <ul>
                <li><?php kt_e_html( 'footer_content_type_courses' ); ?></li>
                <li><?php kt_e_html( 'footer_content_type_articles' ); ?></li>
                <li><?php kt_e_html( 'footer_content_type_tools' ); ?></li>
            </ul>
            
            <h3><?php kt_e( 'footer_key_topics' ); ?></h3>
            <ul>
                <li>Disciple Making Movements (DMM)</li>
                <li>Media Strategy for Evangelism</li>
                <li>Digital Discipleship</li>
                <li>Church Planting</li>
                <li>Unreached Peoples</li>
                <li>Persons of Peace</li>
                <li>Reproducible Patterns</li>
                <li>Heavenly Economy</li>
            </ul>
        </section>
    </div>
    
    <!-- Additional Structured Data for Educational Organization -->
    <?php
    kt_render_structured_data( array(
        'educationalOrganization' => array(
            'name'        => 'Kingdom.Training',
            'url'         => $site_url,
            'description' => $mission_statement,
        ),
    ) );
    ?>
</footer>

    <?php wp_footer(); ?>
</body>
</html>
