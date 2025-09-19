class Ctr {

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
            form = document.querySelector(selector);
        } else {
            form = document.getElementById(selector);
        }
        if (!form) {
            console.error(`Tag with id '${selector}' not found.`);
            return;
        }
        if (typeof callable !== "function") {
            console.error("Callable is not a function.");
            return;
        }
        form.addEventListener("click", function () {
            callable();
        });
    }

    submit(selector, callable) {
        const sel = document.querySelector(selector);
        let formdata = this.form_data(`#` + sel.getAttribute("id"));
        sel.addEventListener('submit', function (event) {
            event.preventDefault();
            let formobject = new FormData(sel);
            callable(formobject, formdata, event);
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

window.CTR = new Ctr();