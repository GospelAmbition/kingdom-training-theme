/**
 * Course Progress Tracker
 * Manages course step completion using localStorage (no login required)
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'strategy_course_progress';

    /**
     * Get all completed course step slugs from localStorage
     */
    function getCompletedSteps() {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return new Set();
        }

        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                const steps = JSON.parse(stored);
                return new Set(Array.isArray(steps) ? steps : []);
            }
        } catch (error) {
            console.error('Error reading course progress from localStorage:', error);
        }

        return new Set();
    }

    /**
     * Mark a course step as completed by slug
     */
    function markStepCompleted(slug) {
        if (typeof window === 'undefined' || typeof localStorage === 'undefined') {
            return;
        }

        try {
            const completed = getCompletedSteps();
            if (!completed.has(slug)) {
                completed.add(slug);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(completed)));
                
                // Dispatch custom event to notify other components
                window.dispatchEvent(new CustomEvent('courseProgressUpdated'));
            }
        } catch (error) {
            console.error('Error saving course progress to localStorage:', error);
        }
    }

    /**
     * Check if a course step is completed
     */
    function isStepCompleted(slug) {
        const completed = getCompletedSteps();
        return completed.has(slug);
    }

    /**
     * Get progress percentage for a list of course steps
     */
    function getProgressPercentage(stepSlugs) {
        if (!stepSlugs || stepSlugs.length === 0) return 0;
        
        const completed = getCompletedSteps();
        const completedCount = stepSlugs.filter(function(slug) {
            return completed.has(slug);
        }).length;
        
        return Math.round((completedCount / stepSlugs.length) * 100);
    }

    /**
     * Get completed count for a list of course steps
     */
    function getCompletedCount(stepSlugs) {
        if (!stepSlugs || stepSlugs.length === 0) return 0;
        
        const completed = getCompletedSteps();
        return stepSlugs.filter(function(slug) {
            return completed.has(slug);
        }).length;
    }

    /**
     * Get progress message based on completion status
     */
    function getProgressMessage(completedCount, total) {
        if (completedCount === 0) {
            return 'Start your journey through the MVP Strategy Course!';
        } else if (completedCount === total) {
            return 'Congratulations! You\'ve completed all steps of the MVP Strategy Course!';
        } else {
            return 'Keep going! You\'re making great progress.';
        }
    }

    /**
     * Initialize progress tracker
     */
    function initProgressTracker() {
        // Find all progress indicator containers
        const progressContainers = document.querySelectorAll('[data-progress-tracker]');
        
        progressContainers.forEach(function(container) {
            const stepSlugsJson = container.getAttribute('data-step-slugs');
            const currentSlug = container.getAttribute('data-current-slug');
            
            if (!stepSlugsJson) {
                console.warn('Progress tracker: No step slugs provided');
                return;
            }

            let stepSlugs;
            try {
                stepSlugs = JSON.parse(stepSlugsJson);
            } catch (error) {
                console.error('Progress tracker: Invalid step slugs JSON', error);
                return;
            }

            // Mark current step as completed
            if (currentSlug) {
                markStepCompleted(currentSlug);
            }

            // Calculate progress
            const progress = getProgressPercentage(stepSlugs);
            const completedCount = getCompletedCount(stepSlugs);
            const total = stepSlugs.length;

            // Update progress bar
            const progressBar = container.querySelector('.progress-bar-fill');
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }

            // Update completed count
            const countElement = container.querySelector('.progress-count');
            if (countElement) {
                countElement.textContent = completedCount + ' / ' + total;
            }

            // Update progress message
            const messageElement = container.querySelector('.progress-message');
            if (messageElement) {
                messageElement.textContent = getProgressMessage(completedCount, total);
            }

            // Update progress percentage
            const percentageElement = container.querySelector('.progress-percentage');
            if (percentageElement) {
                percentageElement.textContent = progress + '%';
            }
        });

        // Listen for storage changes (from other tabs/windows)
        window.addEventListener('storage', function(e) {
            if (e.key === STORAGE_KEY) {
                // Re-initialize to update progress
                initProgressTracker();
            }
        });

        // Listen for custom events (from same tab)
        window.addEventListener('courseProgressUpdated', function() {
            initProgressTracker();
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProgressTracker);
    } else {
        initProgressTracker();
    }

    // Expose functions globally for manual use if needed
    window.KTCourseProgress = {
        markStepCompleted: markStepCompleted,
        isStepCompleted: isStepCompleted,
        getProgressPercentage: getProgressPercentage,
        getCompletedCount: getCompletedCount,
        getCompletedSteps: getCompletedSteps,
        init: initProgressTracker
    };

})();
