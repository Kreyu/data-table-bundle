import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap'; // Import Bootstrap

export default class extends Controller {
    static targets = ['modal'];

    static values = {
        url: String,
    }

    open(event) {
        event.preventDefault();
        const modalContent = this.modalTarget;

        fetch(this.urlValue)
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
