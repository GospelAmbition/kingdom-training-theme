<?php
/**
 * Template Name: Login Page
 * 
 * @package KingdomTraining
 */

// Redirect if already logged in
if ( is_user_logged_in() ) {
    wp_redirect( kt_get_language_url( '/' ) );
    exit;
}

get_header();

// SEO Meta
kt_render_seo_meta( array(
    'title'       => kt_t( 'nav_login' ) . ' - ' . get_bloginfo( 'name' ),
    'description' => 'Login to your Kingdom.Training account.',
    'url'         => kt_get_language_url( '/login' ),
) );
?>

<section class="py-16 bg-background-50 min-h-[60vh] flex items-center">
    <div class="container-custom">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">
                    <?php kt_e( 'nav_login' ); ?>
                </h1>
                
                <?php
                // Display login form
                $args = array(
                    'echo'           => true,
                    'redirect'       => kt_get_language_url( '/' ),
                    'form_id'        => 'loginform',
                    'label_username' => __( 'Username or Email Address' ),
                    'label_password' => __( 'Password' ),
                    'label_remember' => __( 'Remember Me' ),
                    'label_log_in'   => kt_t( 'nav_login' ),
                    'id_username'    => 'user_login',
                    'id_password'    => 'user_pass',
                    'id_remember'    => 'rememberme',
                    'id_submit'      => 'wp-submit',
                    'remember'       => true,
                    'value_username' => '',
                    'value_remember' => false,
                );
                ?>
                
                <form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post" class="space-y-4">
                    <div>
                        <label for="user_login" class="block text-sm font-medium text-gray-700 mb-1">
                            Username or Email Address
                        </label>
                        <input 
                            type="text" 
                            name="log" 
                            id="user_login" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="user_pass" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input 
                            type="password" 
                            name="pwd" 
                            id="user_pass" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                            required
                        >
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="rememberme" id="rememberme" value="forever" class="mr-2 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                            <span class="text-sm text-gray-600">Remember Me</span>
                        </label>
                        
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-sm text-primary-500 hover:text-primary-600">
                            Forgot Password?
                        </a>
                    </div>
                    
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url( admin_url() ); ?>">
                    
                    <button 
                        type="submit" 
                        name="wp-submit" 
                        id="wp-submit" 
                        class="w-full px-6 py-4 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors"
                    >
                        <?php kt_e( 'nav_login' ); ?>
                    </button>
                </form>
                
                <?php if ( get_option( 'users_can_register' ) ) : ?>
                    <p class="mt-6 text-center text-gray-600">
                        Don't have an account? 
                        <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="text-primary-500 hover:text-primary-600 font-medium">
                            Register
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
