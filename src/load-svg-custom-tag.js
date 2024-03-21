customElements.define("load-svg", class extends HTMLElement {
    async connectedCallback() {
        const src = this.getAttribute("src")
        const shadowRoot = this.shadowRoot || this.attachShadow({ mode: "open" })

        shadowRoot.innerHTML = await (await fetch(src)).text()

        shadowRoot.append(...this.querySelectorAll("[shadowRoot]"))

        if (this.hasAttribute("replaceWith")) {
            this.replaceWith(...shadowRoot.childNodes);
        }
    }
})
