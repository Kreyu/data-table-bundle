import { Controller } from '@hotwired/stimulus'
import '../styles/responsive.css'

/* stimulusFetch: 'eager' */
export default class extends Controller {
    static targets = ['toggleButton', 'collapsibleRow']

    static values = {
        breakpoints: { type: Object, default: {} },
        currentBreakpoint: { type: String, default: '' },
    }

    #resizeObserver = null
    #debounceTimeout = null

    connect() {
        const frame = this.element.closest('turbo-frame')

        if (!frame) {
            this.#reveal()
            return
        }

        // Synchronous check before first paint: does the server breakpoint match reality?
        const width = Math.round(frame.getBoundingClientRect().width)

        if (width > 0) {
            const breakpoint = this.#resolveBreakpoint(width)

            if (breakpoint !== this.currentBreakpointValue) {
                // Mismatch: keep hidden (CSS class), reload with correct breakpoint
                this.currentBreakpointValue = breakpoint
                this.#reloadFrame(breakpoint)
            } else {
                // Match: reveal immediately
                this.#reveal()
            }
        } else {
            this.#reveal()
        }

        this.#resizeObserver = new ResizeObserver((entries) => {
            this.#onResize(Math.round(entries[0].contentRect.width))
        })
        this.#resizeObserver.observe(frame)
    }

    disconnect() {
        if (this.#resizeObserver) {
            this.#resizeObserver.disconnect()
            this.#resizeObserver = null
        }

        if (this.#debounceTimeout) {
            clearTimeout(this.#debounceTimeout)
            this.#debounceTimeout = null
        }
    }

    toggle(event) {
        const button = event.currentTarget
        const index = button.dataset.rowIndex
        const row = this.collapsibleRowTargets.find(r => r.dataset.rowIndex === index)

        if (!row) {
            return
        }

        row.hidden = !row.hidden
        button.setAttribute('aria-expanded', String(!row.hidden))
        button.querySelector('.kreyu-dt-toggle-icon').textContent = row.hidden ? '+' : '\u2212'
    }

    #reveal() {
        this.element.classList.remove('kreyu-dt-responsive-pending')
    }

    #onResize(width) {
        if (this.#debounceTimeout) {
            clearTimeout(this.#debounceTimeout)
        }

        this.#debounceTimeout = setTimeout(() => this.#detectAndUpdate(width), 250)
    }

    #detectAndUpdate(width) {
        const breakpoint = this.#resolveBreakpoint(width)

        if (breakpoint === this.currentBreakpointValue) {
            return
        }

        this.currentBreakpointValue = breakpoint
        this.#reloadFrame(breakpoint)
    }

    #resolveBreakpoint(width) {
        const breakpoints = this.breakpointsValue

        for (const [name, maxWidth] of Object.entries(breakpoints)) {
            if (width <= maxWidth) {
                return name
            }
        }

        // Above all breakpoints: return the largest one
        const names = Object.keys(breakpoints)
        return names.length > 0 ? names[names.length - 1] : ''
    }

    #reloadFrame(breakpoint) {
        const frame = this.element.closest('turbo-frame')

        if (!frame) {
            return
        }

        const baseUrl = frame.getAttribute('src') || window.location.href
        const url = new URL(baseUrl, window.location.origin)
        url.searchParams.set('_breakpoint', breakpoint)

        frame.src = url.toString()
    }
}
