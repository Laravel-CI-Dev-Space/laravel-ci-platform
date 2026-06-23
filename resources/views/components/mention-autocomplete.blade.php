<style>
.mention-dropdown {
    position: absolute;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,.12);
    z-index: 9999;
    min-width: 220px;
    max-height: 240px;
    overflow-y: auto;
    padding: 4px 0;
}
.mention-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    cursor: pointer;
    font-size: .85rem;
    transition: background .1s;
}
.mention-item:hover,
.mention-item.mention-item--active {
    background: #f5f6fa;
}
.mention-item__avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    background: #e8580a;
}
.mention-item__avatar--placeholder {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    font-weight: 700;
    color: #fff;
}
.mention-item__username { color: #e8580a; font-weight: 600; }
.mention-item__name { color: #6b7280; font-size: .78rem; margin-left: 2px; }
</style>

<script>
(function () {
    let dropdown = null;
    let activeIndex = -1;
    let currentTextarea = null;
    let triggerPos = -1;
    let debounceTimer = null;
    let results = [];

    function createDropdown() {
        const el = document.createElement('div');
        el.className = 'mention-dropdown';
        el.id = 'mention-dropdown';
        document.body.appendChild(el);
        return el;
    }

    function removeDropdown() {
        if (dropdown) { dropdown.remove(); dropdown = null; }
        activeIndex = -1;
        results = [];
    }

    function getCaretCoords(textarea, pos) {
        const mirror = document.createElement('div');
        const style = window.getComputedStyle(textarea);
        ['boxSizing','width','height','overflowX','overflowY','borderTopWidth','borderRightWidth',
         'borderBottomWidth','borderLeftWidth','paddingTop','paddingRight','paddingBottom','paddingLeft',
         'fontStyle','fontVariant','fontWeight','fontStretch','fontSize','fontSizeAdjust','lineHeight',
         'fontFamily','textAlign','textTransform','textIndent','letterSpacing','wordSpacing',
         'tabSize','MozTabSize'].forEach(p => mirror.style[p] = style[p]);
        mirror.style.position = 'absolute';
        mirror.style.visibility = 'hidden';
        mirror.style.whiteSpace = 'pre-wrap';
        mirror.style.wordWrap = 'break-word';
        mirror.style.top = '0'; mirror.style.left = '0';
        document.body.appendChild(mirror);

        const text = textarea.value.substring(0, pos);
        mirror.textContent = text;
        const span = document.createElement('span');
        span.textContent = textarea.value.substring(pos) || '.';
        mirror.appendChild(span);

        const rect = textarea.getBoundingClientRect();
        const spanRect = span.getBoundingClientRect();
        document.body.removeChild(mirror);

        return {
            top:  rect.top  + window.scrollY + spanRect.top  - mirror.getBoundingClientRect().top  + parseInt(style.paddingTop),
            left: rect.left + window.scrollX + spanRect.left - mirror.getBoundingClientRect().left + parseInt(style.paddingLeft),
        };
    }

    function showDropdown(textarea, members) {
        removeDropdown();
        if (!members.length) return;

        results = members;
        activeIndex = -1;
        dropdown = createDropdown();

        members.forEach((m, i) => {
            const item = document.createElement('div');
            item.className = 'mention-item';
            item.dataset.index = i;

            let avatarHtml;
            if (m.avatar) {
                avatarHtml = `<img src="${m.avatar}" class="mention-item__avatar" alt="">`;
            } else {
                const initials = (m.name || m.username || '?').substring(0, 2).toUpperCase();
                avatarHtml = `<span class="mention-item__avatar mention-item__avatar--placeholder">${initials}</span>`;
            }

            item.innerHTML = `${avatarHtml}<span class="mention-item__username">#${m.username}</span><span class="mention-item__name">${m.name || ''}</span>`;
            item.addEventListener('mousedown', (e) => { e.preventDefault(); insertMention(textarea, m.username); });
            dropdown.appendChild(item);
        });

        positionDropdown(textarea);
    }

    function positionDropdown(textarea) {
        if (!dropdown) return;
        const coords = getCaretCoords(textarea, triggerPos);
        const lineH = parseInt(window.getComputedStyle(textarea).lineHeight) || 20;
        dropdown.style.top  = (coords.top + lineH + 4) + 'px';
        dropdown.style.left = coords.left + 'px';
    }

    function insertMention(textarea, username) {
        const val = textarea.value;
        const before = val.substring(0, triggerPos);
        const after  = val.substring(textarea.selectionStart);
        textarea.value = before + '#' + username + ' ' + after;

        const newPos = triggerPos + username.length + 2;
        textarea.selectionStart = textarea.selectionEnd = newPos;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        textarea.focus();
        removeDropdown();
        currentTextarea = null;
        triggerPos = -1;
    }

    function setActiveItem(index) {
        if (!dropdown) return;
        const items = dropdown.querySelectorAll('.mention-item');
        items.forEach(el => el.classList.remove('mention-item--active'));
        activeIndex = Math.max(0, Math.min(index, items.length - 1));
        if (items[activeIndex]) items[activeIndex].classList.add('mention-item--active');
    }

    async function fetchMembers(query) {
        try {
            const res = await fetch(`/api/members/search?q=${encodeURIComponent(query)}`);
            if (!res.ok) return [];
            return await res.json();
        } catch { return []; }
    }

    function onInput(e) {
        const textarea = e.target;
        const pos = textarea.selectionStart;
        const val = textarea.value;

        // find the nearest # before cursor (same line only)
        let found = -1;
        for (let i = pos - 1; i >= 0; i--) {
            const c = val[i];
            if (c === '#') { found = i; break; }
            if (c === ' ' || c === '\n') break;
        }

        if (found === -1) { removeDropdown(); return; }

        const query = val.substring(found + 1, pos);
        if (query.length < 1) { removeDropdown(); return; }

        triggerPos = found;
        currentTextarea = textarea;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
            const members = await fetchMembers(query);
            if (currentTextarea === textarea && triggerPos === found) {
                showDropdown(textarea, members);
            }
        }, 200);
    }

    function onKeydown(e) {
        if (!dropdown) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); setActiveItem(activeIndex + 1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); setActiveItem(activeIndex - 1); }
        else if (e.key === 'Enter' || e.key === 'Tab') {
            if (activeIndex >= 0 && results[activeIndex]) {
                e.preventDefault();
                insertMention(currentTextarea, results[activeIndex].username);
            }
        }
        else if (e.key === 'Escape') { removeDropdown(); }
    }

    function onBlur() {
        setTimeout(removeDropdown, 150);
    }

    function bindTextarea(textarea) {
        if (textarea.dataset.mentionBound) return;
        textarea.dataset.mentionBound = '1';
        textarea.addEventListener('input', onInput);
        textarea.addEventListener('keydown', onKeydown);
        textarea.addEventListener('blur', onBlur);
    }

    function init() {
        document.querySelectorAll('textarea[data-mention]').forEach(bindTextarea);

        // Observe DOM for Livewire-injected textareas
        new MutationObserver(() => {
            document.querySelectorAll('textarea[data-mention]').forEach(bindTextarea);
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Rebind after Livewire navigations
    document.addEventListener('livewire:navigated', init);
    document.addEventListener('livewire:update', init);
})();
</script>
