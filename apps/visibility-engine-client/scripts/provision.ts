import "dotenv/config";
import { randomUUID } from "node:crypto";
import { hashPassword } from "better-auth/crypto";
import { OrgRole } from "@prisma/client";
import { z } from "zod";
import { db } from "../lib/db";
// Operator-only provisioning. No public signup or membership mutation endpoint.
const input = z
  .object({
    email: z.email(),
    name: z.string().min(1),
    password: z.string().min(12).max(128),
    role: z.nativeEnum(OrgRole),
    organisationId: z.string().optional(),
  })
  .parse({
    email: process.env.PROVISION_EMAIL,
    name: process.env.PROVISION_NAME,
    password: process.env.PROVISION_PASSWORD,
    role: process.env.PROVISION_ROLE || "CLIENT",
    organisationId: process.env.PROVISION_ORGANISATION_ID,
  });
try {
  const password = await hashPassword(input.password);
  const user = await db.$transaction(async (tx) => {
    if (
      input.organisationId &&
      !(await tx.organisation.findUnique({
        where: { id: input.organisationId },
      }))
    )
      throw new Error("Organisation does not exist.");
    const user = await tx.user.upsert({
      where: { email: input.email.toLowerCase() },
      create: {
        email: input.email.toLowerCase(),
        name: input.name,
        emailVerified: true,
      },
      update: { name: input.name },
    });
    await tx.account.upsert({
      where: {
        providerId_accountId: { providerId: "credential", accountId: user.id },
      },
      create: {
        id: randomUUID(),
        accountId: user.id,
        userId: user.id,
        providerId: "credential",
        password,
      },
      update: { password },
    });
    // Resetting credentials also revokes existing sessions.
    await tx.session.deleteMany({ where: { userId: user.id } });
    if (input.organisationId) {
      await tx.membership.upsert({
        where: {
          organisationId_userId: {
            organisationId: input.organisationId,
            userId: user.id,
          },
        },
        create: {
          organisationId: input.organisationId,
          userId: user.id,
          role: input.role,
        },
        update: { role: input.role },
      });
      await tx.auditEvent.create({
        data: {
          organisationId: input.organisationId,
          actorId: "operator",
          action: "membership.provisioned",
          entityId: user.id,
          detail: { role: input.role },
        },
      });
    }
    return user;
  });
  console.log(
    `Provisioned user ${user.id}. Password is never printed. Organisation membership ${input.organisationId ? "saved" : "will be established during onboarding"}.`,
  );
} finally {
  await db.$disconnect();
}
