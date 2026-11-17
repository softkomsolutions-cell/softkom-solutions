"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  LayoutDashboard,
  Compass,
  CalendarDays,
  FileText,
  ChartNoAxesCombined,
  Sparkles,
  Handshake,
  Plug,
  FileChartColumn,
  Settings,
  Network,
} from "lucide-react";
const links = [
  ["dashboard", "Dashboard", LayoutDashboard],
  ["opportunities", "Opportunities", Compass],
  ["calendar", "Content calendar", CalendarDays],
  ["content", "Content & approvals", FileText],
  ["strategy", "Strategy map", Network],
  ["analytics", "Search analytics", ChartNoAxesCombined],
  ["ai", "AI visibility", Sparkles],
  ["leads", "Leads & revenue", Handshake],
  ["connections", "Site connections", Plug],
  ["reports", "Reports", FileChartColumn],
  ["settings", "Settings", Settings],
] as const;
export function Navigation() {
  const path = usePathname();
  return (
    <nav aria-label="Workspace">
      {links.map(([slug, title, Icon]) => (
        <Link
          key={slug}
          href={`/workspace/${slug}`}
          aria-current={
            path.startsWith(`/workspace/${slug}`) ? "page" : undefined
          }
        >
          <Icon size={18} strokeWidth={1.7} />
          <span>{title}</span>
        </Link>
      ))}
    </nav>
  );
}
