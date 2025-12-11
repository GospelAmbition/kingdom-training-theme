<?php
/**
 * Single Template for Tools
 * 
 * @package KingdomTraining
 */

get_header();

$current_lang = kt_get_current_language();
$tool = null;

// Get the tool
if ( have_posts() ) {
    the_post();
    $tool = KT_Query_Tools::format_tool( get_post() );
}

if ( ! $tool ) {
    // Tool not found
    ?>
    <div class="container-custom py-16 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php kt_e( 'error_tool_not_found' ); ?></h1>
        <p class="text-gray-600 mb-8"><?php kt_e( 'error_tool_not_found_desc' ); ?></p>
        <a href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>" class="text-primary-500 hover:text-primary-600 font-medium">
            ← <?php kt_e( 'ui_back_to' ); ?> <?php kt_e( 'nav_tools' ); ?>
        </a>
    </div>
    <?php
    get_footer();
    return;
}

// Get related tools
$related_tools = KT_Query_Tools::get_related_tools( $tool->id, 3, $current_lang );

// Process content
$processed_content = kt_process_image_widths( $tool->content );

// Get description for SEO
$description = kt_truncate( $tool->excerpt ?: $tool->content, 160 );

// SEO Meta
kt_render_seo_meta( array(
    'title'          => $tool->title . ' - ' . get_bloginfo( 'name' ),
    'description'    => $description,
    'keywords'       => 'M2DMM tool, ' . $tool->title . ', disciple making tool, ministry resource',
    'image'          => $tool->featured_image_url,
    'url'            => $tool->permalink,
    'type'           => 'article',
    'author'         => $tool->author_name,
    'published_time' => $tool->date,
    'modified_time'  => $tool->modified,
) );

// Structured Data
$logo_url = get_template_directory_uri() . '/dist/kt-logo-header.webp';
kt_render_structured_data( array(
    'article' => array(
        'title'         => $tool->title,
        'description'   => $description,
        'image'         => $tool->featured_image_url,
        'datePublished' => $tool->date,
        'dateModified'  => $tool->modified,
        'author'        => $tool->author_name,
        'publisherLogo' => $logo_url,
        'url'           => $tool->permalink,
    ),
    'breadcrumb' => array(
        'items' => array(
            array( 'name' => kt_t( 'breadcrumb_home' ), 'url' => home_url( '/' ) ),
            array( 'name' => kt_t( 'breadcrumb_tools' ), 'url' => kt_get_language_url( '/tools' ) ),
            array( 'name' => $tool->title, 'url' => $tool->permalink ),
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

    <!-- Tool Header -->
    <header class="py-12 bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    <?php echo esc_html( $tool->title ); ?>
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-gray-600 mb-8">
                    <span><?php echo esc_html( kt_get_reading_time( $tool->content ) ); ?> min read</span>
                </div>
                
                <?php if ( $tool->featured_image_url ) : ?>
                    <div class="rounded-lg overflow-hidden shadow-lg">
                        <img 
                            src="<?php echo esc_url( $tool->featured_image_url ); ?>" 
                            alt="<?php echo esc_attr( $tool->title ); ?>"
                            class="w-full h-auto"
                            loading="eager"
                        >
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Tool Content -->
    <div class="py-12 bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto">
                <div class="wp-content prose prose-lg max-w-none">
                    <?php echo $processed_content; ?>
                </div>
                
                <!-- Back Link -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <a href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>" class="text-primary-500 hover:text-primary-600 font-medium">
                        ← <?php kt_e( 'ui_back_to' ); ?> <?php kt_e( 'nav_tools' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Tools -->
    <?php if ( ! empty( $related_tools ) ) : ?>
        <section class="py-16 bg-background-50">
            <div class="container-custom">
                <h2 class="text-2xl font-bold text-gray-800 mb-8"><?php kt_e( 'single_related_tools' ); ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ( $related_tools as $related ) : ?>
                        <?php get_template_part( 'template-parts/content-card', null, array(
                            'post' => $related,
                            'type' => 'tools',
                        ) ); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>

<?php get_footer(); ?>
