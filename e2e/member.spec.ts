import { expect, test } from '@playwright/test';

test.describe('Member dashboard flows', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/_e2e/login/e2e-member@laravelci.com');
    });

    test('member dashboard overview loads', async ({ page }) => {
        await page.goto('/dashboard/member');
        await expect(page.locator('h1')).toContainText('Welcome back');
    });

    test('member can post a new forum question', async ({ page }) => {
        const title = `Question E2E ${Date.now()}`;

        await page.goto('/forum/ask');
        await page.locator('input[wire\\:model\\.live="title"]').fill(title);
        await page.locator('textarea[wire\\:model\\.live="body"]').fill(
            "Voici le contenu détaillé de ma question créée par le test Playwright."
        );
        // Sélectionne le premier tag disponible dans la liste de droite.
        await page.locator('[wire\\:click^="addTag("]').first().click();
        await page.getByRole('button', { name: /Publier la question/ }).click();

        await expect(page).toHaveURL(/\/forum\//);
        await expect(page.getByText(title)).toBeVisible();

        await page.goto('/dashboard/member/questions');
        await expect(page.getByText(title)).toBeVisible();
    });

    test('member can apply to a job offer with a cover letter', async ({ page }) => {
        await page.goto('/jobs');
        await page.getByText('Développeur Laravel — Offre E2E').click();
        await expect(page).toHaveURL(/\/jobs\//);

        await page.locator('textarea[wire\\:model\\.live="coverLetter"]').fill(
            "Je suis très intéressé par cette offre et je pense correspondre au profil recherché."
        );
        await page.getByRole('button', { name: 'Envoyer ma candidature' }).click();

        await expect(page.getByText('Candidature envoyée !')).toBeVisible();
    });

    test('member can register for an upcoming event', async ({ page }) => {
        await page.goto('/events');
        await page.waitForLoadState('networkidle');
        // Event list is Livewire-rendered; wait for at least one event link to appear
        const eventLink = page.locator('a[href*="/events/"]').first();
        await eventLink.waitFor({ timeout: 10_000 });
        await eventLink.click();
        await expect(page).toHaveURL(/\/events\//);

        await page.getByRole('button', { name: "S'inscrire maintenant" }).click();

        await expect(page.getByText('Inscrit ✓')).toBeVisible();
    });
});
