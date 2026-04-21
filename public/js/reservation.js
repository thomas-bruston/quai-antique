
document.addEventListener('DOMContentLoaded', () => {

    const dateInput      = document.getElementById('date');
    const slotsGrid      = document.getElementById('slots-grid');
    const slotsPlaceholder = document.getElementById('slots-placeholder');
    const heureHidden    = document.getElementById('heure-hidden');
    const btnReserver    = document.getElementById('btn-reserver');

    if (!dateInput) return;

    // --- Chargement des créneaux au changement de date ---
    dateInput.addEventListener('change', () => {
        const date = dateInput.value;
        if (!date) return;

        // Afficher le loader
        slotsPlaceholder.style.display = 'none';
        slotsGrid.style.display        = 'none';
        heureHidden.value              = '';
        btnReserver.disabled           = true;

        fetch(`/reservation/check-availability?date=${encodeURIComponent(date)}`)
            .then(res => {
                if (!res.ok) throw new Error('Erreur réseau');
                return res.json();
            })
            .then(data => {

                if (data.error) {
                    slotsPlaceholder.textContent = data.error;
                    slotsPlaceholder.style.display = 'block';
                    return;
                }

                renderSlots(data.slots);
            })
            .catch(() => {
                slotsPlaceholder.textContent   = 'Impossible de charger les créneaux. Veuillez réessayer.';
                slotsPlaceholder.style.display = 'block';
            });
    });

    // --- Rendu des créneaux ---
    function renderSlots(slots) {
        slotsGrid.innerHTML = '';

        if (!slots || slots.length === 0) {
            slotsPlaceholder.textContent   = 'Aucun créneau disponible pour cette date.';
            slotsPlaceholder.style.display = 'block';
            return;
        }

        slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'slot-btn' + (slot.available ? '' : ' unavailable');
            btn.setAttribute('aria-label', slot.heure + (slot.available ? ' disponible' : ' complet'));
            btn.setAttribute('aria-pressed', 'false');

            btn.innerHTML = `
                <span class="slot-dot ${slot.available ? '' : 'unavailable'}" aria-hidden="true"></span>
                ${slot.heure}
            `;

            if (slot.available) {
                btn.addEventListener('click', () => selectSlot(btn, slot.heure));
            }

            slotsGrid.appendChild(btn);
        });

        slotsGrid.style.display = 'grid';
    }

    // --- Sélection d'un créneau ---
    function selectSlot(btn, heure) {
        // Désactiver tous les boutons
        slotsGrid.querySelectorAll('.slot-btn').forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-pressed', 'false');
        });

        // Activer le bouton sélectionné
        btn.classList.add('active');
        btn.setAttribute('aria-pressed', 'true');

        // Mettre à jour le champ caché
        heureHidden.value    = heure;
        btnReserver.disabled = false;
    }
});
