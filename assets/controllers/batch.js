import {Controller} from '@hotwired/stimulus'

export default class extends Controller {
    static targets = [
        'selectAllCheckbox',
        'selectRowCheckbox',
        'selectedCounter',
        'infoBar',
        'identifierHolder',
    ];

    connect() {
        this.#updateIdentifierHolder();
    }

    selectAll() {
        this.selectRowCheckboxTargets.forEach(checkbox => {
            checkbox.checked = this.selectAllCheckboxTarget.checked;
        });

        this.#updateInfoBar();
        this.#updateIdentifierHolder();
    }

    selectRow() {
        this.#updateInfoBar();
        this.#updateIdentifierHolder();

        this.selectAllCheckboxTarget.indeterminate = this.#selectedCount !== 0
            && this.#selectedCount !== this.selectRowCheckboxTarget.length;
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

        href.searchParams.delete('id[]');

        for (const identifier of this.selectedIdentifiers) {
            href.searchParams.append('id[]', identifier);
        }

        this.identifierHolderTarget.setAttribute('href', href.toString());
    }

    #updateInfoBar() {
        this.selectedCounterTarget.innerHTML = this.#selectedCount;
        this.infoBarTarget.hidden = this.#selectedCount === 0;
    }

    #updateSelectedCounter() {
        this.selectedCounterTarget.innerHTML = this.selectRowCheckboxTargets.filter(checkbox => checkbox.checked).length;
    }

    get #selectedCount() {
        return this.selectRowCheckboxTargets.filter(checkbox => checkbox.checked).length;
    }

    get selectedIdentifiers() {
        return this.selectRowCheckboxTargets.filter(checkbox => checkbox.checked).map(checkbox => 1);
    }
}
