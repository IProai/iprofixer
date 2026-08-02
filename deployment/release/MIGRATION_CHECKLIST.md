# IPROFIXER — MIGRATION CHECKLIST

Overview of all 12 database migrations included in Production V1.

---

## Migration Sequence & Compatibility

1. **`2026_07_29_170500_create_platform_governance_foundation.php`**
   - Creates: `users`, `password_reset_tokens`, `sessions`, `markets`, `legal_entities`, `tax_profiles`, `system_settings`, `audit_events`, `content_pages`, `content_translations`, `media_assets`, `proof_items`, `form_submissions`, `consent_records`
   - MySQL/MariaDB: Fully compatible (`json()` columns, UUID string primary keys)

2. **`2026_07_30_031500_create_permission_tables.php`**
   - Creates: Spatie permission tables (`roles`, `permissions`, `model_has_permissions`, etc.)
   - MySQL/MariaDB: Fully compatible

3. **`2026_07_31_000100_create_rfq_attachments_table.php`**
   - Creates: `form_submission_attachments`
   - MySQL/MariaDB: Fully compatible

4. **`2026_07_31_021500_extend_rfq_operational_workflow.php`**
   - Updates: `form_submissions` (adds `reference`, SLA columns)
   - MySQL/MariaDB: Fully compatible

5. **`2026_08_01_040500_create_rfq_notes_table.php`**
   - Creates: `form_submission_notes`
   - MySQL/MariaDB: Fully compatible

6. **`2026_08_01_120000_create_content_page_revisions_table.php`**
   - Creates: `content_page_revisions`
   - MySQL/MariaDB: Fully compatible

7. **`2026_08_01_130000_extend_media_assets_table.php`**
   - Updates: `media_assets` (soft deletes, focal points)
   - MySQL/MariaDB: Fully compatible

8. **`2026_08_01_140000_create_navigation_tables.php`**
   - Creates: `navigation_menus`, `navigation_items`
   - MySQL/MariaDB: Fully compatible

9. **`2026_08_02_150000_create_redirect_rules_table.php`**
   - Creates: `redirect_rules`
   - MySQL/MariaDB: Fully compatible

10. **`2026_08_02_160000_extend_content_translations_seo_table.php`**
    - Updates: `content_translations` (SEO metadata, structured data)
    - MySQL/MariaDB: Fully compatible

11. **`2026_08_03_170000_create_commercial_workspace_foundation.php`**
    - Creates: `organization_groups`, `organizations`, `properties`, `contacts`, `leads`, `opportunities`, `crm_activities`, `crm_tasks`
    - MySQL/MariaDB: Fully compatible (`json()` columns)

12. **`2026_08_03_180000_create_database_driver_infrastructure.php`**
    - Creates: `jobs`, `job_batches`, `failed_jobs`, `cache`, `cache_locks`
    - MySQL/MariaDB: Fully compatible

---

## Required Production Seeders
After running `php artisan migrate --force`, execute:
```bash
php artisan db:seed --class=CmsPermissionSeeder --force
php artisan db:seed --class=NavigationSeeder --force
```
Both seeders are idempotent (`firstOrCreate`) and safe to execute multiple times.
