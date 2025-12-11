<?php
/**
 * Template Name: Newsletter Page
 * 
 * @package KingdomTraining
 */

get_header();
?>

<?php kt_render_edit_link(); ?>

<?php
// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'nav_newsletter' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => kt_t( 'newsletter_description' ),
    'url'         => kt_get_language_url( '/newsletter' ),
) );
?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'       => 'Newsletter',
    'description' => 'Stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements.',
    'bg_class'    => 'bg-gradient-to-r from-primary-700 to-primary-500',
) ); ?>

<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="max-w-2xl mx-auto">
            <!-- Newsletter Form -->
            <div class="bg-background-50 rounded-lg p-8 shadow-lg">
                <!-- Form Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Subscribe to Our Newsletter</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Get the latest training resources, articles, and insights delivered directly to your inbox. Join our community of disciple makers committed to using media strategically for Kingdom impact.
                    </p>
                </div>

                <!-- Gospel Ambition Web Forms Shortcode -->
                <div class="newsletter-form-wrapper">
                    <?php echo do_shortcode( '[go_display_opt_in source="kt_news" name="Kingdom.Training"]' ); ?>
                </div>

                <!-- What to Expect Section -->
                <div class="mt-10 pt-8 border-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">What to Expect</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Latest articles and insights on Media to Disciple Making Movements</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Practical tools and strategies for disciple makers</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Stories from the field and testimonies of impact</span>
                        </li>
                    </ul>
                </div>

                <!-- Privacy Statement -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p>
                        We respect your privacy. Unsubscribe at any time. 
                        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'privacy' ) ) ); ?>" class="text-primary-600 hover:text-primary-700 underline">
                            Learn more about our privacy policy
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Style the Gospel Ambition Web Forms shortcode to match the theme */
.newsletter-form-wrapper .go-opt-in__form {
    width: 100%;
}

.newsletter-form-wrapper .go-opt-in__form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.newsletter-form-wrapper .go-opt-in__form .input-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.newsletter-form-wrapper .go-opt-in__form .input-group:last-of-type {
    grid-template-columns: 1fr;
}

.newsletter-form-wrapper .go-opt-in__form input[type="text"],
.newsletter-form-wrapper .go-opt-in__form input[type="email"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s;
}

.newsletter-form-wrapper .go-opt-in__form input[type="text"]:focus,
.newsletter-form-wrapper .go-opt-in__form input[type="email"]:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.newsletter-form-wrapper .go-opt-in__form input[type="checkbox"] {
    margin-right: 0.5rem;
    width: auto;
}

.newsletter-form-wrapper .go-opt-in__form button {
    width: 100%;
    padding: 1rem;
    background-color: #3b82f6;
    color: white;
    font-weight: 600;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background-color 0.2s;
    margin-top: 1rem;
}

.newsletter-form-wrapper .go-opt-in__form button:hover {
    background-color: #2563eb;
}

.newsletter-form-wrapper .go-opt-in__form button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.newsletter-form-wrapper .cf-turnstile {
    margin: 1rem 0;
    display: flex;
    justify-content: center;
}

.newsletter-form-wrapper .dt-form-error {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: block;
}

.newsletter-form-wrapper .dt-form-success {
    color: #16a34a;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: block;
}
</style>

<?php get_footer(); ?>
