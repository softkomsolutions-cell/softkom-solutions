const kpis = [
  ["12", "Tracked opportunities"],
  ["8", "Pages ready / live"],
  ["3", "Awaiting approval"],
  ["4", "Funnel conversions"]
];

const calendar = [
  ["Mon", "Quote follow-up", "Revenue Recovery"],
  ["Tue", "Manual admin", "Business Efficiency"],
  ["Wed", "Software cost", "Business Efficiency"],
  ["Thu", "Invoice chasing", "Revenue Recovery"],
  ["Fri", "Management reporting", "Visibility"]
];

export default function DashboardPage() {
  return (
    <main className="shell">
      <aside className="sidebar">
        <div className="brand">VISIBILITY <span>ENGINE</span></div>
        <nav>
          <a className="active">Dashboard</a>
          <a>Content Calendar</a>
          <a>Opportunities</a>
          <a>Analytics</a>
          <a>AI Visibility</a>
          <a>Backlinks</a>
          <a>Leads & Revenue</a>
          <a>Site Connections</a>
          <a>Settings</a>
        </nav>
        <div className="managed">Managed by Softkom</div>
      </aside>

      <section className="content">
        <header>
          <div>
            <p className="eyebrow">CLIENT WORKSPACE</p>
            <h1>Visibility Dashboard</h1>
            <p>See what is being researched, written, published and converted into business.</p>
          </div>
          <button>+ Add Search Theme</button>
        </header>

        <section className="kpis">
          {kpis.map(([value,label]) => (
            <article key={label}><strong>{value}</strong><span>{label}</span></article>
          ))}
        </section>

        <section className="panel">
          <div className="panelHead">
            <div><p className="eyebrow">CONTENT CALENDAR</p><h2>This month</h2></div>
            <button className="secondary">Review planned content</button>
          </div>
          <div className="calendar">
            {calendar.map(([day,title,cluster]) => (
              <article key={title}>
                <span>{day}</span>
                <h3>{title}</h3>
                <p>{cluster}</p>
                <b>Planned</b>
              </article>
            ))}
          </div>
        </section>

        <section className="twoCol">
          <article className="panel">
            <p className="eyebrow">WHAT SOFTKOM IS WORKING ON</p>
            <h2>Opportunity queue</h2>
            <ul className="queue">
              <li><span>Invoice chasing automation</span><b>Commercial</b></li>
              <li><span>Reduce duplicate data entry</span><b>Commercial</b></li>
              <li><span>AI policy for SMEs</span><b>Decision support</b></li>
              <li><span>Key-person dependency</span><b>Problem awareness</b></li>
            </ul>
          </article>

          <article className="panel">
            <p className="eyebrow">FUNNEL PERFORMANCE</p>
            <h2>From visibility to revenue</h2>
            <div className="funnel">
              <div><strong>4,820</strong><span>Search impressions</span></div>
              <div><strong>312</strong><span>Visits</span></div>
              <div><strong>18</strong><span>Qualified actions</span></div>
              <div><strong>4</strong><span>Sales opportunities</span></div>
            </div>
          </article>
        </section>

        <section className="panel">
          <p className="eyebrow">CONTENT STRATEGY</p>
          <h2>How your topics connect</h2>
          <div className="clusters">
            <div><h3>Business Efficiency</h3><p>Manual admin · software waste · reporting</p></div>
            <div><h3>Revenue Recovery</h3><p>Lead recovery · quotes · invoice chasing</p></div>
            <div><h3>Systems & Automation</h3><p>Integrations · workflows · custom systems</p></div>
            <div><h3>Practical AI</h3><p>AI usage · staff tools · governance</p></div>
          </div>
        </section>
      </section>
    </main>
  );
}
