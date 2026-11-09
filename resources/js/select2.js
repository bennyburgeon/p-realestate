/**
 * Application-wide Select2 bootstrapper.
 *
 * Every <select data-searchable-select> on the page is enhanced exactly once with a
 * single shared configuration, driven by data attributes (normally rendered by
 * the <x-select> Blade component):
 *
 *   data-select2-placeholder="…"      placeholder text (single selects use the empty option)
 *   data-select2-allow-clear="false"  disable the clear (×) button
 *   data-select2-search="false"       hide the search box (search is on by default)
 *   data-select2-width="…"            override the computed width
 *   data-select2-size="sm|md|lg"       control height (defaults to sm for text-sm/text-xs selects)
 *   data-select2-accent="primary"     use the navy brand accent instead of emerald
 *   data-select2-ajax-url="…"         load options remotely (expects {results, pagination})
 *   data-select2-depends-on="#id"     parent select; its value is sent as `parent` and a
 *                                     change of parent clears this field
 *   data-select2-depends-param="…"    query-string name for the parent value (default: parent)
 *   data-select2-params='{"k":"v"}'   extra static query parameters for AJAX requests
 *   data-select2-dropdown-parent      (on an ancestor) render the dropdown inside it
 *
 * Select2 emits jQuery-only `change` events, which Livewire's wire:model, Alpine's
 * x-model and inline onchange="" handlers never see. A bridge re-dispatches them as
 * native events, so existing form behaviour keeps working unchanged.
 *
 * Livewire morphing would otherwise strip Select2's attributes from the <select>
 * and realign/remove its sibling container; the hooks registered below preserve
 * both, then re-sync the rendered selection after every update.
 */

// Not `data-select2`: Select2 itself reads $(el).data('select2') as its instance slot.
const SELECTOR = 'select[data-searchable-select]';
const INITIALISED_CLASS = 'select2-hidden-accessible';
const SELECT2_SELECT_ATTRIBUTES = ['class', 'data-select2-id', 'aria-hidden', 'tabindex'];

const $ = () => window.jQuery;

function isInitialised(select) {
    return select.classList.contains(INITIALISED_CLASS) && Boolean($()(select).data('select2'));
}

function readBoolean(select, attribute, fallback) {
    const value = select.getAttribute(attribute);

    if (value === null) {
        return fallback;
    }

    return value !== 'false' && value !== '0';
}

function placeholderFor(select) {
    if (select.dataset.select2Placeholder) {
        return select.dataset.select2Placeholder;
    }

    const emptyOption = Array.from(select.options).find((option) => option.value === '');

    return emptyOption ? emptyOption.textContent.trim() : undefined;
}

function dropdownParentFor(select) {
    const parent = select.closest('[data-select2-dropdown-parent], [role="dialog"], .modal');

    return parent ? $()(parent) : $()(document.body);
}

function widthFor(select) {
    if (select.dataset.select2Width) {
        return select.dataset.select2Width;
    }

    return select.classList.contains('w-full') ? '100%' : 'resolve';
}

function labelFor(select) {
    if (select.id) {
        const label = document.querySelector(`label[for="${CSS.escape(select.id)}"]`);

        if (label) {
            return label;
        }
    }

    return select.closest('label');
}

function parentSelectFor(select) {
    const selector = select.dataset.select2DependsOn;

    return selector ? document.querySelector(selector) : null;
}

function ajaxOptionsFor(select) {
    const url = select.dataset.select2AjaxUrl;

    if (!url) {
        return undefined;
    }

    let staticParams = {};

    try {
        staticParams = JSON.parse(select.dataset.select2Params || '{}');
    } catch {
        staticParams = {};
    }

    return {
        url,
        dataType: 'json',
        delay: 250,
        cache: true,
        data(params) {
            const query = { ...staticParams, q: params.term || '', page: params.page || 1 };
            const parent = parentSelectFor(select);

            if (parent) {
                query[select.dataset.select2DependsParam || 'parent'] = parent.value;
            }

            return query;
        },
        processResults(data) {
            return {
                results: Array.isArray(data) ? data : data.results || [],
                pagination: { more: Boolean(data.pagination && data.pagination.more) },
            };
        },
    };
}

/** Show an optional secondary line (e.g. district, state) under AJAX results. */
function renderResult(item) {
    if (item.loading || !item.meta) {
        return item.text;
    }

    return $()('<span>')
        .append($()('<span>').text(item.text))
        .append($()('<span class="select2-results__option-meta">').text(item.meta));
}

/**
 * Re-render the selection from the underlying <select> without emitting events.
 * (`$(el).trigger('change.select2')` would also run inline onchange="" handlers,
 * e.g. auto-submitting filter forms.) Mirrors Select2's own change.select2 handler.
 */
function rerender(select) {
    const instance = $()(select).data('select2');

    if (!instance) {
        return;
    }

    instance.dataAdapter.current((data) => instance.trigger('selection:update', { data }));
}

function syncInvalidState(select) {
    const instance = $()(select).data('select2');

    if (!instance) {
        return;
    }

    instance.$container.toggleClass('select2-container--invalid', select.getAttribute('aria-invalid') === 'true');
}

function syncDependentState(select) {
    const parent = parentSelectFor(select);

    if (!parent) {
        return;
    }

    // Stay enabled while holding a value (e.g. a locality arriving via a shared URL without its city).
    const hasValue = select.multiple ? select.selectedOptions.length > 0 : select.value !== '';
    const shouldDisable = !parent.value && !hasValue;

    if (select.disabled !== shouldDisable && select.dataset.select2DisableWithoutParent !== 'false') {
        select.disabled = shouldDisable;
        rerender(select);
    }
}

/**
 * Bridge Select2's jQuery-triggered change into a native DOM event.
 * jQuery-triggered events carry `isTrigger`; natively dispatched ones do not,
 * which also prevents the bridge from looping on its own events.
 *
 * jQuery's trigger() also invokes inline on* handlers, so an inline onchange="" is
 * moved onto a listener first; otherwise it would run twice (or on silent re-syncs).
 */
function bridgeNativeEvents(select) {
    if (typeof select.onchange === 'function') {
        const inlineHandler = select.onchange;
        select.onchange = null;
        select.addEventListener('change', inlineHandler);
    }

    $()(select).on('change.select2-bridge', (event) => {
        if (!event.isTrigger) {
            return;
        }

        select.dispatchEvent(new Event('input', { bubbles: true }));
        select.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

function wireDependentField(select) {
    const parent = parentSelectFor(select);

    if (!parent || parent.__select2DependentBound?.has(select)) {
        return;
    }

    parent.__select2DependentBound ??= new WeakSet();
    parent.__select2DependentBound.add(select);

    parent.addEventListener('change', () => {
        if (!select.isConnected) {
            return;
        }

        // AJAX-backed children only hold the currently chosen option; drop it.
        if (select.dataset.select2AjaxUrl) {
            Array.from(select.options)
                .filter((option) => option.value !== '')
                .forEach((option) => option.remove());
        }

        if (select.multiple ? select.selectedOptions.length > 0 : select.value !== '') {
            $()(select).val(select.multiple ? [] : '').trigger('change');
        }

        syncDependentState(select);
    });
}

function wireAccessibility(select) {
    const instance = $()(select).data('select2');
    const label = labelFor(select);

    if (!instance || !label) {
        return;
    }

    const labelText = label.textContent.trim();
    instance.$container.find('.select2-selection').attr('aria-label', labelText);
    instance.$container.find('.select2-search__field').attr('aria-label', labelText);

    if (label.tagName === 'LABEL' && label.htmlFor === select.id && !label.__select2Bound) {
        label.__select2Bound = true;
        label.addEventListener('click', (event) => {
            if (!select.isConnected || select.disabled || !isInitialised(select)) {
                return;
            }

            event.preventDefault();
            $()(select).select2('open');
        });
    }
}

export function initSelect(select) {
    if (!$()?.fn?.select2 || isInitialised(select)) {
        return;
    }

    // A previous instance may have been half-removed by a DOM swap; clear any orphaned container.
    const sibling = select.nextElementSibling;
    if (sibling?.classList.contains('select2-container')) {
        sibling.remove();
    }

    const placeholder = placeholderFor(select);
    const hasPlaceholder = placeholder !== undefined;
    const searchable = readBoolean(select, 'data-select2-search', true);

    $()(select).select2({
        width: widthFor(select),
        placeholder: hasPlaceholder ? { id: '', text: placeholder } : undefined,
        allowClear: hasPlaceholder && !select.required && readBoolean(select, 'data-select2-allow-clear', true),
        minimumResultsForSearch: searchable ? 0 : Infinity,
        dropdownParent: dropdownParentFor(select),
        ajax: ajaxOptionsFor(select),
        dropdownAutoWidth: false,
        templateResult: renderResult,
        language: {
            noResults: () => select.dataset.select2NoResults || 'No matches found',
            searching: () => 'Searching…',
            inputTooShort: () => 'Type to search…',
        },
    });

    const instance = $()(select).data('select2');
    const accent = select.dataset.select2Accent;

    if (accent) {
        instance.$container.addClass(`select2-container--accent-${accent}`);
        instance.$dropdown.addClass(`select2-container--accent-${accent}`);
    }

    const size = select.dataset.select2Size
        || (select.classList.contains('text-sm') || select.classList.contains('text-xs') ? 'sm' : 'md');
    if (size !== 'md') {
        instance.$container.addClass(`select2-container--${size}`);
        instance.$dropdown.addClass(`select2-container--${size}`);
    }

    // With jQuery >= 3.6 Select2 4.0.x no longer focuses the search box on open.
    $()(select).on('select2:open.select2-focus', () => {
        window.setTimeout(() => {
            const field = instance.$dropdown.find('.select2-search__field').get(0)
                || instance.$container.find('.select2-search__field').get(0);
            field?.focus();
        }, 0);
    });

    bridgeNativeEvents(select);
    wireDependentField(select);
    wireAccessibility(select);
    syncInvalidState(select);
    syncDependentState(select);
}

export function destroySelect(select) {
    if (!isInitialised(select)) {
        return;
    }

    try {
        $()(select).off('.select2-bridge .select2-focus').select2('destroy');
    } catch {
        // The element was already detached; nothing left to tear down.
    }
}

export function initSelect2(root = document) {
    if (root instanceof Element && root.matches(SELECTOR)) {
        initSelect(root);
    }

    root.querySelectorAll?.(SELECTOR).forEach(initSelect);
}

/**
 * Re-render every enhanced select under `root` from its underlying <select>,
 * picking up value / option / validation changes made by Livewire or Alpine.
 */
export function refreshSelect2(root = document) {
    root.querySelectorAll?.(SELECTOR).forEach((select) => {
        if (!isInitialised(select)) {
            initSelect(select);

            return;
        }

        rerender(select);
        syncInvalidState(select);
        syncDependentState(select);
    });
}

function registerLivewireHooks(Livewire) {
    // Keep Select2's own attributes on the <select> so morphing doesn't unhook it.
    Livewire.hook('morph.updating', ({ el, toEl }) => {
        if (el.tagName !== 'SELECT' || !el.classList.contains(INITIALISED_CLASS)) {
            return;
        }

        SELECT2_SELECT_ATTRIBUTES.forEach((attribute) => {
            if (attribute === 'class') {
                toEl.classList.add(INITIALISED_CLASS);

                return;
            }

            if (el.hasAttribute(attribute)) {
                toEl.setAttribute(attribute, el.getAttribute(attribute));
            }
        });
    });

    // The rendered container has no server-side counterpart; keep it while its select lives.
    Livewire.hook('morph.removing', ({ el, skip }) => {
        if (!el.classList?.contains('select2-container')) {
            return;
        }

        const owner = el.previousElementSibling;

        if (owner?.matches(`select.${INITIALISED_CLASS}`) || el.classList.contains('select2-container--open')) {
            skip();
        }
    });

    Livewire.hook('morphed', ({ el }) => {
        // Let Alpine/x-model apply bound values before re-rendering the selection.
        window.setTimeout(() => refreshSelect2(el), 0);
    });
}

function observeDynamicContent() {
    let pending = false;

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.removedNodes.forEach((node) => {
                if (node.nodeType !== Node.ELEMENT_NODE || node.isConnected) {
                    return;
                }

                (node.matches(SELECTOR) ? [node] : Array.from(node.querySelectorAll(SELECTOR))).forEach(destroySelect);
            });
        });

        if (pending) {
            return;
        }

        pending = true;
        // A timer rather than requestAnimationFrame: rAF is paused in background tabs.
        window.setTimeout(() => {
            pending = false;
            initSelect2(document);
        }, 0);
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

export function bootSelect2() {
    window.Select2Kit = { init: initSelect2, refresh: refreshSelect2, destroy: destroySelect };

    const start = () => {
        initSelect2(document);
        observeDynamicContent();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }

    if (window.Livewire) {
        registerLivewireHooks(window.Livewire);
    } else {
        document.addEventListener('livewire:init', () => registerLivewireHooks(window.Livewire), { once: true });
    }

    // wire:model / x-model assign initial values after Alpine boots.
    document.addEventListener('livewire:initialized', () => refreshSelect2(document));
    document.addEventListener('alpine:initialized', () => refreshSelect2(document));
}
