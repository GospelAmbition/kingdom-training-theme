# Final Review: Hard-Coded English Strings

This document lists all remaining hard-coded English strings found in the codebase that should be moved to the translation system.

## Summary

Found **additional hard-coded strings** beyond what was documented in `TRANSLATION-STRINGS-BY-FILE.md`. These are primarily:
- SEO meta descriptions and titles
- Structured data (FAQ, definitions, educational metadata)
- Video iframe titles
- Console messages (lower priority)

---

## High Priority (User-Facing Content)

### HomePage.tsx

**Line 92:** `title="Home"`
- **Current:** Hard-coded SEO title
- **Should use:** `t('page_home')` (already exists)

**Line 93:** SEO description
- **Current:** `"Training disciple makers to use media to accelerate Disciple Making Movements. Learn practical strategies that bridge online engagement with face-to-face discipleship. Start your M2DMM strategy course today."`
- **Should use:** `t('seo_home_description')`

**Line 100:** StructuredData website description
- **Current:** `'Training disciple makers to use media to accelerate Disciple Making Movements. Equipping practitioners with practical strategies that bridge online engagement with face-to-face discipleship.'`
- **Should use:** `t('seo_home_description')` or `t('footer_mission_statement')`

**Line 267:** Video iframe title
- **Current:** `title="Kingdom Training Video"`
- **Should use:** `t('video_kingdom_training_title')`

**Lines 310-332:** KeyInfoSection items (6 items with terms and definitions)
- **Current:** All hard-coded English text
- **Should use:** Translation keys for each term/definition pair:
  - `content_m2dmm_term` / `content_m2dmm_definition`
  - `content_digital_disciple_making_term` / `content_digital_disciple_making_definition`
  - `content_mvp_course_term` / `content_mvp_course_definition`
  - `content_ai_discipleship_term` / `content_ai_discipleship_definition`
  - `content_heavenly_economy_term` / `content_heavenly_economy_definition`
  - `content_kingdom_training_for_term` / `content_kingdom_training_for_definition`

### Footer.tsx

**Lines 30, 39:** StructuredData descriptions
- **Current:** `'Training disciple makers to use media to accelerate Disciple Making Movements. Equipping practitioners with practical strategies that bridge online engagement with face-to-face discipleship.'`
- **Should use:** `t('footer_mission_statement')` (already exists)

**Lines 44-62:** FAQ structured data (5 questions/answers)
- **Current:** All hard-coded English
- **Should use:** Translation keys:
  - `faq_m2dmm_question` / `faq_m2dmm_answer`
  - `faq_mvp_course_question` / `faq_mvp_course_answer`
  - `faq_disciple_tools_question` / `faq_disciple_tools_answer`
  - `faq_heavenly_economy_question` / `faq_heavenly_economy_answer`
  - `faq_kingdom_training_for_question` / `faq_kingdom_training_for_answer`

**Lines 161-175:** Definition list (5 definitions)
- **Current:** All hard-coded English
- **Should use:** Translation keys (can reuse same keys as KeyInfoSection or create separate ones):
  - `definition_m2dmm`
  - `definition_dmm`
  - `definition_persons_of_peace`
  - `definition_heavenly_economy`
  - `definition_unreached_peoples`

### ArticlesPage.tsx

**Line 93:** SEO description
- **Current:** `"Practical guidance, best practices, and real-world insights from the Media to Disciple Making Movements community. Learn from practitioners implementing M2DMM strategies around the world."`
- **Should use:** `t('seo_articles_description')`

**Line 94:** SEO keywords
- **Current:** `"M2DMM articles, disciple making movements, media strategy, digital evangelism, church planting articles, online ministry, practical discipleship, field insights, kingdom training articles"`
- **Note:** Keywords may intentionally remain in English for SEO

**Line 99:** PageHeader description
- **Current:** Same as line 93 (duplicate)
- **Should use:** `t('seo_articles_description')`

### ToolsPage.tsx

**Line 93:** SEO description
- **Current:** `"Essential tools and resources for Media to Disciple Making Movements work. Discover Disciple.Tools—our free, open-source disciple relationship management system—and other practical resources designed specifically for M2DMM practitioners."`
- **Should use:** `t('seo_tools_description')`

**Line 94:** SEO keywords
- **Current:** `"M2DMM tools, Disciple.Tools, disciple making tools, church planting tools, digital ministry tools, CRM for discipleship, open source tools, kingdom training tools, ministry resources"`
- **Note:** Keywords may intentionally remain in English for SEO

**Line 99:** PageHeader description
- **Current:** Same as line 93 (duplicate)
- **Should use:** `t('seo_tools_description')`

### LoginPage.tsx

**Line 119:** SEO description
- **Current:** `"Login to Kingdom.Training to access your account and WordPress admin dashboard."`
- **Should use:** `t('seo_login_description')`

### NewsletterPage.tsx

**Line 349:** SEO description
- **Current:** `"Subscribe to Kingdom.Training newsletter and stay connected with the latest training resources, articles, and updates on Media to Disciple Making Movements. Get practical insights delivered to your inbox."`
- **Should use:** `t('seo_newsletter_description')`

### StrategyCourseDetailPage.tsx

**Lines 201-203:** Educational metadata `teaches` array
- **Current:** 
  ```javascript
  teaches: [
    'Media to Disciple Making Movements',
    'Digital Evangelism',
    'Online Ministry Strategy',
    'Disciple Making Movements',
  ]
  ```
- **Should use:** Translation keys or keep as technical metadata (may be acceptable to keep in English)

**Line 199:** `educationalLevel`
- **Current:** `'Professional Development'`
- **Should use:** `t('course_educational_level')` or keep as technical metadata

**Line 211:** Breadcrumb name
- **Current:** `{ name: 'Strategy Courses', url: ... }`
- **Should use:** `t('nav_strategy_courses')` (already exists)

### AdminEditLink.tsx

**Lines 122-123:** Title and aria-label
- **Current:** `title="Edit in WordPress Admin"` and `aria-label="Edit in WordPress Admin"`
- **Should use:** `t('admin_edit_link_title')` / `t('admin_edit_link_aria_label')`

### LanguageSelector.tsx

**Line 102:** aria-label
- **Current:** `aria-label="No languages available"`
- **Should use:** `t('ui_no_languages_available')`

**Line 103:** title attribute
- **Current:** `title="No languages configured in Polylang. Check console for details."`
- **Should use:** `t('ui_no_languages_title')` (or keep as developer message)

---

## Medium Priority (Structured Data / SEO)

### Structured Data Considerations

Many of the structured data fields (FAQ, definitions, educational metadata) are used for:
- SEO optimization
- Answer Engine Optimization (AEO)
- LLM/crawler consumption

**Recommendation:** 
- For SEO purposes, these may intentionally remain in English
- However, for better international SEO, consider translating them
- Create separate translation keys for structured data vs. user-facing content

---

## Low Priority (Developer/Console Messages)

### Console Messages

These are typically only seen by developers and may not need translation:
- `console.error()` messages
- `console.log()` messages
- `console.warn()` messages

**Files with console messages:**
- `NewsletterCTA.tsx` - Multiple console.log/error messages
- `NewsletterPage.tsx` - Multiple console.log/error messages
- `SearchModal.tsx` - console.error
- `ArticlesPage.tsx` - console.error
- `ToolsPage.tsx` - console.error
- `StrategyCoursesPage.tsx` - console.error
- `StrategyCourseDetailPage.tsx` - console.error
- `ArticleDetailPage.tsx` - console.error
- `ToolDetailPage.tsx` - console.error

**Recommendation:** Keep console messages in English (developer-facing)

---

## Brand Names (Acceptable to Keep in English)

These are intentionally kept in English as brand names:
- `'Kingdom.Training'` - Brand name
- `'Disciple.Tools'` - Product name
- Alt text for logos (e.g., `alt="Kingdom.Training"`)

---

## Translation Keys Needed

### SEO Meta Tags
- `seo_home_description`
- `seo_home_title` (or use existing `page_home`)
- `seo_articles_description`
- `seo_tools_description`
- `seo_login_description`
- `seo_newsletter_description`

### Video/Media
- `video_kingdom_training_title`

### KeyInfoSection Terms & Definitions
- `content_m2dmm_term` / `content_m2dmm_definition`
- `content_digital_disciple_making_term` / `content_digital_disciple_making_definition`
- `content_mvp_course_term` / `content_mvp_course_definition`
- `content_ai_discipleship_term` / `content_ai_discipleship_definition`
- `content_heavenly_economy_term` / `content_heavenly_economy_definition`
- `content_kingdom_training_for_term` / `content_kingdom_training_for_definition`

### FAQ Structured Data
- `faq_m2dmm_question` / `faq_m2dmm_answer`
- `faq_mvp_course_question` / `faq_mvp_course_answer`
- `faq_disciple_tools_question` / `faq_disciple_tools_answer`
- `faq_heavenly_economy_question` / `faq_heavenly_economy_answer`
- `faq_kingdom_training_for_question` / `faq_kingdom_training_for_answer`

### Footer Definitions
- `definition_m2dmm`
- `definition_dmm`
- `definition_persons_of_peace`
- `definition_heavenly_economy`
- `definition_unreached_peoples`

### Course Metadata (Optional)
- `course_educational_level` (e.g., "Professional Development")
- `course_teaches_m2dmm`
- `course_teaches_digital_evangelism`
- `course_teaches_online_ministry`
- `course_teaches_dmm`

### Admin/Developer UI
- `admin_edit_link_title`
- `admin_edit_link_aria_label`
- `ui_no_languages_available`
- `ui_no_languages_title` (optional - developer message)

---

## Implementation Priority

1. **High Priority:** SEO descriptions and titles (affects user experience)
2. **Medium Priority:** KeyInfoSection content (visible to users)
3. **Medium Priority:** FAQ structured data (affects SEO/AEO)
4. **Low Priority:** Footer definitions (hidden but accessible to crawlers)
5. **Optional:** Course metadata (technical/structured data)

---

## Notes

- **SEO Keywords:** May intentionally remain in English for SEO purposes, but consider translating for international SEO
- **Structured Data:** Balance between SEO benefits (English) and internationalization (translated)
- **Brand Names:** Keep in English (Kingdom.Training, Disciple.Tools)
- **Console Messages:** Keep in English (developer-facing)
- **Video Titles:** Should be translated for accessibility

---

## Files Requiring Updates

1. `frontend/src/pages/HomePage.tsx` - SEO, video title, KeyInfoSection
2. `frontend/src/components/Footer.tsx` - FAQ, definitions
3. `frontend/src/pages/ArticlesPage.tsx` - SEO description
4. `frontend/src/pages/ToolsPage.tsx` - SEO description
5. `frontend/src/pages/LoginPage.tsx` - SEO description
6. `frontend/src/pages/NewsletterPage.tsx` - SEO description
7. `frontend/src/pages/StrategyCourseDetailPage.tsx` - Educational metadata, breadcrumb name
8. `frontend/src/components/AdminEditLink.tsx` - Title and aria-label
9. `frontend/src/components/LanguageSelector.tsx` - aria-label and title
10. `frontend/src/lib/translations.ts` - Add all new translation keys

---

## Next Steps

1. Add all new translation keys to `translations.ts` interface and default translations
2. Update components to use `t()` function for hard-coded strings
3. Consider creating separate translation keys for structured data vs. user-facing content
4. Test with different languages
5. Decide on SEO strategy (translate vs. keep English for SEO)

