<?php
/**
 * Front Page Template
 * 
 * The template for displaying the home page.
 *
 * @package KingdomTraining
 */

get_header();

$current_lang = kt_get_current_language();
$default_lang = kt_get_default_language();

// Get course steps
$course_steps = KT_Query_Courses::get_ordered_steps( $current_lang );

// Get featured articles
$articles_result = KT_Query_Articles::get_articles( array(
    'per_page' => 3,
    'orderby'  => 'date',
    'order'    => 'DESC',
    'lang'     => $current_lang,
) );
$articles = $articles_result['items'];

// Get featured tools
$tools_result = KT_Query_Tools::get_tools( array(
    'per_page' => 3,
    'orderby'  => 'date',
    'order'    => 'DESC',
    'lang'     => $current_lang,
) );
$tools = $tools_result['items'];

// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'page_home' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => kt_t( 'seo_home_description' ),
    'keywords'    => 'disciple making movements, media to movements, M2DMM, digital discipleship, online evangelism, church planting, unreached peoples, kingdom training, strategy course, MVP course',
    'type'        => 'website',
) );

// Structured Data
kt_render_structured_data( array(
    'website' => array(
        'name'        => 'Kingdom.Training',
        'url'         => home_url(),
        'description' => kt_t( 'footer_mission_statement' ),
    ),
) );
?>

<!-- Hero Section -->
<?php get_template_part( 'template-parts/hero', null, array(
    'subtitle'    => kt_t( 'hero_subtitle_media_ai' ),
    'title'       => kt_t( 'hero_title_innovate_word' ) . ' → ' . kt_t( 'hero_title_accelerate_word' ) . ' → ' . kt_t( 'hero_title_make_disciples_word' ),
    'description' => kt_t( 'hero_description' ),
    'cta_text'    => kt_t( 'nav_start_mvp' ),
    'cta_link'    => kt_get_language_url( '/strategy-course' ),
) ); ?>

<!-- Newsletter CTA Section -->
<section class="py-12 relative overflow-hidden">
    <!-- Background text with course step titles -->
    <?php
    // Get course step titles
    $step_titles = array();
    if ( ! empty( $course_steps ) ) {
        foreach ( $course_steps as $step ) {
            $step_titles[] = $step->title;
        }
    }
    // Duplicate to get 20 items
    $background_items = array_merge( $step_titles, $step_titles );
    ?>
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="relative w-full h-full min-h-[600px]">
            <?php 
            // Spread 20 items evenly across the section
            // Create a grid-like distribution: 4 columns x 5 rows
            $columns = 4;
            $rows = 5;
            
            // Predefined offsets for slight variation (deterministic)
            $x_offsets = array( 2, -3, 4, -2, 3, -4, 1, -3, 2, -1, 3, -2, 4, -3, 1, -4, 2, -1, 3, -2 );
            $y_offsets = array( 1, -2, 3, -1, 2, -3, 1, -2, 2, -1, 3, -2, 1, -3, 2, -1, 3, -2, 1, -2 );
            
            foreach ( $background_items as $index => $item ) : 
                // Calculate grid position
                $col = $index % $columns;
                $row = floor( $index / $columns );
                
                // Spread evenly: 10% to 90% horizontally, 20% to 70% vertically (compressed)
                $base_x = 10 + ( $col * ( 80 / max( 1, ($columns - 1) ) ) );
                $base_y = 20 + ( $row * ( 50 / max( 1, ($rows - 1) ) ) );
                
                // Add slight variation
                $x = $base_x + ( $x_offsets[ $index % count( $x_offsets ) ] );
                $y = $base_y + ( $y_offsets[ $index % count( $y_offsets ) ] );
                
                // Ensure values stay within bounds
                $x = max( 5, min( 95, $x ) );
                $y = max( 10, min( 80, $y ) );
            ?>
                <div 
                    class="absolute text-3xl md:text-3xl lg:text-4xl font-bold text-gray-300 whitespace-nowrap blur-xs"
                    style="opacity: 0.2; left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%; transform: translate(-50%, -50%);"
                >
                    <?php echo esc_html( $item ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="relative z-10">
    <?php get_template_part( 'template-parts/newsletter-cta', null, array(
        'variant'          => 'banner',
        'title'            => kt_t( 'newsletter_cta_title' ),
        'description'      => kt_t( 'newsletter_cta_description' ),
        'show_email_input' => false,
        'white_background' => true,
    ) ); ?>
    </div>
</section>

<!-- The MVP Course Feature - Primary Conversion -->
<section class="relative py-20 bg-gradient-to-br from-secondary-900 to-secondary-700 text-white overflow-hidden">
    <!-- Neural Network Background -->
    <div class="neuralnet-background absolute inset-0 z-0">
        <canvas id="neural-canvas-mvp"></canvas>
    </div>
    
    <!-- Background gradient overlay -->
    <div class="absolute inset-0 opacity-20 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-transparent"></div>
    </div>
    
    <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-5xl font-bold mb-4">
                <?php kt_e( 'mvp_course_title' ); ?>
            </h2>
            <p class="text-xl text-secondary-100 mb-8 max-w-2xl mx-auto">
                <?php kt_e( 'mvp_course_description' ); ?>
            </p>
            
            <div class="relative bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8 text-left overflow-hidden">
                <div class="relative z-10">
                <h3 class="text-xl font-semibold mb-4 text-accent-500">
                    <?php echo esc_html( kt_t_replace( 'page_step_curriculum', array( 'count' => count( $course_steps ) > 0 ? count( $course_steps ) : 10 ) ) ); ?>
                </h3>
                
                <?php if ( ! empty( $course_steps ) ) : ?>
                    <?php
                    // Split course steps into two columns (5 items each)
                    $first_column = array_slice( $course_steps, 0, 5 );
                    $second_column = array_slice( $course_steps, 5, 5 );
                    ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <!-- First Column -->
                        <div class="flex flex-col gap-3">
                            <?php foreach ( $first_column as $index => $step ) : ?>
                                <a
                                    href="<?php echo esc_url( kt_get_language_url( '/strategy-course/' . $step->slug ) ); ?>"
                                    class="hover:text-accent-400 transition-colors"
                                >
                                    <?php echo esc_html( ( $step->steps ?: ( $index + 1 ) ) . '. ' . $step->title ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Second Column -->
                        <div class="flex flex-col gap-3">
                            <?php foreach ( $second_column as $index => $step ) : ?>
                                <?php
                                // Calculate the correct step number (starting from 6 for second column)
                                $step_number = $step->steps ?: ( $index + 6 );
                                ?>
                            <a
                                href="<?php echo esc_url( kt_get_language_url( '/strategy-course/' . $step->slug ) ); ?>"
                                class="hover:text-accent-400 transition-colors"
                            >
                                    <?php echo esc_html( $step_number . '. ' . $step->title ); ?>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <p class="text-secondary-200"><?php kt_e( 'home_loading_steps' ); ?></p>
                <?php endif; ?>
                </div>
            </div>
            
            <a
                href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>"
                class="inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200 text-lg"
            >
                <?php kt_e( 'nav_enroll_mvp' ); ?>
            </a>
        </div>
    </div>
</section>

<!-- Featured Articles -->
<section class="py-16 bg-background-50">
    <div class="container-custom">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800"><?php kt_e( 'page_latest_articles' ); ?></h2>
            <a
                href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>"
                class="text-primary-500 hover:text-primary-600 font-medium"
            >
                <?php kt_e( 'ui_view_all' ); ?> →
            </a>
        </div>
        
        <?php if ( ! empty( $articles ) ) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ( $articles as $article ) : ?>
                    <?php get_template_part( 'template-parts/content-card', null, array(
                        'post' => $article,
                        'type' => 'articles',
                    ) ); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-center py-12 bg-white rounded-lg">
                <p class="text-gray-600 mb-4"><?php kt_e( 'msg_no_articles' ); ?></p>
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>"
                    class="text-primary-500 hover:text-primary-600 font-medium"
                >
                    <?php kt_e( 'ui_browse_all' ); ?> <?php echo esc_html( strtolower( kt_t( 'nav_articles' ) ) ); ?> →
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Featured Tools -->
<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800"><?php kt_e( 'page_featured_tools' ); ?></h2>
            <a
                href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>"
                class="text-primary-500 hover:text-primary-600 font-medium"
            >
                <?php kt_e( 'ui_view_all' ); ?> →
            </a>
        </div>
        
        <?php if ( ! empty( $tools ) ) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ( $tools as $tool ) : ?>
                    <?php get_template_part( 'template-parts/content-card', null, array(
                        'post' => $tool,
                        'type' => 'tools',
                    ) ); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-center py-12 bg-background-50 rounded-lg">
                <p class="text-gray-600 mb-4"><?php kt_e( 'msg_no_tools' ); ?></p>
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>"
                    class="text-primary-500 hover:text-primary-600 font-medium"
                >
                    <?php kt_e( 'ui_browse_all' ); ?> <?php echo esc_html( strtolower( kt_t( 'nav_tools' ) ) ); ?> →
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Mission/Foundation Section -->
<section class="py-20 bg-primary-800 text-white">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Video Section -->
            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-8">
                    <?php kt_e( 'content_digital_disciple_making' ); ?>
                </h2>
                <div class="relative w-full" style="padding-bottom: 56.25%;">
                    <iframe
                        src="https://player.vimeo.com/video/436776178?title=0&byline=0&portrait=0"
                        class="absolute top-0 left-0 w-full h-full rounded-lg shadow-2xl"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        title="<?php echo esc_attr( kt_t( 'video_kingdom_training_title' ) ); ?>"
                        loading="lazy"
                    ></iframe>
                </div>
            </div>

            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                <?php kt_e( 'content_heavenly_economy' ); ?>
            </h2>
            <p class="text-lg text-primary-100 leading-relaxed mb-6">
                <?php kt_e( 'home_heavenly_economy' ); ?>
            </p>
            <p class="text-lg text-primary-100 leading-relaxed mb-8">
                <?php kt_e( 'home_mission_statement' ); ?>
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>"
                    class="inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200"
                >
                    <?php kt_e( 'page_start_strategy_course' ); ?>
                </a>
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>"
                    class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 hover:border-white text-white font-semibold rounded-lg transition-colors duration-200"
                >
                    <?php kt_e( 'ui_read_articles' ); ?>
                </a>
                <a
                    href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>"
                    class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 hover:border-white text-white font-semibold rounded-lg transition-colors duration-200"
                >
                    <?php kt_e( 'ui_explore_tools' ); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Key Information Section for Answer Engine Optimization -->
<?php get_template_part( 'template-parts/key-info-section', null, array(
    'title' => kt_t( 'key_info_title' ),
    'items' => array(
        array(
            'term'       => kt_t( 'key_info_m2dmm_term' ),
            'definition' => kt_t( 'key_info_m2dmm_definition' ),
        ),
        array(
            'term'       => kt_t( 'key_info_digital_term' ),
            'definition' => kt_t( 'key_info_digital_definition' ),
        ),
        array(
            'term'       => kt_t( 'key_info_mvp_term' ),
            'definition' => kt_t( 'key_info_mvp_definition' ),
        ),
        array(
            'term'       => kt_t( 'key_info_ai_term' ),
            'definition' => kt_t( 'key_info_ai_definition' ),
        ),
        array(
            'term'       => kt_t( 'key_info_heavenly_economy_term' ),
            'definition' => kt_t( 'key_info_heavenly_economy_definition' ),
        ),
        array(
            'term'       => kt_t( 'key_info_who_term' ),
            'definition' => kt_t( 'key_info_who_definition' ),
        ),
    ),
) ); ?>

<?php get_footer(); ?>
