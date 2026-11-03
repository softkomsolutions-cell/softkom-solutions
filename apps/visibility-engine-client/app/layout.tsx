import type { ReactNode } from "react";
import { headers } from "next/headers";
import "./styles.css";

export const metadata = {
  title: "Visibility Engine | Softkom",
  description: "Managed search and AI visibility for growing businesses.",
};

export default async function RootLayout({
  children,
}: {
  children: ReactNode;
}) {
  // Dynamic rendering lets Next attach this request's CSP nonce to scripts.
  await headers();
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
