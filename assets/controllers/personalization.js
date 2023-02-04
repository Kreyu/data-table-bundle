import { Controller } from '@hotwired/stimulus'
import Sortable from 'sortablejs'

export default class extends Controller {
    static targets = [ 'visibleColumns', 'hiddenColumns' ]

    connect() {
        this.#initializeSortable(this.visibleColumnsTarget)
        this.#initializeSortable(this.hiddenColumnsTarget)
    }

    #initializeSortable(target) {
        new Sortable(target, {
            group: 'shared',
            animation: 150,
            onAdd: event => {
                const input = event.item.querySelector('[name$="[visible]"]')

                input.value = event.to.dataset.visible
            },
            onChange: event => {
                const orderInput = event.item.querySelector('[name$="[order]"]')
                const originalOrderInput = event.originalEvent.target.querySelector('[name$="[order]"]')

                orderInput.value = event.newIndex
                originalOrderInput.value = event.oldIndex
            }
        })
    }
}
