# IPROFIXER — PRODUCTION SMOKE TEST CHECKLIST

Execute every check immediately after production deployment or rollback.

---

## 1. System Health & Infrastructure
- [ ] `https://www.iprofixer.com/health` returns `200 OK` with `{"status":"ok"}`
- [ ] `https://www.iprofixer.com/ready` returns `200 OK` with `{"status":"ready","database":"available"}`
- [ ] `https://www.iprofixer.com/robots.txt` serves environment-safe directives
- [ ] `https://www.iprofixer.com/sitemap.xml` serves valid XML sitemap

## 2. Public Experience (English & Arabic)
- [ ] Homepage loads in light mode with 200 OK
- [ ] Swapping to Arabic via header toggle redirects and renders `dir="rtl"`
- [ ] Service pages load cleanly (`/services`, `/services/cutlery-restoration`)
- [ ] Industry pages load cleanly (`/industries`, `/industries/hotels-resorts`)
- [ ] Process (`/process`), Results (`/results`), About (`/about`), Resources (`/resources`) load cleanly
- [ ] Contact page (`/contact`) renders assessment form
- [ ] `/portal` returns `404 Not Found` (client portal disabled for V1)

## 3. End-to-End RFQ Submission Journey
- [ ] Submit test RFQ form on `/contact` with valid details and test attachment
- [ ] Submission succeeds and displays green reference code (e.g. `RFQ-20260803-XXXXXX`)
- [ ] Submitting form without required consent is blocked by client/server validation
- [ ] Submitting oversized or invalid attachment is rejected gracefully

## 4. Admin Operator Console
- [ ] Navigate to `/admin/rfqs` and log in with seed admin account
- [ ] New submitted RFQ appears in admin inbox
- [ ] Open RFQ details, add note, view metadata
- [ ] Verify attachment download URL requires authentication
- [ ] Test RFQ-to-Lead conversion in Commercial Workspace (`/admin/leads`)
- [ ] Verify unauthorized user receives 403 / redirect on admin routes

## 5. Security & Isolation
- [ ] Verify `APP_DEBUG=false` by requesting non-existent URL `/invalid-path` (shows generic 404 page, NO stack trace)
- [ ] Verify HTTP automatically redirects to HTTPS
- [ ] Verify `storage/app/rfq/` directory files are NOT directly accessible via public browser URL
