/**
 * Search Modal JavaScript
 * 
 * Handles search modal functionality and live search
 */

(function() {
    'use strict';

    const searchModal = document.getElementById('search-modal');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const searchBackdrop = document.getElementById('search-backdrop');
    const searchClose = document.getElementById('search-close');
    const searchToggle = document.getElementById('search-toggle');
    const mobileSearchToggle = document.getElementById('mobile-search-toggle');
    const mobileSearchTriggers = document.querySelectorAll('.mobile-search-trigger');

    if (!searchModal || !searchInput || !searchResults) {
        return;
    }

    // Get translations from localized script data
    const translations = window.ktTranslations || {
        searchPlaceholder: 'Search strategy courses, articles, and tools...',
        noResults: 'No results found',
        loading: 'Loading...',
        searchStartTyping: 'Start typing to search',
    };

    let searchTimeout = null;
    let isOpen = false;

    function openSearch() {
        isOpen = true;
        searchModal.classList.remove('hidden');
        setTimeout(function() {
            searchInput.focus();
        }, 100);
        document.body.style.overflow = 'hidden';
    }

    function closeSearch() {
        isOpen = false;
        searchModal.classList.add('hidden');
        searchInput.value = '';
        searchResults.innerHTML = '<div class="p-6 text-center text-gray-500">' + translations.searchStartTyping + '</div>';
        document.body.style.overflow = '';
    }

    async function performSearch(query) {
        if (query.length < 2) {
            searchResults.innerHTML = '<div class="p-6 text-center text-gray-500">' + translations.searchStartTyping + '</div>';
            return;
        }

        searchResults.innerHTML = '<div class="p-6 text-center text-gray-500">' + translations.loading + '</div>';

        try {
            // Get current language from localized data
            const currentLang = translations.currentLanguage || '';
            
            // Build search URLs with language parameter if available
            const langParam = currentLang ? '&lang=' + encodeURIComponent(currentLang) : '';
            const coursesUrl = '/wp-json/wp/v2/strategy-course?search=' + encodeURIComponent(query) + '&per_page=5' + langParam;
            const toolsUrl = '/wp-json/wp/v2/tools?search=' + encodeURIComponent(query) + '&per_page=5' + langParam;
            const articlesUrl = '/wp-json/wp/v2/articles?search=' + encodeURIComponent(query) + '&per_page=5' + langParam;
            
            // Search courses, tools, and articles via REST API
            const [coursesResponse, toolsResponse, articlesResponse] = await Promise.all([
                fetch(coursesUrl),
                fetch(toolsUrl),
                fetch(articlesUrl)
            ]);

            // Helper function to safely parse response
            async function safeParseResponse(response) {
                if (!response.ok) {
                    return [];
                }
                try {
                    const data = await response.json();
                    return Array.isArray(data) ? data : [];
                } catch (error) {
                    return [];
                }
            }

            // Parse all responses with error handling
            const [courses, tools, articles] = await Promise.all([
                safeParseResponse(coursesResponse),
                safeParseResponse(toolsResponse),
                safeParseResponse(articlesResponse)
            ]);

            const allResults = [
                ...courses.map(function(item) { return { ...item, type: 'strategy-course' }; }),
                ...tools.map(function(item) { return { ...item, type: 'tools' }; }),
                ...articles.map(function(item) { return { ...item, type: 'article' }; })
            ];

            if (allResults.length === 0) {
                searchResults.innerHTML = '<div class="p-6 text-center text-gray-500">' + translations.noResults + '</div>';
                return;
            }

            // Sort results - prioritize title matches
            const queryLower = query.toLowerCase();
            allResults.sort(function(a, b) {
                const aTitle = a.title.rendered.toLowerCase();
                const bTitle = b.title.rendered.toLowerCase();
                const aTitleMatch = aTitle.includes(queryLower);
                const bTitleMatch = bTitle.includes(queryLower);
                if (aTitleMatch && !bTitleMatch) return -1;
                if (!aTitleMatch && bTitleMatch) return 1;
                return 0;
            });

            // Render results
            let html = '<ul class="divide-y divide-gray-100">';
            allResults.forEach(function(result) {
                let linkPath;
                if (result.type === 'strategy-course') {
                    linkPath = 'strategy-course';
                } else if (result.type === 'tools') {
                    linkPath = 'tools';
                } else if (result.type === 'article') {
                    linkPath = 'articles';
                }
                const link = '/' + linkPath + '/' + result.slug;
                
                html += '<li>';
                html += '<a href="' + link + '" class="block p-4 hover:bg-gray-50 transition-colors">';
                html += '<h4 class="font-medium text-gray-900 break-words">' + result.title.rendered + '</h4>';
                if (result.excerpt && result.excerpt.rendered) {
                    const excerpt = result.excerpt.rendered.replace(/<[^>]*>/g, '').substring(0, 100);
                    html += '<p class="text-sm text-gray-500 mt-1 line-clamp-2">' + excerpt + '</p>';
                }
                html += '</a>';
                html += '</li>';
            });
            html += '</ul>';

            searchResults.innerHTML = html;
        } catch (error) {
            console.error('Search error:', error);
            searchResults.innerHTML = '<div class="p-6 text-center text-red-500">Search failed. Please try again.</div>';
        }
    }

    // Event Listeners
    if (searchToggle) {
        searchToggle.addEventListener('click', openSearch);
    }

    if (mobileSearchToggle) {
        mobileSearchToggle.addEventListener('click', openSearch);
    }

    mobileSearchTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            // Close mobile menu first
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            if (mobileMenu) {
                mobileMenu.classList.add('translate-x-full');
                mobileMenu.classList.remove('translate-x-0');
            }
            if (mobileMenuOverlay) {
                mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
            }
            // Then open search
            openSearch();
        });
    });

    if (searchClose) {
        searchClose.addEventListener('click', closeSearch);
    }

    if (searchBackdrop) {
        searchBackdrop.addEventListener('click', closeSearch);
    }

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch(e.target.value);
        }, 300);
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closeSearch();
        }
    });

    // Open search with keyboard shortcut (Cmd/Ctrl + K)
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            if (isOpen) {
                closeSearch();
            } else {
                openSearch();
            }
        }
    });
})();
