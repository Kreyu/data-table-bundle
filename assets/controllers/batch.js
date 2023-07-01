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

    #update() {
        this.#updateBatchActionBar();
        this.#updateIndeterminateStates();
        this.#updateIdentifierHolder();
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

    #updateIdentifierHolder() {
        const identifierMap = new Map();
        const checkboxes = this.selectRowCheckboxTargets.filter(checkbox => checkbox.checked);

        for (const checkbox of checkboxes) {
            const identifier = checkbox.value;
            const identifierName = checkbox.dataset.identifierName || 'id';
            const identifiers = identifierMap.get(identifierName) || [];

            identifiers.push(identifier);

            identifierMap.set(identifierName, identifiers);
        }

        console.log(this.identifierHolderTargets);

        for (const identifierHolder of this.identifierHolderTargets) {
            this.#updateIdentifierHolderHref(identifierHolder, identifierMap);
            this.#updateIdentifierHolderDataParam(identifierHolder, identifierMap);
        }
    }

    #updateIdentifierHolderHref(identifierHolder, identifierMap) {
        let href;

        try {
            href = new URL(identifierHolder.href);
        } catch (exception) {
            return;
        }

        for (const [identifierName, identifiers] of identifierMap) {
            for (const identifier of identifiers) {
                href.searchParams.set(identifierName + '[]', identifier);
            }
        }

        identifierHolder.href = href.toString();
    }

    #updateIdentifierHolderDataParam(identifierHolder, identifierMap) {
        for (const [identifierName, identifiers] of identifierMap) {
            identifierHolder.dataset[identifierName] = JSON.stringify(identifiers);
        }
    }

    #getUniqueSelectedCount() {
        const selectedIndexes = this.selectRowCheckboxTargets
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.dataset.index);

        return new Set(selectedIndexes).size;
    }

    #getSelectAllCheckboxes(identifierName) {
        return this.selectAllCheckboxTargets
            .filter(checkbox => checkbox.dataset.identifierName === identifierName);
    }

    #getSelectRowCheckboxes(identifierName) {
        return this.selectRowCheckboxTargets
            .filter(checkbox => checkbox.dataset.identifierName === identifierName);
    }
}
