# Hard-Coded English Strings - Translation Report

This document identifies all hard-coded English strings found in the frontend codebase that should be included in the Polylang translation strings strategy.

## Summary

Found **100+ hard-coded English strings** across multiple components and pages that need to be moved to the translation system.

---

## Translation Keys Needed

### Loading States
- `ui_loading` (already exists) - "Loading..."
- `ui_loading_course_steps` - "Loading course steps..."
- `ui_searching` - "Searching..."
- `ui_submitting` - "Submitting..."
- `ui_submitting_subscription` - "Submitting your subscription..."
- `ui_logging_in` - "Logging in..."

### Error Messages
- `error_course_not_found` - "Course Not Found"
- `error_course_not_found_desc` - "The course you're looking for doesn't exist."
- `error_article_not_found` - "Article Not Found"
- `error_article_not_found_desc` - "The article you're looking for doesn't exist."
- `error_tool_not_found` - "Tool Not Found"
- `error_tool_not_found_desc` - "The tool you're looking for doesn't exist."
- `error_404_title` - "404"
- `error_404_heading` - "Page Not Found"
- `error_404_desc` - "The page you're looking for doesn't exist or has been moved."
- `error_return_home` - "Return Home"
- `error_login_failed` - "Login failed. Please check your credentials."
- `error_logout_failed` - "Logout failed."
- `error_security_token_not_found` - "Security token not found. Please refresh the page."
- `error_subscribe_failed` - "There was an error subscribing you. Please try again."
- `error_subscribe_generic` - "Something went wrong. Please try again."
- `error_submit_failed` - "Failed to submit. Please try again."

### Navigation & Actions
- `nav_back_to_course_overview` - "Back to Course Overview"
- `nav_back_to_strategy_courses` - "← Back to Strategy Courses"
- `nav_start_step` - "Start this step"
- `nav_start_step_number` - "Start Step {number}: {title}"
- `nav_previous` - "Previous"
- `nav_next` - "Next"
- `nav_step` - "Step {number}"
- `nav_enroll_mvp` - "Enroll in The MVP Course"
- `nav_start_mvp` - "Start the MVP Course"
- `nav_subscribe_newsletter` - "Subscribe to Newsletter"
- `nav_subscribe_now` - "Subscribe now"
- `nav_go_to_admin` - "Go to WordPress Admin"
- `nav_log_out` - "Log Out"
- `nav_log_in` - "Log In"
- `nav_forgot_password` - "Forgot your password?"

### Page Headers & Titles
- `page_home` - "Home"
- `page_strategy_course` - "Strategy Course"
- `page_strategy_course_mvp` - "The MVP: Strategy Course"
- `page_strategy_course_description` - "Comprehensive training to craft your Media to Disciple Making Movements strategy. Follow the 10-step program below to develop your complete M2DMM strategy."
- `page_articles` - "Articles"
- `page_tools` - "Tools"
- `page_newsletter` - "Newsletter"
- `page_newsletter_description` - "Stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements."
- `page_about` - "About Us"
- `page_about_kingdom_training` - "About Kingdom.Training"
- `page_privacy_policy` - "Privacy Policy"
- `page_login` - "Login"
- `page_logged_in` - "Logged In"
- `page_welcome_back` - "Welcome back, {name}!"

### Content Sections
- `content_digital_disciple_making` - "What is Digital Disciple Making?"
- `content_heavenly_economy` - "The Heavenly Economy"
- `content_our_vision` - "Our Vision"
- `content_our_mission` - "Our Mission"
- `content_how_it_works` - "How it works"
- `content_our_foundation` - "Our Foundation"
- `content_media_content` - "1. Media Content"
- `content_digital_filtering` - "2. Digital Filtering"
- `content_face_to_face` - "3. Face-to-Face Discipleship"
- `content_additional_resources` - "Additional Course Resources"
- `content_additional_article_resources` - "Additional Article Resources"
- `content_additional_tool_resources` - "Additional Tool Resources"
- `content_supplementary_materials` - "Discover supplementary materials and resources to deepen your understanding and enhance your M2DMM strategy development."
- `content_key_information` - "Key Information"
- `content_key_information_m2dmm` - "Key Information About Media to Disciple Making Movements"

### Course/Content Specific
- `course_strategy_course` - "Strategy Course"
- `course_article` - "Article"
- `course_tool` - "Tool"
- `course_no_steps_found` - "No course steps found. Please add strategy courses with step numbers in WordPress admin."
- `course_complete_plan` - "Complete your plan step by step."
- `course_flagship_description` - "Our flagship course guides you through 10 core elements needed to craft a Media to Disciple Making Movements strategy for any context. Complete your plan in 6-7 hours."
- `course_progress_your_progress` - "Your Progress"
- `course_progress_steps_completed` - "{completed} of {total} steps completed"
- `course_progress_start_message` - "Start with Step 1 to begin your M2DMM strategy development journey."
- `course_progress_complete_message` - "Congratulations! You've completed all steps in the strategy course."
- `course_progress_keep_going` - "Keep going! You're making great progress on your M2DMM strategy."

### Newsletter
- `newsletter_stay_connected` - "Stay Connected"
- `newsletter_default_description` - "Get the latest training resources and insights delivered to your inbox."
- `newsletter_subscribe_title` - "Subscribe to Our Newsletter"
- `newsletter_subscribe_description` - "Get the latest training resources, articles, and insights delivered directly to your inbox. Join our community of disciple makers committed to using media strategically for Kingdom impact."
- `newsletter_what_to_expect` - "What to Expect"
- `newsletter_expect_articles` - "Latest articles and insights on Media to Disciple Making Movements"
- `newsletter_expect_tools` - "Practical tools and strategies for disciple makers"
- `newsletter_expect_stories` - "Stories from the field and testimonies of impact"
- `newsletter_privacy_note` - "We respect your privacy. Unsubscribe at any time."
- `newsletter_privacy_link` - "Learn more about our privacy policy"
- `newsletter_success_message` - "Thank you for subscribing! Please check your email to confirm your subscription."
- `newsletter_check_email` - "Please check your email to confirm your subscription."
- `newsletter_subscribed` - "Subscribed!"
- `newsletter_confirm_subscribe` - "You must confirm that you want to subscribe."
- `newsletter_security_loading` - "Security verification widget is loading. Please wait a moment and try again."
- `newsletter_security_complete` - "Please complete the security verification above."
- `newsletter_try_again` - "Try Again"

### Search
- `search_placeholder_courses_tools` - "Search strategy courses and tools..."
- `search_no_results` - "No results found"
- `search_no_results_try` - "Try a different search term"
- `search_start_typing` - "Start typing to search..."
- `search_start_typing_desc` - "Search strategy courses and tools"
- `search_close` - "Close search"
- `search_strategy_course` - "Strategy Course"
- `search_tool` - "Tool"

### Content Lists
- `content_no_articles_found` - "No Articles Found"
- `content_no_articles_try` - "Try adjusting your filters or check back later."
- `content_no_tools_found` - "No Tools Found"
- `content_no_tools_try` - "Try adjusting your filters or check back later."
- `content_categories` - "Categories"
- `content_tags` - "Tags"

### Hero Section
- `hero_subtitle_media_ai` - "Media, Advertising, and AI"
- `hero_title_innovate` - "Innovate → Accelerate → Make Disciples"
- `hero_cta_explore_resources` - "Explore Our Resources" (default)
- `hero_cta_about_us` - "About Us"
- `hero_newsletter_title` - "Get the newest insights, techniques, and strategies."

### Footer
- `footer_kingdom_training` - "Kingdom Training"
- `footer_key_definitions` - "Key Definitions"

### Form Labels & Placeholders
- `form_username_email` - "Username or Email"
- `form_username_email_placeholder` - "Enter your username or email"
- `form_password` - "Password"
- `form_password_placeholder` - "Enter your password"

### Structured Data / SEO (may want to keep in English for SEO)
- These are typically kept in English for SEO purposes, but could be translated:
- `seo_home_description` - "Training disciple makers to use media to accelerate Disciple Making Movements. Learn practical strategies that bridge online engagement with face-to-face discipleship. Start your M2DMM strategy course today."
- `seo_articles_description` - "Practical guidance, best practices, and real-world insights from the Media to Disciple Making Movements community. Learn from practitioners implementing M2DMM strategies around the world."
- `seo_tools_description` - "Essential tools and resources for Media to Disciple Making Movements work. Discover Disciple.Tools—our free, open-source disciple relationship management system—and other practical resources designed specifically for M2DMM practitioners."

### Privacy Policy (Full Page Content)
The Privacy Policy page contains extensive hard-coded English text. Consider:
- Either translating the entire page content
- Or creating a WordPress page/post for privacy policy that can be translated via Polylang

### About Page (Full Page Content)
The About page contains extensive hard-coded English text. Consider:
- Either translating the entire page content
- Or creating a WordPress page/post for about that can be translated via Polylang

---

## Files Requiring Updates

### High Priority (User-Facing UI)
1. **HomePage.tsx** - Multiple hard-coded strings
2. **StrategyCoursesPage.tsx** - Course-specific strings
3. **StrategyCourseDetailPage.tsx** - Navigation and content strings
4. **ArticlesPage.tsx** - Empty state messages
5. **ToolsPage.tsx** - Empty state messages
6. **ArticleDetailPage.tsx** - Error messages and section headers
7. **ToolDetailPage.tsx** - Error messages and section headers
8. **Header.tsx** - Mobile menu aria-labels
9. **Footer.tsx** - Structured data content (FAQ section)
10. **SearchModal.tsx** - Search interface strings
11. **NewsletterCTA.tsx** - Newsletter CTA strings
12. **NewsletterPage.tsx** - Newsletter page content
13. **NotFoundPage.tsx** - 404 page content
14. **LoginPage.tsx** - Login form strings
15. **ProgressIndicator.tsx** - Progress messages
16. **Sidebar.tsx** - Category and tag labels
17. **Hero.tsx** - Hero section strings

### Medium Priority (Less Frequently Seen)
18. **PrivacyPage.tsx** - Full privacy policy (consider WordPress page)
19. **AboutPage.tsx** - Full about content (consider WordPress page)
20. **KeyInfoSection.tsx** - Section title

---

## Implementation Recommendations

1. **Add all new translation keys** to `translations.ts` interface and default translations
2. **Update components** to use `t()` function for all hard-coded strings
3. **Consider WordPress pages** for long-form content like Privacy Policy and About page
4. **Test thoroughly** to ensure all strings are properly translated
5. **Document** any strings that should remain in English (e.g., technical terms, brand names)

---

## Notes

- Some strings like "Kingdom.Training" (brand name) may intentionally remain in English
- Structured data content may need to remain in English for SEO purposes
- Error messages and form validation should definitely be translated
- Loading states and UI feedback should be translated for better UX

