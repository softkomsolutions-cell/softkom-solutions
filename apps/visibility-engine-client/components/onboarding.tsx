"use client";
import { useActionState } from "react";
import { completeOnboarding } from "@/app/actions";

export function Onboarding({ businessName }: { businessName: string }) {
  const [state, action, pending] = useActionState(completeOnboarding, {});
  return (
    <div className="onboarding">
      <form action={action} className="form">
        <section className="panel">
          <p className="eyebrow">1 · ORGANISATION</p>
          <h2>Let’s start with your business.</h2>
          <p>This will be your private organisation workspace.</p>
          <label>
            Business name
            <input
              name="businessName"
              defaultValue={businessName}
              required
              maxLength={300}
            />
          </label>
        </section>

        <section className="panel">
          <p className="eyebrow">2 · WEBSITE</p>
          <h2>Where should visibility grow?</h2>
          <p>Add your website. You can connect analytics and publishing after setup.</p>
          <label>
            Site name
            <input name="name" required maxLength={300} />
          </label>
          <label>
            Website URL
            <input
              name="baseUrl"
              type="url"
              placeholder="https://yourbusiness.com"
              required
            />
          </label>
          <label>
            Content management system
            <select name="cms">
              <option value="WORDPRESS">WordPress</option>
              <option value="SHOPIFY">Shopify</option>
              <option value="WEBFLOW">Webflow</option>
              <option value="OTHER">Other</option>
            </select>
          </label>
        </section>

        <section className="panel">
          <p className="eyebrow">3 · COMMERCIAL PROFILE</p>
          <h2>What matters commercially?</h2>
          <p>Your offers and customers should shape every recommendation.</p>
          <label>
            Key services or products
            <textarea name="services" required maxLength={5000} />
          </label>
          <label>
            Target customers
            <textarea name="customers" required maxLength={5000} />
          </label>
          <label>
            Geographic market
            <input name="market" required maxLength={300} />
          </label>
          <label>
            Primary conversion goal
            <input
              name="conversionGoal"
              placeholder="e.g. book a business process review"
              required
              maxLength={300}
            />
          </label>
        </section>

        <section className="panel">
          <p className="eyebrow">4 · CONNECTIONS</p>
          <h2>A clear starting point.</h2>
          <p>
            Your workspace and first site will be created now. External services
            remain disconnected until you authorise them.
          </p>
          <div className="connection-row">
            <strong>Google Search Console & GA4</strong>
            <span className="badge">Connect after setup</span>
          </div>
          <div className="connection-row">
            <strong>CMS publishing</strong>
            <span className="badge">Connect after setup</span>
          </div>
        </section>

        {state.error && (
          <p role="alert" className="notice error">
            {state.error}
          </p>
        )}

        <div className="form-actions">
          <button type="submit" disabled={pending}>
            {pending ? "Creating workspace…" : "Create workspace & start trial →"}
          </button>
        </div>
      </form>
    </div>
  );
}
