import { expect, test } from '@playwright/test';

test.describe('Public site core flows', () => {
    test('home page loads', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveTitle(/Laravel|Côte d'Ivoire/);
    });

    test('forum index lists questions', async ({ page }) => {
        await page.goto('/forum');
        await expect(page.locator('h1')).toContainText('Forum');
        await expect(page.getByText('Comment fonctionne ce test E2E sur le forum ?')).toBeVisible();
    });

    test('forum question detail page is reachable', async ({ page }) => {
        await page.goto('/forum');
        await page.getByText('Comment fonctionne ce test E2E sur le forum ?').click();
        await expect(page).toHaveURL(/\/forum\//);
        await expect(page.getByText('Comment fonctionne ce test E2E sur le forum ?')).toBeVisible();
    });

    test('blog index lists published articles', async ({ page }) => {
        await page.goto('/blog');
        await expect(page).toHaveTitle(/Blog/);
        await expect(page.getByText('Article de test pour la suite E2E Playwright')).toBeVisible();
    });

    test('jobs index lists active job offers', async ({ page }) => {
        await page.goto('/jobs');
        await expect(page).toHaveTitle(/Job Board/);
        await expect(page.getByText('Développeur Laravel — Offre E2E')).toBeVisible();
    });

    test('events index loads', async ({ page }) => {
        await page.goto('/events');
        await expect(page).toHaveTitle(/Événements/);
    });
});
