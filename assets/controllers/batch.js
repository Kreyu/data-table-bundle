import {Controller} from '@hotwired/stimulus'

export default class extends Controller {
    static targets = [
        'selectAllCheckbox',
        'selectRowCheckbox',
        'selectedCounter',
        'batchActionBar',
        'identifierHolder',
    ];

    connect() {
        // The timeout is required so Stimulus can catch initial input values.
        // https://github.com/hotwired/stimulus/issues/328
        setTimeout(() => this.#update(), 1);
    }

    #update() {
        this.#updateBatchActionBar();
        this.#updateIndeterminateStates();
        this.#updateIdentifierHolder();
    }

    #getSelectAllCheckboxes(identifierName) {
        return this.selectAllCheckboxTargets
            .filter(checkbox => checkbox.dataset.identifierName === identifierName);
    }

    #getSelectRowCheckboxes(identifierName) {
        return this.selectRowCheckboxTargets
            .filter(checkbox => checkbox.dataset.identifierName === identifierName);
    }

    selectAll(event) {
        const selectAllCheckbox = event.target;
        const identifierName = selectAllCheckbox.dataset.identifierName;

        this.#getSelectRowCheckboxes(identifierName).forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });

        this.#update();
    }

    selectRow() {
        this.#update()
    }

    #updateIdentifierHolder() {
        this.#updateIdentifierHolderHref();
    }

    #updateIdentifierHolderHref() {
        if (!this.identifierHolderTarget.hasAttribute('href')) {
            return;
        }

        let href = null;

        try {
            href = new URL(this.identifierHolderTarget.href);
        } catch (exception) {
            return;
        }

        if (null === href) {
            return;
        }

        const identifierMap = {};

        for (const selector of this.selectRowCheckboxTargets) {
            if (!selector.checked) {
                continue;
            }

            const identifier = selector.value;
            const identifierName = selector.dataset.identifierName || 'id';

            identifierMap[identifierName] ??= [];
            identifierMap[identifierName].push(identifier);
        }

        for (const [identifierName, identifiers] of Object.entries(identifierMap)) {
            href.searchParams.delete(identifierName + '[]');

            for (const identifier of identifiers) {
                href.searchParams.append(identifierName + '[]', identifier);
            }
        }

        this.identifierHolderTarget.setAttribute('href', href.toString());
    }

    #updateBatchActionBar() {
        const uniqueSelectedCount = this.#getUniqueSelectedCount();

        this.selectedCounterTarget.innerHTML = uniqueSelectedCount;
        this.batchActionBarTarget.hidden = uniqueSelectedCount === 0;
    }

    #updateIndeterminateStates() {
        for (const selectAllCheckbox of this.selectAllCheckboxTargets) {
            const identifierName = selectAllCheckbox.dataset.identifierName;

            const selectRowCheckboxes = this.#getSelectRowCheckboxes(identifierName);
            const selectedRowCheckboxes = selectRowCheckboxes.filter(checkbox => checkbox.checked);

            selectAllCheckbox.indeterminate = selectedRowCheckboxes.length > 0
                && selectRowCheckboxes.length !== selectedRowCheckboxes.length;
        }
    }

    #getUniqueSelectedCount() {
        const selectedIndexes = this.selectRowCheckboxTargets
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.dataset.index);

        return new Set(selectedIndexes).size;
    }
}
