class PersonalizationForm {
    #visibleColumnsSelector;
    #hiddenColumnsSelector;
    #connectionSelector;

    constructor(visibleColumnsSelector, hiddenColumnsSelector, connectionSelector) {
        this.#visibleColumnsSelector = visibleColumnsSelector;
        this.#hiddenColumnsSelector = hiddenColumnsSelector;
        this.#connectionSelector = connectionSelector;
    }

    initialize() {
        this.#attachEventHandlers();
    }

    #attachEventHandlers() {
        $(this.#getSortableElementSelector()).sortable(this.#getSortableConfiguration());
    }

    #getSortableElementSelector() {
        return `#${this.#visibleColumnsSelector}, #${this.#hiddenColumnsSelector}`;
    }

    #getSortableConfiguration() {
        return {
            connectWith: this.#connectionSelector,
            update: function (event) {
                $(event.target).find('.ui-sortable-handle').each(function () {
                    $(this).find('[name$="[order]"]').val($(this).index());
                });
            },
            receive: function (event, ui) {
                $(ui.item).find('[name$="[visible]"]').val(parseInt($(event.target).data('visible')));
            }
        };
    }
}