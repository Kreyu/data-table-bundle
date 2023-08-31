import { Controller } from '@hotwired/stimulus'
import Sortable from 'sortablejs'

export default class extends Controller {
    static targets = [ 'visibleColumns', 'hiddenColumns' ]

    #visibleColumnsSortable = null
    #hiddenColumnSortable = null

    connect() {
        this.#visibleColumnsSortable = this.#initializeSortable(this.visibleColumnsTarget)
        this.#hiddenColumnSortable = this.#initializeSortable(this.hiddenColumnsTarget)
    }

    disconnect() {
        this.#visibleColumnsSortable.destroy();
        this.#hiddenColumnSortable.destroy();
    }

    #initializeSortable(target) {
        return new Sortable(target, {
            group: 'shared',
            animation: 150,
            onAdd: event => {
                const input = event.item.querySelector('[name$="[visible]"]')

                input.value = event.to.dataset.visible
            },
            onChange: event => {
                const priorityInput = event.item.querySelector('[name$="[priority]"]')
                const originalPriorityInput = event.originalEvent.target.querySelector('[name$="[priority]"]')

                priorityInput.value = this.#calculatePriority(target, event.newIndex)
                originalPriorityInput.value = this.#calculatePriority(target, event.oldIndex)
            }
        })
    }

    #calculatePriority(target, index) {
        return target.childElementCount - index - 1;
    }
}
