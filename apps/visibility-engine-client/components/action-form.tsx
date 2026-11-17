"use client";
import { useActionState, useEffect, type ReactNode } from "react";
import { mutate, type ActionState } from "@/app/actions";
export function ActionForm({
  operation,
  children,
  action = mutate,
  className = "",
}: {
  operation?: string;
  children: ReactNode;
  action?: (state: ActionState, form: FormData) => Promise<ActionState>;
  className?: string;
}) {
  const [state, formAction, pending] = useActionState(action, {});
  useEffect(() => {
    if (state.success) {
      // Complete the action before refreshing the document. This also gives
      // the refreshed tree a consistent CSP nonce instead of merging two
      // streamed document contexts during a mutation.
      sessionStorage.setItem(
        "visibility-feedback",
        JSON.stringify({
          message: state.success,
          pathname: window.location.pathname,
          at: Date.now(),
        }),
      );
      window.location.reload();
    }
  }, [state]);
  return (
    <form
      action={formAction}
      className={`form ${className}`}
      onSubmit={() =>
        window.dispatchEvent(new Event("visibility:clear-feedback"))
      }
    >
      {operation && <input type="hidden" name="operation" value={operation} />}
      <fieldset disabled={pending}>{children}</fieldset>
      {pending && <p role="status">Saving your changes…</p>}
      {state.error && !pending && (
        <p className="notice error" role="alert">
          {state.error}
        </p>
      )}
      {state.success && !pending && (
        <p className="notice success" role="status">
          {state.success}
        </p>
      )}
    </form>
  );
}
