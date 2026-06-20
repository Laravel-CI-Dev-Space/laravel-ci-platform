import { expect, test } from '@playwright/test';

test.describe('Filament admin resources', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/_e2e/login/e2e-admin@laravelci.com');
    });

    test('admin dashboard is accessible', async ({ page }) => {
        await page.goto('/admin');
        await expect(page).toHaveURL(/\/admin/);
        await expect(page.locator('body')).not.toContainText('403');
        await expect(page.locator('body')).not.toContainText('Forbidden');
    });

    test('questions resource lists seeded question', async ({ page }) => {
        await page.goto('/admin/questions');
        // Use role=link to target the table row link, not the notification bell span
        await expect(page.getByRole('link', { name: /Comment fonctionne ce test E2E/ })).toBeVisible();
    });

    test('articles resource lists seeded article', async ({ page }) => {
        await page.goto('/admin/articles');
        await expect(page.getByRole('link', { name: /Article de test pour la suite E2E/ })).toBeVisible();
    });

    test('job offers resource lists seeded offer', async ({ page }) => {
        await page.goto('/admin/job-offers');
        await expect(page.getByText('Développeur Laravel — Offre E2E')).toBeVisible();
    });

    test('company accounts resource lists seeded account', async ({ page }) => {
        await page.goto('/admin/companies/company-accounts');
        await expect(page.getByText('e2e-company@laravelci.com')).toBeVisible();
    });
});
