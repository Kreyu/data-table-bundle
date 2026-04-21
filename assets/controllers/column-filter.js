import { Controller } from '@hotwired/stimulus'

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        delay: { type: Number, default: 400 },
    }

    static targets = ['form']

    connect() {
        this.timeout = null
    }

    disconnect() {
        this.clear()
    }

    queueSubmit() {
        this.clear()
        this.timeout = setTimeout(() => {
            if (this.formTarget.requestSubmit) {
                this.formTarget.requestSubmit()
            } else {
                this.formTarget.submit()
            }
        }, this.delayValue)
    }

    clear() {
        if (this.timeout) {
            clearTimeout(this.timeout)
            this.timeout = null
        }
    }
}
