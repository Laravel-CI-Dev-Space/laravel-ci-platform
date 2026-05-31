is_pinned (boolean, default false)
Une question épinglée remonte toujours en tête de liste, quel que soit le tri actif. Seuls les admins/modérateurs peuvent épingler. Dans la query listing : orderByDesc('is_pinned') en premier critère.

is_closed (boolean, default false)
Une question fermée n'accepte plus de nouvelles réponses. Le formulaire de réponse est masqué/désactivé côté UI. Utilisé pour : hors-sujet, doublon, question résolue et archivée. Seuls les modérateurs/admins ferment.

views (unsignedInteger, default 0)
Incrémenté à chaque visite de la page détail (via Question::incrementViews()). Sert au tri "populaire". On utilisera increment() pour éviter les race conditions — pas de read-modify-write.

accepted_answer_id (nullable, FK → answers.id)
L'auteur de la question désigne la réponse correcte. Quand il est renseigné, la question est considérée résolue. C'est une dépendance circulaire (questions → answers → questions) — on crée la colonne sans contrainte FK pour l'instant, la contrainte sera ajoutée dans une migration séparée lors de la phase answers.

content (text)
Contenu Markdown de la question. text suffit, pas besoin de longText pour une question. Le rendu HTML se fait côté front, jamais stocké en base.