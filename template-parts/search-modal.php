<?php
/**
 * Search Modal Template Part
 * 
 * @package KingdomTraining
 */
?>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div id="search-backdrop" class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Modal Content -->
    <div class="relative flex items-start justify-center pt-20 px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
            <!-- Search Input -->
            <div class="relative">
                <input 
                    type="text" 
                    id="search-input"
                    class="w-full px-6 py-4 text-lg border-0 rounded-t-lg focus:outline-none focus:ring-0"
                    placeholder="<?php echo esc_attr( kt_t( 'search_placeholder_courses_tools' ) ); ?>"
                    autocomplete="off"
                >
                <button 
                    type="button" 
                    id="search-close"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    aria-label="<?php echo esc_attr( kt_t( 'ui_close' ) ); ?>"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Search Results -->
            <div id="search-results" class="border-t border-gray-100 max-h-96 overflow-y-auto">
                <!-- Results will be populated by JavaScript -->
                <div class="p-6 text-center text-gray-500">
                    <?php kt_e( 'search_start_typing' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
