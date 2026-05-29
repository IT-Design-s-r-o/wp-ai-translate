(function () {
    if (window.AITMT_ADMIN_READY) {
        return;
    }

    window.AITMT_ADMIN_READY = true;

    function initLanguageSearch() {
        var searches = Array.prototype.slice.call(document.querySelectorAll('.aitmt-language-search, .aitmt-fallback-language-search'));

        searches.forEach(function (search) {
            var scope = search.closest ? search.closest('td, .aitmt-onboarding-step, .aitmt-wide-card, .aitmt-fallback-card, .form-table') : document;
            var grid = search.nextElementSibling && search.nextElementSibling.matches && search.nextElementSibling.matches('.aitmt-fallback-language-grid, .aitmt-language-list') ? search.nextElementSibling : null;
            var options = Array.prototype.slice.call((grid || scope || document).querySelectorAll('.aitmt-language-option, .aitmt-fallback-language-grid label'));

            if (!options.length) {
                options = Array.prototype.slice.call(document.querySelectorAll('.aitmt-language-option, .aitmt-fallback-language-grid label'));
            }

            search.addEventListener('input', function () {
                var needle = search.value.trim().toLowerCase();

                options.forEach(function (option) {
                    var text = option.textContent.toLowerCase();
                    option.classList.toggle('is-hidden', !!needle && text.indexOf(needle) === -1);
                });
            });
        });
    }

    function initMenuSwitcherMetabox() {
        var box = document.querySelector('#aitmt-language-switcher-menu');

        if (!box) {
            return;
        }

        var optionInputs = Array.prototype.slice.call(box.querySelectorAll('[data-aitmt-menu-option], [data-aitmt-menu-display]'));
        var classInputs = Array.prototype.slice.call(box.querySelectorAll('.aitmt-menu-item-classes'));

        function selectedDisplay() {
            var checked = box.querySelector('[data-aitmt-menu-display]:checked');

            return checked && checked.value === 'dropdown' ? 'dropdown' : 'list';
        }

        function optionEnabled(name) {
            var input = box.querySelector('[data-aitmt-menu-option="' + name + '"]');

            return !!(input && input.checked);
        }

        function updateClasses() {
            var suffixes = [];
            var display = selectedDisplay();

            suffixes.push(display === 'dropdown' ? 'aitmt-menu-display-dropdown' : 'aitmt-menu-display-list');

            if (optionEnabled('show-flag')) {
                suffixes.push('aitmt-menu-show-flag');
            }

            if (optionEnabled('show-name')) {
                suffixes.push('aitmt-menu-show-name');
            }

            if (optionEnabled('show-code')) {
                suffixes.push('aitmt-menu-show-code');
            }

            if (optionEnabled('hide-current')) {
                suffixes.push('aitmt-menu-hide-current');
            }

            classInputs.forEach(function (input) {
                var base = input.getAttribute('data-aitmt-base-classes') || '';
                input.value = (base + ' ' + suffixes.join(' ')).trim().replace(/\s+/g, ' ');
            });
        }

        optionInputs.forEach(function (input) {
            input.addEventListener('change', updateClasses);
        });
        updateClasses();
    }

    function initAdminMode() {
        var wrap = document.querySelector('.aitmt-admin-page');
        var radios = Array.prototype.slice.call(document.querySelectorAll('input[name="aitmt_options[admin_mode]"]'));

        if (!wrap || !radios.length) {
            return;
        }

        function applyMode() {
            var checked = radios.filter(function (radio) {
                return radio.checked;
            })[0];
            var mode = checked && checked.value === 'advanced' ? 'advanced' : 'basic';

            wrap.classList.toggle('aitmt-mode-basic', mode === 'basic');
            wrap.classList.toggle('aitmt-mode-advanced', mode === 'advanced');
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', applyMode);
        });
        applyMode();
    }

    function initAdmin() {
        initLanguageSearch();
        initAdminMode();
        initMenuSwitcherMetabox();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdmin);
    } else {
        initAdmin();
    }
})();
