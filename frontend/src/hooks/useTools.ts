/**
 * React Query hooks for Tools
 * Provides intelligent caching and deduplication for tool data
 */

import { useQuery } from '@tanstack/react-query';
import { getTools, getToolBySlug, getToolCategories, WordPressPost } from '@/lib/wordpress';
import { queryKeys, STALE_TIMES, CACHE_TIMES } from '@/lib/query-client';

interface ToolsParams {
  per_page?: number;
  page?: number;
  orderby?: string;
  order?: 'asc' | 'desc';
  tool_categories?: string;
  tags?: string;
  search?: string;
  lang?: string;
  enabled?: boolean;
}

/**
 * Hook to fetch a list of tools with caching
 */
export function useTools(params: ToolsParams = {}) {
  const { enabled = true, ...queryParams } = params;
  return useQuery({
    queryKey: queryKeys.tools.list(queryParams as Record<string, unknown>),
    queryFn: () => getTools(queryParams),
    staleTime: STALE_TIMES.TOOLS,
    gcTime: CACHE_TIMES.TOOLS,
    enabled,
  });
}

/**
 * Hook to fetch a single tool by slug
 */
export function useTool(slug: string | undefined, lang?: string) {
  return useQuery({
    queryKey: queryKeys.tools.detail(slug || '', lang),
    queryFn: () => slug ? getToolBySlug(slug, lang) : null,
    enabled: !!slug,
    staleTime: STALE_TIMES.TOOLS,
    gcTime: CACHE_TIMES.TOOLS,
  });
}

/**
 * Hook to fetch tool categories
 */
export function useToolCategories() {
  return useQuery({
    queryKey: queryKeys.tools.categories(),
    queryFn: () => getToolCategories(),
    staleTime: STALE_TIMES.CATEGORIES,
    gcTime: CACHE_TIMES.CATEGORIES,
  });
}

/**
 * Filter tools by language (client-side filter for results)
 * Optimized with early return and reduced function calls
 */
export function filterToolsByLanguage(
  tools: WordPressPost[],
  targetLang: string | null
): WordPressPost[] {
  // Early return for empty arrays
  if (tools.length === 0) {
    return [];
  }
  
  // If no target language, filter for null/undefined language
  if (targetLang === null) {
    return tools.filter(tool => tool.language == null);
  }
  
  // Specific language: only include posts matching that language
  return tools.filter(tool => tool.language === targetLang);
}

