import { Tyrux } from "./lib/tyrux.js";
import { DOMclass } from "./lib/functions.js";

const baseURL = "";   //Backend url end-point
const baseRoute = "";   // Default api rout
const backend = "?be=";  // This app default backend path

const headers = {
    Authorization: "Bearer sometoken",
    "Content-Type": "application/json",
};

const config = {
    error: (err, message)=>{ // Tyrax error handler ::CodeTazer
        alert(message);
    },

    baseURL: backend
};

const api = new Tyrux();
const tyreq = new Tyrux(config);

function tyrux(request) {
    // Use for default setup..:: CodeYRO
    const head = request.headers ?? null;

    if (head) {
        request.headers = { ...head, ...headers };

        const ctype = head["Content-Type"]?.toLowerCase();

        if (ctype === "pict" ||
            ctype === "image" ||
            ctype === "file" ||
            ctype === "multipart/form-data") {
            delete request.headers["Content-Type"];
        }
    } else {
        request.headers = { ...headers };
    }

    tyreq.request(request);
}

//Exports here...
window.tyrux = tyrux;
window.TYRUX = Tyrux;
window.baseURL = baseURL;
window.baseRoute = baseRoute;
window.tyreq = tyreq;
window.backend = backend;

/**
 * tyruxRequest is a raw request
 * above setup doesn't apply here, but you can use them and attach to tyruxRequest
 */

const tyrequest = { // For raw/universal request :: CodeYRO
    config: {},
    api(option) {
        api.request(configure._mergeOptions(option, this));
    },
    post(option) {
        option.method = "POST";
        api.request(configure._mergeOptions(option), this);
    },
    put(option) {
        option.method = "PUT";
        api.request(configure._mergeOptions(option), this);
    },
    get(option) {
        option.method = "GET";
        api.request(configure._mergeOptions(option), this);
    },
    patch(option) {
        option.method = "PATCH";
        api.request(configure._mergeOptions(option), this);
    },
    delete(option) {
        option.method = "DELETE";
        api.request(configure._mergeOptions(option), this);
    },
    head(option) {
        option.method = "HEAD";
        api.request(configure._mergeOptions(option), this);
    },
    options(option) {
        option.method = "OPTIONS";
        api.request(configure._mergeOptions(option), this);
    },
    async(option) {
        return new Promise((resolve, reject) => {
            api.request(configure._mergeOptions({
                ...option,
                response: res => resolve(res),
                error: err => reject(err)
            }, this));
        });
    }
};

const configure = {
    _mergeOptions(option, tyrax) {
        const global = tyrax.config || {};
        const merged = {
            ...global,
            ...option,
            headers: {
                ...(global.headers || {}),
                ...(option.headers || {})
            },
            response: (res) => {
                if (typeof global?.response === "function") global.response(res);
                if (typeof global?.Response === "function") global.Response(res);
                if (typeof option?.response === "function") option.response(res);
                if (typeof option?.Response === "function") option.Response(res);
            }
        };

        if (typeof global?.wait === "function" || typeof option?.wait === "function") {
            merged.wait = (xhr) => {
                if (typeof global?.wait === "function") global.wait(xhr);
                if (typeof option?.wait === "function") option.wait(xhr);
            };
        }

        if (typeof global?.done === "function" || typeof option?.done === "function") {
            merged.done = (xhr) => {
                if (typeof global?.done === "function") global.done(xhr);
                if (typeof option?.done === "function") option.done(xhr);
            };
        }

        if (typeof global?.error === "function" || typeof option?.error === "function") {
            merged.error = (err) => {
                if (typeof global?.error === "function") global.error(err);
                if (typeof option?.error === "function") option.error(err);
            };
        }
        return merged;
    }
};

const opt = {
    url: undefined,
    request: undefined,
    response: undefined,
    wait: undefined,
    done: undefined,
    headers: undefined,
    error: undefined,
    catch: undefined,
    test: undefined,
    inspect: undefined,
    csrf: true
};

const tyrax = { // tyrux default config :: CodeTazeR
    config: {},
    api(op) {
        tyrux(configure._mergeOptions(op, this));
    },

    post(option = opt) {
        option.method = "POST";
        tyrux(configure._mergeOptions(option, this));
    },

    put(option = opt) {
        option.method = "PUT";
        tyrux(configure._mergeOptions(option, this));
    },

    get(option = opt) {
        option.method = "GET";
        tyrux(configure._mergeOptions(option, this));
    },

    patch(option = opt) {
        option.method = "PATCH";
        tyrux(configure._mergeOptions(option, this));
    },

    delete(option = opt) {
        option.method = "DELETE";
        tyrux(configure._mergeOptions(option, this));
    },

    head(option = opt) {
        option.method = "HEAD";
        tyrux(configure._mergeOptions(option, this));
    },

    options(option = opt) {
        option.method = "OPTIONS";
        tyrux(configure._mergeOptions(option, this));
    },

    async(option = opt) {
        return new Promise((resolve, reject) => {
            tyrux({
                ...option,
                response: res => resolve(res),
                error: err => reject(err)
            });
        });
    }
};

const tyrsync = { // For async/await tyrax :: CodeTazeR
    config: {},
    api(option = opt) {
        return tyrax.async(configure._mergeOptions(option));
    },
    post(option = opt) {
        return tyrax.async(configure._mergeOptions({ ...option, method: "POST" }, this));
    },
    put(option = opt) {
        return tyrax.async(configure._mergeOptions({ ...option, method: "PUT" }, this));
    },
    get(option = opt) {
        return tyrax.async(configure._mergeOptions({ ...option, method: "GET" }, this));
    },
    patch(option = opt) {
        return tyrax.async(configure._mergeOptions({ ...option, method: "PATCH" }, this));
    },
    delete(option = opt) {
        return tyrax.async(configure._mergeOptions({ ...option, method: "DELETE" }, this));
    },
    head(option = opt) {
        return tyrax.async(configure._mergeOptions({ ...option, method: "HEAD" }, this));
    }
};

function get_form_data(selector) {
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

const DOM = new DOMclass();

if (typeof window !== "undefined") {
    window.get_form_data = get_form_data;
    window.tyrequest = tyrequest;
    window.tyrax = tyrax;
    window.tyrasync = tyrsync;
    window.DOM = DOM;
}

if (typeof module !== "undefined" && typeof module.exports !== "undefined") {
    module.exports = SECURE;
}

export {
    tyrax,
    tyrequest,
    get_form_data,
    tyrsync,
    tyrux,
};