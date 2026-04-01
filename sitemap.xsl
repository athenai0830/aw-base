<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sm="http://www.sitemaps.org/schemas/sitemap/0.9">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html lang="ja">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Sitemap</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f9f9f9; color: #333; padding: 2rem; }
    h1 { font-size: 1.4rem; margin-bottom: 1.5rem; color: #111; }
    h2 { font-size: 1rem; margin: 2rem 0 0.75rem; color: #555; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    thead tr { background: #222; color: #fff; }
    th, td { padding: 10px 16px; text-align: left; font-size: 0.875rem; }
    th { font-weight: 600; }
    tbody tr:nth-child(even) { background: #fafafa; }
    tbody tr:hover { background: #f0f4ff; }
    td a { color: #1a6ec8; text-decoration: none; word-break: break-all; }
    td a:hover { text-decoration: underline; }
    td.meta { color: #777; white-space: nowrap; }
    .count { font-size: 0.8rem; color: #888; margin-left: 0.5rem; }
  </style>
</head>
<body>
  <h1>XML Sitemap</h1>

  <!-- sitemapindex -->
  <xsl:if test="sm:sitemapindex">
    <h2>サイトマップ一覧 <span class="count">(<xsl:value-of select="count(sm:sitemapindex/sm:sitemap)"/> files)</span></h2>
    <table>
      <thead><tr><th>URL</th></tr></thead>
      <tbody>
        <xsl:for-each select="sm:sitemapindex/sm:sitemap">
          <tr>
            <td><a href="{sm:loc}"><xsl:value-of select="sm:loc"/></a></td>
          </tr>
        </xsl:for-each>
      </tbody>
    </table>
  </xsl:if>

  <!-- urlset -->
  <xsl:if test="sm:urlset">
    <h2>URL一覧 <span class="count">(<xsl:value-of select="count(sm:urlset/sm:url)"/> URLs)</span></h2>
    <table>
      <thead>
        <tr>
          <th>URL</th>
          <th>最終更新</th>
          <th>更新頻度</th>
        </tr>
      </thead>
      <tbody>
        <xsl:for-each select="sm:urlset/sm:url">
          <tr>
            <td><a href="{sm:loc}"><xsl:value-of select="sm:loc"/></a></td>
            <td class="meta"><xsl:value-of select="sm:lastmod"/></td>
            <td class="meta"><xsl:value-of select="sm:changefreq"/></td>
          </tr>
        </xsl:for-each>
      </tbody>
    </table>
  </xsl:if>

</body>
</html>
</xsl:template>

</xsl:stylesheet>
