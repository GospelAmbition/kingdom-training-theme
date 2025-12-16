<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr( kt_get_dir_attribute() ); ?>">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php
    // Add language meta tag
    $current_lang = kt_get_current_language();
    if ( $current_lang ) {
        echo '<meta name="language" content="' . esc_attr( $current_lang ) . '">' . "\n";
    }
    
    // Add theme color for mobile browsers
    echo '<meta name="theme-color" content="#1a365d">' . "\n";
    echo '<meta name="msapplication-TileColor" content="#1a365d">' . "\n";
    
    // Add format detection (disable phone number detection)
    echo '<meta name="format-detection" content="telephone=no">' . "\n";
    ?>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen flex flex-col' ); ?>>
<?php wp_body_open(); ?>

<?php
// Get current page path for active state detection
$current_path = $_SERVER['REQUEST_URI'] ?? '/';
$current_lang = kt_get_current_language();

// Helper function to check if link is active
function kt_is_nav_active( $path ) {
    global $current_path;
    
    // Check for home page
    if ( $path === '/' ) {
        return $current_path === '/' || $current_path === '' || is_front_page();
    }
    
    // Check for articles section (archive and single pages)
    if ( $path === '/articles' ) {
        return strpos( $current_path, '/articles' ) !== false 
            || is_singular( 'article' ) 
            || is_post_type_archive( 'article' );
    }
    
    // Check for tools section (archive and single pages)
    if ( $path === '/tools' ) {
        return strpos( $current_path, '/tools' ) !== false 
            || is_singular( 'tool' ) 
            || is_post_type_archive( 'tool' );
    }
    
    // Check for strategy course section (archive and single pages)
    if ( $path === '/strategy-course' ) {
        return strpos( $current_path, '/strategy-course' ) !== false 
            || is_singular( 'strategy_course' ) 
            || is_post_type_archive( 'strategy_course' );
    }
    
    // Default: check if path is in current path
    return strpos( $current_path, $path ) !== false;
}

// Helper function to get nav link classes
function kt_get_nav_class( $path ) {
    $base = 'text-gray-700 hover:text-primary-500 font-medium transition-colors relative py-1';
    $active = "text-primary-600 after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-full after:h-0.5 after:bg-primary-500 after:rounded-full";
    return kt_is_nav_active( $path ) ? "{$base} {$active}" : $base;
}

function kt_get_mobile_nav_class( $path ) {
    $base = 'block py-3 px-4 text-gray-700 hover:text-primary-500 hover:bg-gray-50 font-medium transition-colors border-b border-gray-100';
    $active = 'text-primary-600 bg-primary-50';
    return kt_is_nav_active( $path ) ? "{$base} {$active}" : $base;
}

$logo_url = 'https://ai.kingdom.training/wp-content/uploads/2025/12/kt-logo-header.webp';
$is_front_page = is_front_page();
?>

<header class="bg-white fixed top-0 left-0 right-0 z-50">
    <nav class="container-custom <?php echo $is_front_page ? 'py-4' : 'py-6'; ?>">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="<?php echo esc_url( kt_get_language_url( '/' ) ); ?>" class="flex items-center space-x-3 z-50">
                <img
                    src="<?php echo esc_url( $logo_url ); ?>"
                    alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    width="200"
                    height="40"
                    class="h-10 w-auto"
                    style="aspect-ratio: 200 / 40"
                >
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="<?php echo esc_url( kt_get_language_url( '/' ) ); ?>" class="<?php echo esc_attr( kt_get_nav_class( '/' ) ); ?>">
                    <?php kt_e( 'nav_home' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>" class="<?php echo esc_attr( kt_get_nav_class( '/strategy-course' ) ); ?>">
                    <?php kt_e( 'nav_strategy_course' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>" class="<?php echo esc_attr( kt_get_nav_class( '/articles' ) ); ?>">
                    <?php kt_e( 'nav_articles' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>" class="<?php echo esc_attr( kt_get_nav_class( '/tools' ) ); ?>">
                    <?php kt_e( 'nav_tools' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span><?php kt_e( 'nav_newsletter' ); ?></span>
                </a>
                <button
                    type="button"
                    id="search-toggle"
                    class="text-gray-700 hover:text-primary-500 transition-colors"
                    aria-label="<?php echo esc_attr( kt_t( 'nav_search' ) ); ?>"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <div class="flex items-center">
                    <?php kt_language_selector( array( 'dropdown' => true, 'show_flags' => false ) ); ?>
                </div>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( admin_url() ); ?>" class="text-gray-700 hover:text-primary-500 transition-colors" aria-label="<?php echo esc_attr( 'Go to Admin' ); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( kt_get_language_url( '/login' ) ); ?>" class="text-gray-700 hover:text-primary-500 transition-colors" aria-label="<?php echo esc_attr( kt_t( 'nav_login' ) ); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16l4-4m0 0l-4-4m4 4H3m5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Actions -->
            <div class="md:hidden flex items-center gap-4 z-50">
                <button
                    type="button"
                    id="mobile-search-toggle"
                    class="text-gray-700 hover:text-primary-500 transition-colors"
                    aria-label="<?php echo esc_attr( kt_t( 'nav_search' ) ); ?>"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <?php kt_language_selector( array( 'dropdown' => true, 'show_flags' => false ) ); ?>
                <button
                    type="button"
                    id="mobile-menu-toggle"
                    class="text-gray-700 hover:text-primary-500 transition-colors"
                    aria-label="<?php echo esc_attr( kt_t( 'ui_toggle_menu' ) ); ?>"
                    aria-expanded="false"
                >
                    <svg id="menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <!-- Mobile Menu Sidebar -->
    <div id="mobile-menu" class="fixed top-0 right-0 h-full w-80 max-w-[85vw] bg-white shadow-xl z-50 md:hidden transform transition-transform duration-300 ease-in-out translate-x-full">
        <div class="flex flex-col h-full">
            <!-- Mobile Menu Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <span class="text-lg font-semibold text-gray-800"><?php kt_e( 'nav_menu' ); ?></span>
                <button
                    type="button"
                    id="mobile-menu-close"
                    class="text-gray-700 hover:text-primary-500 transition-colors"
                    aria-label="<?php echo esc_attr( kt_t( 'ui_close' ) ); ?>"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation Links -->
            <nav class="flex-1 overflow-y-auto">
                <a href="<?php echo esc_url( kt_get_language_url( '/' ) ); ?>" class="<?php echo esc_attr( kt_get_mobile_nav_class( '/' ) ); ?>">
                    <?php kt_e( 'nav_home' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/strategy-course' ) ); ?>" class="<?php echo esc_attr( kt_get_mobile_nav_class( '/strategy-course' ) ); ?>">
                    <?php kt_e( 'nav_strategy_course' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/articles' ) ); ?>" class="<?php echo esc_attr( kt_get_mobile_nav_class( '/articles' ) ); ?>">
                    <?php kt_e( 'nav_articles' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/tools' ) ); ?>" class="<?php echo esc_attr( kt_get_mobile_nav_class( '/tools' ) ); ?>">
                    <?php kt_e( 'nav_tools' ); ?>
                </a>
                <a href="<?php echo esc_url( kt_get_language_url( '/newsletter' ) ); ?>" class="block py-3 px-4 text-gray-700 hover:text-primary-500 hover:bg-gray-50 font-medium transition-colors border-b border-gray-100">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <?php kt_e( 'nav_newsletter' ); ?>
                    </span>
                </a>
            </nav>

            <!-- Mobile Menu Footer Actions -->
            <div class="p-4 border-t border-gray-200 space-y-3">
                <div class="px-4 py-2">
                    <?php kt_language_selector( array( 'dropdown' => true, 'show_flags' => true ) ); ?>
                </div>
                <button
                    type="button"
                    class="mobile-search-trigger w-full flex items-center justify-center gap-2 py-2 px-4 text-gray-700 hover:text-primary-500 hover:bg-gray-50 font-medium transition-colors rounded-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span><?php kt_e( 'nav_search' ); ?></span>
                </button>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( admin_url() ); ?>" class="w-full flex items-center justify-center gap-2 py-2 px-4 text-gray-700 hover:text-primary-500 hover:bg-gray-50 font-medium transition-colors rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <span>Admin</span>
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( kt_get_language_url( '/login' ) ); ?>" class="w-full flex items-center justify-center gap-2 py-2 px-4 text-gray-700 hover:text-primary-500 hover:bg-gray-50 font-medium transition-colors rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16l4-4m0 0l-4-4m4 4H3m5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <span><?php kt_e( 'nav_login' ); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Search Modal -->
<?php get_template_part( 'template-parts/search-modal' ); ?>

<main class="flex-grow pt-16">
