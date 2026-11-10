import { currentUser } from "@/lib/context";
import { Onboarding } from "@/components/onboarding";
export const dynamic = "force-dynamic";
export default async function Page({ searchParams }: { searchParams: Promise<{ plan?: string }> }) {
  const { plan = "" } = await searchParams;
  const selectedPlan = ["starter", "growth", "pro"].includes(plan.toLowerCase()) ? plan.toLowerCase() : "";
  await currentUser();
  return (
    <main className="standalone">
      <p className="eyebrow">SOFTKOM VISIBILITY ENGINE / WELCOME</p>
      <h1>A stronger connection to your next customer.</h1>
      <p>
        Four steps to give your visibility strategy a useful starting point.
      </p>
      <section className="panel">
        {selectedPlan && <p className="fine">Interested in the <strong>{selectedPlan.charAt(0).toUpperCase() + selectedPlan.slice(1)}</strong> plan? Your workspace begins on the 14-day trial. Upgrade after setup to activate paid limits.</p>}
        <Onboarding businessName="" />
      </section>
    </main>
  );
}
