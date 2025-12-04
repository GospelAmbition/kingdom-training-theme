import { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import { Mail, ArrowRight } from 'lucide-react';
import { renderShortcode } from '@/lib/wordpress';
import { useTranslation } from '@/hooks/useTranslation';

interface NewsletterCTAProps {
  variant?: 'inline' | 'banner' | 'card';
  title?: string;
  description?: string;
  showEmailInput?: boolean;
  className?: string;
  whiteBackground?: boolean;
  noWrapper?: boolean;
}

export default function NewsletterCTA({
  variant = 'inline',
  title,
  description,
  showEmailInput = false,
  className = '',
  whiteBackground = false,
  noWrapper = false
}: NewsletterCTAProps) {
  const { t } = useTranslation();
  const [shortcodeHtml, setShortcodeHtml] = useState<string>('');
  const [shortcodeLoading, setShortcodeLoading] = useState(true);
  const formContainerRef = useRef<HTMLDivElement>(null);

  // Fetch and render the shortcode only when showEmailInput is true
  useEffect(() => {
    // Skip fetch if we're not showing the email input
    if (!showEmailInput) {
      setShortcodeLoading(false);
      return;
    }
    
    async function fetchShortcode() {
      try {
        const html = await renderShortcode('[go_display_opt_in source="kt_news" name="Kingdom.Training"]');
        setShortcodeHtml(html);
      } catch (error) {
        console.error('Error fetching shortcode:', error);
        // Silently fail - don't show error to user if shortcode fails
      } finally {
        setShortcodeLoading(false);
      }
    }
    fetchShortcode();
  }, [showEmailInput]);

  // Intercept form submission after shortcode is rendered
  useEffect(() => {
    if (!shortcodeHtml || shortcodeLoading || !showEmailInput) return;

    const formElement = formContainerRef.current?.querySelector('form');
    const submitButtonElement = formContainerRef.current?.querySelector('#go-submit-form-button');

    if (!formElement || !submitButtonElement) return;

    // Store references that TypeScript knows are non-null
    const form = formElement;
    const submitButton = submitButtonElement;

    // Initialize global token storage
    (window as any).cf_token = null;

    // Cloudflare Turnstile site key (fallback if not provided by server)
    const TURNSTILE_SITE_KEY = '0x4AAAAAAA1dT7LSth0AgFDm';

    // Ensure Turnstile script is loaded
    // Note: Scripts in dangerouslySetInnerHTML don't execute, so we must load it ourselves
    const ensureTurnstileScript = (): Promise<void> => {
      return new Promise((resolve) => {
        // Check if Turnstile API is already available
        if (typeof (window as any).turnstile !== 'undefined') {
          console.log('Turnstile API already loaded');
          resolve();
          return;
        }

        // Remove any non-functional script tags from shortcode (they don't execute via dangerouslySetInnerHTML)
        const deadScripts = document.querySelectorAll('script[src*="challenges.cloudflare.com/turnstile"]');
        deadScripts.forEach(script => {
          // Check if this script actually loaded (API would be available)
          // If API is not available, this script tag is "dead" and should be removed
          if (typeof (window as any).turnstile === 'undefined') {
            script.remove();
          }
        });

        // Load the script fresh with explicit render mode
        const script = document.createElement('script');
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
        script.async = true;
        script.onload = () => {
          console.log('Turnstile script loaded successfully');
          resolve();
        };
        script.onerror = (e) => {
          console.error('Failed to load Turnstile script:', e);
          resolve(); // Resolve anyway to avoid hanging
        };
        document.head.appendChild(script);
      });
    };

    // Initialize Turnstile widget
    const initializeTurnstileWidget = async () => {
      console.log('Initializing Turnstile widget...');
      
      // Ensure script is loaded FIRST
      await ensureTurnstileScript();
      
      // Verify Turnstile API is available
      if (typeof (window as any).turnstile === 'undefined') {
        console.error('Turnstile API not available after script load');
        return;
      }
      
      let turnstileWidget = form.querySelector('.cf-turnstile') as HTMLElement;
      
      // Create widget container if it doesn't exist
      if (!turnstileWidget) {
        console.log('Creating Turnstile widget container');
        turnstileWidget = document.createElement('div');
        turnstileWidget.className = 'cf-turnstile';
        turnstileWidget.id = 'turnstile-widget';
        // Insert before submit button
        const submitBtn = form.querySelector('#go-submit-form-button');
        if (submitBtn && submitBtn.parentNode) {
          submitBtn.parentNode.insertBefore(turnstileWidget, submitBtn);
        } else {
          form.appendChild(turnstileWidget);
        }
      }

      // Always set/update site key to ensure it's correct
      turnstileWidget.setAttribute('data-sitekey', TURNSTILE_SITE_KEY);
      turnstileWidget.setAttribute('data-theme', 'light');

      // Check if widget is already rendered
      const existingIframe = turnstileWidget.querySelector('iframe');
      if (existingIframe) {
        console.log('Turnstile widget already rendered');
        return;
      }

      // Clear any previous content in the widget container
      turnstileWidget.innerHTML = '';

      // Render the widget explicitly
      try {
        console.log('Rendering Turnstile widget with site key:', TURNSTILE_SITE_KEY);
        const widgetId = (window as any).turnstile.render(turnstileWidget, {
          sitekey: TURNSTILE_SITE_KEY,
          theme: 'light',
          callback: (token: string) => {
            console.log('Turnstile token received');
            (window as any).cf_token = token;
            // Also call save_cf if it exists
            if (typeof (window as any).save_cf === 'function') {
              (window as any).save_cf(token);
            }
          },
          'error-callback': () => {
            console.error('Turnstile widget error');
          },
          'expired-callback': () => {
            console.log('Turnstile token expired');
            (window as any).cf_token = null;
          },
        });
        console.log('Turnstile widget rendered, ID:', widgetId);
      } catch (error) {
        console.error('Error rendering Turnstile widget:', error);
      }
    };

    // Wait for Turnstile script to load and widget to render
    const checkTurnstileReady = () => {
      const turnstileWidget = form.querySelector('.cf-turnstile');
      const siteKey = turnstileWidget?.getAttribute('data-sitekey');
      
      // If no site key is configured, skip widget checks
      if (!siteKey) {
        console.warn('Cloudflare Turnstile site key not configured');
        return;
      }
      
      // Check if Turnstile API is loaded
      if (typeof (window as any).turnstile === 'undefined') {
        console.log('Waiting for Cloudflare Turnstile script to load...');
        return false;
      }
      
      // Check if widget has rendered (has an iframe)
      const widgetIframe = turnstileWidget?.querySelector('iframe');
      if (!widgetIframe) {
        console.log('Waiting for Cloudflare Turnstile widget to render...');
        return false;
      }
      
      return true;
    };

    // Set up save_cf callback globally BEFORE initializing widget
    (window as any).save_cf = function(token: string) {
      console.log('save_cf called with token');
      (window as any).cf_token = token;
    };

    // Initialize Turnstile widget after a short delay to ensure DOM is ready
    const initTimeoutId = setTimeout(async () => {
      await initializeTurnstileWidget();
      
      // Check if widget is ready after a delay
      setTimeout(() => {
        if (!checkTurnstileReady()) {
          console.warn('Turnstile widget may not have rendered properly. Retrying...');
          // Try to re-render
          initializeTurnstileWidget();
        }
      }, 2000);
    }, 200);

    // Extract nonce from the rendered HTML script tag
    const extractNonce = (): string | null => {
      const scripts = formContainerRef.current?.querySelectorAll('script');
      if (!scripts) return null;
      
      for (const script of Array.from(scripts)) {
        const scriptContent = script.textContent || '';
        const nonceMatch = scriptContent.match(/X-WP-Nonce['"]:\s*['"]([^'"]+)['"]/);
        if (nonceMatch) {
          return nonceMatch[1];
        }
      }
      return null;
    };

    // Get Cloudflare token from global variable (set by Turnstile widget)
    const getCloudflareToken = (): string | null => {
      // Access the global cf_token variable set by the Turnstile widget callback
      return (window as any).cf_token || null;
    };

    const handleSubmit = async (e: Event) => {
      e.preventDefault();
      e.stopPropagation();

      try {
        // Get form data
        const email2 = (form.querySelector('input[name="email2"]') as HTMLInputElement)?.value || '';
        const email = (form.querySelector('input[name="email"]') as HTMLInputElement)?.value || '';
        const firstName = (form.querySelector('input[name="first_name"]') as HTMLInputElement)?.value || '';
        const lastName = (form.querySelector('input[name="last_name"]') as HTMLInputElement)?.value || '';
        const confirmSubscribe = (form.querySelector('#confirm-subscribe') as HTMLInputElement)?.checked;

        // Validate honeypot (email field should be empty)
        if (email) {
          return; // Honeypot caught - ignore submission
        }

        // Validate checkbox
        if (!confirmSubscribe) {
          const errorSpan = form.querySelector('.dt-form-error');
          if (errorSpan) {
            errorSpan.textContent = t('newsletter_confirm_subscribe');
            (errorSpan as HTMLElement).style.display = 'block';
          }
          return;
        }

        // Get required tokens
        const nonce = extractNonce();
        const cfToken = getCloudflareToken();

        if (!nonce) {
          console.error('WordPress nonce not found');
          throw new Error(t('error_security_token_not_found'));
        }

        // Check if Turnstile widget exists and is visible
        const turnstileWidget = form.querySelector('.cf-turnstile');
        const siteKey = turnstileWidget?.getAttribute('data-sitekey');
        
        // Only require token if widget is configured
        if (siteKey && !cfToken) {
          const errorSpan = form.querySelector('.dt-form-error');
          if (errorSpan) {
            // Check if widget is actually visible
            const widgetVisible = turnstileWidget && 
              (turnstileWidget as HTMLElement).offsetHeight > 0 && 
              (turnstileWidget as HTMLElement).offsetWidth > 0;
            
            if (!widgetVisible) {
              errorSpan.textContent = t('newsletter_security_loading');
            } else {
              errorSpan.textContent = t('newsletter_security_complete');
            }
            (errorSpan as HTMLElement).style.display = 'block';
            
            // Scroll to widget if it exists
            if (turnstileWidget) {
              turnstileWidget.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          }
          return;
        }
        
        // If widget is not configured, log a warning but don't block submission
        if (!siteKey) {
          console.warn('Cloudflare Turnstile site key not configured. Form submission may fail on the server.');
        }

        // Get the form action URL (should be the double-optin endpoint)
        const formAction = form.getAttribute('action') || '/wp-json/go-webform/double-optin';
        const apiUrl = formAction.startsWith('http') 
          ? formAction 
          : `${window.location.origin}${formAction}`;

        // Disable button during submission
        submitButton.setAttribute('disabled', 'disabled');
        const originalText = submitButton.textContent;
        submitButton.textContent = t('ui_submitting');

        // Hide any previous errors
        const errorSpan = form.querySelector('.dt-form-error');
        if (errorSpan) {
          (errorSpan as HTMLElement).style.display = 'none';
        }

        // Submit via fetch with proper headers and JSON body
        const response = await fetch(apiUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': nonce,
          },
          body: JSON.stringify({
            email: email2,
            first_name: firstName,
            last_name: lastName,
            source: 'kt_news',
            cf_turnstile: cfToken,
          }),
        });

        const data = await response.json();

        if (response.ok && data !== false) {
          submitButton.textContent = t('newsletter_subscribed');
          submitButton.classList.add('bg-green-500');
          form.reset();
          // Show success message
          const successDiv = form.querySelector('.dt-form-success');
          if (successDiv) {
            successDiv.textContent = t('newsletter_check_email');
            (successDiv as HTMLElement).style.display = 'block';
          }
          // Reset button after 3 seconds
          setTimeout(() => {
            submitButton.removeAttribute('disabled');
            submitButton.textContent = originalText;
            submitButton.classList.remove('bg-green-500');
          }, 3000);
        } else {
          submitButton.removeAttribute('disabled');
          submitButton.textContent = originalText || t('newsletter_try_again');
          // Show error message
          if (errorSpan) {
            errorSpan.textContent = data?.message || t('error_subscribe_failed');
            (errorSpan as HTMLElement).style.display = 'block';
          }
        }
      } catch (error) {
        console.error('Error submitting form:', error);
        submitButton.removeAttribute('disabled');
        submitButton.textContent = t('newsletter_try_again');
        const errorSpan = form.querySelector('.dt-form-error');
        if (errorSpan) {
          errorSpan.textContent = t('error_subscribe_failed');
          (errorSpan as HTMLElement).style.display = 'block';
        }
      }
    };

    form.addEventListener('submit', handleSubmit);

    return () => {
      clearTimeout(initTimeoutId);
      form.removeEventListener('submit', handleSubmit);
    };
  }, [shortcodeHtml, shortcodeLoading, showEmailInput]);

  const defaultTitle = title || t('newsletter_stay_connected');
  const defaultDescription = description || t('newsletter_default_description');

  if (variant === 'banner') {
    const bgClasses = whiteBackground 
      ? 'bg-white text-gray-900' 
      : 'bg-gradient-to-r from-primary-800 to-primary-600 text-white';
    const titleClasses = whiteBackground ? 'text-gray-900' : '';
    const descriptionClasses = whiteBackground ? 'text-gray-700' : 'text-primary-100';
    const iconClasses = whiteBackground ? 'text-primary-600' : 'text-accent-500';
    
    const content = (
      <div className={`${noWrapper ? '' : bgClasses} ${noWrapper ? '' : 'py-12'} ${className}`}>
        <div className="container-custom">
          <div className="max-w-4xl mx-auto text-center">
            <Mail className={`w-12 h-12 mx-auto mb-4 ${iconClasses}`} />
            <h2 className={`text-3xl font-bold mb-4 ${titleClasses}`}>{defaultTitle}</h2>
            <p className={`text-xl mb-8 max-w-2xl mx-auto ${descriptionClasses}`}>{defaultDescription}</p>
            
            {showEmailInput ? (
              <div className="max-w-md mx-auto" ref={formContainerRef}>
                {shortcodeLoading ? (
                  <div className="flex items-center justify-center py-4">
                    <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  </div>
                ) : shortcodeHtml ? (
                  <div dangerouslySetInnerHTML={{ __html: shortcodeHtml }} />
                ) : null}
              </div>
            ) : (
              <Link
                to="/newsletter"
                className={`inline-flex items-center justify-center px-8 py-4 font-semibold rounded-lg transition-colors duration-200 text-lg ${
                  whiteBackground 
                    ? 'bg-primary-600 hover:bg-primary-700 text-white' 
                    : 'bg-accent-600 hover:bg-accent-500 text-secondary-900'
                }`}
              >
                {t('nav_subscribe_newsletter')}
                <ArrowRight className="w-5 h-5 ml-2" />
              </Link>
            )}
          </div>
        </div>
      </div>
    );

    if (noWrapper) {
      return content;
    }

    return <section>{content}</section>;
  }

  if (variant === 'card') {
    return (
      <div className={`bg-background-50 border-2 border-primary-200 rounded-lg p-6 md:p-8 ${className}`}>
        <div className="flex items-start gap-4">
          <div className="flex-shrink-0 w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center">
            <Mail className="w-6 h-6 text-white" />
          </div>
          <div className="flex-1">
            <h3 className="text-xl font-bold text-gray-900 mb-2">{defaultTitle}</h3>
            <p className="text-gray-700 mb-4">{defaultDescription}</p>
            
            {showEmailInput ? (
              <div ref={formContainerRef}>
                {shortcodeLoading ? (
                  <div className="flex items-center justify-center py-4">
                    <div className="w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                  </div>
                ) : shortcodeHtml ? (
                  <div dangerouslySetInnerHTML={{ __html: shortcodeHtml }} />
                ) : null}
              </div>
            ) : (
              <Link
                to="/newsletter"
                className="inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold"
              >
                {t('nav_subscribe_now')}
                <ArrowRight className="w-4 h-4 ml-1" />
              </Link>
            )}
          </div>
        </div>
      </div>
    );
  }

  // Default inline variant
  return (
    <div className={`flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-primary-50 rounded-lg ${className}`}>
      <div className="flex-1">
        <h3 className="text-lg font-semibold text-gray-900 mb-1">{defaultTitle}</h3>
        <p className="text-sm text-gray-700">{defaultDescription}</p>
      </div>
      {showEmailInput ? (
        <div className="w-full sm:w-auto" ref={formContainerRef}>
          {shortcodeLoading ? (
            <div className="flex items-center justify-center py-2">
              <div className="w-5 h-5 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
          ) : shortcodeHtml ? (
            <div dangerouslySetInnerHTML={{ __html: shortcodeHtml }} />
          ) : null}
        </div>
      ) : (
        <Link
          to="/newsletter"
          className="inline-flex items-center px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg transition-colors whitespace-nowrap"
        >
          {t('newsletter_subscribe')}
          <ArrowRight className="w-4 h-4 ml-2" />
        </Link>
      )}
    </div>
  );
}

