<?php
/**
 * Archive Template for Strategy Courses
 * 
 * @package KingdomTraining
 */

get_header();

$current_lang = kt_get_current_language();

// Get filter parameters
$category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : null;
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

// Get courses (excluding those with step numbers for additional resources section)
$courses_result = KT_Query_Courses::get_courses( array(
    'per_page'      => 20,
    'page'          => $paged,
    'orderby'       => 'date',
    'order'         => 'DESC',
    'category'      => $category,
    'lang'          => $current_lang,
    'exclude_steps' => true,
) );
$courses = $courses_result['items'];
$total_pages = $courses_result['total_pages'];

// Get ordered steps for the MVP curriculum
$course_steps = KT_Query_Courses::get_ordered_steps( $current_lang );

// Get categories for sidebar
$categories = KT_Query_Courses::get_categories( $current_lang );

// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'page_strategy_courses' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => kt_t( 'seo_courses_description' ),
    'keywords'    => 'M2DMM strategy course, MVP course, disciple making course, media strategy training, digital evangelism course, church planting training',
    'url'         => kt_get_language_url( '/strategy-course' ),
) );
?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'          => kt_t( 'archive_strategy_course_title' ),
    'description'    => kt_t( 'archive_strategy_course_description' ),
    'bg_class'       => 'bg-gradient-to-r from-secondary-900 to-secondary-700',
    'show_neuralnet' => true,
) ); ?>

<!-- MVP Course Steps -->
<?php if ( ! empty( $course_steps ) ) : ?>
<section class="relative py-20 bg-white overflow-hidden" id="mvp-course-steps-section">
    <!-- Roadmap Background with Parallax -->
    <div class="map-background map-background-soft roadmap-parallax" style="opacity: 0.08;"></div>
    
    <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto">
            <!-- Title -->
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 text-center">
                <?php kt_e( 'mvp_course_title' ); ?>
            </h2>
            
            <!-- Description -->
            <p class="text-base md:text-lg text-gray-700 mb-12 text-center max-w-2xl mx-auto leading-relaxed">
                <?php kt_e( 'mvp_course_description' ); ?>
            </p>
            
            <!-- Course Steps Cards -->
            <div class="flex flex-col gap-5">
                <?php foreach ( $course_steps as $index => $step ) : ?>
                    <div class="relative">
                        <a
                            href="<?php echo esc_url( kt_get_language_url( '/strategy-course/' . $step->slug ) ); ?>"
                            class="relative z-10 group flex items-center gap-4 p-6 md:p-8 my-1.5 bg-white rounded-lg border border-primary-500 shadow-sm hover:shadow-md transition-all duration-200"
                        >
                            <!-- Step Number Circle -->
                            <span class="flex-shrink-0 w-12 h-12 bg-primary-500 text-white rounded-full flex items-center justify-center font-bold text-base">
                                <?php echo esc_html( $step->steps ?: $index + 1 ); ?>
                            </span>
                            
                            <!-- Step Content -->
                            <div class="flex-1 flex flex-col gap-1.5">
                                <span class="text-base font-bold text-gray-900"><?php echo esc_html( $step->title ); ?></span>
                                <span class="text-sm text-primary-500 font-medium"><?php kt_e( 'mvp_course_start_step' ); ?></span>
                            </div>
                            
                            <!-- Right Chevron -->
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-800 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        
                        <!-- Connecting Line (not on last item) -->
                        <?php if ( $index < count( $course_steps ) - 1 ) : ?>
                            <div class="absolute left-6 top-1/2 w-[2px] bg-primary-500 z-0" style="height: calc(50% + 1.25rem + 24px);"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Progress Bar -->
            <?php get_template_part( 'template-parts/course-progress-bar' ); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- All Courses -->
<section class="py-16 bg-background-50">
    <div class="container-custom">
        <!-- Section Header -->
        <div class="mb-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                <?php kt_e( 'archive_additional_resources_title' ); ?>
            </h2>
            <p class="text-base md:text-lg text-gray-700 max-w-2xl mx-auto leading-relaxed">
                <?php kt_e( 'archive_additional_resources_description' ); ?>
            </p>
        </div>
        
        <?php if ( ! empty( $categories ) ) : ?>
            <!-- Category Filter -->
            <div class="mb-8 flex flex-wrap gap-2">
                <a 
                    href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo empty( $category ) ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'; ?>"
                >
                    <?php kt_e( 'sidebar_all_categories' ); ?>
                </a>
                <?php foreach ( $categories as $cat ) : ?>
                    <a 
                        href="<?php echo esc_url( add_query_arg( 'category', $cat['slug'], kt_get_language_url( '/strategy-course' ) ) ); ?>"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?php echo $category === $cat['slug'] ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'; ?>"
                    >
                        <?php echo esc_html( $cat['name'] ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $courses ) ) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ( $courses as $course ) : ?>
                    <?php get_template_part( 'template-parts/content-card', null, array(
                        'post' => $course,
                        'type' => 'strategy-course',
                    ) ); ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ( $total_pages > 1 ) : ?>
                <nav class="mt-12 flex justify-center" aria-label="Pagination">
                    <div class="flex items-center gap-2">
                        <?php if ( $paged > 1 ) : ?>
                            <a href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>" class="px-4 py-2 bg-white hover:bg-gray-100 rounded-lg transition-colors">
                                <?php kt_e( 'pagination_previous' ); ?>
                            </a>
                        <?php endif; ?>
                        
                        <span class="px-4 py-2 text-gray-600">
                            <?php echo esc_html( kt_t_replace( 'pagination_page_of', array( 'current' => $paged, 'total' => $total_pages ) ) ); ?>
                        </span>
                        
                        <?php if ( $paged < $total_pages ) : ?>
                            <a href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>" class="px-4 py-2 bg-white hover:bg-gray-100 rounded-lg transition-colors">
                                <?php kt_e( 'pagination_next' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>
        <?php else : ?>
            <div class="text-center py-16 bg-white rounded-lg">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                    <?php kt_e( 'msg_no_courses', 'No courses found' ); ?>
                </h3>
                <p class="text-gray-600">
                    <?php kt_e( 'content_no_courses_try' ); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
