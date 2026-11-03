export default function Loading() {
  return (
    <div role="status" aria-label="Loading workspace" className="loading">
      <div className="skeleton heading" />
      <div className="kpis">
        {[1, 2, 3, 4].map((i) => (
          <div key={i} className="skeleton" />
        ))}
      </div>
      <div className="skeleton large" />
      <p>Loading your workspace…</p>
    </div>
  );
}
