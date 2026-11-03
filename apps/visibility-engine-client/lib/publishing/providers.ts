import { CmsType } from "@prisma/client";
export type PublishInput = {
  title: string;
  html: string;
  slug: string;
  metaTitle: string;
  metaDescription: string;
  idempotencyKey: string;
};
export type PublishResult = {
  externalId: string;
  liveUrl: string;
  publishedAt: Date;
  response: Record<string, string>;
};
export interface PublishingProvider {
  available: boolean;
  publish(input: PublishInput): Promise<PublishResult>;
}
class UnavailableProvider implements PublishingProvider {
  available = false;
  async publish(): Promise<PublishResult> {
    throw new Error(
      "Publishing connector is not yet available. Requires Softkom setup.",
    );
  }
}
export function publishingProvider(cms: CmsType): PublishingProvider {
  switch (cms) {
    case "WORDPRESS":
    case "SHOPIFY":
    case "WEBFLOW":
    case "OTHER":
      return new UnavailableProvider();
  }
}
