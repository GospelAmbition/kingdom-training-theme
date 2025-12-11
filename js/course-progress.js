// Course Progress Tracker
// Tracks user progress through the 10-step MVP Strategy Course
// Stores progress in localStorage

(function() {
    'use strict';
    
    const STORAGE_KEY = 'kt_course_progress';
    const TOTAL_STEPS = 10;
    
    // Get current step slug from URL
    function getCurrentStepSlug() {
        const path = window.location.pathname;
        // Match strategy-course slug, handling language prefixes (e.g., /en/strategy-course/slug or /strategy-course/slug)
        const match = path.match(/(?:\/[a-z]{2}\/)?strategy-course\/([^\/\?]+)/);
        return match ? match[1] : null;
    }
    
    // Extract slug from any URL
    function extractSlugFromUrl(url) {
        // Handle both absolute and relative URLs
        const urlObj = url.startsWith('http') ? new URL(url) : new URL(url, window.location.origin);
        const path = urlObj.pathname;
        // Match strategy-course slug, handling language prefixes
        const match = path.match(/(?:\/[a-z]{2}\/)?strategy-course\/([^\/\?]+)/);
        return match ? match[1] : null;
    }
    
    // Get progress from localStorage
    function getProgress() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                return JSON.parse(stored);
            }
        } catch (e) {
            console.error('Error reading progress from localStorage:', e);
        }
        return { completedSteps: [] };
    }
    
    // Save progress to localStorage
    function saveProgress(progress) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(progress));
        } catch (e) {
            console.error('Error saving progress to localStorage:', e);
        }
    }
    
    // Mark a step as visited/completed
    function markStepVisited(slug) {
        if (!slug) {
            console.log('markStepVisited called with no slug');
            return;
        }
        
        const progress = getProgress();
        if (!progress.completedSteps) {
            progress.completedSteps = [];
        }
        
        if (!progress.completedSteps.includes(slug)) {
            progress.completedSteps.push(slug);
            saveProgress(progress);
            console.log('Step marked as visited:', slug, 'Total:', progress.completedSteps.length); // Debug log
            updateProgressBar();
        } else {
            console.log('Step already visited:', slug); // Debug log
        }
    }
    
    // Update the progress bar display
    function updateProgressBar() {
        const progressBar = document.getElementById('course-progress-bar');
        if (!progressBar) {
            console.log('Progress bar element not found');
            return;
        }
        
        const progress = getProgress();
        const completedCount = progress.completedSteps ? progress.completedSteps.length : 0;
        const percentage = Math.min((completedCount / TOTAL_STEPS) * 100, 100);
        
        console.log('Updating progress bar:', completedCount, 'steps completed,', percentage + '%'); // Debug log
        
        // Update progress fill
        const progressFill = document.getElementById('progress-fill');
        if (progressFill) {
            progressFill.style.width = percentage + '%';
        }
        
        // Update progress text
        const progressText = document.getElementById('progress-text');
        if (progressText) {
            progressText.textContent = `${completedCount} of ${TOTAL_STEPS} steps completed`;
        }
        
        // Update message
        const progressMessage = document.getElementById('progress-message');
        if (progressMessage) {
            if (completedCount === 0) {
                progressMessage.textContent = 'Start with Step 1 to begin your M2DMM strategy development journey.';
            } else if (completedCount < TOTAL_STEPS) {
                const remaining = TOTAL_STEPS - completedCount;
                progressMessage.textContent = `Continue with the next step. ${remaining} step${remaining !== 1 ? 's' : ''} remaining.`;
            } else {
                progressMessage.textContent = 'Congratulations! You have completed all 10 steps of the MVP Strategy Course.';
            }
        }
    }
    
    // Initialize progress tracking
    function init() {
        // Wait a bit to ensure DOM is fully loaded
        setTimeout(function() {
            // Mark current step as visited
            const currentSlug = getCurrentStepSlug();
            if (currentSlug) {
                console.log('Tracking step:', currentSlug); // Debug log
                markStepVisited(currentSlug);
            } else {
                console.log('No step slug found in URL:', window.location.pathname); // Debug log
            }
            
            // Update progress bar display
            updateProgressBar();
        }, 200);
        
        // Track clicks on step links to mark them as visited
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href*="/strategy-course/"]');
            if (link) {
                const href = link.getAttribute('href');
                const slug = extractSlugFromUrl(href);
                if (slug) {
                    console.log('Tracking clicked step:', slug); // Debug log
                    // Mark as visited immediately (optimistic update)
                    markStepVisited(slug);
                }
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
