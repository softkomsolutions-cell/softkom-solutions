"use client";
import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";

// This stores only a generic acknowledgement, never session or business data.
export function Feedback() {
  const pathname = usePathname();
  const [message, setMessage] = useState("");
  useEffect(() => {
    try {
      const saved = JSON.parse(
        sessionStorage.getItem("visibility-feedback") || "null",
      );
      if (
        saved?.pathname === pathname &&
        typeof saved.message === "string" &&
        Date.now() - saved.at < 60000
      )
        setMessage(saved.message);
      else {
        sessionStorage.removeItem("visibility-feedback");
        setMessage("");
      }
    } catch {
      sessionStorage.removeItem("visibility-feedback");
      setMessage("");
    }
    const clear = () => {
      sessionStorage.removeItem("visibility-feedback");
      setMessage("");
    };
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
        onClick={() => {
          sessionStorage.removeItem("visibility-feedback");
          setMessage("");
        }}
      >
        Dismiss
      </button>
    </div>
  );
}
