(function () {
    if (window.AITMT_ADMIN_READY) {
        return;
    }

    window.AITMT_ADMIN_READY = true;

    function initLanguageSearch() {
        var searches = Array.prototype.slice.call(document.querySelectorAll('.AITMT-language-search, .AITMT-fallback-language-search'));

        searches.forEach(function (search) {
            var scope = search.closest ? search.closest('td, .AITMT-onboarding-step, .AITMT-wide-card, .AITMT-fallback-card, .form-table') : document;
            var grid = search.nextElementSibling && search.nextElementSibling.matches && search.nextElementSibling.matches('.AITMT-fallback-language-grid, .AITMT-language-list') ? search.nextElementSibling : null;
            var options = Array.prototype.slice.call((grid || scope || document).querySelectorAll('.AITMT-language-option, .AITMT-fallback-language-grid label'));

            if (!options.length) {
                options = Array.prototype.slice.call(document.querySelectorAll('.AITMT-language-option, .AITMT-fallback-language-grid label'));
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
        var box = document.querySelector('#AITMT-language-switcher-menu');

        if (!box) {
            return;
        }

        var optionInputs = Array.prototype.slice.call(box.querySelectorAll('[data-AITMT-menu-option], [data-AITMT-menu-display]'));
        var classInputs = Array.prototype.slice.call(box.querySelectorAll('.AITMT-menu-item-classes'));

        function selectedDisplay() {
            var checked = box.querySelector('[data-AITMT-menu-display]:checked');

            return checked && checked.value === 'dropdown' ? 'dropdown' : 'list';
        }

        function optionEnabled(name) {
            var input = box.querySelector('[data-AITMT-menu-option="' + name + '"]');

            return !!(input && input.checked);
        }

        function updateClasses() {
            var suffixes = [];
            var display = selectedDisplay();

            suffixes.push(display === 'dropdown' ? 'AITMT-menu-display-dropdown' : 'AITMT-menu-display-list');

            if (optionEnabled('show-flag')) {
                suffixes.push('AITMT-menu-show-flag');
            }

            if (optionEnabled('show-name')) {
                suffixes.push('AITMT-menu-show-name');
            }

            if (optionEnabled('show-code')) {
                suffixes.push('AITMT-menu-show-code');
            }

            if (optionEnabled('hide-current')) {
                suffixes.push('AITMT-menu-hide-current');
            }

            classInputs.forEach(function (input) {
                var base = input.getAttribute('data-AITMT-base-classes') || '';
                input.value = (base + ' ' + suffixes.join(' ')).trim().replace(/\s+/g, ' ');
            });
        }

        optionInputs.forEach(function (input) {
            input.addEventListener('change', updateClasses);
        });
        updateClasses();
    }

    function initAdminMode() {
        var wrap = document.querySelector('.AITMT-admin-page');
        var radios = Array.prototype.slice.call(document.querySelectorAll('input[name="AITMT_options[admin_mode]"]'));

        if (!wrap || !radios.length) {
            return;
        }

        function applyMode() {
            var checked = radios.filter(function (radio) {
                return radio.checked;
            })[0];
            var mode = checked && checked.value === 'advanced' ? 'advanced' : 'basic';

            wrap.classList.toggle('AITMT-mode-basic', mode === 'basic');
            wrap.classList.toggle('AITMT-mode-advanced', mode === 'advanced');
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
