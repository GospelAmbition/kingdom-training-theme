/**
 * Mobile Menu JavaScript
 * 
 * Handles mobile menu toggle functionality
 */

(function() {
    'use strict';

    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const menuIconOpen = document.getElementById('menu-icon-open');
    const menuIconClose = document.getElementById('menu-icon-close');

    if (!mobileMenuToggle || !mobileMenu || !mobileMenuOverlay) {
        return;
    }

    let isOpen = false;

    function openMenu() {
        isOpen = true;
        mobileMenu.classList.remove('translate-x-full');
        mobileMenu.classList.add('translate-x-0');
        mobileMenuOverlay.classList.remove('opacity-0', 'pointer-events-none');
        mobileMenuOverlay.classList.add('opacity-100');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
        
        if (menuIconOpen && menuIconClose) {
            menuIconOpen.classList.add('hidden');
            menuIconClose.classList.remove('hidden');
        }
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        isOpen = false;
        mobileMenu.classList.add('translate-x-full');
        mobileMenu.classList.remove('translate-x-0');
        mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
        mobileMenuOverlay.classList.remove('opacity-100');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        
        if (menuIconOpen && menuIconClose) {
            menuIconOpen.classList.remove('hidden');
            menuIconClose.classList.add('hidden');
        }
        
        // Restore body scroll
        document.body.style.overflow = '';
    }

    function toggleMenu() {
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    // Event Listeners
    mobileMenuToggle.addEventListener('click', toggleMenu);
    
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', closeMenu);
    }
    
    mobileMenuOverlay.addEventListener('click', closeMenu);

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closeMenu();
        }
    });

    // Close menu on window resize (if switching to desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && isOpen) { // md breakpoint
            closeMenu();
        }
    });
})();
