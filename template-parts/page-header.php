<?php
/**
 * Page Header Template Part
 * 
 * @param array $args {
 *     @type string $title       Page title
 *     @type string $description Page description
 *     @type string $bg_class    Background CSS class
 * }
 * 
 * @package KingdomTraining
 */

$title       = $args['title'] ?? '';
$description = $args['description'] ?? '';
$bg_class    = $args['bg_class'] ?? 'bg-gradient-to-r from-secondary-900 to-secondary-700';
$show_neuralnet = $args['show_neuralnet'] ?? false;
$show_ideas_background = $args['show_ideas_background'] ?? false;
$show_llm_background = $args['show_llm_background'] ?? false;
?>

<section class="relative py-24 md:py-32 lg:py-40 <?php echo esc_attr( $bg_class ); ?> text-white overflow-hidden">
    <?php if ( $show_neuralnet ) : ?>
        <!-- Neural Network Background -->
        <div class="neuralnet-background absolute inset-0 z-0">
            <canvas id="neural-canvas"></canvas>
        </div>
    <?php endif; ?>
    
    <?php if ( $show_ideas_background ) : ?>
        <!-- Ideas Background - Books & Sparks -->
        <div class="ideas-background absolute inset-0 z-0">
            <div class="books-container" id="books-container"></div>
            <div class="sparks-container" id="sparks-container"></div>
        </div>
    <?php endif; ?>
    
    <?php if ( $show_llm_background ) : ?>
        <!-- LLM Background - Code Animation (Three Columns) -->
        <div class="llm-background absolute inset-0 z-0">
            <div class="code-container code-container-left" id="code-container-left"></div>
            <div class="code-container code-container-middle" id="code-container-middle"></div>
            <div class="code-container code-container-right" id="code-container-right"></div>
        </div>
    <?php endif; ?>
    
    <div class="container-custom relative z-10">
        <div class="max-w-3xl text-left">
            <?php if ( $title ) : ?>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                    <?php echo esc_html( $title ); ?>
                </h1>
            <?php endif; ?>
            
            <?php if ( $description ) : ?>
                <p class="text-lg text-secondary-100 leading-relaxed">
                    <?php echo esc_html( $description ); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>
