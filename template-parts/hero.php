<?php
/**
 * Hero Section Template Part
 * 
 * @param array $args {
 *     @type string $subtitle    Hero subtitle text
 *     @type string $title       Hero title text
 *     @type string $description Hero description text
 *     @type string $cta_text    Call-to-action button text
 *     @type string $cta_link    Call-to-action button URL
 * }
 * 
 * @package KingdomTraining
 */

$subtitle    = $args['subtitle'] ?? '';
$title       = $args['title'] ?? '';
$description = $args['description'] ?? '';
$cta_text    = $args['cta_text'] ?? '';
$cta_link    = $args['cta_link'] ?? '';
?>

<section class="relative overflow-hidden bg-gradient-to-br from-secondary-900 via-secondary-800 to-secondary-700 text-white min-h-[720px] md:min-h-[840px]">
    <!-- GenMap Background Animation -->
    <div class="genmap-background hidden md:block" data-platform="desktop">
        <!-- Text Input Layer (Bottom) -->
        <div class="genmap-text-layer">
            <div class="text-prompt" id="genmap-text-prompt"></div>
        </div>
        
        <!-- Processing Particles -->
        <div class="genmap-particles-container" id="genmap-particles-container"></div>
        
        <!-- Video Generation Layer (Middle) -->
        <div class="genmap-video-generation">
            <div class="genmap-frame-container" id="genmap-frame-container"></div>
        </div>
        
        <!-- Video Player Output Layer (Top) -->
        <div class="genmap-youtube-layer">
            <div class="youtube-player">
                <div class="player-screen" id="genmap-player-screen">
                    <!-- YouTube Play Button -->
                    <div class="play-button youtube-play">
                        <svg viewBox="0 0 68 48">
                            <path d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#FF0000"/>
                            <path d="M 45,24 27,14 27,34" fill="#FFFFFF"/>
                        </svg>
                    </div>
                </div>
                <div class="player-controls">
                    <div class="progress-bar">
                        <div class="progress-fill" id="genmap-progress-fill"></div>
                    </div>
                    <div class="control-buttons">
                        <div class="control-icon play-icon"></div>
                        <div class="time-display">0:00 / 2:45</div>
                        <div class="right-controls">
                            <div class="control-icon volume-icon"></div>
                            <div class="control-icon settings-icon"></div>
                            <div class="control-icon fullscreen-icon"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Background gradient overlay -->
    <div class="absolute inset-0 opacity-20 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-transparent"></div>
    </div>
    
    <div class="container-custom py-[7.2rem] md:py-[12rem] relative z-10">
        <div class="hero-content max-w-4xl text-left pt-[20px] pb-[20px]">
            <?php if ( $subtitle ) : ?>
                <p class="text-lg md:text-xl text-primary-300 mb-4 font-medium">
                    <?php echo esc_html( $subtitle ); ?>
                </p>
            <?php endif; ?>
            
            <?php if ( $title ) : ?>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <?php echo esc_html( $title ); ?>
                </h1>
            <?php endif; ?>
            
            <?php if ( $description ) : ?>
                <p class="text-lg md:text-xl text-secondary-100 mb-8 max-w-2xl leading-relaxed">
                    <?php echo esc_html( $description ); ?>
                </p>
            <?php endif; ?>
            
            <?php if ( $cta_text && $cta_link ) : ?>
                <a 
                    href="<?php echo esc_url( $cta_link ); ?>"
                    class="inline-flex items-center justify-center px-8 py-4 bg-accent-600 hover:bg-accent-500 text-secondary-900 font-semibold rounded-lg transition-colors duration-200 text-lg shadow-lg hover:shadow-xl mb-8"
                >
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Wave Divider -->
    <div class="relative h-0 bg-white overflow-visible">
        <svg
            class="absolute top-0 left-0 w-full"
            style="height: 4rem; margin-top: -4rem; display: block;"
            preserveAspectRatio="none"
            viewBox="0 0 1440 54"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill="#ffffff"
                d="M0 32L120 37.3C240 43 480 53 720 53.3C960 53 1200 43 1320 37.3L1440 32V54H1320C1200 54 960 54 720 54C480 54 240 54 120 54H0V32Z"
            />
        </svg>
    </div>
</section>
