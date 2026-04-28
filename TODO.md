# Dark Mode & Settings Implementation Plan

## Steps

- [x] 1. Add comprehensive dark mode CSS to `css/portal.min.css`
- [x] 2. Refactor `admin/settings.php` with polished UI and fixed theme script
- [x] 3. Update `admin/head.php` with anti-FOUC theme script
- [x] 4. Update `admin/header_sidebar.php` — cleaned conflicting inline styles; settings gear button is self-contained in `admin/settings.php`
- [x] 5. Create `settings.php` in root for students
- [x] 6. Update `head.php` (student) with anti-FOUC theme script
- [x] 7. Update `header.php` (student) with settings gear button (`studentSettingsBtn`)
- [x] 8. Clean conflicting inline dark mode from `admin/index.php`, `admin/manage_results.php`, `admin/students.php`, `admin/header_sidebar.php`
- [x] 9. Verify zero remaining inline `body[data-theme='dark']` styles across all PHP files

