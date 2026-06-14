@if (session('viewing_as_member', false))
    <div class="viewing-as-banner">
        <span>
            <i class="fa-solid fa-user-secret"></i>
            Vous naviguez en tant que <strong>Membre</strong> — toutes vos actions sont réelles.
        </span>
        <form method="POST" action="{{ route('view-as-member.disable') }}">
            @csrf
            <button type="submit" class="btn-back-admin">
                <i class="fa-solid fa-arrow-rotate-left"></i> Retour au mode Administrateur
            </button>
        </form>
    </div>
@endif
