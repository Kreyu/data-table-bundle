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

    #onVisibilityChange(event) {
        if (this.context.application.debug) {
            console.groupCollapsed(this.identifier + ' #onVisibilityChange');
        }

        const visibilityInput = this.#getVisibilityInput(event.item);
        const visibilityBefore = visibilityInput.value;
        const visibilityAfter = event.to.dataset.visible;

        visibilityInput.value = visibilityAfter;

        if (this.context.application.debug) {
            const columnName = this.#getNameInput(event.item).value;

            console.log(`Column "${columnName}" visibility changed from ${visibilityBefore} to ${visibilityAfter}`);
            console.groupEnd();
        }
    }

    #onPriorityChange(event) {
        const priorityInputs = this.#getPriorityInputs(event.to);

        const changes = {};

        if (this.context.application.debug) {
            console.groupCollapsed(this.identifier + ' #onPriorityChange');
        }

        for (const [index, item] of Object.entries(priorityInputs)) {
            const priorityAfter = priorityInputs.length - index - 1;

            if (this.context.application.debug) {
                const columnName = this.#getNameInput(item.parentElement).value;
                const priorityBefore = Number(item.value);

                changes[columnName] = { priorityBefore, priorityAfter };
            }

            item.value = priorityAfter;
        }

        if (this.context.application.debug) {
            console.table(changes);
            console.groupEnd();
        }
    }

    #initializeSortable(target) {
        return new Sortable(target, {
            group: 'shared',
            animation: 150,
            onAdd: (event) => {
                this.#onVisibilityChange(event);
                this.#onPriorityChange(event);
            },
            onChange: this.#onPriorityChange.bind(this),
        })
    }

    #getNameInput(target) {
        return target.querySelector('[name$="[name]"]');
    }

    #getVisibilityInput(target) {
        return target.querySelector('[name$="[visible]"]');
    }

    #getPriorityInputs(target) {
        return target.querySelectorAll('[name$="[priority]"]');
    }
}
