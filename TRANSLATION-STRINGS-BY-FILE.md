# Hard-Coded Strings by File - Quick Reference

Quick reference guide for implementing translations, organized by file.

## HomePage.tsx

**Line 79:** `"Loading..."`
- Use: `t('ui_loading')` (already exists)

**Line 92:** `"Home"`
- Use: `t('page_home')` (already exists via breadcrumb_home)

**Line 104:** `"Media, Advertising, and AI"`
- Use: `t('hero_subtitle_media_ai')`

**Line 105:** `"Innovate → Accelerate → Make Disciples"`
- Use: `t('hero_title_innovate')`

**Line 107:** `"Start the MVP Course"`
- Use: `t('nav_start_mvp')`

**Line 117:** `"Get the newest insights, techniques, and strategies."`
- Use: `t('hero_newsletter_title')`

**Line 180:** `"Enroll in The MVP Course"`
- Use: `t('nav_enroll_mvp')`

**Line 258:** `"What is Digital Disciple Making?"`
- Use: `t('content_digital_disciple_making')`

**Line 273:** `"The Heavenly Economy"`
- Use: `t('content_heavenly_economy')` (or use existing `home_heavenly_economy`)

**Line 307:** `"Key Information About Media to Disciple Making Movements"`
- Use: `t('content_key_information_m2dmm')` (or use existing `page_key_information`)

---

## StrategyCoursesPage.tsx

**Line 120:** `"Strategy Course - The MVP"`
- Use: `t('page_strategy_course_mvp')`

**Line 121:** `"Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program to develop your complete M2DMM strategy. Complete your plan in 6-7 hours."`
- Use: `t('page_strategy_course_description')`

**Line 126:** `"Strategy Course"`
- Use: `t('page_strategy_course')`

**Line 127:** `"Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program below to develop your complete M2DMM strategy."`
- Use: `t('page_strategy_course_description')`

**Line 152:** `"The MVP: Strategy Course"`
- Use: `t('page_strategy_course_mvp')` (or existing `page_mvp_strategy_course`)

**Line 155-156:** `"Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context. Complete your plan step by step."`
- Use: `t('course_flagship_description')` + `t('course_complete_plan')`

**Line 164:** `"Loading course steps..."`
- Use: `t('ui_loading_course_steps')`

**Line 192:** `"Start this step"`
- Use: `t('nav_start_step')`

**Line 220:** `"Start Step {number}: {title}"`
- Use: `t('nav_start_step_number')` with `tWithReplace()`

**Line 228:** `"No course steps found. Please add strategy courses with step numbers in WordPress admin."`
- Use: `t('course_no_steps_found')`

**Line 242:** `"Additional Course Resources"`
- Use: `t('content_additional_resources')`

**Line 245:** `"Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development."`
- Use: `t('content_supplementary_materials')`

---

## StrategyCourseDetailPage.tsx

**Line 143:** `"Loading..."`
- Use: `t('ui_loading')` (already exists)

**Line 152:** `"Course Not Found"`
- Use: `t('error_course_not_found')`

**Line 153:** `"The course you're looking for doesn't exist."`
- Use: `t('error_course_not_found_desc')`

**Line 155:** `"← Back to Strategy Courses"`
- Use: `t('nav_back_to_strategy_courses')`

**Line 227:** `"Strategy Course"`
- Use: `t('course_strategy_course')`

**Line 281:** `"Previous"`
- Use: `t('nav_previous')`

**Line 282:** `"Step {number}"`
- Use: `t('nav_step')` with `tWithReplace()`

**Line 296:** `"Next"`
- Use: `t('nav_next')`

**Line 306:** `"Back to Course Overview"`
- Use: `t('nav_back_to_course_overview')`

**Line 323:** `"Additional Course Resources"`
- Use: `t('content_additional_resources')`

**Line 326:** `"Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development."`
- Use: `t('content_supplementary_materials')`

---

## ArticlesPage.tsx

**Line 81:** `"Loading..."`
- Use: `t('ui_loading')` (already exists)

**Line 90:** `"Articles"`
- Use: `t('nav_articles')` (already exists)

**Line 96:** `"Articles"`
- Use: `t('nav_articles')` (already exists)

**Line 126:** `"No Articles Found"`
- Use: `t('content_no_articles_found')`

**Line 129:** `"Try adjusting your filters or check back later."`
- Use: `t('content_no_articles_try')`

---

## ToolsPage.tsx

**Line 81:** `"Loading..."`
- Use: `t('ui_loading')` (already exists)

**Line 90:** `"Tools"`
- Use: `t('nav_tools')` (already exists)

**Line 96:** `"Tools"`
- Use: `t('nav_tools')` (already exists)

**Line 126:** `"No Tools Found"`
- Use: `t('content_no_tools_found')`

**Line 129:** `"Try adjusting your filters or check back later."`
- Use: `t('content_no_tools_try')`

---

## ArticleDetailPage.tsx

**Line 87:** `"Loading..."`
- Use: `t('ui_loading')` (already exists)

**Line 96:** `"Article Not Found"`
- Use: `t('error_article_not_found')`

**Line 97:** `"The article you're looking for doesn't exist."`
- Use: `t('error_article_not_found_desc')`

**Line 167:** `"Article"`
- Use: `t('course_article')`

**Line 190:** `"Additional Article Resources"`
- Use: `t('content_additional_article_resources')`

---

## ToolDetailPage.tsx

**Line 87:** `"Loading..."`
- Use: `t('ui_loading')` (already exists)

**Line 96:** `"Tool Not Found"`
- Use: `t('error_tool_not_found')`

**Line 97:** `"The tool you're looking for doesn't exist."`
- Use: `t('error_tool_not_found_desc')`

**Line 167:** `"Tool"`
- Use: `t('course_tool')`

**Line 190:** `"Additional Tool Resources"`
- Use: `t('content_additional_tool_resources')`

**Line 193:** Uses existing `t('msg_discover_supplementary')` ✓

---

## Header.tsx

**Line 139:** `"Search"` (aria-label)
- Use: `t('nav_search')` (already exists)

**Line 147:** `"Toggle menu"` (aria-label)
- Use: `t('nav_menu')` (already exists) or add `ui_toggle_menu`

---

## Footer.tsx

**Line 27-28:** `"Kingdom.Training"` (structured data - may keep in English)
- Consider: Keep as brand name or translate

**Line 30, 39:** Long description strings (structured data - may keep in English)
- Consider: Keep for SEO or translate

**Line 44-62:** FAQ questions and answers (structured data - may keep in English)
- Consider: Keep for SEO or translate

**Line 74:** `"Kingdom Training"`
- Use: `t('footer_kingdom_training')`

**Line 159:** `"Key Definitions"`
- Use: `t('footer_key_definitions')`

**Line 161-175:** Definition list content (structured data - may keep in English)
- Consider: Keep for SEO or translate

---

## SearchModal.tsx

**Line 131:** `"Search strategy courses and tools..."`
- Use: `t('search_placeholder_courses_tools')`

**Line 138:** `"Close search"` (aria-label)
- Use: `t('search_close')`

**Line 149:** `"Searching..."`
- Use: `t('ui_searching')`

**Line 157:** `"Strategy Course"` / `"Tool"`
- Use: `t('search_strategy_course')` / `t('search_tool')`

**Line 195:** `"No results found"`
- Use: `t('search_no_results')` (already exists)

**Line 196:** `"Try a different search term"`
- Use: `t('search_no_results_try')`

**Line 202:** `"Start typing to search..."`
- Use: `t('search_start_typing')`

**Line 203:** `"Search strategy courses and tools"`
- Use: `t('search_start_typing_desc')`

---

## NewsletterCTA.tsx

**Line 262:** `"You must confirm that you want to subscribe."`
- Use: `t('newsletter_confirm_subscribe')`

**Line 274:** `"Security token not found. Please refresh the page."`
- Use: `t('error_security_token_not_found')`

**Line 291:** `"Security verification widget is loading. Please wait a moment and try again."`
- Use: `t('newsletter_security_loading')`

**Line 293:** `"Please complete the security verification above."`
- Use: `t('newsletter_security_complete')`

**Line 319:** `"Submitting..."`
- Use: `t('ui_submitting')`

**Line 346:** `"Subscribed!"`
- Use: `t('newsletter_subscribed')`

**Line 352:** `"Please check your email to confirm your subscription."`
- Use: `t('newsletter_check_email')`

**Line 363:** `"Try Again"`
- Use: `t('newsletter_try_again')`

**Line 366:** `"There was an error subscribing you. Please try again."`
- Use: `t('error_subscribe_failed')`

**Line 373:** `"Try Again"`
- Use: `t('newsletter_try_again')`

**Line 376:** `"There was an error subscribing you. Please try again."`
- Use: `t('error_subscribe_failed')`

**Line 390:** `"Stay Connected"`
- Use: `t('newsletter_stay_connected')`

**Line 391:** `"Get the latest training resources and insights delivered to your inbox."`
- Use: `t('newsletter_default_description')`

**Line 428:** `"Subscribe to Newsletter"`
- Use: `t('nav_subscribe_newsletter')` (or existing `footer_subscribe`)

**Line 470:** `"Subscribe now"`
- Use: `t('nav_subscribe_now')`

**Line 502:** `"Subscribe"`
- Use: `t('newsletter_subscribe')` (already exists)

---

## NewsletterPage.tsx

**Line 249:** `"You must confirm that you want to subscribe."`
- Use: `t('newsletter_confirm_subscribe')`

**Line 260:** `"Security token not found. Please refresh the page."`
- Use: `t('error_security_token_not_found')`

**Line 277:** `"Security verification widget is loading. Please wait a moment and try again."`
- Use: `t('newsletter_security_loading')`

**Line 280:** `"Please complete the security verification above."`
- Use: `t('newsletter_security_complete')`

**Line 321:** `"Thank you for subscribing! Please check your email to confirm your subscription."`
- Use: `t('newsletter_success_message')`

**Line 326:** `"Something went wrong. Please try again."`
- Use: `t('error_subscribe_generic')`

**Line 331:** `"Failed to submit. Please try again."`
- Use: `t('error_submit_failed')`

**Line 346:** `"Newsletter"`
- Use: `t('page_newsletter')` (already exists via nav_newsletter)

**Line 353:** `"Newsletter"`
- Use: `t('page_newsletter')`

**Line 354:** `"Stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements."`
- Use: `t('page_newsletter_description')`

**Line 367:** `"Subscribe to Our Newsletter"`
- Use: `t('newsletter_subscribe_title')`

**Line 369-371:** Long description
- Use: `t('newsletter_subscribe_description')`

**Line 395:** `"Submitting your subscription..."`
- Use: `t('ui_submitting_subscription')`

**Line 421:** `"What to Expect"`
- Use: `t('newsletter_what_to_expect')`

**Line 425:** `"Latest articles and insights on Media to Disciple Making Movements"`
- Use: `t('newsletter_expect_articles')`

**Line 429:** `"Practical tools and strategies for disciple makers"`
- Use: `t('newsletter_expect_tools')`

**Line 433:** `"Stories from the field and testimonies of impact"`
- Use: `t('newsletter_expect_stories')`

**Line 440:** `"We respect your privacy. Unsubscribe at any time."`
- Use: `t('newsletter_privacy_note')`

**Line 442:** `"Learn more about our privacy policy"`
- Use: `t('newsletter_privacy_link')`

---

## NotFoundPage.tsx

**Line 8:** `"404 - Page Not Found"`
- Use: `t('error_404_title')` + `t('error_404_heading')`

**Line 9:** `"The page you're looking for doesn't exist or has been moved."`
- Use: `t('error_404_desc')`

**Line 17:** `"404"`
- Use: `t('error_404_title')`

**Line 18:** `"Page Not Found"`
- Use: `t('error_404_heading')`

**Line 19:** `"The page you're looking for doesn't exist or has been moved."`
- Use: `t('error_404_desc')`

**Line 22:** `"Return Home"`
- Use: `t('error_return_home')`

---

## LoginPage.tsx

**Line 44:** `"Login failed. Please check your credentials."`
- Use: `t('error_login_failed')`

**Line 56:** `"Logout failed."`
- Use: `t('error_logout_failed')`

**Line 64:** `"Logged In"`
- Use: `t('page_logged_in')`

**Line 65, 72:** `"Welcome back, {name}!"`
- Use: `t('page_welcome_back')` with `tWithReplace()`

**Line 97:** `"Go to WordPress Admin"`
- Use: `t('nav_go_to_admin')`

**Line 103:** `"Log Out"`
- Use: `t('nav_log_out')`

**Line 116:** `"Login"`
- Use: `t('page_login')`

**Line 123:** `"Login"`
- Use: `t('page_login')`

**Line 139:** `"Username or Email"`
- Use: `t('form_username_email')`

**Line 148:** `"Enter your username or email"`
- Use: `t('form_username_email_placeholder')`

**Line 154:** `"Password"`
- Use: `t('form_password')`

**Line 163:** `"Enter your password"`
- Use: `t('form_password_placeholder')`

**Line 172:** `"Logging in..."` / `"Log In"`
- Use: `t('ui_logging_in')` / `t('nav_log_in')`

**Line 181:** `"Forgot your password?"`
- Use: `t('nav_forgot_password')`

---

## ProgressIndicator.tsx

**Line 47:** `"Start with Step 1 to begin your M2DMM strategy development journey."`
- Use: `t('course_progress_start_message')`

**Line 49:** `"Congratulations! You've completed all steps in the strategy course."`
- Use: `t('course_progress_complete_message')`

**Line 51:** `"Keep going! You're making great progress on your M2DMM strategy."`
- Use: `t('course_progress_keep_going')`

**Line 58:** `"Your Progress"`
- Use: `t('course_progress_your_progress')`

**Line 60:** `"{completed} of {total} steps completed"`
- Use: `t('course_progress_steps_completed')` with `tWithReplace()`

---

## Sidebar.tsx

**Line 17:** `"Categories"`
- Use: `t('content_categories')`

**Line 41:** `"Tags"`
- Use: `t('content_tags')`

---

## Hero.tsx

**Line 22:** `"Explore Our Resources"` (default)
- Use: `t('hero_cta_explore_resources')` (already exists)

**Line 59:** `"About Us"`
- Use: `t('hero_about_us')` (already exists)

---

## KeyInfoSection.tsx

**Line 17:** `"Key Information"` (default)
- Use: `t('content_key_information')` (already exists via `page_key_information`)

---

## PrivacyPage.tsx & AboutPage.tsx

These pages contain extensive hard-coded content. Consider:
1. Moving content to WordPress pages/posts that can be translated via Polylang
2. Or adding all strings to translation system (would be many keys)

---

## Next Steps

1. Add all new translation keys to `translations.ts`
2. Update each component file to use `t()` function
3. Test with different languages
4. Consider WordPress pages for long-form content

