<?php
/**
 * Auto Translate Dashboard Template
 * 
 * Main dashboard view for translation management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get scanner for summary data
$scanner = new GAAL_Translation_Scanner();
$summary = $scanner->get_summary();
$post_types = $scanner->get_post_types_with_labels();

// Get enabled languages
$dashboard = GAAL_Translation_Dashboard::get_instance();
$languages = $dashboard->get_enabled_languages_data();
?>

<div class="wrap gaal-auto-translate-dashboard">
    <h1><?php echo esc_html__('Auto Translate Dashboard', 'kingdom-training'); ?></h1>
    
    <!-- Tab Navigation -->
    <nav class="nav-tab-wrapper gaal-tabs">
        <a href="#overview" class="nav-tab nav-tab-active" data-tab="overview">
            <span class="dashicons dashicons-dashboard"></span>
            <?php echo esc_html__('Overview', 'kingdom-training'); ?>
        </a>
        <a href="#gaps" class="nav-tab" data-tab="gaps">
            <span class="dashicons dashicons-warning"></span>
            <?php echo esc_html__('Translation Gaps', 'kingdom-training'); ?>
        </a>
        <a href="#translations" class="nav-tab" data-tab="translations">
            <span class="dashicons dashicons-admin-site-alt3"></span>
            <?php echo esc_html__('Translations', 'kingdom-training'); ?>
        </a>
        <a href="#strings" class="nav-tab" data-tab="strings">
            <span class="dashicons dashicons-editor-textcolor"></span>
            <?php echo esc_html__('Strings', 'kingdom-training'); ?>
        </a>
        <a href="#queue" class="nav-tab" data-tab="queue">
            <span class="dashicons dashicons-clock"></span>
            <?php echo esc_html__('Queue', 'kingdom-training'); ?>
        </a>
        <a href="#history" class="nav-tab" data-tab="history">
            <span class="dashicons dashicons-backup"></span>
            <?php echo esc_html__('History', 'kingdom-training'); ?>
        </a>
    </nav>
    
    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Overview Tab -->
        <div id="overview" class="tab-pane active">
            <!-- Summary Cards -->
            <div class="gaal-summary-cards">
                <div class="gaal-card">
                    <div class="gaal-card-icon">
                        <span class="dashicons dashicons-media-document"></span>
                    </div>
                    <div class="gaal-card-content">
                        <div class="gaal-card-number" id="stat-posts-needing"><?php echo esc_html($summary['posts_needing_translation']); ?></div>
                        <div class="gaal-card-label"><?php echo esc_html__('Posts Need Translation', 'kingdom-training'); ?></div>
                    </div>
                </div>
                <div class="gaal-card">
                    <div class="gaal-card-icon">
                        <span class="dashicons dashicons-translation"></span>
                    </div>
                    <div class="gaal-card-content">
                        <div class="gaal-card-number" id="stat-translations-needed"><?php echo esc_html($summary['total_translations_needed']); ?></div>
                        <div class="gaal-card-label"><?php echo esc_html__('Total Translations Needed', 'kingdom-training'); ?></div>
                    </div>
                </div>
                <div class="gaal-card">
                    <div class="gaal-card-icon">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
                    </div>
                    <div class="gaal-card-content">
                        <div class="gaal-card-number" id="stat-languages"><?php echo esc_html($summary['languages_enabled']); ?></div>
                        <div class="gaal-card-label"><?php echo esc_html__('Languages Enabled', 'kingdom-training'); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="gaal-section">
                <h2><?php echo esc_html__('Quick Actions', 'kingdom-training'); ?></h2>
                <div class="gaal-quick-actions">
                    <button type="button" class="button button-primary button-hero" id="btn-scan">
                        <span class="dashicons dashicons-search"></span>
                        <?php echo esc_html__('Scan for Gaps', 'kingdom-training'); ?>
                    </button>
                    <button type="button" class="button button-secondary button-hero" id="btn-create-all-drafts" disabled>
                        <span class="dashicons dashicons-welcome-add-page"></span>
                        <?php echo esc_html__('Create All Drafts', 'kingdom-training'); ?>
                    </button>
                    <button type="button" class="button button-primary button-hero" id="btn-translate-all" disabled>
                        <span class="dashicons dashicons-translation"></span>
                        <?php echo esc_html__('Translate All', 'kingdom-training'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Progress Section (hidden until active) -->
            <div class="gaal-progress-section" id="progress-section" style="display: none;">
                <h3>
                    <span class="dashicons dashicons-update gaal-spinning"></span>
                    <?php echo esc_html__('Translation Progress', 'kingdom-training'); ?>
                </h3>
                <div class="gaal-progress-bar-container">
                    <div class="gaal-progress-bar" id="overall-progress" style="width: 0%"></div>
                </div>
                <div class="gaal-progress-stats">
                    <span id="progress-completed">0</span> / <span id="progress-total">0</span> <?php echo esc_html__('completed', 'kingdom-training'); ?>
                    <span class="gaal-progress-failed" id="progress-failed-container" style="display: none;">
                        (<span id="progress-failed">0</span> <?php echo esc_html__('failed', 'kingdom-training'); ?>)
                    </span>
                </div>
                <div class="gaal-current-item" id="current-item">
                    <!-- Current translation details -->
                </div>
                <div class="gaal-progress-controls">
                    <button type="button" class="button" id="btn-pause">
                        <span class="dashicons dashicons-controls-pause"></span>
                        <?php echo esc_html__('Pause', 'kingdom-training'); ?>
                    </button>
                    <button type="button" class="button" id="btn-resume" style="display: none;">
                        <span class="dashicons dashicons-controls-play"></span>
                        <?php echo esc_html__('Resume', 'kingdom-training'); ?>
                    </button>
                    <button type="button" class="button button-link-delete" id="btn-cancel">
                        <span class="dashicons dashicons-no"></span>
                        <?php echo esc_html__('Cancel', 'kingdom-training'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Language Breakdown -->
            <?php if (!empty($summary['by_language'])): ?>
            <div class="gaal-section">
                <h2><?php echo esc_html__('By Language', 'kingdom-training'); ?></h2>
                <div class="gaal-language-grid">
                    <?php foreach ($summary['by_language'] as $lang_code => $count): ?>
                        <div class="gaal-language-card">
                            <div class="gaal-lang-code"><?php echo esc_html(strtoupper($lang_code)); ?></div>
                            <div class="gaal-lang-name">
                                <?php echo isset($languages[$lang_code]) ? esc_html($languages[$lang_code]['name']) : esc_html($lang_code); ?>
                            </div>
                            <div class="gaal-lang-count"><?php echo esc_html($count); ?> <?php echo esc_html__('missing', 'kingdom-training'); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Post Type Breakdown -->
            <?php if (!empty($summary['by_post_type'])): ?>
            <div class="gaal-section">
                <h2><?php echo esc_html__('By Post Type', 'kingdom-training'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Post Type', 'kingdom-training'); ?></th>
                            <th><?php echo esc_html__('Posts Needing Translation', 'kingdom-training'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($summary['by_post_type'] as $pt => $count): ?>
                        <tr>
                            <td><?php echo isset($post_types[$pt]) ? esc_html($post_types[$pt]) : esc_html($pt); ?></td>
                            <td><?php echo esc_html($count); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Gaps Tab -->
        <div id="gaps" class="tab-pane">
            <!-- Filters -->
            <div class="gaal-filters">
                <select id="filter-post-type">
                    <option value=""><?php echo esc_html__('All Post Types', 'kingdom-training'); ?></option>
                    <?php foreach ($post_types as $pt_slug => $pt_label): ?>
                        <option value="<?php echo esc_attr($pt_slug); ?>"><?php echo esc_html($pt_label); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="filter-language">
                    <option value=""><?php echo esc_html__('All Languages', 'kingdom-training'); ?></option>
                    <?php foreach ($languages as $lang_code => $lang_data): ?>
                        <?php if ($lang_code !== 'en'): ?>
                            <option value="<?php echo esc_attr($lang_code); ?>"><?php echo esc_html($lang_data['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                
                <button type="button" class="button" id="btn-refresh-gaps">
                    <span class="dashicons dashicons-update"></span>
                    <?php echo esc_html__('Refresh', 'kingdom-training'); ?>
                </button>
            </div>
            
            <!-- Bulk Actions -->
            <div class="gaal-bulk-actions">
                <label class="gaal-select-all-label">
                    <input type="checkbox" id="select-all-gaps">
                    <?php echo esc_html__('Select All', 'kingdom-training'); ?>
                </label>
                <span class="gaal-selected-count" id="selected-count">0 <?php echo esc_html__('selected', 'kingdom-training'); ?></span>
                <button type="button" class="button" id="btn-create-selected-drafts" disabled>
                    <span class="dashicons dashicons-welcome-add-page"></span>
                    <?php echo esc_html__('Create Drafts for Selected', 'kingdom-training'); ?>
                </button>
                <button type="button" class="button button-primary" id="btn-translate-selected" disabled>
                    <span class="dashicons dashicons-translation"></span>
                    <?php echo esc_html__('Translate Selected', 'kingdom-training'); ?>
                </button>
            </div>
            
            <!-- Gaps Table -->
            <table class="wp-list-table widefat fixed striped" id="gaps-table">
                <thead>
                    <tr>
                        <th class="check-column"><input type="checkbox" id="select-all-header"></th>
                        <th class="column-title"><?php echo esc_html__('Title', 'kingdom-training'); ?></th>
                        <th class="column-type"><?php echo esc_html__('Type', 'kingdom-training'); ?></th>
                        <th class="column-missing"><?php echo esc_html__('Missing Languages', 'kingdom-training'); ?></th>
                        <th class="column-existing"><?php echo esc_html__('Existing', 'kingdom-training'); ?></th>
                        <th class="column-chunks"><?php echo esc_html__('Est. Chunks', 'kingdom-training'); ?></th>
                        <th class="column-actions"><?php echo esc_html__('Actions', 'kingdom-training'); ?></th>
                    </tr>
                </thead>
                <tbody id="gaps-tbody">
                    <tr class="gaal-loading-row">
                        <td colspan="7">
                            <span class="spinner is-active"></span>
                            <?php echo esc_html__('Click "Scan for Gaps" to find posts needing translation...', 'kingdom-training'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="gaal-no-gaps" id="no-gaps-message" style="display: none;">
                <span class="dashicons dashicons-yes-alt"></span>
                <p><?php echo esc_html__('No translation gaps found! All content is translated.', 'kingdom-training'); ?></p>
            </div>
        </div>
        
        <!-- Translations Tab -->
        <div id="translations" class="tab-pane">
            <!-- Filters -->
            <div class="gaal-filters">
                <select id="translations-filter-post-type">
                    <option value=""><?php echo esc_html__('All Post Types', 'kingdom-training'); ?></option>
                    <?php foreach ($post_types as $pt_slug => $pt_label): ?>
                        <option value="<?php echo esc_attr($pt_slug); ?>"><?php echo esc_html($pt_label); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="translations-filter-language">
                    <option value=""><?php echo esc_html__('All Languages', 'kingdom-training'); ?></option>
                    <?php foreach ($languages as $lang_code => $lang_data): ?>
                        <?php if ($lang_code !== 'en'): ?>
                            <option value="<?php echo esc_attr($lang_code); ?>"><?php echo esc_html($lang_data['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                
                <select id="translations-filter-status">
                    <option value=""><?php echo esc_html__('All Statuses', 'kingdom-training'); ?></option>
                    <option value="publish"><?php echo esc_html__('Published', 'kingdom-training'); ?></option>
                    <option value="draft"><?php echo esc_html__('Draft', 'kingdom-training'); ?></option>
                    <option value="pending"><?php echo esc_html__('Pending', 'kingdom-training'); ?></option>
                </select>
                
                <button type="button" class="button" id="btn-load-translations">
                    <span class="dashicons dashicons-update"></span>
                    <?php echo esc_html__('Load Translations', 'kingdom-training'); ?>
                </button>
            </div>
            
            <!-- Bulk Actions -->
            <div class="gaal-bulk-actions">
                <label class="gaal-select-all-label">
                    <input type="checkbox" id="select-all-translations">
                    <?php echo esc_html__('Select All', 'kingdom-training'); ?>
                </label>
                <span class="gaal-selected-count" id="translations-selected-count">0 <?php echo esc_html__('selected', 'kingdom-training'); ?></span>
                <button type="button" class="button" id="btn-retranslate-selected" disabled>
                    <span class="dashicons dashicons-update"></span>
                    <?php echo esc_html__('Re-translate Selected', 'kingdom-training'); ?>
                </button>
                <button type="button" class="button button-secondary" id="btn-llm-review-selected" disabled>
                    <span class="dashicons dashicons-welcome-learn-more"></span>
                    <?php echo esc_html__('LLM Review Selected', 'kingdom-training'); ?>
                </button>
            </div>
            
            <!-- Translations Table -->
            <table class="wp-list-table widefat fixed striped" id="translations-table">
                <thead>
                    <tr>
                        <th class="check-column"><input type="checkbox" id="select-all-translations-header"></th>
                        <th class="column-title"><?php echo esc_html__('Title', 'kingdom-training'); ?></th>
                        <th class="column-source"><?php echo esc_html__('Source (EN)', 'kingdom-training'); ?></th>
                        <th class="column-language"><?php echo esc_html__('Language', 'kingdom-training'); ?></th>
                        <th class="column-type"><?php echo esc_html__('Type', 'kingdom-training'); ?></th>
                        <th class="column-status"><?php echo esc_html__('Status', 'kingdom-training'); ?></th>
                        <th class="column-score"><?php echo esc_html__('LLM Score', 'kingdom-training'); ?></th>
                        <th class="column-actions"><?php echo esc_html__('Actions', 'kingdom-training'); ?></th>
                    </tr>
                </thead>
                <tbody id="translations-tbody">
                    <tr class="gaal-loading-row">
                        <td colspan="8">
                            <span class="spinner is-active"></span>
                            <?php echo esc_html__('Click "Load Translations" to view existing translations...', 'kingdom-training'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="gaal-no-translations" id="no-translations-message" style="display: none;">
                <span class="dashicons dashicons-info"></span>
                <p><?php echo esc_html__('No translations found matching your filters.', 'kingdom-training'); ?></p>
            </div>
        </div>
        
        <!-- LLM Review Modal -->
        <div id="llm-review-modal" class="gaal-modal" style="display: none;">
            <div class="gaal-modal-content">
                <div class="gaal-modal-header">
                    <h2><?php echo esc_html__('LLM Translation Review', 'kingdom-training'); ?></h2>
                    <button type="button" class="gaal-modal-close">&times;</button>
                </div>
                <div class="gaal-modal-body">
                    <div class="gaal-review-info">
                        <p><strong><?php echo esc_html__('Post:', 'kingdom-training'); ?></strong> <span id="review-post-title"></span></p>
                        <p><strong><?php echo esc_html__('Language:', 'kingdom-training'); ?></strong> <span id="review-language"></span></p>
                    </div>
                    <div class="gaal-review-progress" id="review-progress" style="display: none;">
                        <span class="spinner is-active"></span>
                        <span id="review-progress-text"><?php echo esc_html__('Evaluating translation...', 'kingdom-training'); ?></span>
                    </div>
                    <div class="gaal-review-result" id="review-result" style="display: none;">
                        <div class="gaal-review-score">
                            <span class="score-label"><?php echo esc_html__('Quality Score:', 'kingdom-training'); ?></span>
                            <span class="score-value" id="review-score">--</span>
                            <span class="score-max">/100</span>
                        </div>
                        <div class="gaal-review-feedback">
                            <h4><?php echo esc_html__('Evaluation Details', 'kingdom-training'); ?></h4>
                            <div id="review-feedback"></div>
                        </div>
                        <div class="gaal-review-actions">
                            <button type="button" class="button button-primary" id="btn-apply-improvement">
                                <span class="dashicons dashicons-yes"></span>
                                <?php echo esc_html__('Apply LLM Improvement', 'kingdom-training'); ?>
                            </button>
                            <button type="button" class="button" id="btn-close-review">
                                <?php echo esc_html__('Close', 'kingdom-training'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Strings Tab -->
        <div id="strings" class="tab-pane">
            <!-- Strings Progress Section -->
            <div class="gaal-progress-section" id="strings-progress-section" style="display: none;">
                <h3>
                    <span class="dashicons dashicons-update gaal-spinning"></span>
                    <?php echo esc_html__('Translation Progress', 'kingdom-training'); ?>
                </h3>
                <div class="gaal-progress-bar-container">
                    <div class="gaal-progress-bar" id="strings-progress" style="width: 0%"></div>
                </div>
                <div class="gaal-progress-stats">
                    <span id="strings-progress-completed">0</span> / <span id="strings-progress-total">0</span> <?php echo esc_html__('strings translated', 'kingdom-training'); ?>
                </div>
            </div>
            
            <!-- Summary Cards -->
            <div class="gaal-strings-summary">
                <div class="gaal-summary-cards">
                    <div class="gaal-card gaal-card-small">
                        <div class="gaal-card-content">
                            <div class="gaal-card-number" id="strings-total">--</div>
                            <div class="gaal-card-label"><?php echo esc_html__('Total Strings', 'kingdom-training'); ?></div>
                        </div>
                    </div>
                    <div class="gaal-card gaal-card-small">
                        <div class="gaal-card-content">
                            <div class="gaal-card-number gaal-text-success" id="strings-complete">--</div>
                            <div class="gaal-card-label"><?php echo esc_html__('Fully Translated', 'kingdom-training'); ?></div>
                        </div>
                    </div>
                    <div class="gaal-card gaal-card-small">
                        <div class="gaal-card-content">
                            <div class="gaal-card-number gaal-text-warning" id="strings-incomplete">--</div>
                            <div class="gaal-card-label"><?php echo esc_html__('Need Translation', 'kingdom-training'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="gaal-filters">
                <select id="strings-filter-language">
                    <option value=""><?php echo esc_html__('Select Language', 'kingdom-training'); ?></option>
                    <?php foreach ($languages as $lang_code => $lang_data): ?>
                        <?php if ($lang_code !== 'en'): ?>
                            <option value="<?php echo esc_attr($lang_code); ?>"><?php echo esc_html($lang_data['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                
                <select id="strings-filter-group">
                    <option value=""><?php echo esc_html__('All Groups', 'kingdom-training'); ?></option>
                    <!-- Populated dynamically -->
                </select>
                
                <select id="strings-filter-status">
                    <option value=""><?php echo esc_html__('All Strings', 'kingdom-training'); ?></option>
                    <option value="missing"><?php echo esc_html__('Missing Translation', 'kingdom-training'); ?></option>
                    <option value="translated"><?php echo esc_html__('Already Translated', 'kingdom-training'); ?></option>
                </select>
                
                <button type="button" class="button" id="btn-load-strings">
                    <span class="dashicons dashicons-update"></span>
                    <?php echo esc_html__('Load Strings', 'kingdom-training'); ?>
                </button>
            </div>
            
            <!-- Bulk Actions -->
            <div class="gaal-bulk-actions">
                <label class="gaal-select-all-label">
                    <input type="checkbox" id="select-all-strings">
                    <?php echo esc_html__('Select All Visible', 'kingdom-training'); ?>
                </label>
                <span class="gaal-selected-count" id="strings-selected-count">0 <?php echo esc_html__('selected', 'kingdom-training'); ?></span>
                <button type="button" class="button button-primary" id="btn-translate-strings" disabled>
                    <span class="dashicons dashicons-translation"></span>
                    <?php echo esc_html__('Translate Selected', 'kingdom-training'); ?>
                </button>
            </div>
            
            <!-- Strings Table -->
            <table class="wp-list-table widefat fixed striped" id="strings-table">
                <thead>
                    <tr>
                        <th class="check-column"><input type="checkbox" id="select-all-strings-header"></th>
                        <th class="column-group"><?php echo esc_html__('Group', 'kingdom-training'); ?></th>
                        <th class="column-original"><?php echo esc_html__('Original (English)', 'kingdom-training'); ?></th>
                        <th class="column-translation"><?php echo esc_html__('Translation', 'kingdom-training'); ?></th>
                        <th class="column-actions"><?php echo esc_html__('Actions', 'kingdom-training'); ?></th>
                    </tr>
                </thead>
                <tbody id="strings-tbody">
                    <tr class="gaal-loading-row">
                        <td colspan="5">
                            <?php echo esc_html__('Select a language and click "Load Strings" to view translatable strings.', 'kingdom-training'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="gaal-no-strings" id="no-strings-message" style="display: none;">
                <span class="dashicons dashicons-info"></span>
                <p><?php echo esc_html__('No strings found matching your filters.', 'kingdom-training'); ?></p>
            </div>
        </div>
        
        <!-- String Edit Modal -->
        <div id="string-edit-modal" class="gaal-modal" style="display: none;">
            <div class="gaal-modal-content">
                <div class="gaal-modal-header">
                    <h2><?php echo esc_html__('Edit String Translation', 'kingdom-training'); ?></h2>
                    <button type="button" class="gaal-modal-close" id="string-modal-close">&times;</button>
                </div>
                <div class="gaal-modal-body">
                    <div class="gaal-string-edit-form">
                        <div class="gaal-form-group">
                            <label><?php echo esc_html__('Original (English):', 'kingdom-training'); ?></label>
                            <div class="gaal-original-text" id="edit-string-original"></div>
                        </div>
                        <div class="gaal-form-group">
                            <label for="edit-string-translation"><?php echo esc_html__('Translation:', 'kingdom-training'); ?></label>
                            <textarea id="edit-string-translation" rows="4" class="large-text"></textarea>
                        </div>
                        <input type="hidden" id="edit-string-value">
                        <input type="hidden" id="edit-string-language">
                    </div>
                    <div class="gaal-modal-actions">
                        <button type="button" class="button" id="btn-auto-translate-string">
                            <span class="dashicons dashicons-translation"></span>
                            <?php echo esc_html__('Auto Translate', 'kingdom-training'); ?>
                        </button>
                        <button type="button" class="button button-primary" id="btn-save-string">
                            <span class="dashicons dashicons-yes"></span>
                            <?php echo esc_html__('Save Translation', 'kingdom-training'); ?>
                        </button>
                        <button type="button" class="button" id="btn-cancel-string">
                            <?php echo esc_html__('Cancel', 'kingdom-training'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Queue Tab -->
        <div id="queue" class="tab-pane">
            <div class="gaal-queue-header">
                <h2><?php echo esc_html__('Translation Queue', 'kingdom-training'); ?></h2>
                <div class="gaal-queue-stats">
                    <span class="gaal-stat">
                        <strong id="queue-pending">0</strong> <?php echo esc_html__('pending', 'kingdom-training'); ?>
                    </span>
                    <span class="gaal-stat">
                        <strong id="queue-processing">0</strong> <?php echo esc_html__('processing', 'kingdom-training'); ?>
                    </span>
                    <span class="gaal-stat">
                        <strong id="queue-completed">0</strong> <?php echo esc_html__('completed', 'kingdom-training'); ?>
                    </span>
                    <span class="gaal-stat gaal-stat-failed">
                        <strong id="queue-failed">0</strong> <?php echo esc_html__('failed', 'kingdom-training'); ?>
                    </span>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped" id="queue-table">
                <thead>
                    <tr>
                        <th class="column-title"><?php echo esc_html__('Title', 'kingdom-training'); ?></th>
                        <th class="column-language"><?php echo esc_html__('Language', 'kingdom-training'); ?></th>
                        <th class="column-status"><?php echo esc_html__('Status', 'kingdom-training'); ?></th>
                        <th class="column-progress"><?php echo esc_html__('Progress', 'kingdom-training'); ?></th>
                        <th class="column-actions"><?php echo esc_html__('Actions', 'kingdom-training'); ?></th>
                    </tr>
                </thead>
                <tbody id="queue-tbody">
                    <tr class="gaal-empty-row">
                        <td colspan="5">
                            <?php echo esc_html__('No items in queue. Select posts from the Gaps tab to start translating.', 'kingdom-training'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- History Tab -->
        <div id="history" class="tab-pane">
            <div class="gaal-history-header">
                <h2><?php echo esc_html__('Translation History', 'kingdom-training'); ?></h2>
                <button type="button" class="button" id="btn-refresh-history">
                    <span class="dashicons dashicons-update"></span>
                    <?php echo esc_html__('Refresh', 'kingdom-training'); ?>
                </button>
            </div>
            
            <table class="wp-list-table widefat fixed striped" id="history-table">
                <thead>
                    <tr>
                        <th class="column-title"><?php echo esc_html__('Title', 'kingdom-training'); ?></th>
                        <th class="column-language"><?php echo esc_html__('Language', 'kingdom-training'); ?></th>
                        <th class="column-status"><?php echo esc_html__('Status', 'kingdom-training'); ?></th>
                        <th class="column-date"><?php echo esc_html__('Date', 'kingdom-training'); ?></th>
                        <th class="column-actions"><?php echo esc_html__('Actions', 'kingdom-training'); ?></th>
                    </tr>
                </thead>
                <tbody id="history-tbody">
                    <tr class="gaal-empty-row">
                        <td colspan="5">
                            <?php echo esc_html__('No translation history available.', 'kingdom-training'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
