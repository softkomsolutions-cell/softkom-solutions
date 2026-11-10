-- Preserve the current revision of pre-existing content without altering copy,
-- metadata, statuses or historic approval decisions.
INSERT INTO "ContentRevision" ("id", "contentId", "number", "title", "body", "metaTitle", "metaDesc", "internalLinks", "authorId", "createdAt")
SELECT 'legacy_revision_' || md5(c."id"), c."id", c."revision", c."title", c."body", c."metaTitle", c."metaDesc", c."internalLinks", 'legacy-import', c."updatedAt"
FROM "ContentItem" c
WHERE NOT EXISTS (SELECT 1 FROM "ContentRevision" r WHERE r."contentId" = c."id" AND r."number" = c."revision");

-- A pre-existing site is not evidence of a functioning integration.
INSERT INTO "SiteConnection" ("id", "siteId", "provider", "status", "updatedAt")
SELECT 'legacy_connection_' || md5(s."id" || p."provider"::text), s."id", p."provider", p."status", now()
FROM "Site" s CROSS JOIN (VALUES
  ('WORDPRESS'::"ConnectionProvider", 'REQUIRES_SETUP'::"ConnectionState"),
  ('GOOGLE_SEARCH_CONSOLE'::"ConnectionProvider", 'COMING_SOON'::"ConnectionState"),
  ('GOOGLE_ANALYTICS_4'::"ConnectionProvider", 'COMING_SOON'::"ConnectionState")
) AS p("provider", "status")
ON CONFLICT ("siteId", "provider") DO NOTHING;
