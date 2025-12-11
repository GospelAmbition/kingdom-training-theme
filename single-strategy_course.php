<?php
/**
 * Single Template for Strategy Courses
 * 
 * @package KingdomTraining
 */

get_header();

$current_lang = kt_get_current_language();
$course = null;

// Get the course
if ( have_posts() ) {
    the_post();
    $course = KT_Query_Courses::format_course( get_post() );
}

if ( ! $course ) {
    // Course not found
    ?>
    <div class="container-custom py-16 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php kt_e( 'error_course_not_found' ); ?></h1>
        <p class="text-gray-600 mb-8"><?php kt_e( 'error_course_not_found_desc' ); ?></p>
        <a href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>" class="text-primary-500 hover:text-primary-600 font-medium">
            ← <?php kt_e( 'ui_back_to' ); ?> <?php kt_e( 'nav_strategy_courses' ); ?>
        </a>
    </div>
    <?php
    get_footer();
    return;
}

// Get adjacent steps for navigation
$adjacent = array( 'prev' => null, 'next' => null );
if ( $course->steps ) {
    $adjacent = KT_Query_Courses::get_adjacent_steps( $course->steps, $current_lang );
}

// Process content
$processed_content = kt_process_image_widths( $course->content );

// Get description for SEO
$description = kt_truncate( $course->excerpt ?: $course->content, 160 );

// SEO Meta
kt_render_seo_meta( array(
    'title'          => $course->title . ' - ' . get_bloginfo( 'name' ),
    'description'    => $description,
    'keywords'       => 'M2DMM course, ' . $course->title . ', strategy course, disciple making training',
    'image'          => $course->featured_image_url,
    'url'            => $course->permalink,
    'type'           => 'article',
    'author'         => $course->author_name,
    'published_time' => $course->date,
    'modified_time'  => $course->modified,
) );

// Structured Data
$logo_url = get_template_directory_uri() . '/dist/kt-logo-header.webp';
kt_render_structured_data( array(
    'course' => array(
        'name'        => $course->title,
        'description' => $description,
    ),
    'breadcrumb' => array(
        'items' => array(
            array( 'name' => kt_t( 'breadcrumb_home' ), 'url' => home_url( '/' ) ),
            array( 'name' => kt_t( 'breadcrumb_strategy_courses' ), 'url' => kt_get_language_url( '/strategy-course' ) ),
            array( 'name' => $course->title, 'url' => $course->permalink ),
        ),
    ),
) );
?>

<article>
    <?php kt_render_edit_link(); ?>
    
    <!-- Breadcrumbs -->
    <div class="bg-gray-50 py-4 pt-20">
        <div class="container-custom">
            <?php kt_render_breadcrumbs(); ?>
        </div>
    </div>

    <!-- Course Header -->
    <header class="relative py-12 bg-gradient-to-br from-secondary-900 via-secondary-800 to-secondary-700 text-white overflow-hidden">
        <!-- Neural Network Background Animation -->
        <div class="neuralnet-background">
            <canvas id="neural-canvas"></canvas>
        </div>
        
        <!-- Background gradient overlay -->
        <div class="absolute inset-0 opacity-20 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-transparent"></div>
        </div>
        
        <div class="container-custom relative z-10 neuralnet-background-container">
            <div class="max-w-4xl mx-auto">
                <?php if ( $course->steps ) : ?>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex-shrink-0 w-10 h-10 bg-primary-500 text-white rounded-full flex items-center justify-center font-bold">
                            <?php echo esc_html( $course->steps ); ?>
                        </span>
                        <div class="flex items-center gap-3">
                            <span class="text-secondary-200 text-sm uppercase tracking-wide font-medium">
                                Step <?php echo esc_html( $course->steps ); ?> of the MVP Course
                            </span>
                            <span class="text-secondary-400">•</span>
                            <span class="text-secondary-200 text-sm"><?php echo esc_html( kt_get_reading_time( $course->content ) ); ?> min read</span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
                    <?php echo esc_html( $course->title ); ?>
                </h1>
                
                <?php if ( $course->featured_image_url ) : ?>
                    <div class="rounded-lg overflow-hidden shadow-lg">
                        <img 
                            src="<?php echo esc_url( $course->featured_image_url ); ?>" 
                            alt="<?php echo esc_attr( $course->title ); ?>"
                            class="w-full h-auto"
                            loading="eager"
                        >
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Course Content -->
    <div class="py-12 bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto">
                <div class="wp-content prose prose-lg max-w-none">
                    <?php echo $processed_content; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="py-8 bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto">
                <?php get_template_part( 'template-parts/course-progress-bar' ); ?>
            </div>
        </div>
    </div>
    
    <!-- Step Navigation -->
    <?php if ( $course->steps && ( $adjacent['prev'] || $adjacent['next'] ) ) : ?>
        <div class="py-8 bg-gray-50 border-t border-gray-200">
            <div class="container-custom">
                <div class="max-w-4xl mx-auto">
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <?php if ( $adjacent['prev'] ) : ?>
                            <a 
                                href="<?php echo esc_url( kt_get_language_url( '/strategy-course/' . $adjacent['prev']->slug ) ); ?>"
                                class="flex items-center gap-3 p-4 bg-primary-500 hover:bg-primary-600 text-white rounded-lg shadow-sm hover:shadow-md transition-colors duration-200 font-medium"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <span><?php kt_e( 'single_previous_step' ); ?></span>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                        
                        <?php if ( $adjacent['next'] ) : ?>
                            <a 
                                href="<?php echo esc_url( kt_get_language_url( '/strategy-course/' . $adjacent['next']->slug ) ); ?>"
                                class="flex items-center gap-3 p-4 bg-primary-500 hover:bg-primary-600 text-white rounded-lg shadow-sm hover:shadow-md transition-colors duration-200 font-medium"
                            >
                                <span><?php kt_e( 'single_next_step' ); ?></span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</article>

<?php get_footer(); ?>
