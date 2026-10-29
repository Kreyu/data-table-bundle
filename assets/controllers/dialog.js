import { Controller } from '@hotwired/stimulus'

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['dialog', 'content'];

    #closeBeforeCache = () => this.close();

    connect() {
        document.addEventListener('turbo:before-cache', this.#closeBeforeCache);
    }

    disconnect() {
        document.removeEventListener('turbo:before-cache', this.#closeBeforeCache);
    }

    open(event) {
        event.preventDefault();

        if (this.hasDialogTarget) {
            this.dialogTarget.showModal();
        }

        const url = event.currentTarget.getAttribute('href');

        if (this.hasContentTarget && url) {
            this.#loadContent(url);
        }
    }

    close() {
        if (this.hasDialogTarget) {
            this.dialogTarget.close();
        }
    }

    #loadContent(url) {
        const content = this.contentTarget;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error loading content.');
                }

                return response.text();
            })
            .then(html => {
                content.innerHTML = html;
            })
        ;
    }
}
