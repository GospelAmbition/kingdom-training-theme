<?php
/**
 * Archive Template for Articles
 * 
 * @package KingdomTraining
 */

get_header();

$current_lang = kt_get_current_language();

// Get filter parameters
$category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : null;
$tag = isset( $_GET['tag'] ) ? absint( $_GET['tag'] ) : null;
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

// Get articles
$articles_result = KT_Query_Articles::get_articles( array(
    'per_page' => 12,
    'page'     => $paged,
    'orderby'  => 'date',
    'order'    => 'DESC',
    'category' => $category,
    'tag'      => $tag,
    'lang'     => $current_lang,
) );
$articles = $articles_result['items'];
$total_pages = $articles_result['total_pages'];

// Get categories and tags for sidebar
$categories = KT_Query_Articles::get_categories( $current_lang );
$tags_args = array( 'taxonomy' => 'post_tag', 'hide_empty' => true );
$tags = get_terms( $tags_args );
if ( is_wp_error( $tags ) ) {
    $tags = array();
}

// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'page_articles' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => kt_t( 'seo_articles_description' ),
    'keywords'    => 'M2DMM articles, disciple making movements, media strategy, digital evangelism, church planting articles, online ministry, practical discipleship, field insights, kingdom training articles',
    'url'         => kt_get_language_url( '/articles' ),
) );
?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'               => kt_t( 'archive_articles_title' ),
    'description'         => kt_t( 'archive_articles_description' ),
    'bg_class'            => 'bg-gradient-to-r from-secondary-900 to-secondary-700',
    'show_ideas_background' => true,
) ); ?>

<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <?php get_template_part( 'template-parts/sidebar', null, array(
                    'categories'   => $categories,
                    'tags'         => $tags,
                    'base_path'    => kt_get_language_url( '/articles' ),
                    'current_cat'  => $category,
                    'current_tag'  => $tag,
                ) ); ?>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <?php if ( ! empty( $articles ) ) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php foreach ( $articles as $article ) : ?>
                            <?php get_template_part( 'template-parts/content-card', null, array(
                                'post' => $article,
                                'type' => 'articles',
                            ) ); ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ( $total_pages > 1 ) : ?>
                        <nav class="mt-12 flex justify-center" aria-label="Pagination">
                            <div class="flex items-center gap-2">
                                <?php if ( $paged > 1 ) : ?>
                                    <a href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                        <?php kt_e( 'pagination_previous' ); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <span class="px-4 py-2 text-gray-600">
                                    <?php echo esc_html( kt_t_replace( 'pagination_page_of', array( 'current' => $paged, 'total' => $total_pages ) ) ); ?>
                                </span>
                                
                                <?php if ( $paged < $total_pages ) : ?>
                                    <a href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                        <?php kt_e( 'pagination_next' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="text-center py-16 bg-gray-50 rounded-lg">
                        <div class="text-6xl mb-4">📝</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            <?php kt_e( 'content_no_articles_found' ); ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php kt_e( 'content_no_articles_try' ); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
