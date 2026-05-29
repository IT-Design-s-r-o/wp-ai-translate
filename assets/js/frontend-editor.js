(function () {
    if (!window.AITMT_EDITOR || window.AITMT_INLINE_EDITOR_READY) {
        return;
    }

    window.AITMT_INLINE_EDITOR_READY = true;

    var config = window.AITMT_EDITOR || {};
    var active = false;
    var toolbar = document.createElement('div');
    var brand = document.createElement('a');
    var brandImage = document.createElement('img');
    var brandText = document.createElement('span');
    var button = document.createElement('button');
    var status = document.createElement('span');
    var modal = document.createElement('div');
    var dialog = document.createElement('div');
    var modalTitle = document.createElement('h2');
    var textarea = document.createElement('textarea');
    var modalActions = document.createElement('div');
    var modalNotice = document.createElement('div');
    var saveButton = document.createElement('button');
    var autoButton = document.createElement('button');
    var cancelButton = document.createElement('button');
    var currentTarget = null;

    toolbar.className = 'aitmt-inline-editor-toolbar';
    toolbar.setAttribute('data-aitmt-no-translate', '1');
    brand.className = 'aitmt-inline-editor-brand';
    brand.href = config.siteUrl || '#';
    brand.target = '_blank';
    brand.rel = 'noopener noreferrer';

    if (config.logoUrl) {
        brandImage.src = config.logoUrl;
        brandImage.alt = '';
        brand.appendChild(brandImage);
    }

    brandText.textContent = config.brandLabel || 'AI Translate';
    brand.appendChild(brandText);
    button.type = 'button';
    button.textContent = config.editLabel || 'AI Translate edit';
    status.className = 'aitmt-inline-editor-status';
    toolbar.appendChild(brand);
    toolbar.appendChild(button);
    toolbar.appendChild(status);
    status.textContent = config.targetNotice || '';

    modal.className = 'aitmt-inline-editor-modal';
    modal.setAttribute('data-aitmt-no-translate', '1');
    modal.setAttribute('aria-hidden', 'true');
    dialog.className = 'aitmt-inline-editor-dialog';
    dialog.setAttribute('role', 'dialog');
    dialog.setAttribute('aria-modal', 'true');
    modalTitle.textContent = config.promptLabel || 'Edit translation';
    textarea.className = 'aitmt-inline-editor-textarea';
    modalNotice.className = 'aitmt-inline-editor-notice';
    modalNotice.setAttribute('role', 'status');
    modalNotice.setAttribute('aria-live', 'polite');
    modalActions.className = 'aitmt-inline-editor-dialog-actions';
    saveButton.type = 'button';
    saveButton.textContent = config.saveLabel || 'Save';
    autoButton.type = 'button';
    autoButton.textContent = config.autoTranslateLabel || 'Auto Translate';
    autoButton.className = 'is-auto';
    cancelButton.type = 'button';
    cancelButton.textContent = config.cancelLabel || 'Cancel';
    cancelButton.className = 'is-secondary';
    modalActions.appendChild(cancelButton);
    modalActions.appendChild(autoButton);
    modalActions.appendChild(saveButton);
    dialog.appendChild(modalTitle);
    dialog.appendChild(textarea);
    dialog.appendChild(modalNotice);
    dialog.appendChild(modalActions);
    modal.appendChild(dialog);

    function mount() {
        if (document.body && !document.querySelector('.aitmt-inline-editor-toolbar')) {
            document.body.appendChild(toolbar);
            document.body.appendChild(modal);
        }
    }

    function setActive(next) {
        active = typeof next === 'boolean' ? next : !active;
        document.body.classList.toggle('aitmt-editor-active', active);
        button.classList.toggle('is-active', active);
        status.textContent = active ? (config.activeLabel || 'Editing on') : (config.targetNotice || '');

        if (active && !document.querySelector('.aitmt-editable')) {
            status.textContent = config.emptyLabel || 'No editable text found yet.';
        }
    }

    window.AITMT_INLINE_TOGGLE = function () {
        setActive();
    };

    button.addEventListener('click', function () {
        setActive();
    });

    document.addEventListener('click', function (event) {
        var target = event.target.closest ? event.target.closest('.aitmt-editable') : null;

        if (!active || !target) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        openEditor(target);
    }, true);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeEditor();
        }
    });

    cancelButton.addEventListener('click', function () {
        closeEditor();
    });

    textarea.addEventListener('input', function () {
        resizeTextarea();
    });

    autoButton.addEventListener('click', function () {
        autoTranslate();
    });

    saveButton.addEventListener('click', function () {
        if (!currentTarget) {
            closeEditor();
            return;
        }

        var nextText = textarea.value;

        if (nextText === currentTarget.textContent) {
            closeEditor();
            return;
        }

        saveTranslation(currentTarget, nextText);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeEditor();
        }
    });

    function openEditor(target) {
        currentTarget = target;
        textarea.value = target.textContent;
        modalNotice.textContent = '';
        resizeTextarea();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        window.setTimeout(function () {
            resizeTextarea();
            textarea.focus();
            textarea.select();
        }, 20);
    }

    function closeEditor() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        currentTarget = null;
        textarea.value = '';
        modalNotice.textContent = '';
        saveButton.disabled = false;
        autoButton.disabled = false;
        cancelButton.disabled = false;
        modal.classList.remove('is-loading');
    }

    function setLoading(loading, message) {
        modal.classList.toggle('is-loading', loading);
        saveButton.disabled = loading;
        autoButton.disabled = loading;
        cancelButton.disabled = loading;
        modalNotice.textContent = message || '';
    }

    function resizeTextarea() {
        textarea.style.height = 'auto';
        textarea.style.height = Math.max(160, textarea.scrollHeight) + 'px';
    }

    function autoTranslate() {
        if (!currentTarget) {
            return;
        }

        var sourceText = decodeSource(currentTarget.getAttribute('data-aitmt-source') || '');
        var body = new URLSearchParams();

        body.set('action', 'aitmt_auto_translate_frontend');
        body.set('nonce', config.nonce || '');
        body.set('sourceLanguage', config.sourceLanguage || '');
        body.set('targetLanguage', config.targetLanguage || '');
        body.set('sourceText', sourceText);

        setLoading(true, config.translatingLabel || 'AI translating...');

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body.toString()
        }).then(function (response) {
            return response.json().then(function (payload) {
                return {ok: response.ok, status: response.status, payload: payload};
            });
        }).then(function (result) {
            var payload = result.payload || {};
            var data = payload.data || {};

            if (!result.ok || !payload.success || !data.translation) {
                throw new Error(data.message || config.translateFailedLabel || 'Translation failed. Please try again.');
            }

            textarea.value = data.translation;
            resizeTextarea();
            setLoading(false, data.message || config.translationReadyLabel || 'AI Translation Ready');
            textarea.focus();
        }).catch(function (error) {
            setLoading(false, error && error.message ? error.message : (config.translateFailedLabel || 'Translation failed. Please try again.'));
        });
    }

    function saveTranslation(target, nextText) {
        var sourceText = decodeSource(target.getAttribute('data-aitmt-source') || '');
        var body = new URLSearchParams();

        body.set('action', 'aitmt_save_translation');
        body.set('nonce', config.nonce || '');
        body.set('sourceLanguage', config.sourceLanguage || '');
        body.set('targetLanguage', config.targetLanguage || '');
        body.set('sourceText', sourceText);
        body.set('translatedText', nextText);
        status.textContent = '...';
        setLoading(true, config.savingLabel || 'Saving...');

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body.toString()
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Save failed');
            }

            return response.json();
        }).then(function (payload) {
            if (!payload || !payload.success) {
                throw new Error('Save failed');
            }

            target.textContent = nextText;
            closeEditor();
            status.textContent = config.savedLabel || 'Saved';
            setTimeout(function () {
                if (active) {
                    status.textContent = config.activeLabel || 'Editing on';
                }
            }, 1600);
        }).catch(function () {
            status.textContent = config.errorLabel || 'Error';
            setLoading(false, config.errorLabel || 'Error');
        });
    }

    function decodeSource(value) {
        try {
            var binary = window.atob(value);
            var bytes = Array.prototype.map.call(binary, function (character) {
                return '%' + ('00' + character.charCodeAt(0).toString(16)).slice(-2);
            }).join('');

            return decodeURIComponent(bytes);
        } catch (error) {
            return '';
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mount);
    } else {
        mount();
    }
})();
