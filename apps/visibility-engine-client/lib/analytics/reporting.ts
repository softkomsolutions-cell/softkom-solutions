export function brandClass(query: string, terms: string) {
  const normalise = (value: string) =>
    value
      .normalize("NFKC")
      .toLocaleLowerCase("en")
      .replace(/[^\p{L}\p{N}]+/gu, " ")
      .trim();
  const phrases = terms.split(/\r?\n/).map(normalise).filter(Boolean);
  if (!phrases.length) return "Unclassified";
  const text = ` ${normalise(query)} `;
  return phrases.some((p) => text.includes(` ${p} `))
    ? "Branded"
    : "Non-branded";
}
export function canonicalPage(value: string) {
  try {
    const url = new URL(value);
    url.hash = "";
    for (const key of [...url.searchParams.keys()])
      if (key.startsWith("utm_") || ["gclid", "fbclid"].includes(key))
        url.searchParams.delete(key);
    url.pathname = url.pathname.replace(/\/+$/, "") || "/";
    return url.href;
  } catch {
    return value;
  }
}
