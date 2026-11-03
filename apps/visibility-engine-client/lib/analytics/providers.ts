export interface SearchObservation {
  date: Date;
  query: string;
  pageUrl: string;
  clicks: number;
  impressions: number;
  ctr: number;
  position: number;
}
export interface SearchProvider {
  fetch(siteId: string, from: Date, to: Date): Promise<SearchObservation[]>;
}
export interface AIVisibilityProvider {
  observe(prompt: string): Promise<{
    platform: string;
    brandMentioned: boolean;
    citationUrl: string | null;
    source: string;
    confidence: string;
    observedAt: Date;
  }>;
}
// No provider is registered until a real authenticated integration is implemented.
export const searchProviders: ReadonlyMap<string, SearchProvider> = new Map();
export const aiProviders: ReadonlyMap<string, AIVisibilityProvider> = new Map();
