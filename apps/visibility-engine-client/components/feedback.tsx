"use client";
import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";

// This stores only a generic acknowledgement, never session or business data.
export function Feedback() {
  const pathname = usePathname();
  const [message, setMessage] = useState("");
  useEffect(() => {
    const saved = sessionStorage.getItem("visibility-feedback");
    sessionStorage.removeItem("visibility-feedback");
    setMessage(saved || "");
    const clear = () => setMessage("");
    window.addEventListener("visibility:clear-feedback", clear);
    return () => window.removeEventListener("visibility:clear-feedback", clear);
  }, [pathname]);
  if (!message) return null;
  return (
    <div className="notice success feedback" role="status">
      <span>{message}</span>
      <button
        className="text-button"
        aria-label="Dismiss notification"
        onClick={() => setMessage("")}
      >
        Dismiss
      </button>
    </div>
  );
}
