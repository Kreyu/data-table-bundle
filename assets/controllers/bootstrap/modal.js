import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['modal'];

    static values = {
        url: String,
    }

    open(event) {
        event.preventDefault();
        const modalContent = this.modalTarget;

        fetch(this.element.getAttribute('href'))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error loading content.');
                }
                return response.text();
            })
            .then(html => {
                modalContent.innerHTML = html;
            })
        ;
    }
}
