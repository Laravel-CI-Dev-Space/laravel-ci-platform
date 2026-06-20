import { expect, test } from '@playwright/test';

test.describe('Company portal flows', () => {
    test('company account can log in via the login form', async ({ page }) => {
        await page.goto('/company/login');
        await page.locator('input[name="email"]').fill('e2e-company@laravelci.com');
        await page.locator('input[name="password"]').fill('password');
        await page.getByRole('button', { name: /Se connecter|Connexion|Login/i }).click();

        await expect(page).toHaveURL(/\/company\/portal/);
    });

    test.describe('authenticated company', () => {
        test.beforeEach(async ({ page }) => {
            await page.goto('/_e2e/login-company/e2e-company@laravelci.com');
        });

        test('company portal dashboard loads', async ({ page }) => {
            await page.goto('/company/portal');
            await expect(page).toHaveURL(/\/company\/portal/);
            await expect(page.locator('body')).not.toContainText('Forbidden');
        });

        test('company job offers list shows seeded offer', async ({ page }) => {
            await page.goto('/company/portal/company-job-offers');
            await expect(page.getByText('Développeur Laravel — Offre E2E')).toBeVisible();
        });

        test('company can navigate to create a new job offer', async ({ page }) => {
            await page.goto('/company/portal/company-job-offers/create');
            await expect(page).toHaveURL(/company-job-offers\/create/);
            await expect(page.locator('body')).not.toContainText('Forbidden');
        });

        test('company applications list loads', async ({ page }) => {
            await page.goto('/company/portal/company-applications');
            await expect(page).toHaveURL(/company-applications/);
            await expect(page.locator('body')).not.toContainText('Forbidden');
        });
    });
});
