<?php
/**
 * Default Page Template
 * 
 * @package KingdomTraining
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        
        $page_title = get_the_title();
        $page_content = apply_filters( 'the_content', get_the_content() );
        
        // SEO Meta
        kt_render_seo_meta( array(
            'title'       => $page_title . ' - ' . get_bloginfo( 'name' ),
            'description' => kt_truncate( wp_strip_all_tags( $page_content ), 160 ),
            'url'         => get_permalink(),
            'image'       => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
        ) );
?>

<?php kt_render_edit_link(); ?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'    => $page_title,
    'bg_class' => 'bg-gradient-to-r from-secondary-900 to-secondary-700',
) ); ?>

<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="max-w-4xl mx-auto">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="mb-8 rounded-lg overflow-hidden shadow-lg">
                    <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-auto' ) ); ?>
                </div>
            <?php endif; ?>
            
            <div class="wp-content prose prose-lg max-w-none">
                <?php echo $page_content; ?>
            </div>
        </div>
    </div>
</section>

<?php
    endwhile;
endif;

get_footer();
?>
