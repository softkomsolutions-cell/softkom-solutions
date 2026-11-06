import { currentUser } from "@/lib/context";
import { Onboarding } from "@/components/onboarding";
export const dynamic = "force-dynamic";
export default async function Page() {
  await currentUser();
  return (
    <main className="standalone">
      <p className="eyebrow">SOFTKOM VISIBILITY ENGINE / WELCOME</p>
      <h1>A stronger connection to your next customer.</h1>
      <p>
        Four steps to give your visibility strategy a useful starting point.
      </p>
      <section className="panel">
        <Onboarding businessName="" />
      </section>
    </main>
  );
}
