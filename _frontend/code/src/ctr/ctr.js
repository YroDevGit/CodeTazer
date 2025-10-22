class CtrClass {

    constructor(rootpath = "") {
        this.global_root = rootpath;
        this.frontend = "?page=";
        this.backend = "?be=";
        this.func = "?funcpage=";
    }

    page($page = "", params = {}) {
        if (!$page || $page == "/") {
            return "/";
        }
        let url = this.frontend + $page;
        if (typeof params === "object" && Object.keys(params).length > 0) {
            const query = Object.entries(params)
                .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
                .join("&");
            url += "?" + query;
        }
        return url;
    }

    backend($be = "", params = {}) {
        let url = this.backend + $be;
        if (typeof params === "object" && Object.keys(params).length > 0) {
            const query = Object.entries(params)
                .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
                .join("&");
            url += (url.includes("?") ? "&" : "&") + query;
        }
        return url;
    }

    redirect(page = "", params = {}) {
        window.location.href = this.page(page, params);
    }

    reload(hardRefresh = false) {
        if (hardRefresh) {
            caches.keys().then(names => {
                for (let name of names) caches.delete(name);
            }).then(() => {
                location.reload(true);
            });
        }
        else {
            window.location.reload();
        }
    }

    funcpage($page = "", params = {}) {
        let url = this.func + $page;
        if (typeof params === "object" && Object.keys(params).length > 0) {
            const query = Object.entries(params)
                .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
                .join("&");
            url += (url.includes("?") ? "&" : "&") + query;
        }
        return url;
    }

    dom_loaded(callable) {
        window.addEventListener("DOMContentLoaded", callable());
    }

    set_html(selector, strhtml) {
        let elements = [];

        if (selector.charAt(0) === "#") {
            const element = document.getElementById(selector.substring(1));
            if (element) {
                elements.push(element);
            }
        } else if (selector.charAt(0) === ".") {
            elements = Array.from(document.querySelectorAll(selector));
        } else {
            const element = document.getElementById(selector);
            if (element) {
                elements.push(element);
            }
        }
        if (elements.length > 0) {
            elements.forEach(element => {
                element.innerHTML = strhtml;
            });
        } else {
            console.warn(`No elements found for selector: "${selector}"`);
        }
    }

    add_html(selector, strhtml) {
        let elements = [];

        if (selector.charAt(0) === "#") {
            const element = document.getElementById(selector.substring(1));
            if (element) {
                elements.push(element);
            }
        } else if (selector.charAt(0) === ".") {
            elements = Array.from(document.querySelectorAll(selector));
        } else {
            const element = document.getElementById(selector);
            if (element) {
                elements.push(element);
            }
        }

        if (elements.length > 0) {
            elements.forEach(element => {
                element.insertAdjacentHTML('beforeend', strhtml);
            });
        } else {
            console.warn(`No elements found for selector: "${selector}"`);
        }
    }

    loaded(callable) {
        window.addEventListener("DOMContentLoaded", function () {
            callable();
        });
    }

    click(selector, callable) {
        let form = null;
        if (selector.charAt(0) === "#" || selector.charAt(0) === ".") {
            form = document.querySelectorAll(selector);
            form.forEach(element => {
                const attrs = {};
                for (let attr of element.attributes) {
                    attrs[attr.name] = attr.value;
                }
                element.addEventListener("click", function () {
                    callable(attrs);
                });
            });
        } else {
            form = document.getElementById(selector);
            const attrs = {};
            for (let attr of form.attributes) {
                attrs[attr.name] = attr.value;
            }
            form.addEventListener("click", function () {
                callable(attrs);
            });
        }
    }

    submit(selector, callable) {
        let elem = document.querySelectorAll(selector);
        elem.forEach(element => {
            element.addEventListener('submit', function (event) {
                event.preventDefault();
                let formobject = new FormData(element);
                const dataObject = {};
                formobject.forEach((value, key) => {
                    dataObject[key] = value;
                });
                callable(formobject, dataObject, event);
            });
        });
    }

    apply(...callable) {
        callable.forEach(call => {
            if (typeof call !== "function") {
                return;
            }
            call();
        });
    }

    form_data(selector) {
        let form = null;
        if (selector.charAt(0) === "#" || selector.charAt(0) === ".") {
            form = document.querySelector(selector);
        } else {
            form = document.querySelector(`#${selector}`);
        }

        if (!form) return null;

        const formData = new FormData(form);

        const dataObject = {};
        formData.forEach((value, key) => {
            dataObject[key] = value;
        });

        return dataObject;
    }

    form_object(selector) {
        const element = document.querySelector(selector);
        const formdata = new FormData(element);
        return formdata;
    }

    storage_set(name, item) {
        localStorage.setItem(name, item);
    }

    storage_get(name) {
        localStorage.getItem(name);
    }

    storage_clear() {
        localStorage.clear();
    }

    storage_remove(name) {
        localStorage.removeItem(name);
    }
    $(selector) {
        return document.querySelector(selector);
    }
    $all(selector) {
        return document.querySelectorAll(selector);
    }
    load(callable) {
        window.addEventListener("DOMContentLoaded", function () {
            callable();
        });
    }

    value(selector) {
        return document.querySelector(selector).value;
    }

    child(selector) {
        return document.querySelector(selector).innerHTML;
    }

}

const CTR = new CtrClass();
const Ctr = CTR;

if (typeof window !== "undefined") {
    window.CTR = CTR;
    window.Ctr = CTR;
}

if (typeof module !== "undefined" && typeof module.exports !== "undefined") {
    module.exports = CTR;
    module.exports = Ctr;
}

export { Ctr };
export default CTR;