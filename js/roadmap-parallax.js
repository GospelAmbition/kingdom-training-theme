// Roadmap Parallax Background Effect
(function() {
    'use strict';
    
    const parallaxElement = document.querySelector('.roadmap-parallax');
    if (!parallaxElement) return;
    
    const section = document.getElementById('mvp-course-steps-section');
    if (!section) return;
    
    let ticking = false;
    
    function updateParallax() {
        if (ticking) return;
        
        requestAnimationFrame(function() {
            const rect = section.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const sectionTop = section.offsetTop;
            
            // Calculate how much of the section is visible
            const sectionStart = sectionTop - scrollTop;
            const sectionEnd = sectionStart + section.offsetHeight;
            
            // Only animate when section is in or near viewport
            if (sectionEnd < 0 || sectionStart > windowHeight) {
                ticking = false;
                return;
            }
            
            // Calculate parallax offset (moves slower than scroll)
            // The background moves at 50% speed, creating parallax effect
            const parallaxSpeed = 0.5;
            const scrollProgress = scrollTop - sectionTop;
            const offset = scrollProgress * parallaxSpeed;
            
            // Apply transform to create parallax effect
            parallaxElement.style.transform = `translateY(${offset}px)`;
            
            ticking = false;
        });
        
        ticking = true;
    }
    
    // Throttled scroll event listener
    window.addEventListener('scroll', updateParallax, { passive: true });
    window.addEventListener('resize', updateParallax, { passive: true });
    
    // Initial call
    updateParallax();
})();
