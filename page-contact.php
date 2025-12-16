<?php
/**
 * Template Name: Contact Page
 *
 * @package KingdomTraining
 */

get_header();
?>

<?php kt_render_edit_link(); ?>

<?php
// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'nav_contact' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => 'Get in touch with Kingdom.Training. We would love to hear from you.',
    'url'         => kt_get_language_url( '/contact' ),
) );
?>

<!-- Page Header -->
<?php get_template_part( 'template-parts/page-header', null, array(
    'title'       => 'Contact Us',
    'description' => 'Have a question or want to connect? We would love to hear from you.',
    'bg_class'    => 'bg-gradient-to-r from-primary-700 to-primary-500',
) ); ?>

<section class="py-16 bg-white">
    <div class="container-custom">
        <div class="max-w-2xl mx-auto">
            <!-- Contact Form -->
            <div class="bg-background-50 rounded-lg p-8 shadow-lg">
                <!-- Form Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Send Us a Message</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Whether you have a question about our training resources, want to share a testimony, or are interested in partnering with us, we are here to help.
                    </p>
                </div>

                <!-- Contact Form -->
                <?php $cf_token = get_option( 'dt_webform_cf_site_key', '' ); ?>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
                <style>
                    .go-contact-form__error {
                        color: #cc4b37;
                        font-size: 0.875rem;
                        font-weight: bold;
                        margin-top: 0.5rem;
                    }
                    .go-contact-form__success {
                        color: #4CAF50;
                        font-size: 0.875rem;
                        font-weight: bold;
                        margin-top: 0.5rem;
                    }
                    .go-contact-form label {
                        display: block;
                        margin-bottom: 0.5rem;
                        font-weight: 500;
                        color: #374151;
                    }
                    .go-contact-form .form-group {
                        margin-bottom: 1rem;
                    }
                    .go-contact-form input[type="text"],
                    .go-contact-form input[type="email"],
                    .go-contact-form textarea {
                        width: 100%;
                        padding: 0.75rem;
                        border: 1px solid #d1d5db;
                        border-radius: 0.5rem;
                        font-size: 1rem;
                        transition: all 0.2s;
                    }
                    .go-contact-form input:focus,
                    .go-contact-form textarea:focus {
                        outline: none;
                        border-color: #3b82f6;
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                    }
                    .go-contact-form textarea {
                        min-height: 150px;
                        resize: vertical;
                    }
                    .go-contact-form .cf-turnstile {
                        margin: 1rem 0;
                        display: flex;
                        justify-content: center;
                    }
                </style>
                <div class="go-contact-form">
                    <form id="go-contact-form" action="/wp-json/go-webform/contact" method="post">
                        <div class="form-group">
                            <label><strong>Name</strong></label>
                            <input type="text" name="name" placeholder="Your name">
                        </div>
                        <div class="form-group">
                            <label><strong>Email Address</strong> <span style="color: #dc2626">*</span></label>
                            <input type="email" name="email2" placeholder="your@email.com" required>
                            <input type="email" name="email" placeholder="Email" style="display: none">
                        </div>
                        <div class="form-group">
                            <label><strong>Subject</strong></label>
                            <input type="text" name="subject" placeholder="What is this about?">
                        </div>
                        <div class="form-group">
                            <label><strong>Message</strong> <span style="color: #dc2626">*</span></label>
                            <textarea name="message" placeholder="Your message..." required></textarea>
                        </div>

                        <div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $cf_token ); ?>" data-theme="light" data-callback="save_contact_cf"></div>
                        <button id="go-contact-submit" type="submit" class="w-full p-4 bg-primary-500 hover:bg-primary-600 text-white font-semibold border-none rounded-lg cursor-pointer transition-colors mt-2 disabled:opacity-60 disabled:cursor-not-allowed">
                            Send Message
                            <svg id="go-contact-spinner" style="display: none; height: 20px; width: 20px; vertical-align: middle; margin-left: 8px; animation: spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="1"></path>
                            </svg>
                        </button>
                        <div class="go-contact-form__success"></div>
                        <span class="go-contact-form__error"></span>
                    </form>
                </div>
                <style>
                    @keyframes spin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }
                </style>
                <script>
                    let contact_cf_token = null;
                    function save_contact_cf(token){
                        contact_cf_token = token;
                    }

                    let contact_form = document.getElementById('go-contact-form');
                    let contact_error = contact_form.querySelector('.go-contact-form__error');
                    let contact_success = contact_form.querySelector('.go-contact-form__success');

                    contact_form.addEventListener('submit', function(e){
                        e.preventDefault();

                        // Honeypot check
                        let honeypot = contact_form.querySelector('input[name="email"]').value;
                        if ( honeypot ){
                            return;
                        }

                        contact_error.style.display = 'none';
                        contact_success.style.display = 'none';

                        contact_form.querySelector('#go-contact-spinner').style.display = 'inline-block';
                        contact_form.querySelector('#go-contact-submit').disabled = true;

                        let data = {
                            name: contact_form.querySelector('input[name="name"]').value,
                            email: contact_form.querySelector('input[name="email2"]').value,
                            subject: contact_form.querySelector('input[name="subject"]').value,
                            message: contact_form.querySelector('textarea[name="message"]').value,
                            cf_turnstile: contact_cf_token
                        };

                        fetch('/wp-json/go-webform/contact', {
                            method: 'POST',
                            body: JSON.stringify(data),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': '<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>',
                            }
                        }).then(function(response){
                            contact_form.querySelector('#go-contact-spinner').style.display = 'none';
                            contact_form.querySelector('#go-contact-submit').disabled = false;
                            if ( response.status !== 200 ){
                                contact_error.innerHTML = 'There was an error sending your message. Please try again.';
                                contact_error.style.display = 'block';
                            } else {
                                contact_success.innerHTML = 'Thank you! Your message has been sent. We will get back to you soon.';
                                contact_success.style.display = 'block';
                                contact_form.reset();
                            }
                        }).catch(function(error){
                            contact_form.querySelector('#go-contact-spinner').style.display = 'none';
                            contact_form.querySelector('#go-contact-submit').disabled = false;
                            contact_error.innerHTML = 'There was an error sending your message. Please try again.';
                            contact_error.style.display = 'block';
                        });
                    });
                </script>

                <!-- Privacy Statement -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p>
                        We respect your privacy and will never share your information.
                        <a href="<?php echo esc_url( kt_get_language_url( '/privacy' ) ); ?>" class="text-primary-600 hover:text-primary-700 underline">
                            Learn more about our privacy policy
                        </a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
