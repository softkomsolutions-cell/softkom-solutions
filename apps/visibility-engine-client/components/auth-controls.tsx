"use client";
import { createAuthClient } from "better-auth/react";
import { useState } from "react";
import { useRouter } from "next/navigation";
const authClient = createAuthClient();
export function LoginForm() {
  const [error, setError] = useState("");
  const [pending, setPending] = useState(false);
  const router = useRouter();
  return (
    <form
      className="form"
      onSubmit={async (event) => {
        event.preventDefault();
        setPending(true);
        setError("");
        const data = new FormData(event.currentTarget);
        try {
          const result = await authClient.signIn.email({
            email: String(data.get("email")),
            password: String(data.get("password")),
          });
          if (result.error)
            setError(
              "Sign-in failed. Check your details, or wait a minute before trying again.",
            );
          else {
            router.push("/workspace/dashboard");
            router.refresh();
          }
        } catch {
          setError("Unable to reach the sign-in service. Please try again.");
        } finally {
          setPending(false);
        }
      }}
    >
      <label>
        Work email
        <input
          name="email"
          type="email"
          autoComplete="username"
          required
          maxLength={254}
        />
      </label>
      <label>
        Password
        <input
          name="password"
          type="password"
          autoComplete="current-password"
          required
          maxLength={128}
        />
      </label>
      {error && (
        <p role="alert" className="notice error">
          {error}
        </p>
      )}
      <button disabled={pending}>
        {pending ? "Signing in…" : "Sign in to your workspace →"}
      </button>
      <p className="fine">
        Access is arranged by your Softkom manager. Contact them if you need an
        account or password reset.
      </p>
    </form>
  );
}
export function Logout() {
  const router = useRouter();
  const [error, setError] = useState(false);
  return (
    <>
      <button
        className="text-button"
        onClick={async () => {
          try {
            const result = await authClient.signOut();
            if (result.error) {
              setError(true);
              return;
            }
            router.push("/login");
            router.refresh();
          } catch {
            setError(true);
          }
        }}
      >
        Sign out
      </button>
      {error && <span role="alert">Could not sign out. Retry.</span>}
    </>
  );
}
