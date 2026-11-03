import type { WorkspaceData } from "@/lib/queries";
import { searchTotals } from "@/lib/queries";
import { canonicalPage, brandClass } from "@/lib/analytics/reporting";
import { date, Badge } from "./ui";
const coverage = (rows: WorkspaceData["metrics"]) =>
  rows.length
    ? `${date(new Date(rows.reduce((value, row) => Math.min(value, row.date.getTime()), Infinity)))} – ${date(new Date(rows.reduce((value, row) => Math.max(value, row.date.getTime()), 0)))}`
    : "No observations";
export function SearchInsights({
  data,
  terms,
}: {
  data: WorkspaceData;
  terms: string;
}) {
  const current = data.metrics.filter(
    (m) => m.date >= data.range.from && m.date < data.range.to,
  );
  const previous = data.metrics.filter(
    (m) => m.date >= data.range.previous && m.date < data.range.from,
  );
  const groupQueries = (rows: WorkspaceData["metrics"]) => {
    const map = new Map<string, WorkspaceData["metrics"]>();
    for (const row of rows) {
      if (!row.query) continue;
      const group = map.get(row.query) || [];
      group.push(row);
      map.set(row.query, group);
    }
    return map;
  };
  const priorQueries = groupQueries(previous);
  const queries = Array.from(groupQueries(current), ([query, rows]) => {
    const prior = priorQueries.get(query) || [];
    const totals = searchTotals(rows);
    return {
      query,
      ...totals,
      delta: totals.clicks - searchTotals(prior).clicks,
      prior: prior.length > 0,
    };
  });
  return (
    <>
      <section className="panel">
        <h2>Your data, with its context</h2>
        <p>
          Observed dates in this view: {coverage(current)}. Comparison dates:{" "}
          {coverage(previous)}.
        </p>
        <p className="fine">
          Search Console syncs finalised web-search data ending three days ago.
          Google can withhold anonymised or low-volume queries; these are
          observed query/page totals, not a complete traffic census. Today’s
          results are not complete.
        </p>
        {data.sites.map((s) => {
          const c = s.connections.find(
              (c) => c.provider === "GOOGLE_SEARCH_CONSOLE",
            ),
            job = c?.sync;
          return (
            <div className="connection-row" key={s.id}>
              <div>
                <strong>{s.name}</strong>
                <p>{c?.selectedProperty || "No property selected"}</p>
              </div>
              <div>
                <Badge value={job?.status || c?.status || "NOT_CONNECTED"}>
                  {job?.status === "LIVE" ? "Completed" : undefined}
                </Badge>
                <p>
                  Latest data:{" "}
                  {job?.latestDataDate
                    ? date(job.latestDataDate)
                    : "No data synced"}
                  <br />
                  Last sync:{" "}
                  {c?.lastSyncedAt ? date(c.lastSyncedAt) : "Not yet"}
                  <br />
                  Ingestion range:{" "}
                  {job?.lastFrom
                    ? `${date(job.lastFrom)} – ${date(job.lastTo)}`
                    : "Not yet"}
                  <br />
                  {job?.nextRetryAt
                    ? `Retry: ${date(job.nextRetryAt)}`
                    : "Daily sync when the worker runs"}
                </p>
                {job?.error && <p>{job.error}</p>}
              </div>
            </div>
          );
        })}
      </section>
      {current.length > 0 && (
        <>
          <section className="panel">
            <h2>Branded and non-branded visibility</h2>
            <p className="fine">
              Whole phrase matches against your editable branded terms in
              Connections. No terms means unclassified; this does not estimate
              search volume.
            </p>
            {["Branded", "Non-branded", "Unclassified"].map((group) => {
              const rows = current.filter(
                  (m) => m.query && brandClass(m.query, terms) === group,
                ),
                sum = searchTotals(rows);
              return (
                <div className="connection-row" key={group}>
                  <strong>{group}</strong>
                  <span>
                    {sum.clicks} clicks · {sum.impressions} impressions ·{" "}
                    {sum.ctr === null ? "—" : (sum.ctr * 100).toFixed(2) + "%"}{" "}
                    CTR
                  </span>
                </div>
              );
            })}
          </section>
          <div className="two-col">
            {[
              "Improving queries",
              "Declining queries",
              "Newly observed queries",
            ].map((heading, index) => (
              <section className="panel" key={heading}>
                <h2>{heading}</h2>
                <p className="fine">
                  {index === 2
                    ? "Not observed in the recorded comparison period; this does not prove first-ever discovery."
                    : "Click change between recorded periods; date coverage may differ."}
                </p>
                {queries
                  .filter((q) =>
                    index === 2
                      ? previous.length > 0 && !q.prior
                      : q.prior && (index === 0 ? q.delta > 0 : q.delta < 0),
                  )
                  .slice(0, 20)
                  .map((q) => (
                    <div className="connection-row" key={q.query}>
                      <span>{q.query}</span>
                      <strong>
                        {index === 2
                          ? q.clicks
                          : `${q.delta > 0 ? "+" : ""}${q.delta}`}{" "}
                        clicks
                      </strong>
                    </div>
                  ))}
              </section>
            ))}
          </div>
          <section className="panel">
            <h2>Your published content, measured</h2>
            <p className="fine">
              Mapped by recorded live URL → content → topic → cluster. Recorded
              leads remain attributed observations, not proof of causation.
            </p>
            <div className="table-wrap">
              <table className="search-content-table">
                <thead>
                  <tr>
                    <th>Content / cluster</th>
                    <th>Clicks</th>
                    <th>Impressions</th>
                    <th>CTR</th>
                    <th>Position</th>
                    <th>Trend</th>
                    <th>Coverage</th>
                    <th>Recorded leads</th>
                  </tr>
                </thead>
                <tbody>
                  {data.content
                    .filter((c) =>
                      c.publications.some(
                        (p) => p.status === "LIVE" && p.verifiedAt,
                      ),
                    )
                    .map((c) => {
                      const urls = c.publications
                        .filter((p) => p.status === "LIVE" && p.liveUrl)
                        .map((p) => canonicalPage(p.liveUrl!));
                      const rows = current.filter(
                          (m) =>
                            m.pageUrl &&
                            urls.includes(canonicalPage(m.pageUrl)),
                        ),
                        prior = previous.filter(
                          (m) =>
                            m.pageUrl &&
                            urls.includes(canonicalPage(m.pageUrl)),
                        ),
                        sum = searchTotals(rows),
                        delta = sum.clicks - searchTotals(prior).clicks;
                      return (
                        <tr key={c.id}>
                          <td>
                            {c.title}
                            <small>
                              {c.topic?.cluster || "Unassigned cluster"}
                            </small>
                          </td>
                          <td>{sum.clicks}</td>
                          <td>{sum.impressions}</td>
                          <td>
                            {sum.ctr === null
                              ? "—"
                              : (sum.ctr * 100).toFixed(2) + "%"}
                          </td>
                          <td>{sum.position?.toFixed(1) || "—"}</td>
                          <td>
                            {rows.length && prior.length
                              ? `${delta > 0 ? "+" : ""}${delta} clicks`
                              : "Insufficient comparison data"}
                          </td>
                          <td>{coverage(rows)}</td>
                          <td>
                            {
                              data.leads.filter(
                                (l) =>
                                  l.contentId === c.id ||
                                  urls.includes(canonicalPage(l.landingUrl)),
                              ).length
                            }
                          </td>
                        </tr>
                      );
                    })}
                </tbody>
              </table>
            </div>
          </section>
        </>
      )}
    </>
  );
}
