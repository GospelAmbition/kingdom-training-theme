/**
 * React Query hooks for Articles
 * Provides intelligent caching and deduplication for article data
 */

import { useQuery } from '@tanstack/react-query';
import { getArticles, getArticleBySlug, getArticleCategories, WordPressPost } from '@/lib/wordpress';
import { queryKeys, STALE_TIMES, CACHE_TIMES } from '@/lib/query-client';

interface ArticlesParams {
  per_page?: number;
  page?: number;
  orderby?: string;
  order?: 'asc' | 'desc';
  article_categories?: string;
  tags?: string;
  lang?: string;
  enabled?: boolean;
}

/**
 * Hook to fetch a list of articles with caching
 */
export function useArticles(params: ArticlesParams = {}) {
  const { enabled = true, ...queryParams } = params;
  return useQuery({
    queryKey: queryKeys.articles.list(queryParams as Record<string, unknown>),
    queryFn: () => getArticles(queryParams),
    staleTime: STALE_TIMES.ARTICLES,
    gcTime: CACHE_TIMES.ARTICLES,
    enabled,
  });
}

/**
 * Hook to fetch a single article by slug
 */
export function useArticle(slug: string | undefined, lang?: string) {
  return useQuery({
    queryKey: queryKeys.articles.detail(slug || '', lang),
    queryFn: () => slug ? getArticleBySlug(slug, lang) : null,
    enabled: !!slug,
    staleTime: STALE_TIMES.ARTICLES,
    gcTime: CACHE_TIMES.ARTICLES,
  });
}

/**
 * Hook to fetch article categories
 */
export function useArticleCategories() {
  return useQuery({
    queryKey: queryKeys.articles.categories(),
    queryFn: () => getArticleCategories(),
    staleTime: STALE_TIMES.CATEGORIES,
    gcTime: CACHE_TIMES.CATEGORIES,
  });
}

/**
 * Filter articles by language (client-side filter for results)
 * Optimized with early return and reduced function calls
 */
export function filterArticlesByLanguage(
  articles: WordPressPost[],
  targetLang: string | null
): WordPressPost[] {
  // Early return for empty arrays
  if (articles.length === 0) {
    return [];
  }
  
  // If no target language, filter for null/undefined language
  if (targetLang === null) {
    return articles.filter(article => article.language == null);
  }
  
  // Specific language: only include posts matching that language
  return articles.filter(article => article.language === targetLang);
}

