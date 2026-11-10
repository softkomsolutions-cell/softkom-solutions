import type { Transport } from "../../lib/connections/http";
export function wordpressFixture() {
  const pages = new Map<
    number,
    {
      id: number;
      slug: string;
      link: string;
      status: string;
      title: string;
      content: { raw: string };
    }
  >();
  let creates = 0;
  let failVerification = false;
  let loseResponse = false;
  const transport: Transport = async (value, options = {}) => {
    const url = new URL(value);
    let body: unknown = {};
    if (url.pathname === "/wp-json/") body = { namespaces: ["wp/v2"] };
    else if (url.pathname === "/wp-json/wp/v2/users/me")
      body = {
        id: 1,
        name: "Fixture editor",
        capabilities: {
          edit_pages: true,
          publish_pages: true,
          edit_published_pages: true,
        },
      };
    else if (
      url.pathname === "/wp-json/wp/v2/pages" &&
      options.method !== "POST"
    )
      body = [...pages.values()].filter(
        (p) => p.slug === url.searchParams.get("slug"),
      );
    else if (
      url.pathname.startsWith("/wp-json/wp/v2/pages") &&
      options.method === "POST"
    ) {
      const input = JSON.parse(options.body!);
      let id = Number(url.pathname.split("/").at(-1));
      if (!id) {
        id = ++creates;
      }
      body = {
        id,
        title: input.title,
        slug: input.slug,
        status: "publish",
        link: `https://wordpress.example/${input.slug}/`,
        content: { raw: input.content },
      };
      pages.set(id, body as never);
      if (loseResponse) {
        loseResponse = false;
        throw new Error("socket interrupted with sensitive response");
      }
    } else if (url.pathname.startsWith("/wp-json/wp/v2/pages/"))
      body = pages.get(Number(url.pathname.split("/").at(-1)));
    else {
      if (failVerification)
        return { status: 503, body: "Unavailable", headers: {}, url: value };
      body =
        [...pages.values()].find((p) => url.pathname.includes(p.slug))?.content
          .raw || "";
      return { status: 200, body: String(body), headers: {}, url: value };
    }
    return { status: 200, body: JSON.stringify(body), headers: {}, url: value };
  };
  return {
    transport,
    pages,
    get creates() {
      return creates;
    },
    set failVerification(v: boolean) {
      failVerification = v;
    },
    set loseResponse(v: boolean) {
      loseResponse = v;
    },
  };
}
