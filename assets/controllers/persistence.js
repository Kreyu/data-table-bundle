import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['form'];

    static values = {
        pagination: Object,
        sorting: Object,
    }

    connect() {
        this.#loadFormsState();

        if (this.paginationValue.page > 0) {
            this.#loadPaginationState();
        }

        if (this.sortingValue.columns) {
            this.#loadSortingState();
        }
    }

    #loadFormsState() {
        const url = new URL(window.location.href);

        for (const form of this.formTargets) {
            const formData = new FormData(form);

            for (const [key, value] of formData) {
                url.searchParams.set(key, String(value));
            }
        }

        window.history.replaceState(null, null, url);
    }

    #loadPaginationState() {
        const url = new URL(window.location.href);

        url.searchParams.set(this.paginationValue.parameter, this.paginationValue.page);

        window.history.replaceState(null, null, url);
    }

    #loadSortingState() {
        const url = new URL(window.location.href);

        for (const [name, direction] of Object.entries(this.sortingValue.columns)) {
            url.searchParams.set(this.sortingValue.parameter + '[' + name + ']', direction);
        }

        window.history.replaceState(null, null, url);
    }
}
