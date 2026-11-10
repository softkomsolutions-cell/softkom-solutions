import Link from "next/link";
import { ArrowUpRight, Compass } from "lucide-react";
import type { ReactNode, ComponentProps } from "react";
import type { Filters, WorkspaceData } from "@/lib/queries";
export const label = (value: string) =>
  value
    .toLowerCase()
    .replaceAll("_", " ")
    .replace(/^./, (c) => c.toUpperCase());
export const date = (value?: Date | null) =>
  value
    ? new Intl.DateTimeFormat("en-ZA", {
        day: "numeric",
        month: "short",
        year: "numeric",
        timeZone: "Africa/Johannesburg",
      }).format(value)
    : "Not scheduled";
export function Badge({
  children,
  value = "",
}: {
  children?: ReactNode;
  value?: string;
}) {
  return (
    <span className={`badge state-${value.toLowerCase()}`}>
      {children || label(value)}
    </span>
  );
}
export function PageHeader({
  eyebrow = "YOUR VISIBILITY WORKSPACE",
  title,
  description,
  children,
}: {
  eyebrow?: string;
  title: string;
  description: string;
  children?: ReactNode;
}) {
  return (
    <header className="page-header">
      <div>
        <p className="eyebrow">{eyebrow}</p>
        <h1>{title}</h1>
        <p>{description}</p>
      </div>
      {children}
    </header>
  );
}
export function Empty({
  title,
  children,
  href,
  action,
}: {
  title: string;
  children: ReactNode;
  href?: string;
  action?: string;
}) {
  return (
    <div className="empty">
      <span className="empty-icon">
        <Compass size={25} strokeWidth={1.3} />
      </span>
      <h3>{title}</h3>
      <p>{children}</p>
      {href && (
        <Link className="button secondary" href={href}>
          {action || "See next step"} <ArrowUpRight size={15} />
        </Link>
      )}
    </div>
  );
}
export function Metric({
  title,
  value,
  note,
}: {
  title: string;
  value: ReactNode;
  note: string;
}) {
  return (
    <article className="metric">
      <span>{title}</span>
      <strong>{value}</strong>
      <small>{note}</small>
    </article>
  );
}
export function Field({
  label: title,
  ...props
}: ComponentProps<"input"> & { label: string }) {
  return (
    <label>
      {title}
      <input aria-label={title} {...props} />
    </label>
  );
}
export function Area({
  label: title,
  ...props
}: ComponentProps<"textarea"> & { label: string }) {
  return (
    <label>
      {title}
      <textarea aria-label={title} {...props} />
    </label>
  );
}
export function Select({
  label: title,
  children,
  ...props
}: ComponentProps<"select"> & { label: string }) {
  return (
    <label>
      {title}
      <select aria-label={title} {...props}>
        {children}
      </select>
    </label>
  );
}
export function SiteOptions({ data }: { data: WorkspaceData }) {
  return data.sites.map((s) => (
    <option value={s.id} key={s.id}>
      {s.name}
    </option>
  ));
}
export function FiltersBar({
  data,
  filters,
  calendar = false,
  month = true,
}: {
  data: WorkspaceData;
  filters: Filters;
  calendar?: boolean;
  month?: boolean;
}) {
  return (
    <form className="filters">
      <Select label="Website" name="site" defaultValue={filters.site || ""}>
        <option value="">All sites</option>
        <SiteOptions data={data} />
      </Select>
      {month && (
        <Field
          label="Period"
          type="month"
          name="month"
          defaultValue={data.range.month}
        />
      )}
      {calendar && (
        <>
          <Select
            label="Status"
            name="status"
            defaultValue={filters.status || ""}
          >
            <option value="">All statuses</option>
            {[
              "DRAFT",
              "CLIENT_REVIEW",
              "CHANGES_REQUESTED",
              "APPROVED",
              "SCHEDULED",
              "PUBLISHED",
              "ARCHIVED",
            ].map((s) => (
              <option key={s} value={s}>
                {label(s)}
              </option>
            ))}
          </Select>
          <Select
            label="Cluster"
            name="cluster"
            defaultValue={filters.cluster || ""}
          >
            <option value="">All clusters</option>
            {Array.from(new Set(data.topics.map((t) => t.cluster))).map((c) => (
              <option key={c}>{c}</option>
            ))}
          </Select>
          <Select
            label="View"
            name="view"
            defaultValue={filters.view || "calendar"}
          >
            <option value="calendar">Calendar</option>
            <option value="list">List</option>
          </Select>
        </>
      )}
      <button className="secondary">Apply filters</button>
    </form>
  );
}
