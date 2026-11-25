"use client";
import { createAuthClient } from "better-auth/react";
import { useState } from "react";
import { useRouter } from "next/navigation";
const authClient = createAuthClient();
export function LoginForm({ inviteToken = "" }: { inviteToken?: string }) {
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
            router.push(inviteToken ? `/invite/${inviteToken}` : "/workspace/dashboard");
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
        New here? <a href="/signup">Start a 14-day trial</a>.
      </p>
    </form>
  );
}

export function SignupForm({ inviteToken = "", selectedPlan = "" }: { inviteToken?: string; selectedPlan?: string }) {
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
        const password = String(data.get("password"));
        const confirm = String(data.get("confirmPassword"));
        if (password !== confirm) {
          setError("Passwords do not match.");
          setPending(false);
          return;
        }
        try {
          const result = await authClient.signUp.email({
            email: String(data.get("email")),
            password,
            name: String(data.get("name")),
          });
          if (result.error) {
            setError("Account could not be created. Use another email or check the password requirements.");
          } else {
            router.push(inviteToken ? `/invite/${inviteToken}` : selectedPlan ? `/onboarding?plan=${encodeURIComponent(selectedPlan)}` : "/onboarding");
            router.refresh();
          }
        } catch {
          setError("Unable to reach the account service. Please try again.");
        } finally {
          setPending(false);
        }
      }}
    >
      <label>
        Your name
        <input name="name" required maxLength={200} autoComplete="name" />
      </label>
      <label>
        Work email
        <input name="email" type="email" required maxLength={254} autoComplete="email" />
      </label>
      <label>
        Password
        <input name="password" type="password" required minLength={12} maxLength={128} autoComplete="new-password" />
      </label>
      <label>
        Confirm password
        <input name="confirmPassword" type="password" required minLength={12} maxLength={128} autoComplete="new-password" />
      </label>
      {error && <p role="alert" className="notice error">{error}</p>}
      <button disabled={pending}>{pending ? "Creating account…" : "Start your 14-day trial →"}</button>
      <p className="fine">
        No payment details are required to create a trial workspace. By creating
        an account you agree to the <a href="/terms">service terms</a> and
        acknowledge the <a href="/privacy">privacy notice</a>.
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
