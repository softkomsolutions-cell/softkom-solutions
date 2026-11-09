import { ConnectionPanels } from "./connections";
import { SearchInsights } from "./search-insights";
import Link from "next/link";
import { localDay } from "@/lib/time";
import { ArrowRight, ArrowUpRight, CheckCheck, Target } from "lucide-react";
import type { Context } from "@/lib/context";
import { canManage } from "@/lib/security";
import { searchTotals, type WorkspaceData, type Filters } from "@/lib/queries";
import { ActionForm } from "./action-form";
import { comparablePeriods } from "@/lib/reporting/comparison";
import {
  Area,
  Badge,
  date,
  Empty,
  Field,
  FiltersBar,
  label,
  Metric,
  Select,
  SiteOptions,
} from "./ui";

type Props = { data: WorkspaceData; ctx: Context; filters: Filters };
function Revenue({ data }: { data: WorkspaceData }) {
  const currencies = Array.from(
    new Set(
      data.leads.filter((l) => l.revenue !== null).map((l) => l.currency),
    ),
  );
  return currencies.length ? (
    <>
      {currencies.map((currency) => (
        <span key={currency} className="money">
          {new Intl.NumberFormat("en-ZA", {
            style: "currency",
            currency,
          }).format(
            data.leads
              .filter((l) => l.currency === currency && l.stage === "WON")
              .reduce((sum, l) => sum + Number(l.revenue || 0), 0),
          )}
        </span>
      ))}
    </>
  ) : (
    <>—</>
  );
}
function analyticsTotals(
  rows: WorkspaceData["analytics"],
) {
  return rows.reduce(
    (totals, row) => ({
      sessions: totals.sessions + row.sessions,
      engagedSessions: totals.engagedSessions + row.engagedSessions,
      conversions: totals.conversions + row.conversions,
      revenue: totals.revenue + Number(row.totalRevenue),
    }),
    { sessions: 0, engagedSessions: 0, conversions: 0, revenue: 0 },
  );
}
export function Dashboard({ data, ctx }: Props) {
  const reviews = data.content.filter((c) => c.status === "CLIENT_REVIEW");
  const totals = searchTotals(
    data.metrics.filter((m) => m.date >= data.range.from),
  );
  const analytics = analyticsTotals(
    data.analytics.filter((m) => m.date >= data.range.from),
  );
  const next = reviews.length
    ? {
        title: `${reviews.length} content ${reviews.length === 1 ? "item is" : "items are"} ready for your review.`,
        text: "Your feedback keeps every page accurate and aligned with your business.",
        href: `/workspace/content/${reviews[0].id}`,
        action: "Review content",
      }
    : !data.sites.length
      ? {
          title: "Your next customer starts with a clear strategy.",
          text: "Tell us about your website, offers and customers to give your workspace a useful starting point.",
          href: "/onboarding",
          action: "Set up your workspace",
        }
      : {
          title: "A considered strategy. A measurable next step.",
          text: data.topics.length
            ? "Explore your opportunity queue and the commercial reasoning behind each recommendation."
            : "Your website is on the map. Softkom can now research opportunities aligned with your offers.",
          href: "/workspace/opportunities",
          action: "Explore opportunities",
        };
  return (
    <>
      <section className="hero-panel">
        <div>
          <span className="eyebrow">THE NEXT RIGHT MOVE</span>
          <h2>{next.title}</h2>
          <p>{next.text}</p>
          <Link className="button" href={next.href}>
            {next.action}
            <ArrowUpRight size={16} />
          </Link>
        </div>
        <div className="orbit" aria-hidden="true">
          <div className="orbit-inner">
            <Target size={44} strokeWidth={1} />
            <span>
              Opportunity
              <br />
              <strong>→ outcome</strong>
            </span>
          </div>
          <span className="orbit-label">
            Human direction. Measurable progress.
          </span>
        </div>
      </section>
      <div className="section-heading">
        <h2>Your workspace, at a glance</h2>
        <span className="fine">
          Live workspace records · no estimated performance
        </span>
      </div>
      <div className="kpis">
        <Metric
          title="Tracked opportunities"
          value={data.topics.length}
          note="Commercial ideas under management"
        />
        <Metric
          title="Content in review"
          value={reviews.length}
          note="Ready for client feedback"
        />
        <Metric
          title="Published content"
          value={
            data.content.filter((c) =>
              c.publications.some((p) => p.status === "LIVE"),
            ).length
          }
          note={`${data.content.filter((c) => c.scheduledAt).length} with a planned publishing date`}
        />
        <Metric
          title="Connected sites"
          value={
            data.sites.filter((s) =>
              s.connections.some((c) => c.status === "CONNECTED"),
            ).length
          }
          note={`${data.sites.length} sites in your workspace`}
        />
      </div>
      <div className="two-col">
        <section className="panel">
          <div className="panel-head">
            <div>
              <span className="eyebrow">STRATEGY INTO ACTION</span>
              <h2>What deserves attention</h2>
            </div>
            <Link
              href="/workspace/opportunities"
              className="icon-link"
              aria-label="All opportunities"
            >
              <ArrowUpRight size={20} />
            </Link>
          </div>
          {data.topics.length ? (
            <div className="opportunity-list">
              {data.topics.slice(0, 4).map((t, i) => (
                <Link href={`/workspace/opportunities/${t.id}`} key={t.id}>
                  <span className="list-index">
                    {String(i + 1).padStart(2, "0")}
                  </span>
                  <div>
                    <strong>{t.title}</strong>
                    <p>
                      {t.cluster} · {t.funnel || "Funnel to be defined"}
                    </p>
                  </div>
                  <ArrowUpRight size={16} />
                </Link>
              ))}
            </div>
          ) : (
            <Empty title="Good opportunities start with your business.">
              Your manager will add research with a clear offer, customer
              problem and next action.
            </Empty>
          )}
        </section>
        <section className="panel">
          <div className="panel-head">
            <div>
              <span className="eyebrow">THE COMMERCIAL PICTURE</span>
              <h2>From visibility to value</h2>
            </div>
            <CheckCheck size={20} />
          </div>
          <div className="outcomes">
            <div>
              <span>
                Search impressions <small>{data.range.month}</small>
              </span>
              <strong>
                {data.metrics.some((m) => m.date >= data.range.from)
                  ? totals.impressions.toLocaleString()
                  : "—"}
              </strong>
            </div>
            <div>
              <span>
                Website sessions <small>{data.range.month}</small>
              </span>
              <strong>
                {data.analytics.length ? analytics.sessions.toLocaleString() : "—"}
              </strong>
            </div>
            <div>
              <span>GA4 lead events</span>
              <strong>
                {data.analytics.length
                  ? analytics.conversions.toLocaleString(undefined, {
                      maximumFractionDigits: 1,
                    })
                  : "—"}
              </strong>
            </div>
            <div>
              <span>Sales opportunities</span>
              <strong>
                {
                  data.leads.filter((l) =>
                    ["OPPORTUNITY", "WON"].includes(l.stage),
                  ).length
                }
              </strong>
            </div>
            <div>
              <span>Attributed revenue</span>
              <strong>
                <Revenue data={data} />
              </strong>
            </div>
          </div>
          <p className="fine">
            Search and GA4 values are observed connector data. GA4 lead events
            count the generate_lead event only. Leads and won revenue reflect
            recorded attribution and are not proof of causation.
          </p>
          <Link className="inline-link" href="/workspace/leads">
            Explore the attribution trail <ArrowRight size={14} />
          </Link>
        </section>
      </div>
      {!data.profile && canManage(ctx.role) && (
        <section className="notice">
          <strong>Give your strategy a stronger foundation.</strong>
          <p>Add your services, customers and conversion goal.</p>
          <Link href="/onboarding">Complete business setup →</Link>
        </section>
      )}
      <div className="section-heading">
        <h2>A clear path to business results</h2>
        <span className="fine">Every stage earns its place</span>
      </div>
      <div className="process-strip">
        {[
          "Research the opportunity",
          "Create useful content",
          "Review & approve",
          "Publish & measure",
          "Attribute & improve",
        ].map((s, i) => (
          <div key={s}>
            <span>0{i + 1}</span>
            <strong>{s}</strong>
          </div>
        ))}
      </div>
      <p className="fine">
        Workspace for {ctx.organisation.name}. Your records remain within your
        organisation.
      </p>
    </>
  );
}
export function Opportunities({ data, ctx, filters }: Props) {
  return (
    <>
      <FiltersBar data={data} filters={filters} month={false} />
      {data.topics.length ? (
        <div className="topic-grid">
          {data.topics.map((t) => (
            <Link
              className="topic-card"
              href={`/workspace/opportunities/${t.id}`}
              key={t.id}
            >
              <div className="card-top">
                <Badge value={t.status} />
                <span className="priority">Priority {t.priority}</span>
              </div>
              <h2>{t.title}</h2>
              <p>
                {t.reasoning || "Research reasoning has not been recorded yet."}
              </p>
              <div className="topic-footer">
                <span>
                  {t.cluster}
                  <small>{label(t.intent)}</small>
                </span>
                <ArrowUpRight size={18} />
              </div>
              <div className="funnel-label">
                Feeds → {t.funnel || "To be defined"}
              </div>
            </Link>
          ))}
        </div>
      ) : (
        <section className="panel">
          <Empty title="The right opportunity is worth researching.">
            There are no opportunities yet. Each recommendation will explain the
            business problem, fit to your offer and intended funnel.
          </Empty>
        </section>
      )}
      {canManage(ctx.role) && data.sites.length > 0 && (
        <details className="panel editor">
          <summary>Add a researched opportunity</summary>
          <ActionForm operation="topic">
            <div className="form-grid">
              <Select label="Site" name="siteId">
                <SiteOptions data={data} />
              </Select>
              <Field
                label="Topic / title"
                name="title"
                required
                maxLength={300}
              />
              <Field
                label="URL slug"
                name="slug"
                required
                pattern="[a-z0-9]+(-[a-z0-9]+)*"
              />
              <Field label="Cluster" name="cluster" list="clusters" required />
              <datalist id="clusters">
                {[
                  "Business Efficiency",
                  "Revenue Recovery",
                  "Systems & Automation",
                  "Reporting & Visibility",
                  "Practical AI",
                  "Business Continuity",
                ].map((s) => (
                  <option key={s}>{s}</option>
                ))}
              </datalist>
              <Select label="Search intent" name="intent">
                {[
                  "COMMERCIAL",
                  "LOCAL_COMMERCIAL",
                  "DECISION_SUPPORT",
                  "INFORMATIONAL",
                  "PROBLEM_AWARENESS",
                ].map((s) => (
                  <option key={s} value={s}>
                    {label(s)}
                  </option>
                ))}
              </Select>
              <Field
                label="Priority (0–100; highest first)"
                name="priority"
                type="number"
                min={0}
                max={100}
                defaultValue={50}
                required
              />
              <Field
                label="Funnel destination / client offer"
                name="funnel"
                required
              />
              <Field
                label="Recommended content type"
                name="contentType"
                defaultValue="Commercial problem page"
                required
              />
            </div>
            <Area
              label="Business problem"
              name="businessProblem"
              required
              maxLength={2000}
            />
            <Area
              label="Why Softkom recommends this"
              name="reasoning"
              required
              maxLength={6000}
            />
            <Area
              label="Research sources / evidence and limitations"
              name="evidence"
              maxLength={2000}
            />
            <button>Add opportunity</button>
          </ActionForm>
        </details>
      )}
    </>
  );
}
function ContentTable({ data }: { data: WorkspaceData }) {
  return (
    <div className="table-wrap">
      <table>
        <caption className="sr-only">
          Content planning and publishing records
        </caption>
        <thead>
          <tr>
            <th>Content</th>
            <th>Status</th>
            <th>Scheduled / reviewer</th>
            <th>Funnel</th>
            <th>Publication</th>
          </tr>
        </thead>
        <tbody>
          {data.content.map((c) => (
            <tr key={c.id}>
              <td>
                <Link
                  className="record-title"
                  href={`/workspace/content/${c.id}`}
                >
                  {c.title}
                </Link>
                <small>
                  {c.topic?.cluster || "No cluster"} · {c.contentType}
                  <br />
                  {c.site.name}
                </small>
              </td>
              <td>
                <Badge value={c.status} />
              </td>
              <td>
                {date(c.scheduledAt)}
                <small>
                  {c.reviewerId
                    ? data.members.find((m) => m.userId === c.reviewerId)?.user
                        .name || "Assigned reviewer"
                    : "Reviewer unassigned"}
                </small>
              </td>
              <td>{c.funnel || c.topic?.funnel || "To be defined"}</td>
              <td>
                {c.publications[0] ? (
                  <>
                    <Badge value={c.publications[0].status} />
                    {c.publications[0].status === "LIVE" &&
                      c.publications[0].liveUrl && (
                        <a
                          href={c.publications[0].liveUrl}
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          Open live page ↗
                        </a>
                      )}
                  </>
                ) : (
                  "Not requested"
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
export function Content({ data, ctx, filters }: Props) {
  return (
    <>
      <FiltersBar data={data} filters={filters} month={false} />
      <section className="panel">
        {data.content.length ? (
          <ContentTable data={data} />
        ) : (
          <Empty title="Your next useful page starts here.">
            Proposed content will appear here with its business purpose,
            revision history and approval controls.
          </Empty>
        )}
      </section>
      {canManage(ctx.role) && data.topics.length > 0 && (
        <details className="panel editor">
          <summary>Propose content for client review</summary>
          <ActionForm operation="content">
            <div className="form-grid">
              <Select label="Site" name="siteId">
                <SiteOptions data={data} />
              </Select>
              <Select
                label="Topic (must belong to selected site)"
                name="topicId"
              >
                {data.topics.map((t) => (
                  <option key={t.id} value={t.id}>
                    {t.site.name} / {t.title}
                  </option>
                ))}
              </Select>
              <Field label="Title" name="title" required />
              <Field
                label="Slug"
                name="slug"
                required
                pattern="[a-z0-9]+(-[a-z0-9]+)*"
              />
              <Field
                label="Content type"
                name="contentType"
                defaultValue="Commercial problem page"
                required
              />
              <Field label="Funnel destination" name="funnel" required />
              <Field
                label="SEO title"
                name="metaTitle"
                required
                maxLength={100}
              />
              <Field
                label="Meta description"
                name="metaDesc"
                required
                maxLength={300}
              />
              <Field
                label="Planned date in SAST (not an automatic publish job)"
                name="scheduledAt"
                type="datetime-local"
              />
              <Select label="Assigned reviewer" name="reviewerId">
                <option value="">Unassigned</option>
                {data.members.map((m) => (
                  <option key={m.id} value={m.userId}>
                    {m.user.name || m.user.email}
                  </option>
                ))}
              </Select>
            </div>
            <Area
              label="Page content (safe HTML supported)"
              name="body"
              required
              rows={12}
              maxLength={100000}
            />
            <Area
              label="Internal linking recommendations"
              name="internalLinks"
              maxLength={2000}
            />
            <p className="fine">
              The page enters client review. Publication requires approval of
              this exact revision.
            </p>
            <button>Send for review</button>
          </ActionForm>
        </details>
      )}
    </>
  );
}
export function Calendar({ data, filters }: Props) {
  const first = new Date(`${data.range.month}-01T00:00:00Z`);
  const offset = (first.getUTCDay() + 6) % 7;
  const days = new Date(
    Date.UTC(first.getUTCFullYear(), first.getUTCMonth() + 1, 0),
  ).getUTCDate();
  return (
    <>
      <FiltersBar data={data} filters={filters} calendar />
      {filters.view === "list" ? (
        <section className="panel">
          {data.content.length ? (
            <ContentTable data={data} />
          ) : (
            <Empty title="A clear calendar, ready for the right work.">
              No scheduled content matches these filters. Unscheduled drafts
              remain in Content & approvals.
            </Empty>
          )}
        </section>
      ) : (
        <section className="panel calendar-panel">
          <div className="section-heading">
            <h2>
              {first.toLocaleDateString("en-ZA", {
                month: "long",
                year: "numeric",
                timeZone: "UTC",
              })}
            </h2>
            <span className="fine">
              Planned dates · approval is still required
            </span>
          </div>
          <div className="calendar-grid">
            {["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((d) => (
              <div className="weekday" key={d}>
                {d}
              </div>
            ))}
            {Array.from({ length: offset }, (_, i) => (
              <div className="calendar-day muted" key={`blank${i}`} />
            ))}
            {Array.from({ length: days }, (_, i) => (
              <div key={i} className="calendar-day">
                <span>{i + 1}</span>
                {data.content
                  .filter(
                    (c) =>
                      (c.scheduledAt ? localDay(c.scheduledAt) : null) ===
                      `${data.range.month}-${String(i + 1).padStart(2, "0")}`,
                  )
                  .map((c) => (
                    <Link
                      className="calendar-event"
                      href={`/workspace/content/${c.id}`}
                      key={c.id}
                    >
                      <strong>{c.title}</strong>
                      <span>{c.topic?.cluster}</span>
                      <Badge value={c.status} />
                    </Link>
                  ))}
              </div>
            ))}
          </div>
          {!data.content.length && (
            <p className="calendar-note">
              No scheduled content this month.{" "}
              <Link href="/workspace/content">
                Review unscheduled content →
              </Link>
            </p>
          )}
        </section>
      )}
    </>
  );
}
export function Connections({ data, ctx }: Props) {
  return (
    <>
      <ConnectionPanels data={data} ctx={ctx} />
      {!data.sites.length && (
        <section className="panel">
          <Empty
            title="Give your strategy a home."
            href="/onboarding"
            action="Set up your site"
          >
            Add your website and business profile to start.
          </Empty>
        </section>
      )}
      {canManage(ctx.role) && (
        <details className="panel editor">
          <summary>Add another website</summary>
          <ActionForm operation="site">
            <div className="form-grid">
              <Field label="Site name" name="name" required />
              <Field label="Website URL" name="baseUrl" type="url" required />
              <Select label="CMS" name="cms">
                {["WORDPRESS", "SHOPIFY", "WEBFLOW", "OTHER"].map((c) => (
                  <option key={c} value={c}>
                    {label(c)}
                  </option>
                ))}
              </Select>
            </div>
            <button>Add site record</button>
          </ActionForm>
        </details>
      )}
    </>
  );
}

function groupedMetrics(
  data: WorkspaceData,
  key: "pageUrl" | "query",
  from: Date,
  to: Date,
) {
  const groups = new Map<string, typeof data.metrics>();
  for (const metric of data.metrics.filter(
    (m) => m.date >= from && m.date < to,
  )) {
    const name = metric[key] || "Not recorded";
    const group = groups.get(name) || [];
    group.push(metric);
    groups.set(name, group);
  }
  return Array.from(groups, ([name, rows]) => ({
    name,
    ...searchTotals(rows),
  })).sort((a, b) => b.clicks - a.clicks);
}
export function Analytics({ data, filters, ctx }: Props) {
  const current = data.metrics.filter((m) => m.date >= data.range.from);
  const totals = searchTotals(current);
  const pages = groupedMetrics(data, "pageUrl", data.range.from, data.range.to);
  const prior = groupedMetrics(
    data,
    "pageUrl",
    data.range.previous,
    data.range.from,
  );
  const trends = pages.map((p) => ({
    ...p,
    prior: prior.find((r) => r.name === p.name),
    delta: p.clicks - (prior.find((r) => r.name === p.name)?.clicks || 0),
  }));
  const hasConnection = data.sites.some((s) =>
    s.connections.some(
      (c) => c.provider === "GOOGLE_SEARCH_CONSOLE" && c.status === "CONNECTED",
    ),
  );
  return (
    <>
      <FiltersBar data={data} filters={filters} />
      {current.length > 0 && (
        <div className="kpis">
          <Metric
            title="Clicks"
            value={totals.clicks.toLocaleString()}
            note="Recorded search-result clicks"
          />
          <Metric
            title="Impressions"
            value={totals.impressions.toLocaleString()}
            note="Recorded search appearances"
          />
          <Metric
            title="CTR"
            value={
              totals.ctr === null ? "—" : `${(totals.ctr * 100).toFixed(2)}%`
            }
            note="Total clicks ÷ total impressions"
          />
          <Metric
            title="Average position"
            value={totals.position?.toFixed(1) || "—"}
            note="Weighted by recorded impressions"
          />
        </div>
      )}
      <SearchInsights data={data} terms={ctx.organisation.brandedTerms} />
      {!hasConnection && (
        <div className="notice">
          <strong>Google Search Console is not connected.</strong>
          <p>
            Automatic search measurement is not available. Existing manually
            imported observations, if any, are labelled by source.
          </p>
          <Link href="/workspace/connections">View site connections →</Link>
        </div>
      )}
      {!current.length ? (
        <section className="panel">
          <Empty title="Real visibility starts with real observations.">
            No search measurements exist for this period. Clicks, impressions,
            CTR and position will appear when data is available.
          </Empty>
        </section>
      ) : (
        <>
          <p className="fine">
            Sources:{" "}
            {Array.from(new Set(current.map((m) => label(m.source)))).join(
              ", ",
            )}
            . Rows are aggregated within the selected month.
          </p>
          <div className="two-col">
            {(["pageUrl", "query"] as const).map((key) => (
              <section className="panel" key={key}>
                <h2>{key === "pageUrl" ? "Top pages" : "Top queries"}</h2>
                <div className="table-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th>{key === "pageUrl" ? "Page URL" : "Query"}</th>
                        <th>Clicks</th>
                        <th>Impressions</th>
                        <th>CTR</th>
                        <th>Position</th>
                      </tr>
                    </thead>
                    <tbody>
                      {groupedMetrics(data, key, data.range.from, data.range.to)
                        .slice(0, 20)
                        .map((r) => (
                          <tr key={r.name}>
                            <td>{r.name}</td>
                            <td>{r.clicks}</td>
                            <td>{r.impressions}</td>
                            <td>
                              {r.ctr === null
                                ? "—"
                                : (r.ctr * 100).toFixed(2) + "%"}
                            </td>
                            <td>{r.position?.toFixed(1) || "—"}</td>
                          </tr>
                        ))}
                    </tbody>
                  </table>
                </div>
              </section>
            ))}
          </div>
          <div className="two-col">
            {[true, false].map((up) => (
              <section className="panel" key={String(up)}>
                <h2>{up ? "Improving" : "Declining"} pages</h2>
                <p className="fine">
                  Click change versus previous calendar month. Only pages with
                  observations in both periods are compared; coverage may
                  differ.
                </p>
                {trends
                  .filter((t) => t.prior && (up ? t.delta > 0 : t.delta < 0))
                  .map((t) => (
                    <div className="connection-row" key={t.name}>
                      <span>{t.name}</span>
                      <strong>
                        {t.delta > 0 ? "+" : ""}
                        {t.delta} clicks
                      </strong>
                    </div>
                  ))}
              </section>
            ))}
          </div>
          <section className="panel">
            <h2>Topic-cluster performance</h2>
            <p className="fine">
              Pages are matched to recorded live publication URLs; unmatched
              pages remain unassigned.
            </p>
            {Array.from(new Set(data.topics.map((t) => t.cluster))).map(
              (cluster) => {
                const urls = data.content
                  .filter((c) => c.topic?.cluster === cluster)
                  .flatMap((c) =>
                    c.publications
                      .filter((p) => p.status === "LIVE")
                      .map((p) => p.liveUrl),
                  );
                const sum = searchTotals(
                  current.filter((m) => urls.includes(m.pageUrl)),
                );
                return (
                  <div className="connection-row" key={cluster}>
                    <strong>{cluster}</strong>
                    <span>
                      {sum.clicks} clicks · {sum.impressions} impressions
                    </span>
                  </div>
                );
              },
            )}
          </section>
        </>
      )}
    </>
  );
}
export function AIVisibility({ data, ctx, filters }: Props) {
  const automated = data.observations.filter(
    (o) =>
      o.source.includes("Mistral Conversations API") ||
      o.source.includes("OpenAI Responses API"),
  );
  const mentions = data.observations.filter((o) => o.brandMentioned).length;
  const citations = data.observations.filter((o) => o.pageCited).length;
  const competitors = data.observations.filter(
    (o) => Boolean(o.competitorMentioned),
  ).length;

  return (
    <>
      <FiltersBar data={data} filters={filters} />
      <div className="notice">
        <strong>
          {automated.length
            ? "Automated AI monitoring is active."
            : "Evidence, not an AI ranking score."}
        </strong>
        <p>
          {automated.length
            ? `${automated.length} automated observation${automated.length === 1 ? "" : "s"} were recorded for this period. Each result keeps the exact prompt, provider source and observation date.`
            : "Each observation records a platform, prompt, date and source. A mention or citation in one response does not establish universal AI visibility."}
        </p>
      </div>
      {data.observations.length > 0 && (
        <div className="kpis">
          <Metric
            title="AI observations"
            value={data.observations.length}
            note={automated.length ? `${automated.length} automated` : "Recorded evidence"}
          />
          <Metric
            title="Brand mentions"
            value={mentions}
            note="Observed responses mentioning the brand"
          />
          <Metric
            title="Page citations"
            value={citations}
            note="Observed responses citing this website"
          />
          <Metric
            title="Competitor mentions"
            value={competitors}
            note="Responses where a competitor was identified"
          />
        </div>
      )}
      {!data.observations.length ? (
        <section className="panel">
          <Empty title="No AI observations recorded yet.">
            Automated monitoring runs through the configured worker provider when credentials are available. Verified observations can also be recorded below or sent through the secure intake endpoint; no results are fabricated.
          </Empty>
        </section>
      ) : (
        <div className="topic-grid">
          {data.observations.map((o) => (
            <article className="panel" key={o.id}>
              <Badge>{o.platform}</Badge>
              <h2>{o.prompt}</h2>
              <p>
                {o.brandMentioned ? "Brand mentioned" : "Brand not mentioned"} ·{" "}
                {o.pageCited ? "Page cited" : "No page cited"}
              </p>
              {o.citationUrl && (
                <a
                  href={o.citationUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Open citation ↗
                </a>
              )}
              <p>Competitor: {o.competitorMentioned || "Not observed"}</p>
              <p className="fine">
                {date(o.observedAt)} · Source: {o.source} · Confidence:{" "}
                {o.confidence}
              </p>
            </article>
          ))}
        </div>
      )}
      {canManage(ctx.role) && data.sites.length > 0 && (
        <details className="panel editor">
          <summary>Record a verified AI observation</summary>
          <ActionForm operation="aiObservation">
            <div className="form-grid">
              <Select label="Site" name="siteId">
                <SiteOptions data={data} />
              </Select>
              <Field label="Platform" name="platform" required maxLength={120} />
              <Field
                label="Observed date"
                name="observedAt"
                type="date"
                defaultValue={localDay()}
                required
              />
              <Select label="Brand mentioned" name="brandMentioned">
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </Select>
              <Select label="Page cited" name="pageCited">
                <option value="no">No</option>
                <option value="yes">Yes</option>
              </Select>
              <Field
                label="Citation URL (when a page was cited)"
                name="citationUrl"
                type="url"
              />
              <Field label="Competitor mentioned" name="competitorMentioned" />
              <Field
                label="Confidence / evidence quality"
                name="confidence"
                required
                placeholder="e.g. Direct captured response"
                maxLength={120}
              />
              <Field
                label="Source"
                name="source"
                required
                placeholder="e.g. ChatGPT manual check"
                maxLength={500}
              />
            </div>
            <Area
              label="Exact prompt measured"
              name="prompt"
              required
              rows={5}
              maxLength={12000}
            />
            <p className="fine">
              Record only observations you actually measured. Do not infer
              coverage from a single response or convert this into a synthetic
              ranking score.
            </p>
            <button>Record observation</button>
          </ActionForm>
        </details>
      )}
    </>
  );
}
export function Leads({ data, ctx, filters }: Props) {
  const group = (key: "landingUrl" | "cluster") =>
    Array.from(new Set(data.leads.map((l) => l[key] || "Unassigned"))).map(
      (name) => ({
        name,
        count: data.leads.filter((l) => (l[key] || "Unassigned") === name)
          .length,
      }),
    );
  return (
    <>
      <FiltersBar data={data} filters={filters} month={false} />
      <div className="notice">
        <strong>A traceable commercial journey.</strong>
        <p>
          Landing page → CTA / funnel → lead → opportunity → revenue. Imported
          and manual records are identified; attribution is not proof that
          content caused a sale.
        </p>
      </div>
      <div className="kpis">
        <Metric
          title="Recorded leads"
          value={data.leads.length}
          note="All recorded stages"
        />
        <Metric
          title="Qualified actions"
          value={
            data.leads.filter((l) =>
              ["QUALIFIED", "OPPORTUNITY", "WON"].includes(l.stage),
            ).length
          }
          note="Qualified, opportunity or won"
        />
        <Metric
          title="Sales opportunities"
          value={
            data.leads.filter((l) => ["OPPORTUNITY", "WON"].includes(l.stage))
              .length
          }
          note="Opportunity or won stage"
        />
        <Metric
          title="Attributed revenue"
          value={<Revenue data={data} />}
          note="Recorded won revenue, by currency"
        />
      </div>
      <section className="panel">
        {data.leads.length ? (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Landing page / content</th>
                  <th>Acquisition</th>
                  <th>Funnel / lead</th>
                  <th>Stage</th>
                  <th>Revenue</th>
                  <th>Source / date</th>
                </tr>
              </thead>
              <tbody>
                {data.leads.map((l) => (
                  <tr key={l.id}>
                    <td>
                      {l.landingUrl}
                      <small>
                        {l.content?.title || "Content not linked"} ·{" "}
                        {l.cluster || "Unassigned cluster"}
                      </small>
                    </td>
                    <td>
                      {l.source || "Unknown"} / {l.medium || "Unknown"}
                      <small>{l.campaign || "No campaign"}</small>
                    </td>
                    <td>
                      {l.funnel}
                      <small>{l.leadId}</small>
                    </td>
                    <td>
                      <Badge value={l.stage} />
                    </td>
                    <td>
                      {l.revenue !== null
                        ? `${l.currency} ${l.revenue.toFixed(2)}`
                        : "—"}
                    </td>
                    <td>
                      <Badge value={l.dataOrigin} />
                      <small>{date(l.createdAt)}</small>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <Empty title="Connect the work to the outcome.">
            There are no attribution records yet. A secure intake endpoint is
            available for source systems, and your manager can record a
            verified result manually until a source is connected.
          </Empty>
        )}
      </section>
      <div className="two-col">
        {(["landingUrl", "cluster"] as const).map((key) => (
          <section className="panel" key={key}>
            <h2>
              Leads by {key === "cluster" ? "topic cluster" : "content page"}
            </h2>
            {group(key).map((r) => (
              <div className="connection-row" key={r.name}>
                <span>{r.name}</span>
                <strong>{r.count}</strong>
              </div>
            ))}
            {!data.leads.length && (
              <p className="fine">No recorded attribution.</p>
            )}
          </section>
        ))}
      </div>
      {canManage(ctx.role) && data.sites.length > 0 && (
        <details className="panel editor">
          <summary>Record a manual attribution result</summary>
          <ActionForm operation="lead">
            <div className="form-grid">
              <Select label="Site" name="siteId">
                <SiteOptions data={data} />
              </Select>
              <Select
                label="Content (must belong to selected site)"
                name="contentId"
              >
                <option value="">Not linked</option>
                {data.content.map((c) => (
                  <option key={c.id} value={c.id}>
                    {c.title}
                  </option>
                ))}
              </Select>
              <Field
                label="Landing URL"
                name="landingUrl"
                type="url"
                required
              />
              <Field label="Source" name="source" />
              <Field label="Medium" name="medium" />
              <Field label="Campaign" name="campaign" />
              <Field label="Funnel" name="funnel" required />
              <Field label="Lead reference" name="leadId" required />
              <Select label="Stage" name="stage">
                {["LEAD", "QUALIFIED", "OPPORTUNITY", "WON", "LOST"].map(
                  (s) => (
                    <option key={s} value={s}>
                      {label(s)}
                    </option>
                  ),
                )}
              </Select>
              <Field
                label="Recognised revenue (won stage only)"
                name="revenue"
                type="number"
                min="0"
                step="0.01"
                defaultValue="0"
                required
              />
              <Field
                label="Currency (ISO code)"
                name="currency"
                defaultValue="ZAR"
                pattern="[A-Z]{3}"
                required
              />
              <Field
                label="Result date"
                name="date"
                type="date"
                defaultValue={localDay()}
                required
              />
            </div>
            <p className="fine">
              This result will be labelled Manual. Record observed revenue, not
              estimated MRR or pipeline value.
            </p>
            <button>Record result</button>
          </ActionForm>
        </details>
      )}
    </>
  );
}
export function Strategy({ data, filters }: Props) {
  const clusters = Array.from(new Set(data.topics.map((t) => t.cluster)));
  return (
    <>
      <FiltersBar data={data} filters={filters} month={false} />
      <section className="strategy-intro">
        <span>CLUSTER</span>
        <ArrowRight size={14} />
        <span>TOPIC</span>
        <ArrowRight size={14} />
        <span>CONTENT</span>
        <ArrowRight size={14} />
        <span>FUNNEL</span>
        <ArrowRight size={14} />
        <span>RESULT</span>
      </section>
      {clusters.length ? (
        clusters.map((cluster) => (
          <details open className="panel strategy-cluster" key={cluster}>
            <summary>
              {cluster}
              <span>
                {data.topics.filter((t) => t.cluster === cluster).length} topics
              </span>
            </summary>
            {data.topics
              .filter((t) => t.cluster === cluster)
              .map((t) => (
                <div className="strategy-row" key={t.id}>
                  <Link href={`/workspace/opportunities/${t.id}`}>
                    <strong>{t.title}</strong>
                    <small>{label(t.status)}</small>
                  </Link>
                  <div>
                    {t.content.length ? (
                      t.content.map((c) => (
                        <Link key={c.id} href={`/workspace/content/${c.id}`}>
                          {c.title} ↗
                        </Link>
                      ))
                    ) : (
                      <span>Content not proposed</span>
                    )}
                  </div>
                  <span>{t.funnel || "Funnel to be defined"}</span>
                  <span>
                    {
                      data.leads.filter(
                        (l) =>
                          l.cluster === cluster &&
                          t.content.some((c) => c.id === l.contentId),
                      ).length
                    }{" "}
                    recorded leads
                  </span>
                </div>
              ))}
          </details>
        ))
      ) : (
        <section className="panel">
          <Empty title="A strategy with a commercial destination.">
            Topic clusters will connect each opportunity to its content, funnel
            and recorded outcomes. Add researched opportunities to build the
            map.
          </Empty>
        </section>
      )}
    </>
  );
}
export function Reports({ data, ctx, filters }: Props) {
  const inMonth = (d: Date) => d >= data.range.from && d < data.range.to;
  const approved = data.content
    .flatMap((c) => c.approvals)
    .filter((a) => a.decision === "APPROVED" && inMonth(a.createdAt));
  const publications = data.content
    .flatMap((c) => c.publications)
    .filter(
      (p) => p.status === "LIVE" && p.publishedAt && inMonth(p.publishedAt),
    );
  const searchPeriods = comparablePeriods(data.metrics, data.range.month);
  const analyticsPeriods = comparablePeriods(data.analytics, data.range.month);
  const now = searchTotals(searchPeriods.current);
  const before = searchTotals(searchPeriods.previous);
  const gaNow = analyticsTotals(analyticsPeriods.current);
  const gaBefore = analyticsTotals(analyticsPeriods.previous);
  const coverage = (range: { from: Date; to: Date } | null) =>
    range ? `${date(range.from)} – ${date(range.to)}` : "No comparable data";
  return (
    <>
      <FiltersBar data={data} filters={filters} />
      <article className="panel report">
        <div className="report-heading">
          <p className="eyebrow">
            MONTHLY BUSINESS REVIEW / {data.range.month}
          </p>
          <h2>{ctx.organisation.name}</h2>
          <p>What moved forward, what it produced, and where to focus next.</p>
        </div>
        <div className="kpis">
          <Metric
            title="Opportunities researched"
            value={data.topics.filter((t) => inMonth(t.createdAt)).length}
            note="Opportunities added this month"
          />
          <Metric
            title="Content created"
            value={data.content.filter((c) => inMonth(c.createdAt)).length}
            note="New proposed content"
          />
          <Metric
            title="Approval decisions"
            value={approved.length}
            note="Approved decisions, including reapprovals"
          />
          <Metric
            title="Published"
            value={publications.length}
            note="Verified live publication records"
          />
        </div>
        <div className="two-col">
          <section>
            <h3>Visibility change</h3>
            {data.metrics.length ? (
              <p>
                Search Console: {now.clicks} clicks and {now.impressions} impressions
                for {coverage(searchPeriods.currentCoverage)}, compared with{" "}
                {before.clicks} clicks and {before.impressions} impressions for{" "}
                {coverage(searchPeriods.previousCoverage)}.
                {data.analytics.length
                  ? ` GA4: ${gaNow.sessions} sessions and ${gaNow.conversions.toFixed(1)} generate_lead events for ${coverage(analyticsPeriods.currentCoverage)}, compared with ${gaBefore.sessions} sessions and ${gaBefore.conversions.toFixed(1)} generate_lead events for ${coverage(analyticsPeriods.previousCoverage)}.`
                  : ""}{" "}
                Comparisons are capped to equivalent observed day coverage so a
                partial month is not compared with a full prior month.
              </p>
            ) : (
              <p>
                Not measured. Search Console is not connected and no
                observations were recorded.
              </p>
            )}
          </section>
          <section>
            <h3>Commercial contribution</h3>
            <p>
              {data.leads.length} recorded leads,{" "}
              {
                data.leads.filter((l) =>
                  ["OPPORTUNITY", "WON"].includes(l.stage),
                ).length
              }{" "}
              sales opportunities.
            </p>
            <strong>
              <Revenue data={data} />
            </strong>
            <p className="fine">
              {Array.from(
                new Set(data.leads.map((l) => label(l.dataOrigin))),
              ).join(", ") || "No attributed revenue data"}
              . Currencies are kept separate.
            </p>
            {data.analytics.length > 0 && (
              <p className="fine">
                GA4 observed site revenue for this month:{" "}
                {Array.from(
                  data.analytics
                    .filter((row) => row.date >= data.range.from)
                    .reduce((totals, row) => {
                      totals.set(
                        row.currency,
                        (totals.get(row.currency) || 0) +
                          Number(row.totalRevenue),
                      );
                      return totals;
                    }, new Map<string, number>()),
                )
                  .map(([currency, amount]) =>
                    new Intl.NumberFormat("en-ZA", {
                      style: "currency",
                      currency,
                    }).format(amount),
                  )
                  .join(" · ") || "—"}
                . This is analytics revenue, not CRM-attributed won revenue.
              </p>
            )}
          </section>
        </div>
        <section>
          <h3>AI visibility evidence</h3>
          {data.observations.length ? (
            <p>
              {data.observations.length} observed AI responses this month:{" "}
              {data.observations.filter((o) => o.brandMentioned).length} brand
              mentions and{" "}
              {data.observations.filter((o) => o.pageCited).length} page
              citations. Results are stored as prompt-level evidence, not a
              synthetic AI ranking score.
            </p>
          ) : (
            <p>No AI observations were recorded for this month.</p>
          )}
        </section>
        <h3>Next recommendations</h3>
        {data.topics
          .filter((t) => !["PAUSED", "PUBLISHED"].includes(t.status))
          .slice(0, 3)
          .map((t) => (
            <div className="report-recommendation" key={t.id}>
              <Link href={`/workspace/opportunities/${t.id}`}>{t.title} ↗</Link>
              <p>
                {t.reasoning ||
                  "Further research is needed before recommending publication."}
              </p>
              <small>Next destination: {t.funnel || "Define a funnel"}</small>
            </div>
          ))}
        {!data.topics.length && (
          <p>
            Complete the business profile and commission commercially focused
            research before creating content.
          </p>
        )}
        <p className="fine">
          This report uses stored workspace records. No automated connector or
          unmeasured outcome is implied.
        </p>
      </article>
    </>
  );
}
export function Settings({ data, ctx }: Props) {
  return (
    <>
      <section className="panel">
        <p className="eyebrow">ORGANISATION</p>
        <h2>{ctx.organisation.name}</h2>
        <p>
          {data.profile
            ? `${data.profile.services} · ${data.profile.market}`
            : "Business profile not completed."}
        </p>
        {data.profile && (
          <dl>
            <dt>Target customers</dt>
            <dd>{data.profile.customers}</dd>
            <dt>Conversion goal</dt>
            <dd>{data.profile.conversionGoal}</dd>
          </dl>
        )}
        {canManage(ctx.role) && (
          <Link className="button secondary" href="/onboarding">
            Update profile & add site <ArrowUpRight size={15} />
          </Link>
        )}
      </section>
      <section className="panel">
        <h2>Workspace members</h2>
        <p className="fine">
          Membership changes and password resets are managed through Softkom’s
          server-side provisioning process.
        </p>
        {data.members.map((m) => (
          <div className="connection-row" key={m.id}>
            <span>
              <strong>{m.user.name}</strong>
              <small>{m.user.email}</small>
            </span>
            <Badge value={m.role} />
          </div>
        ))}
      </section>
      <section className="panel">
        <h2>Recent audit trail</h2>
        {data.audits.map((a) => (
          <div className="connection-row" key={a.id}>
            <span>
              <strong>{label(a.action.replaceAll(".", " "))}</strong>
              <small>
                Actor:{" "}
                {data.members.find((m) => m.userId === a.actorId)?.user.name ||
                  "Former workspace member"}{" "}
                · Record {a.entityId}
              </small>
            </span>
            <span>{date(a.createdAt)}</span>
          </div>
        ))}
        {!data.audits.length && <p>No events recorded yet.</p>}
      </section>
    </>
  );
}
