import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['form'];

    connect() {
        this.#loadFormsState();
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
}
