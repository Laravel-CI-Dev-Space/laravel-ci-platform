<x-filament-panels::page>
    <x-filament::section heading="Comment fonctionne le système de réputation ?" icon="heroicon-o-information-circle">
        <div class="prose prose-sm max-w-none dark:prose-invert">
            <h4>1. Calcul des points</h4>
            <p>
                Chaque membre accumule des points en fonction de son activité sur la plateforme :
                questions publiées, réponses postées (avec un bonus si la réponse est acceptée),
                articles publiés, votes reçus sur ses contenus, commentaires laissés, participations
                aux événements, ainsi qu'un bonus mensuel lié à son ancienneté (plafonné).
                Les valeurs ci-dessous définissent le nombre de points attribués pour chaque action.
            </p>

            <h4>2. Détermination du grade</h4>
            <p>
                Une fois le total de points calculé, un grade est attribué au membre en fonction de
                seuils de points (du grade le plus bas au plus élevé). Le super-administrateur reçoit
                toujours le grade le plus élevé. La liste des grades et leurs seuils se gèrent depuis
                la page <a href="{{ \App\Filament\Resources\Grades\GradeResource::getUrl('index') }}">Grades</a>.
            </p>

            <h4>3. Persistance</h4>
            <p>
                Les points et le grade calculés sont enregistrés sur le profil de chaque membre, et
                affichés dans la liste des <a href="{{ \App\Filament\Resources\Users\UserResource::getUrl('index') }}">Membres</a>.
            </p>

            <h4>4. Quand ça se déclenche</h4>
            <p>
                Le recalcul peut être lancé manuellement via le bouton « Recalculer maintenant »
                ci-dessus, ou automatiquement selon la fréquence configurée dans la section
                « Recalcul automatique » ci-dessous.
            </p>
        </div>
    </x-filament::section>

    <x-filament::section heading="Configuration" description="Modifiez les valeurs ci-dessous puis cliquez sur Enregistrer.">
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
