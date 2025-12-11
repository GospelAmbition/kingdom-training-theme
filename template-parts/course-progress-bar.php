<?php
/**
 * Course Progress Bar Template Part
 * 
 * Displays progress through the 10-step MVP Strategy Course
 * Progress is tracked in localStorage
 * 
 * @package KingdomTraining
 */
?>

<div id="course-progress-bar" class="bg-white rounded-lg border border-primary-200 shadow-sm p-6 mt-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900">Your Progress</h3>
        <span id="progress-text" class="text-sm text-gray-600">0 of 10 steps completed</span>
    </div>
    
    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2 mb-4 overflow-hidden">
        <div id="progress-fill" class="bg-primary-500 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
    </div>
    
    <!-- Instruction Text -->
    <p id="progress-message" class="text-sm text-gray-600">
        Start with Step 1 to begin your M2DMM strategy development journey.
    </p>
</div>
