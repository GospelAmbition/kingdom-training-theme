/**
 * Translation System for Frontend UI Strings
 * Fetches translations from WordPress REST API and provides translation functions
 */

const API_URL = typeof window !== 'undefined' 
  ? '/wp-json' 
  : (import.meta.env.VITE_WORDPRESS_API_URL || 'http://localhost:8888/wp-json');

export interface Translations {
  // Navigation
  nav_home: string;
  nav_articles: string;
  nav_tools: string;
  nav_strategy_course: string;
  nav_strategy_courses: string;
  nav_newsletter: string;
  nav_search: string;
  nav_login: string;
  nav_menu: string;
  nav_about: string;

  // Common UI
  ui_read_more: string;
  ui_view_all: string;
  ui_browse_all: string;
  ui_back_to: string;
  ui_explore: string;
  ui_read_articles: string;
  ui_explore_tools: string;
  ui_select_language: string;
  ui_close: string;
  ui_loading: string;
  ui_loading_course_steps: string;
  ui_searching: string;
  ui_submitting: string;
  ui_submitting_subscription: string;
  ui_logging_in: string;
  ui_toggle_menu: string;

  // Page Headers
  page_latest_articles: string;
  page_featured_tools: string;
  page_key_information: string;
  page_mvp_strategy_course: string;
  page_start_strategy_course: string;
  page_step_curriculum: string;
  page_home: string;
  page_strategy_course: string;
  page_strategy_course_description: string;
  page_articles: string;
  page_tools: string;
  page_newsletter: string;
  page_newsletter_description: string;
  page_about: string;
  page_about_kingdom_training: string;
  page_privacy_policy: string;
  page_login: string;
  page_logged_in: string;
  page_welcome_back: string;

  // Content Messages
  msg_no_articles: string;
  msg_no_tools: string;
  msg_no_content: string;
  msg_discover_supplementary: string;
  msg_discover_more: string;
  content_no_articles_found: string;
  content_no_articles_try: string;
  content_no_tools_found: string;
  content_no_tools_try: string;
  content_categories: string;
  content_tags: string;
  content_additional_resources: string;
  content_additional_article_resources: string;
  content_additional_tool_resources: string;
  content_supplementary_materials: string;
  content_key_information: string;
  content_key_information_m2dmm: string;
  content_digital_disciple_making: string;
  content_heavenly_economy: string;
  content_our_vision: string;
  content_our_mission: string;
  content_how_it_works: string;
  content_our_foundation: string;
  content_media_content: string;
  content_digital_filtering: string;
  content_face_to_face: string;

  // Footer
  footer_quick_links: string;
  footer_our_vision: string;
  footer_subscribe: string;
  footer_privacy_policy: string;
  footer_all_rights: string;
  footer_mission_statement: string;
  footer_scripture_quote: string;
  footer_scripture_citation: string;
  footer_technology_paragraph: string;

  // Newsletter
  newsletter_subscribe: string;
  newsletter_email_placeholder: string;
  newsletter_name_placeholder: string;
  newsletter_success: string;
  newsletter_error: string;

  // Search
  search_placeholder: string;
  search_no_results: string;
  search_results: string;
  search_placeholder_courses_tools: string;
  search_no_results_try: string;
  search_start_typing: string;
  search_start_typing_desc: string;
  search_close: string;
  search_strategy_course: string;
  search_tool: string;

  // Breadcrumbs
  breadcrumb_home: string;
  breadcrumb_articles: string;
  breadcrumb_tools: string;
  breadcrumb_strategy_courses: string;

  // Hero
  hero_explore_resources: string;
  hero_about_us: string;
  hero_description: string;
  hero_subtitle_media_ai: string;
  hero_title_innovate: string;
  hero_cta_explore_resources: string;
  hero_cta_about_us: string;
  hero_newsletter_title: string;

  // Homepage Content (longer text chunks)
  home_mvp_description: string;
  home_newsletter_description: string;
  home_heavenly_economy: string;
  home_mission_statement: string;
  home_loading_steps: string;

  // Navigation & Actions
  nav_back_to_course_overview: string;
  nav_back_to_strategy_courses: string;
  nav_start_step: string;
  nav_start_step_number: string;
  nav_previous: string;
  nav_next: string;
  nav_step: string;
  nav_enroll_mvp: string;
  nav_start_mvp: string;
  nav_subscribe_newsletter: string;
  nav_subscribe_now: string;
  nav_go_to_admin: string;
  nav_log_out: string;
  nav_log_in: string;
  nav_forgot_password: string;

  // Error Messages
  error_course_not_found: string;
  error_course_not_found_desc: string;
  error_article_not_found: string;
  error_article_not_found_desc: string;
  error_tool_not_found: string;
  error_tool_not_found_desc: string;
  error_404_title: string;
  error_404_heading: string;
  error_404_desc: string;
  error_return_home: string;
  error_login_failed: string;
  error_logout_failed: string;
  error_security_token_not_found: string;
  error_subscribe_failed: string;
  error_subscribe_generic: string;
  error_submit_failed: string;

  // Course/Content Specific
  course_strategy_course: string;
  course_article: string;
  course_tool: string;
  course_no_steps_found: string;
  course_complete_plan: string;
  course_flagship_description: string;
  course_progress_your_progress: string;
  course_progress_steps_completed: string;
  course_progress_start_message: string;
  course_progress_complete_message: string;
  course_progress_keep_going: string;

  // Newsletter
  newsletter_stay_connected: string;
  newsletter_default_description: string;
  newsletter_subscribe_title: string;
  newsletter_subscribe_description: string;
  newsletter_what_to_expect: string;
  newsletter_expect_articles: string;
  newsletter_expect_tools: string;
  newsletter_expect_stories: string;
  newsletter_privacy_note: string;
  newsletter_privacy_link: string;
  newsletter_success_message: string;
  newsletter_check_email: string;
  newsletter_subscribed: string;
  newsletter_confirm_subscribe: string;
  newsletter_security_loading: string;
  newsletter_security_complete: string;
  newsletter_try_again: string;

  // Footer
  footer_kingdom_training: string;
  footer_key_definitions: string;

  // Form Labels & Placeholders
  form_username_email: string;
  form_username_email_placeholder: string;
  form_password: string;
  form_password_placeholder: string;
}

// Cache for translations
let translationsCache: Translations | null = null;
let currentLanguage: string | null = null;

/**
 * Fetch translations for a specific language
 */
export async function fetchTranslations(lang?: string | null): Promise<Translations> {
  // Use cached translations if language hasn't changed
  if (translationsCache && currentLanguage === lang) {
    return translationsCache;
  }

  try {
    const langParam = lang ? `?lang=${lang}` : '';
    const response = await fetch(`${API_URL}/gaal/v1/translations${langParam}`);
    
    if (!response.ok) {
      throw new Error(`Failed to fetch translations: ${response.status}`);
    }

    const data = await response.json();
    
    if (data.success && data.translations) {
      translationsCache = data.translations as Translations;
      currentLanguage = lang || null;
      return translationsCache;
    }

    throw new Error('Invalid translation response format');
  } catch (error) {
    console.error('Error fetching translations:', error);
    
    // Return default English translations as fallback
    return getDefaultTranslations();
  }
}

/**
 * Get default English translations (fallback)
 */
function getDefaultTranslations(): Translations {
  return {
    nav_home: 'Home',
    nav_articles: 'Articles',
    nav_tools: 'Tools',
    nav_strategy_course: 'Strategy Course',
    nav_strategy_courses: 'Strategy Courses',
    nav_newsletter: 'Newsletter',
    nav_search: 'Search',
    nav_login: 'Login',
    nav_menu: 'Menu',
    nav_about: 'About',
    ui_read_more: 'Learn more',
    ui_view_all: 'View all',
    ui_browse_all: 'Browse all',
    ui_back_to: 'Back to',
    ui_explore: 'Explore',
    ui_read_articles: 'Read Articles',
    ui_explore_tools: 'Explore Tools',
    ui_select_language: 'Select Language',
    ui_close: 'Close',
    ui_loading: 'Loading...',
    ui_loading_course_steps: 'Loading course steps...',
    ui_searching: 'Searching...',
    ui_submitting: 'Submitting...',
    ui_submitting_subscription: 'Submitting your subscription...',
    ui_logging_in: 'Logging in...',
    ui_toggle_menu: 'Toggle menu',
    page_latest_articles: 'Latest Articles',
    page_featured_tools: 'Featured Tools',
    page_key_information: 'Key Information About Media to Disciple Making Movements',
    page_mvp_strategy_course: 'The MVP: Strategy Course',
    page_start_strategy_course: 'Start Your Strategy Course',
    page_step_curriculum: 'The {count}-Step Curriculum:',
    page_home: 'Home',
    page_strategy_course: 'Strategy Course',
    page_strategy_course_description: 'Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program below to develop your complete M2DMM strategy.',
    page_articles: 'Articles',
    page_tools: 'Tools',
    page_newsletter: 'Newsletter',
    page_newsletter_description: 'Stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements.',
    page_about: 'About Us',
    page_about_kingdom_training: 'About Kingdom.Training',
    page_privacy_policy: 'Privacy Policy',
    page_login: 'Login',
    page_logged_in: 'Logged In',
    page_welcome_back: 'Welcome back, {name}!',
    msg_no_articles: 'Articles will appear here once content is added to WordPress.',
    msg_no_tools: 'Tools will appear here once content is added to WordPress.',
    msg_no_content: 'No content found.',
    msg_discover_supplementary: 'Discover supplementary tools and resources to enhance your M2DMM strategy development and practice.',
    msg_discover_more: 'Discover more articles and resources to deepen your understanding and enhance your M2DMM practice.',
    content_no_articles_found: 'No Articles Found',
    content_no_articles_try: 'Try adjusting your filters or check back later.',
    content_no_tools_found: 'No Tools Found',
    content_no_tools_try: 'Try adjusting your filters or check back later.',
    content_categories: 'Categories',
    content_tags: 'Tags',
    content_additional_resources: 'Additional Course Resources',
    content_additional_article_resources: 'Additional Article Resources',
    content_additional_tool_resources: 'Additional Tool Resources',
    content_supplementary_materials: 'Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development.',
    content_key_information: 'Key Information',
    content_key_information_m2dmm: 'Key Information About Media to Disciple Making Movements',
    content_digital_disciple_making: 'What is Digital Disciple Making?',
    content_heavenly_economy: 'The Heavenly Economy',
    content_our_vision: 'Our Vision',
    content_our_mission: 'Our Mission',
    content_how_it_works: 'How it works',
    content_our_foundation: 'Our Foundation',
    content_media_content: '1. Media Content',
    content_digital_filtering: '2. Digital Filtering',
    content_face_to_face: '3. Face-to-Face Discipleship',
    footer_quick_links: 'Quick Links',
    footer_our_vision: 'Our Vision',
    footer_subscribe: 'Subscribe to Newsletter',
    footer_privacy_policy: 'Privacy Policy',
    footer_all_rights: 'All rights reserved.',
    footer_mission_statement: 'Training disciple makers to use media to accelerate Disciple Making Movements. Equipping practitioners with practical strategies that bridge online engagement with face-to-face discipleship.',
    footer_scripture_quote: 'Of the sons of Issachar, men who understood the times, with knowledge of what Israel should do.',
    footer_scripture_citation: '— 1 Chronicles 12:32',
    footer_technology_paragraph: 'We wonder what the Church could accomplish with technology God has given to this generation for the first time in history.',
    newsletter_subscribe: 'Subscribe',
    newsletter_email_placeholder: 'Enter your email',
    newsletter_name_placeholder: 'Enter your name',
    newsletter_success: 'Successfully subscribed!',
    newsletter_error: 'Failed to subscribe. Please try again.',
    search_placeholder: 'Search...',
    search_no_results: 'No results found',
    search_results: 'Search Results',
    search_placeholder_courses_tools: 'Search strategy courses and tools...',
    search_no_results_try: 'Try a different search term',
    search_start_typing: 'Start typing to search...',
    search_start_typing_desc: 'Search strategy courses and tools',
    search_close: 'Close search',
    search_strategy_course: 'Strategy Course',
    search_tool: 'Tool',
    breadcrumb_home: 'Home',
    breadcrumb_articles: 'Articles',
    breadcrumb_tools: 'Tools',
    breadcrumb_strategy_courses: 'Strategy Courses',
    hero_explore_resources: 'Explore Our Resources',
    hero_about_us: 'About Us',
    hero_description: 'Accelerate your disciple making with strategic use of media, advertising, and AI tools. Kingdom.Training is a resource for disciple makers to use media to accelerate Disciple Making Movements.',
    hero_subtitle_media_ai: 'Media, Advertising, and AI',
    hero_title_innovate: 'Innovate → Accelerate → Make Disciples',
    hero_cta_explore_resources: 'Explore Our Resources',
    hero_cta_about_us: 'About Us',
    hero_newsletter_title: 'Get the newest insights, techniques, and strategies.',
    home_mvp_description: 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context. Complete your plan in 6-7 hours.',
    home_newsletter_description: 'Field driven tools and articles for disciple makers.',
    home_heavenly_economy: 'We operate within what we call the "Heavenly Economy"—a principle that challenges the broken world\'s teaching that "the more you get, the more you should keep." Instead, we reflect God\'s generous nature by offering free training, hands-on coaching, and open-source tools like Disciple.Tools.',
    home_mission_statement: 'Our heart beats with passion for the unreached and least-reached peoples of the world. Every course, article, and tool serves the ultimate vision of seeing Disciple Making Movements catalyzed among people groups where the name of Jesus has never been proclaimed.',
    home_loading_steps: 'Loading course steps...',
    nav_back_to_course_overview: 'Back to Course Overview',
    nav_back_to_strategy_courses: '← Back to Strategy Courses',
    nav_start_step: 'Start this step',
    nav_start_step_number: 'Start Step {number}: {title}',
    nav_previous: 'Previous',
    nav_next: 'Next',
    nav_step: 'Step {number}',
    nav_enroll_mvp: 'Enroll in The MVP Course',
    nav_start_mvp: 'Start the MVP Course',
    nav_subscribe_newsletter: 'Subscribe to Newsletter',
    nav_subscribe_now: 'Subscribe now',
    nav_go_to_admin: 'Go to WordPress Admin',
    nav_log_out: 'Log Out',
    nav_log_in: 'Log In',
    nav_forgot_password: 'Forgot your password?',
    error_course_not_found: 'Course Not Found',
    error_course_not_found_desc: 'The course you\'re looking for doesn\'t exist.',
    error_article_not_found: 'Article Not Found',
    error_article_not_found_desc: 'The article you\'re looking for doesn\'t exist.',
    error_tool_not_found: 'Tool Not Found',
    error_tool_not_found_desc: 'The tool you\'re looking for doesn\'t exist.',
    error_404_title: '404',
    error_404_heading: 'Page Not Found',
    error_404_desc: 'The page you\'re looking for doesn\'t exist or has been moved.',
    error_return_home: 'Return Home',
    error_login_failed: 'Login failed. Please check your credentials.',
    error_logout_failed: 'Logout failed.',
    error_security_token_not_found: 'Security token not found. Please refresh the page.',
    error_subscribe_failed: 'There was an error subscribing you. Please try again.',
    error_subscribe_generic: 'Something went wrong. Please try again.',
    error_submit_failed: 'Failed to submit. Please try again.',
    course_strategy_course: 'Strategy Course',
    course_article: 'Article',
    course_tool: 'Tool',
    course_no_steps_found: 'No course steps found. Please add strategy courses with step numbers in WordPress admin.',
    course_complete_plan: 'Complete your plan step by step.',
    course_flagship_description: 'Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context.',
    course_progress_your_progress: 'Your Progress',
    course_progress_steps_completed: '{completed} of {total} steps completed',
    course_progress_start_message: 'Start with Step 1 to begin your M2DMM strategy development journey.',
    course_progress_complete_message: 'Congratulations! You\'ve completed all steps in the strategy course.',
    course_progress_keep_going: 'Keep going! You\'re making great progress on your M2DMM strategy.',
    newsletter_stay_connected: 'Stay Connected',
    newsletter_default_description: 'Get the latest training resources and insights delivered to your inbox.',
    newsletter_subscribe_title: 'Subscribe to Our Newsletter',
    newsletter_subscribe_description: 'Get the latest training resources, articles, and insights delivered directly to your inbox. Join our community of disciple makers committed to using media strategically for Kingdom impact.',
    newsletter_what_to_expect: 'What to Expect',
    newsletter_expect_articles: 'Latest articles and insights on Media to Disciple Making Movements',
    newsletter_expect_tools: 'Practical tools and strategies for disciple makers',
    newsletter_expect_stories: 'Stories from the field and testimonies of impact',
    newsletter_privacy_note: 'We respect your privacy. Unsubscribe at any time.',
    newsletter_privacy_link: 'Learn more about our privacy policy',
    newsletter_success_message: 'Thank you for subscribing! Please check your email to confirm your subscription.',
    newsletter_check_email: 'Please check your email to confirm your subscription.',
    newsletter_subscribed: 'Subscribed!',
    newsletter_confirm_subscribe: 'You must confirm that you want to subscribe.',
    newsletter_security_loading: 'Security verification widget is loading. Please wait a moment and try again.',
    newsletter_security_complete: 'Please complete the security verification above.',
    newsletter_try_again: 'Try Again',
    footer_kingdom_training: 'Kingdom Training',
    footer_key_definitions: 'Key Definitions',
    form_username_email: 'Username or Email',
    form_username_email_placeholder: 'Enter your username or email',
    form_password: 'Password',
    form_password_placeholder: 'Enter your password',
  };
}

/**
 * Clear translation cache (useful when language changes)
 */
export function clearTranslationCache() {
  translationsCache = null;
  currentLanguage = null;
}

