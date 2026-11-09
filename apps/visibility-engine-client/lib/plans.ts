export type PlanKey = "TRIAL" | "STARTER" | "GROWTH" | "PRO";

export const PLANS: Record<PlanKey, {
  name: string;
  monthlyPrice: number;
  usdMonthlyPrice: number;
  sites: number;
  members: number;
  aiObservationsPerDay: number;
}> = {
  TRIAL: { name: "14-day trial", monthlyPrice: 0, usdMonthlyPrice: 0, sites: 1, members: 2, aiObservationsPerDay: 3 },
  STARTER: { name: "Starter", monthlyPrice: 1490, usdMonthlyPrice: 99, sites: 1, members: 3, aiObservationsPerDay: 3 },
  GROWTH: { name: "Growth", monthlyPrice: 2990, usdMonthlyPrice: 199, sites: 3, members: 8, aiObservationsPerDay: 6 },
  PRO: { name: "Pro", monthlyPrice: 5990, usdMonthlyPrice: 399, sites: 10, members: 20, aiObservationsPerDay: 12 },
};

export const planKey = (value?: string | null): PlanKey =>
  value && value in PLANS ? (value as PlanKey) : "TRIAL";

export function trialEndsAt(from = new Date()) {
  return new Date(from.getTime() + 14 * 24 * 60 * 60 * 1000);
}

export function subscriptionActive(input?: {
  status: string;
  trialEndsAt: Date | null;
  currentPeriodEndsAt: Date | null;
} | null) {
  if (!input) return true;
  if (["ACTIVE", "PAID"].includes(input.status)) return true;
  if (input.status === "TRIALING")
    return !!input.trialEndsAt && input.trialEndsAt.getTime() > Date.now();
  return !!input.currentPeriodEndsAt && input.currentPeriodEndsAt.getTime() > Date.now();
}
