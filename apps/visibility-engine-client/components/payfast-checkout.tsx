"use client";

import { useState } from "react";

export function CheckoutButton({ plan }: { plan: "STARTER" | "GROWTH" | "PRO" }) {
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  async function checkout() {
    setBusy(true);
    setError("");
    try {
      const response = await fetch("/api/payfast/checkout", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ plan }),
        credentials: "same-origin",
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || "Checkout unavailable");
      if (typeof data.checkoutUrl !== "string" || !data.checkoutUrl.startsWith("https://"))
        throw new Error("Invalid payment destination");
      const url = new URL(data.checkoutUrl);
      if (!["sandbox.payfast.co.za", "www.payfast.co.za"].includes(url.hostname))
        throw new Error("Invalid payment destination");
      const form = document.createElement("form");
      form.method = "POST";
      form.action = url.toString();
      for (const [name, value] of Object.entries(data.fields as Record<string, string>)) {
        const field = document.createElement("input");
        field.type = "hidden";
        field.name = name;
        field.value = value;
        form.appendChild(field);
      }
      document.body.appendChild(form);
      form.submit();
    } catch (cause) {
      setError(cause instanceof Error ? cause.message : "Checkout unavailable");
      setBusy(false);
    }
  }
  return (
    <div>
      <button type="button" onClick={checkout} disabled={busy}>
        {busy ? "Opening secure checkout…" : `Subscribe to ${plan.toLowerCase()}`}
      </button>
      {error && <p role="alert">{error}</p>}
    </div>
  );
}
