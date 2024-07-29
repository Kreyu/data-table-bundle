import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static values = {
        urlQueryParameters: Object,
    }

    connect() {
        this.#appendUrlQueryParameters();
    }

    #appendUrlQueryParameters() {
        const url = new URL(window.location.href);

        const parameters = this.#flattenParameters(this.urlQueryParametersValue);

        for (const [key, value] of Object.entries(parameters)) {
            if (!url.searchParams.has(key)) {
                url.searchParams.set(key, String(value));
            }
        }

        window.history.replaceState(null, null, url);
    }

    #flattenParameters(input, keyName) {
        let result = {};

        for (const key in input) {
            const newKey =  keyName ? `${keyName}[${key}]` : key;

            if (typeof input[key] === "object" && !Array.isArray(input[key])) {
                result = {...result, ...this.#flattenParameters(input[key], newKey)}
            } else {
                result[newKey] = input[key];
            }
        }

        return result;
    }
}
