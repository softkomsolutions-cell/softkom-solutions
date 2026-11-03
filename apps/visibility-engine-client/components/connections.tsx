import { ActionForm } from "./action-form";
import { startGoogle } from "@/app/actions";
import { Field, Select, Badge, Area, date, label } from "./ui";
import { canManage } from "@/lib/security";
import type { Context } from "@/lib/context";
import type { WorkspaceData } from "@/lib/queries";
const timestamp = (value: Date | null | undefined) =>
  value
    ? value.toLocaleString("en-ZA", { timeZone: "Africa/Johannesburg" }) +
      " SAST"
    : "Not yet";
export function ConnectionPanels({
  data,
  ctx,
}: {
  data: WorkspaceData;
  ctx: Context;
}) {
  const manage = canManage(ctx.role);
  return (
    <>
      <div className="notice">
        <p>
          Connect WordPress to publish approved pages, then connect Search
          Console to measure their search performance. Credentials stay
          encrypted and are never displayed after saving. An owner,
          administrator or Softkom manager manages connections.
        </p>
      </div>
      {data.sites.map((site) => {
        const wp = site.connections.find((c) => c.provider === "WORDPRESS"),
          gsc = site.connections.find(
            (c) => c.provider === "GOOGLE_SEARCH_CONSOLE",
          );
        const properties = Array.isArray(gsc?.properties)
          ? gsc.properties.filter(
              (p): p is { siteUrl: string; permissionLevel: string } =>
                !!p &&
                typeof p === "object" &&
                !Array.isArray(p) &&
                typeof p.siteUrl === "string" &&
                typeof p.permissionLevel === "string",
            )
          : [];
        return (
          <section className="panel" key={site.id}>
            <div className="panel-head">
              <div>
                <span className="eyebrow">YOUR WEBSITE</span>
                <h2>{site.name}</h2>
                <p>{site.baseUrl}</p>
              </div>
              <Badge value={site.status} />
            </div>
            <div className="connection-grid">
              <article className="connection-card">
                <span className="connection-symbol">W</span>
                <h3>WordPress</h3>
                <Badge value={wp?.status || "NOT_CONNECTED"} />
                <p>
                  {wp?.label || "Publish with your approval"}
                  {wp?.accountLabel ? ` · ${wp.accountLabel}` : ""}
                </p>
                <p className="fine">
                  Last verified: {timestamp(wp?.verifiedAt)}
                </p>
                {wp?.error && (
                  <p role="status" className="notice error">
                    {wp.error}
                  </p>
                )}
                {manage && site.cms === "WORDPRESS" && (
                  <>
                    <details>
                      <summary>
                        {wp?.status === "CONNECTED"
                          ? "Reconnect WordPress"
                          : "Connect WordPress"}
                      </summary>
                      <ActionForm operation="wordpressConnect">
                        <input type="hidden" name="siteId" value={site.id} />
                        <Field
                          label="Connection label"
                          name="label"
                          defaultValue={wp?.label || site.name}
                          required
                          maxLength={200}
                        />
                        <Field
                          label="WordPress site URL"
                          type="url"
                          name="siteUrl"
                          defaultValue={site.baseUrl}
                          required
                        />
                        <Field
                          label="WordPress username"
                          name="username"
                          autoComplete="off"
                          required
                        />
                        <Field
                          label="Application password"
                          name="password"
                          type="password"
                          autoComplete="new-password"
                          required
                        />
                        <p className="fine">
                          Create a WordPress Application Password under Users →
                          Profile for an account that can create, edit and
                          publish pages. Use the canonical public HTTPS
                          installation URL.
                        </p>
                        <button>Verify & connect WordPress</button>
                      </ActionForm>
                    </details>
                    <ActionForm operation="wordpressTest">
                      <input type="hidden" name="siteId" value={site.id} />
                      <button className="secondary">Test connection</button>
                    </ActionForm>
                    <ActionForm operation="disconnect">
                      <input type="hidden" name="siteId" value={site.id} />
                      <input type="hidden" name="provider" value="WORDPRESS" />
                      <button className="secondary">
                        Disconnect WordPress
                      </button>
                    </ActionForm>
                  </>
                )}
              </article>
              <article className="connection-card">
                <span className="connection-symbol">G</span>
                <h3>Google Search Console</h3>
                <Badge value={gsc?.status || "NOT_CONNECTED"} />
                <p>
                  {gsc?.selectedProperty ||
                    "Choose a verified property after Google authorisation."}
                </p>
                <p className="fine">
                  Last verified: {timestamp(gsc?.verifiedAt)}
                  <br />
                  Last sync: {timestamp(gsc?.lastSyncedAt)}
                </p>
                {gsc?.error && <p className="notice error">{gsc.error}</p>}
                {manage && (
                  <>
                    <ActionForm action={startGoogle}>
                      <input type="hidden" name="siteId" value={site.id} />
                      <button>
                        {gsc?.verifiedAt
                          ? "Reconnect Google Search Console"
                          : "Connect Google Search Console"}
                      </button>
                    </ActionForm>
                    {properties.length > 0 && (
                      <ActionForm operation="googleProperty">
                        <input type="hidden" name="siteId" value={site.id} />
                        <Select
                          label="Search Console property"
                          name="property"
                          defaultValue={gsc?.selectedProperty || ""}
                        >
                          <option value="" disabled>
                            Select a property
                          </option>
                          {properties.map((p) => (
                            <option key={p.siteUrl} value={p.siteUrl}>
                              {p.siteUrl} · {p.permissionLevel}
                            </option>
                          ))}
                        </Select>
                        <button className="secondary">Select property</button>
                      </ActionForm>
                    )}
                    {gsc?.status === "CONNECTED" && (
                      <ActionForm operation="searchSync">
                        <input type="hidden" name="siteId" value={site.id} />
                        <button className="secondary">Sync search data</button>
                      </ActionForm>
                    )}
                    <ActionForm operation="disconnect">
                      <input type="hidden" name="siteId" value={site.id} />
                      <input
                        type="hidden"
                        name="provider"
                        value="GOOGLE_SEARCH_CONSOLE"
                      />
                      <button className="secondary">Disconnect Google</button>
                    </ActionForm>
                  </>
                )}
                {gsc?.sync && (
                  <div className="notice">
                    <strong>
                      Search sync:{" "}
                      {gsc.sync.status === "LIVE"
                        ? "Completed"
                        : label(gsc.sync.status)}
                    </strong>
                    <p>
                      Range: {date(gsc.sync.from)} – {date(gsc.sync.to)}
                      <br />
                      Last attempt: {timestamp(gsc.sync.lastAttemptAt)}
                      <br />
                      Last success: {timestamp(gsc.sync.lastSuccessAt)}
                      <br />
                      Latest data: {date(gsc.sync.latestDataDate)}
                      <br />
                      Attempts: {gsc.sync.attempts} of 5<br />
                      Next sync:{" "}
                      {gsc.sync.nextRetryAt
                        ? timestamp(gsc.sync.nextRetryAt)
                        : gsc.sync.lastSuccessAt
                          ? timestamp(
                              new Date(
                                gsc.sync.lastSuccessAt.getTime() + 86400000,
                              ),
                            )
                          : "Awaiting worker"}
                    </p>
                    {gsc.sync.error && <p>{gsc.sync.error}</p>}
                  </div>
                )}
              </article>
              <article className="connection-card">
                <h3>Google Analytics 4</h3>
                <Badge value="COMING_SOON" />
                <p>
                  Conversion analytics are not connected. No GA4 measurements
                  are shown.
                </p>
              </article>
            </div>
          </section>
        );
      })}
      {manage && (
        <section className="panel">
          <h2>Recognise your branded searches</h2>
          <p>
            One brand or product phrase per line. Classification uses
            case-insensitive whole phrase matching; everything else is
            non-branded. Empty terms leave all searches unclassified.
          </p>
          <ActionForm operation="brandTerms">
            <Area
              label="Branded search terms"
              name="terms"
              defaultValue={ctx.organisation.brandedTerms}
              maxLength={3000}
            />
            <button>Save branded terms</button>
          </ActionForm>
        </section>
      )}
    </>
  );
}
