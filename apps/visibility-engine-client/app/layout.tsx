import type { ReactNode } from "react";
import "./styles.css";

export const metadata = {
  title: "Visibility Engine | Softkom",
  description: "Managed search and AI visibility for growing businesses."
};

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
