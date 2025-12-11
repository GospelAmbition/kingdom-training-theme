<?php
/**
 * Main Index Template
 * 
 * This is the fallback template for the Kingdom.Training theme.
 * Specific templates (front-page.php, archive-*.php, single-*.php, etc.)
 * will be used when available.
 *
 * @package KingdomTraining
 */

get_header();

// SEO Meta
kt_render_seo_meta( array(
    'title'       => get_bloginfo( 'name' ),
    'description' => get_bloginfo( 'description' ),
) );
?>

<section class="py-16">
    <div class="container-custom">
        <?php if ( have_posts() ) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'template-parts/content-card', null, array(
                        'post' => kt_format_post_for_display( get_post() ),
                        'type' => get_post_type(),
                    ) ); ?>
                <?php endwhile; ?>
</div>

            <!-- Pagination -->
            <nav class="mt-12 flex justify-center" aria-label="Pagination">
<?php
                echo paginate_links( array(
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'type'      => 'list',
                    'class'     => 'flex items-center gap-2',
                ) );
                ?>
            </nav>
        <?php else : ?>
            <div class="text-center py-16 bg-gray-50 rounded-lg">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">No Content Found</h2>
                <p class="text-gray-600">There's no content to display at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
