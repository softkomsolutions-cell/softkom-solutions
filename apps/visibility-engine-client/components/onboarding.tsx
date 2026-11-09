"use client";
import { useState, useActionState } from "react";
import { completeOnboarding } from "@/app/actions";
export function Onboarding({ businessName }: { businessName: string }) {
  const [step, setStep] = useState(0);
  const [state, action, pending] = useActionState(completeOnboarding, {});
  const steps = ["Organisation", "Website", "Business", "Visibility"];
  return (
    <div className="onboarding">
      <ol className="steps">
        {steps.map((s, i) => (
          <li
            className={i === step ? "current" : i < step ? "done" : ""}
            key={s}
          >
            <span>{i + 1}</span>
            {s}
          </li>
        ))}
      </ol>
      <form action={action} className="form" onSubmit={() => setStep(3)}>
        <fieldset hidden={step !== 0} disabled={pending}>
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
        </fieldset>
        <fieldset hidden={step !== 1} disabled={pending}>
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
        </fieldset>
        <fieldset hidden={step !== 2} disabled={pending}>
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
        </fieldset>
        <fieldset hidden={step !== 3} disabled={pending}>
          <h2>A clear starting point.</h2>
          <p>
            Your site will be saved now. No external service is connected by
            this step.
          </p>
          <div className="connection-row">
            <strong>Google Search Console</strong>
            <span className="badge">Connect after setup</span>
          </div>
          <div className="connection-row">
            <strong>CMS publishing</strong>
            <span className="badge">Connect after setup</span>
          </div>
          <p>
            Search results and publishing success will appear only when
            supported by real data.
          </p>
        </fieldset>
        {state.error && (
          <p role="alert" className="notice error">
            {state.error}
          </p>
        )}
        <div className="form-actions">
          {step > 0 && (
            <button
              type="button"
              className="secondary"
              onClick={() => setStep(step - 1)}
              disabled={pending}
            >
              Back
            </button>
          )}
          {step < 3 ? (
            <button
              key="continue"
              type="button"
              onClick={(e) => {
                e.preventDefault();
                const fields = e.currentTarget.form!.querySelectorAll<
                  HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
                >(
                  `fieldset:nth-of-type(${step + 1}) input, fieldset:nth-of-type(${step + 1}) textarea, fieldset:nth-of-type(${step + 1}) select`,
                );
                if (Array.from(fields).every((field) => field.reportValidity()))
                  setStep(step + 1);
              }}
            >
              Continue →
            </button>
          ) : (
            <button key="complete" type="submit" disabled={pending}>
              {pending ? "Creating workspace…" : "Open your workspace →"}
            </button>
          )}
        </div>
      </form>
    </div>
  );
}
