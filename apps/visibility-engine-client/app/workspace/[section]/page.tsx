import { notFound } from "next/navigation";
import { getContext } from "@/lib/context";
import { workspaceData, type Filters } from "@/lib/queries";
import { PageHeader } from "@/components/ui";
import * as Views from "@/components/views";
const sections = {
  dashboard: [
    "Your visibility, with purpose.",
    "A clear view of the work, the decisions and the business outcomes.",
    Views.Dashboard,
  ],
  opportunities: [
    "The next right opportunity.",
    "Commercially useful ideas, with a reason to pursue each one.",
    Views.Opportunities,
  ],
  calendar: [
    "A considered publishing plan.",
    "See what is planned, who is reviewing it and where it leads.",
    Views.Calendar,
  ],
  content: [
    "Good content. Your approval.",
    "Review the message before it represents your business.",
    Views.Content,
  ],
  analytics: [
    "Understand your search visibility.",
    "Measured impressions, clicks and the pages earning attention.",
    Views.Analytics,
  ],
  ai: [
    "See the evidence in AI.",
    "Transparent observations of brand mentions and page citations.",
    Views.AIVisibility,
  ],
  leads: [
    "From attention to opportunity.",
    "Follow the trail from useful content to qualified business.",
    Views.Leads,
  ],
  strategy: [
    "Every topic has a destination.",
    "Explore how your clusters, content and offers connect to results.",
    Views.Strategy,
  ],
  connections: [
    "Your sites. Connected thoughtfully.",
    "Know exactly what is connected and what still needs setup.",
    Views.Connections,
  ],
  reports: [
    "Progress that means something.",
    "A monthly view of the work and its commercial contribution.",
    Views.Reports,
  ],
  settings: [
    "Your workspace, in order.",
    "Business context, organisation access and the record of important changes.",
    Views.Settings,
  ],
} as const;
export default async function Page({
  params,
  searchParams,
}: {
  params: Promise<{ section: string }>;
  searchParams: Promise<Filters>;
}) {
  const { section } = await params;
  if (!(section in sections)) notFound();
  const [title, description, View] = sections[section as keyof typeof sections];
  const ctx = await getContext();
  const filters = await searchParams;
  const data = await workspaceData(ctx, filters, section);
  return (
    <>
      <PageHeader title={title} description={description} />
      <View data={data} ctx={ctx} filters={filters} />
    </>
  );
}
