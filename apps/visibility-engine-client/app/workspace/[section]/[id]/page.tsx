import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft, ArrowUpRight, ShieldCheck } from "lucide-react";
import { getContext, scopedContent } from "@/lib/context";
import { db } from "@/lib/db";
import { AccessError, canManage, cleanContent, idSchema } from "@/lib/security";
import { ActionForm } from "@/components/action-form";
import {
  Area,
  Badge,
  date,
  Field,
  label,
  PageHeader,
  Select,
} from "@/components/ui";

export default async function Detail({
  params,
}: {
  params: Promise<{ section: string; id: string }>;
}) {
  const { section, id } = await params;
  const ctx = await getContext();
  if (!idSchema.safeParse(id).success) notFound();
  if (section === "opportunities") {
    const topic = await db.topic.findFirst({
      where: { id, site: { organisationId: ctx.organisationId } },
      include: {
        site: true,
        content: { select: { id: true, title: true, status: true } },
      },
    });
    if (!topic) notFound();
    return (
      <>
        <Link className="back-link" href="/workspace/opportunities">
          <ArrowLeft size={15} /> All opportunities
        </Link>
        <PageHeader
          eyebrow={`${topic.cluster} / ${topic.site.name}`}
          title={topic.title}
          description={`Priority ${topic.priority} · ${label(topic.intent)}`}
        >
          <Badge value={topic.status} />
        </PageHeader>
        <div className="detail-grid">
          <section className="panel">
            <p className="eyebrow">WHY THIS IS WORTH PURSUING</p>
            <h2>The commercial reasoning</h2>
            <p className="reasoning">
              {topic.reasoning ||
                "Reasoning not recorded. Ask your manager for research before approving content."}
            </p>
            <h3>The business problem</h3>
            <p>{topic.businessProblem || "Still to be defined."}</p>
            <h3>Evidence & limitations</h3>
            <p className="preserve">
              {topic.evidence ||
                "No supporting sources recorded yet. This is a research opportunity, not a verified demand forecast."}
            </p>
            <div className="notice">
              <ShieldCheck size={20} />
              <p>
                No search-volume or commercial outcome estimate has been
                invented for this topic.
              </p>
            </div>
          </section>
          <aside>
            <section className="panel">
              <h3>The intended next step</h3>
              <dl>
                <dt>Client offer / funnel</dt>
                <dd>{topic.funnel || "Not defined"}</dd>
                <dt>Recommended format</dt>
                <dd>{topic.contentType}</dd>
                <dt>Search intent</dt>
                <dd>{label(topic.intent)}</dd>
              </dl>
            </section>
            <section className="panel">
              <h3>Proposed content</h3>
              {topic.content.map((c) => (
                <Link
                  className="content-link"
                  href={`/workspace/content/${c.id}`}
                  key={c.id}
                >
                  <strong>{c.title}</strong>
                  <Badge value={c.status} />
                </Link>
              ))}
              {!topic.content.length && (
                <p>Content has not been proposed yet.</p>
              )}
              <Link className="inline-link" href="/workspace/content">
                Open content workspace <ArrowUpRight size={14} />
              </Link>
            </section>
          </aside>
        </div>
        {canManage(ctx.role) && (
          <section className="panel editor">
            <h3>Manage opportunity</h3>
            <ActionForm operation="topicStatus">
              <input type="hidden" name="id" value={topic.id} />
              <Select
                name="status"
                label="Workflow status"
                defaultValue={
                  ["PUBLISHED", "APPROVED"].includes(topic.status)
                    ? "REVIEW"
                    : topic.status
                }
              >
                {["RESEARCH", "PLANNED", "WRITING", "REVIEW", "PAUSED"].map(
                  (s) => (
                    <option key={s} value={s}>
                      {label(s)}
                    </option>
                  ),
                )}
              </Select>
              <button>Update status</button>
            </ActionForm>
          </section>
        )}
      </>
    );
  }
  if (section !== "content") notFound();
  const content = await scopedContent(ctx, id).catch((error) => {
    if (error instanceof AccessError) notFound();
    throw error;
  });
  return (
    <>
      <Link className="back-link" href="/workspace/content">
        <ArrowLeft size={15} /> All content
      </Link>
      <PageHeader
        eyebrow={`${content.site.name} / REVISION ${content.revision}`}
        title={content.title}
        description={`${content.contentType} · ${content.topic?.cluster || "Topic cluster not assigned"}`}
      >
        <Badge value={content.status} />
      </PageHeader>
      <div className="detail-grid">
        <div>
          <article className="panel content-preview">
            <div className="preview-label">
              <span>CLIENT REVIEW COPY</span>
              <span>Revision {content.revision}</span>
            </div>
            <h2>{content.title}</h2>
            <div
              className="prose"
              dangerouslySetInnerHTML={{ __html: cleanContent(content.body) }}
            />
          </article>
          <section className="panel">
            <p className="eyebrow">SEARCH PRESENTATION</p>
            <h2>How this page is positioned</h2>
            <dl>
              <dt>SEO title</dt>
              <dd>{content.metaTitle || "Not provided"}</dd>
              <dt>Meta description</dt>
              <dd>{content.metaDesc || "Not provided"}</dd>
              <dt>Target topic / search intent</dt>
              <dd>
                {content.topic?.title || "Not assigned"} ·{" "}
                {content.topic ? label(content.topic.intent) : "Not specified"}
              </dd>
              <dt>Funnel destination</dt>
              <dd>
                {content.funnel || content.topic?.funnel || "Not defined"}
              </dd>
              <dt>Internal linking recommendations</dt>
              <dd className="preserve">
                {content.internalLinks || "Not recorded"}
              </dd>
              <dt>Publishing destination</dt>
              <dd>
                {content.publishingDestination} · {content.site.baseUrl}
              </dd>
            </dl>
          </section>
          <section className="panel">
            <h2>Approval & comment history</h2>
            {!content.approvals.length && !content.comments.length && (
              <p>
                Your feedback will appear here with your name, decision,
                revision and timestamp.
              </p>
            )}
            {[
              ...content.approvals.map((a) => ({
                id: a.id,
                name: a.reviewer?.name || "Former reviewer",
                date: a.createdAt,
                revision: a.revision,
                decision: label(a.decision),
                body: a.comment,
              })),
              ...content.comments.map((c) => ({
                id: c.id,
                name: c.author.name || "Workspace member",
                date: c.createdAt,
                revision: c.revision,
                decision: "Comment",
                body: c.body,
              })),
            ]
              .sort((a, b) => b.date.getTime() - a.date.getTime())
              .map((a) => (
                <article className="history-entry" key={a.id}>
                  <div>
                    <strong>{a.name}</strong>
                    <Badge>{a.decision}</Badge>
                  </div>
                  <p>{a.body || "Approved without additional comments."}</p>
                  <small>
                    Revision {a.revision} ·{" "}
                    {a.date.toLocaleString("en-ZA", {
                      timeZone: "Africa/Johannesburg",
                    })}{" "}
                    SAST
                  </small>
                </article>
              ))}
          </section>
          <section className="panel">
            <h2>Revision history</h2>
            {content.revisions.map((r) => (
              <details className="revision" key={r.id}>
                <summary>
                  Revision {r.number} · {date(r.createdAt)} — {r.title}
                </summary>
                <dl>
                  <dt>SEO title</dt>
                  <dd>{r.metaTitle}</dd>
                  <dt>Meta description</dt>
                  <dd>{r.metaDesc}</dd>
                  <dt>Internal links</dt>
                  <dd>{r.internalLinks || "None recorded"}</dd>
                </dl>
                <div
                  className="prose"
                  dangerouslySetInnerHTML={{ __html: cleanContent(r.body) }}
                />
              </details>
            ))}
            {!content.revisions.length && (
              <p>
                Legacy content has no stored revision snapshot. Save a revision
                before requesting a new approval.
              </p>
            )}
          </section>
        </div>
        <aside className="review-sidebar">
          <section className="panel">
            <span className="eyebrow">YOUR DECISION</span>
            <h2>Make it right for your business.</h2>
            <p>
              Review revision {content.revision}. Your decision will be saved
              with your identity.
            </p>
            <ActionForm operation="review">
              <input type="hidden" name="id" value={content.id} />
              <input type="hidden" name="revision" value={content.revision} />
              <Select label="Review action" name="decision">
                <option value="APPROVED">Approve this revision</option>
                <option value="CHANGES_REQUESTED">Request changes</option>
                <option value="COMMENT">Add comment</option>
              </Select>
              <Area
                label="Comment (required for changes or a comment)"
                name="comment"
                maxLength={6000}
              />
              <button>Save review decision</button>
            </ActionForm>
            <p className="fine">
              Any new revision needs a new approval. Publication is a separate,
              authorised step.
            </p>
          </section>
          <section className="panel">
            <h3>Publishing status</h3>
            {!content.publications.length && (
              <p>No publish request has been made.</p>
            )}
            {content.publications.map((p) => (
              <article className="publication" key={p.id}>
                <Badge value={p.status} />
                <p>{p.error || "Request recorded."}</p>
                {p.status === "LIVE" && p.liveUrl && (
                  <a
                    className="inline-link"
                    href={p.liveUrl}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    Open live page ↗
                  </a>
                )}
                <small>
                  Revision {p.requestedRevision} · Attempt {p.attempts} ·{" "}
                  {date(p.createdAt)}
                  {p.externalId ? ` · External ID ${p.externalId}` : ""}
                </small>
                {p.publishedAt && (
                  <small>Published {date(p.publishedAt)}</small>
                )}
                {p.overrideReason && (
                  <p className="notice">Manager override: {p.overrideReason}</p>
                )}
                <small>
                  {p.nextRetryAt
                    ? `Retry after ${date(p.nextRetryAt)}`
                    : "No automatic retry scheduled"}
                </small>
              </article>
            ))}
            {canManage(ctx.role) && (
              <>
                <div className="notice">
                  <p>
                    WordPress publishing is not implemented. A request records a
                    failure and sends no content.
                  </p>
                </div>
                <ActionForm operation="publish">
                  <input type="hidden" name="id" value={content.id} />
                  {ctx.role === "SOFTKOM_MANAGER" && (
                    <Area
                      label="Optional approval override reason (minimum 20 characters; audited)"
                      name="overrideReason"
                      maxLength={2000}
                    />
                  )}
                  <button className="secondary">Record publish request</button>
                </ActionForm>
              </>
            )}
          </section>
        </aside>
      </div>
      {canManage(ctx.role) && (
        <details className="panel editor">
          <summary>Prepare a new revision</summary>
          <p>
            Saving changes returns the content to client review. Earlier
            approvals remain in the audit history.
          </p>
          <ActionForm operation="revise">
            <input type="hidden" name="id" value={content.id} />
            <input type="hidden" name="revision" value={content.revision} />
            <Field
              label="Title"
              name="title"
              defaultValue={content.title}
              required
            />
            <div className="form-grid">
              <Field
                label="SEO title"
                name="metaTitle"
                defaultValue={content.metaTitle || ""}
                required
                maxLength={100}
              />
              <Field
                label="Meta description"
                name="metaDesc"
                defaultValue={content.metaDesc || ""}
                required
                maxLength={300}
              />
            </div>
            <Area
              label="Page content"
              name="body"
              defaultValue={content.body}
              required
              rows={15}
              maxLength={100000}
            />
            <Area
              label="Internal linking recommendations"
              name="internalLinks"
              defaultValue={content.internalLinks}
              maxLength={2000}
            />
            <button>Save revision & request review</button>
          </ActionForm>
        </details>
      )}
    </>
  );
}
