import {Controller} from '@hotwired/stimulus'

export default class extends Controller {
    static values = {
        toggledClass:  { type: String, default: 'show' },
    }

    static targets = [
        'content',
    ];

    toggle() {
        this.contentTarget.classList.toggle(this.toggledClassValue)
    }
}
