type Dated = { date: Date };

const previousMonth = (month: string) => {
  const [year, value] = month.split("-").map(Number);
  const date = new Date(Date.UTC(year, value - 2, 1));
  return date.toISOString().slice(0, 7);
};

const isoDay = (date: Date) => date.toISOString().slice(0, 10);

export function comparablePeriods<T extends Dated>(rows: T[], month: string) {
  const current = rows.filter((row) => isoDay(row.date).slice(0, 7) === month);
  const priorMonth = previousMonth(month);

  if (!current.length) {
    return { current: [] as T[], previous: [] as T[], currentCoverage: null, previousCoverage: null };
  }

  const latest = new Date(Math.max(...current.map((row) => row.date.getTime())));
  const latestDay = Number(isoDay(latest).slice(8, 10));

  const previous = rows.filter((row) => {
    const day = isoDay(row.date);
    return day.slice(0, 7) === priorMonth && Number(day.slice(8, 10)) <= latestDay;
  });

  const bounds = (items: T[]) =>
    items.length
      ? {
          from: new Date(Math.min(...items.map((row) => row.date.getTime()))),
          to: new Date(Math.max(...items.map((row) => row.date.getTime()))),
        }
      : null;

  return {
    current,
    previous,
    currentCoverage: bounds(current),
    previousCoverage: bounds(previous),
  };
}
