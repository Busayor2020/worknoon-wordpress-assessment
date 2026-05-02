# SEO Diagnosis: New Worknoon Website Not Indexing After Sitemap Submission

## Scenario

> A new Worknoon website has been launched and the XML sitemap has been submitted to
> Google Search Console. However, the site is still not appearing in Google Search results
> after a reasonable waiting period (2–4 weeks).

This document provides a systematic, step-by-step diagnostic framework to identify and
resolve all possible causes of indexing failure.

---

## Diagnostic Framework Overview

```
Not Indexing?
│
├── 1. Crawlability Tests          — Can Google even reach the site?
├── 2. Canonical Checks            — Is the correct URL being declared?
├── 3. Robots.txt & Noindex Audit  — Is the site actively blocking Google?
├── 4. Sitemap Structure Issues    — Is the sitemap valid and reachable?
├── 5. Page Speed Indexing Blockers — Is the site too slow to crawl?
└── 6. Search Console Debugging    — What is Google directly reporting?
```

---

## 1. Crawlability Tests

Before Google can index a page, it must successfully crawl it. Crawl failures are one of the
most common and overlooked causes of indexing problems.

### Tests to Run

**a. Google URL Inspection Tool**

- Open Google Search Console → select the property.
- Paste the homepage URL into the top search bar and press Enter.
- Click **"Request Indexing"** then check the **"Coverage"** tab.
- Look for: _"URL is not on Google"_ — this confirms the crawl issue directly.

**b. Manual Fetch Test (Live URL Test)**

- In URL Inspection, click **"Test Live URL"**.
- This simulates what Googlebot actually sees.
- Check: Does the rendered page look correct? Is content missing? Are JavaScript resources
  loading properly?

**c. Check Server Response Codes**

- Use a tool like `httpstatus.io` or `Screaming Frog` to check that all key pages return
  `HTTP 200 OK`.
- Pages returning `301`, `302`, `404`, `410`, or `5xx` will not be indexed.
- Common issue on new WordPress sites: the homepage redirecting to `http://` (non-HTTPS)
  instead of `https://` — Google de-prioritizes non-HTTPS URLs.

**d. DNS and Server Accessibility**

- Confirm the domain is publicly accessible (not password-protected or behind a maintenance
  mode plugin).
- Check: Is **"Coming Soon" or "Maintenance Mode"** still active? Plugins like SeedProd,
  WP Maintenance Mode, or even themes with a built-in coming-soon feature will return a
  `503` status to Googlebot.
- Verify with: `curl -I https://worknoon.com` in terminal — check the response headers.

**e. Googlebot IP Block Check**

- Check if the hosting server's firewall or Cloudflare/security plugin is blocking
  Googlebot's IP range.
- In cPanel/server settings or Cloudflare, ensure Googlebot's known IP ranges are not
  blocked.
- LiteSpeed Cache (installed on Worknoon) has a crawler detection feature — ensure it is
  not set to block bots.

---

## 2. Canonical Checks

Canonical tags tell Google which version of a URL is the "master" version for indexing.
Incorrect canonicals are a frequent cause of pages not being indexed.

### Tests to Run

**a. Check Self-Referencing Canonicals**

- Every indexable page should have a canonical tag pointing to itself:
  ```html
  <link rel="canonical" href="https://worknoon.com/page-name/" />
  ```
- A page with no canonical, or a canonical pointing to a different URL, may not be indexed
  in its own right.

**b. Canonical Conflicts Between HTTP and HTTPS**

- Confirm all canonicals use `https://` not `http://`.
- Mixed canonicals (some pages on `http://`, some on `https://`) create duplicate content
  and indexing confusion.

**c. Canonical Conflicts with WWW vs Non-WWW**

- Decide on one canonical domain: `https://worknoon.com` OR `https://www.worknoon.com`.
- Ensure all canonical tags, sitemap URLs, and the Search Console property all use the
  same version consistently.
- The non-chosen version should 301-redirect to the canonical version.

**d. RankMath Canonical Settings**

- Since RankMath SEO is installed on this site, go to:
  **RankMath → Titles & Meta → Global Meta** and verify the canonical URL base is correct.
- Check individual pages via **RankMath → Snippet Preview** to confirm each page's
  canonical is correctly generated.

**e. Pagination Canonicals**

- If the site has paginated content (e.g., workspace listing archives), ensure paginated
  pages do NOT all point their canonical to Page 1 — this suppresses the indexing of
  all paginated pages.

---

## 3. Robots.txt & Noindex Audit

This is the **most common cause** of post-launch indexing failure — the site is actively
telling Google not to crawl or index it.

### Tests to Run

**a. Check robots.txt**

- Visit: `https://worknoon.com/robots.txt`
- Verify it does NOT contain:
  ```
  User-agent: *
  Disallow: /
  ```
  This single entry blocks ALL crawlers from the entire site.
- A correct, permissive robots.txt for a new site should look like:
  ```
  User-agent: *
  Disallow:
  Sitemap: https://worknoon.com/sitemap_index.xml
  ```

**b. WordPress "Discourage Search Engines" Setting**

- This is the #1 most common cause of indexing failure on new WordPress sites.
- Go to: **WordPress Admin → Settings → Reading**
- Ensure **"Discourage search engines from indexing this site"** is **UNCHECKED**.
- When checked, WordPress adds `X-Robots-Tag: noindex` to every page response header
  AND adds `Disallow: /` to robots.txt automatically — completely blocking Google.

**c. Per-Page Noindex Tags**

- Use Screaming Frog or Ahrefs Site Audit to scan for pages with:
  ```html
  <meta name="robots" content="noindex" />
  ```
- In RankMath, check individual pages: **Edit Page → RankMath → Advanced → Robots Meta**
  — ensure "No Index" is NOT selected.

**d. Check HTTP Response Headers**

- Use `curl -I https://worknoon.com` to inspect response headers.
- Look for: `X-Robots-Tag: noindex` — this would block indexing even without a meta tag.
- LiteSpeed Cache can sometimes inject header-level noindex rules; verify its settings.

**e. Noindex on Taxonomy / Category Pages**

- In RankMath → Titles & Meta, check that category, tag, and archive pages are not
  set to noindex globally (unless intentionally).

---

## 4. Sitemap Structure Issues

An invalid, inaccessible, or improperly structured sitemap can cause Googlebot to miss
pages entirely.

### Tests to Run

**a. Sitemap Accessibility**

- Visit: `https://worknoon.com/sitemap_index.xml`
- If a 404 error appears, the sitemap is not generated. Enable it in:
  **RankMath → Sitemap Settings → Enable Sitemap**

**b. Sitemap Validation**

- Paste the sitemap URL into **XML Sitemap Validator** (`xmlsitemapvalidator.com`) and
  check for errors:
  - Malformed XML syntax
  - URLs that return non-200 status codes
  - Missing `<lastmod>` or `<loc>` tags
  - URLs in the sitemap that don't match canonical tags

**c. Sitemap URL Format Consistency**

- Every URL in the sitemap must use the canonical domain format:
  `https://worknoon.com/` (not `http://`, not `www.`, not a staging subdomain).
- Mismatched URLs in the sitemap vs. canonical tags confuse Google.

**d. Sitemap Submitted vs. Discovered**

- In Google Search Console → **Sitemaps**, verify the sitemap shows:
  - Status: **"Success"**
  - Discovered URLs count > 0
  - No errors listed
- If the status shows "Couldn't fetch" — the sitemap URL is wrong, the server is blocking
  Googlebot, or the sitemap file is malformed.

**e. Pages Missing from Sitemap**

- Ensure all important pages (homepage, about, services, contact, workspace listings) are
  included in the sitemap.
- In RankMath, check: **Sitemap Settings → Post Types** — ensure "Pages" and relevant
  Custom Post Types are toggled ON.

**f. Staging/Dev URLs in Sitemap**

- If the site was migrated from a staging URL (e.g., `worknoon.laluxuryhair.com`), confirm
  the sitemap does NOT still contain the old staging domain URLs.
- In WordPress: **Settings → General** → verify Site URL and WordPress URL are both set
  to `https://worknoon.com`.

---

## 5. Page Speed Indexing Blockers

Google uses page experience signals — including Core Web Vitals — as crawl priority factors.
An extremely slow site may be crawled infrequently or deprioritized for indexing.

### Tests to Run

**a. Google PageSpeed Insights**

- Run `https://worknoon.com` through **PageSpeed Insights** (`pagespeed.web.dev`).
- Aim for:
  - Mobile Performance Score: 70+
  - LCP (Largest Contentful Paint): under 2.5 seconds
  - CLS (Cumulative Layout Shift): under 0.1
  - FID/INP: under 200ms

**b. Core Web Vitals in Search Console**

- Check: **Search Console → Core Web Vitals** report.
- URLs flagged as "Poor" or "Needs Improvement" may be deprioritized in crawl budget.

**c. LiteSpeed Cache Configuration**

- Since LiteSpeed Cache is installed, verify:
  - **Page Cache** is enabled
  - **Browser Cache** is enabled
  - **Image Optimization/WebP** is enabled
  - **CSS/JS Minification** is enabled
  - **Lazy Load** for images is enabled
- Misconfigured LiteSpeed settings (e.g., cache exclusions for all pages) can make the
  site slower than with no cache at all.

**d. Render-Blocking Resources**

- Check for render-blocking JavaScript or CSS that delays page rendering.
- In LiteSpeed Cache → Page Optimization → CSS/JS, enable "Load CSS Asynchronously"
  and "Defer JS" with caution (test for visual breakage after enabling).

**e. Unoptimized Images**

- Large, uncompressed images are a common speed killer.
- Use LiteSpeed's image optimization or Smush to compress all uploaded images.
- Serve images in next-gen formats (WebP) wherever possible.

**f. Hosting Performance**

- If the site is on shared hosting, server response time (TTFB) may be high.
- Check TTFB using: `https://tools.pingdom.com/` — TTFB should be under 600ms.

---

## 6. Search Console Debugging Steps

Google Search Console is the primary diagnostic tool for indexing issues. Use it
systematically.

### Step-by-Step Search Console Debugging

**Step 1 — Verify Property Ownership**

- Ensure the correct property (`https://worknoon.com`) is verified in Search Console.
- Recommended verification: **HTML tag method** (place meta tag in WordPress `<head>`)
  or **Google Analytics method** (since GA4 is already installed).
- If the property shows as unverified, none of the data or requests will be processed.

**Step 2 — Check Coverage Report**

- Go to: **Search Console → Pages (formerly Coverage)**
- Review each category:
  - **Error** — pages Google tried to index but failed (investigate each error type)
  - **Valid with warning** — indexed but has issues
  - **Valid** — successfully indexed (goal state)
  - **Excluded** — pages Google chose not to index (investigate if important pages appear here)
- Common exclusion reasons to look for:
  - _"Crawled – currently not indexed"_ — page was crawled but Google chose not to index it (usually thin content or duplicate content issue)
  - _"Discovered – currently not indexed"_ — page is in sitemap but hasn't been crawled yet (crawl budget issue, or site is too new)
  - _"Excluded by 'noindex' tag"_ — confirms a noindex tag is present
  - _"Blocked by robots.txt"_ — confirms robots.txt is blocking the URL

**Step 3 — URL Inspection on Key Pages**

- Inspect the homepage, About page, and one workspace listing page individually.
- For each, check:
  - Last crawl date (if never crawled → crawlability issue)
  - Crawl was allowed (robots.txt check)
  - Indexing was allowed (noindex check)
  - Canonical URL matches expected canonical
  - Page resources loaded correctly (screenshot shows full rendered page)

**Step 4 — Resubmit Sitemap**

- Go to: **Search Console → Sitemaps**
- Delete the old sitemap entry and resubmit `https://worknoon.com/sitemap_index.xml`.
- This forces Google to re-fetch and reprocess the sitemap.

**Step 5 — Request Indexing for Priority Pages**

- Use **URL Inspection → Request Indexing** for:
  - Homepage
  - About page
  - Key workspace listing pages
- Note: Google processes these requests within hours to days, not instantly.

**Step 6 — Monitor Crawl Stats**

- Go to: **Search Console → Settings → Crawl Stats**
- Check:
  - Total crawl requests per day (should increase over time as site grows)
  - Response codes (high rate of 404s or 5xx responses will suppress indexing)
  - Crawl purpose breakdown

**Step 7 — Check Manual Actions**

- Go to: **Search Console → Security & Manual Actions → Manual Actions**
- If Google has issued a manual penalty (spam, thin content, unnatural links), no pages
  will be indexed until the penalty is resolved and a reconsideration request is submitted.

**Step 8 — Check Security Issues**

- Go to: **Search Console → Security & Manual Actions → Security Issues**
- Malware, hacked content, or phishing warnings will cause Google to de-index or
  suppress the entire site.

---

## Quick-Reference Diagnostic Checklist

| Check                                        | Tool                             | Status |
| -------------------------------------------- | -------------------------------- | ------ |
| WordPress "Discourage search engines" is OFF | WP Admin → Settings → Reading    |        |
| robots.txt does not have `Disallow: /`       | Browser: `/robots.txt`           |        |
| No noindex meta tags on key pages            | RankMath / Screaming Frog        |        |
| All pages return HTTP 200                    | httpstatus.io                    |        |
| Sitemap is accessible and returns 200        | Browser: `/sitemap_index.xml`    |        |
| Sitemap submitted and "Success" in GSC       | Search Console → Sitemaps        |        |
| No canonical conflicts (HTTPS, WWW)          | Screaming Frog / Manual Audit    |        |
| No staging URLs in sitemap or canonicals     | Manual Check                     |        |
| PageSpeed score above 70 (mobile)            | PageSpeed Insights               |        |
| Core Web Vitals passing in GSC               | Search Console → Core Web Vitals |        |
| Property verified in Search Console          | Search Console → Settings        |        |
| No Manual Actions in Search Console          | Search Console → Manual Actions  |        |
| No Security Issues in Search Console         | Search Console → Security Issues |        |
| URL Inspection shows page is indexable       | Search Console → URL Inspection  |        |

---

## Conclusion

Indexing failures are almost always caused by one of three root issues: **the site is
actively blocking Google** (robots.txt, noindex, or WordPress's discourage setting),
**Google cannot reach the site** (server errors, maintenance mode, or firewall blocks),
or **Google has not yet prioritized crawling the site** (new domain, slow speed, or poor
content signals). Work through this checklist systematically — starting with robots.txt
and the Search Console Pages report — and the root cause will be identifiable within
a single diagnostic session.
