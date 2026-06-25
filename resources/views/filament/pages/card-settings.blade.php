<x-filament-panels::page>
    <x-filament::section heading="Comment fonctionnent les cartes membres ?" icon="heroicon-o-information-circle">
        <div class="prose prose-sm max-w-none dark:prose-invert">
            <h4>Déclenchement automatique</h4>
            <p>
                Chaque fois que les points de réputation d'un membre sont mis à jour, le système vérifie
                si les seuils ci-dessous sont franchis. Si c'est le cas, la carte correspondante est
                créée et activée automatiquement, avec son QR code unique.
            </p>
            <h4>Les 3 niveaux</h4>
            <ul>
                <li><strong>Niveau 1 — Initié</strong> : carte blanche avec bande orange. Premier accomplissement.</li>
                <li><strong>Niveau 2 — Bâtisseur</strong> : carte dark navy. Contributeur régulier.</li>
                <li><strong>Niveau 3 — Maître Artisan</strong> : carte premium gold-orange. Pilier de la communauté.</li>
            </ul>
            <h4>Activation manuelle (admin)</h4>
            <p>
                Depuis la liste "Cartes membres", un admin peut forcer l'activation d'une carte
                pour un membre même s'il n'a pas encore atteint le seuil requis.
            </p>
            <h4>Après modification des seuils</h4>
            <p>
                Utiliser le bouton <strong>"Synchroniser toutes les cartes"</strong> (en haut à droite)
                pour appliquer les nouveaux seuils aux membres existants. Les nouveaux membres sont traités automatiquement.
            </p>
        </div>
    </x-filament::section>

    <x-filament::section heading="Configuration" description="Modifiez les seuils ci-dessous puis cliquez sur Enregistrer.">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit">
                    Enregistrer
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-panels::page>
