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
    error: "alert",
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
    _mergeOptions(option) {
        return {
            ...api.options,
            ...option,
            headers: {
                ...(api.options?.headers || {}),
                ...(option.headers || {})
            },
            request: {
                ...(api.options?.request || api.options?.data || api.options?.Request || {}),
                ...(option.request || option.data || option.Request || {})
            }
        };
    },

    api(option) {
        api.request(this._mergeOptions(option));
    },
    post(option) {
        option.method = "POST";
        api.request(this._mergeOptions(option));
    },
    put(option) {
        option.method = "PUT";
        api.request(this._mergeOptions(option));
    },
    get(option) {
        option.method = "GET";
        api.request(this._mergeOptions(option));
    },
    patch(option) {
        option.method = "PATCH";
        api.request(this._mergeOptions(option));
    },
    delete(option) {
        option.method = "DELETE";
        api.request(this._mergeOptions(option));
    },
    head(option) {
        option.method = "HEAD";
        api.request(this._mergeOptions(option));
    },
    options(option) {
        option.method = "OPTIONS";
        api.request(this._mergeOptions(option));
    },
    async(option) {
        return new Promise((resolve, reject) => {
            api.request(this._mergeOptions({
                ...option,
                response: res => resolve(res),
                error: err => reject(err)
            }));
        });
    }
};

const tyrax = { // tyrux default config :: CodeTazeR
    options: {},

    _mergeOptions(option) {
        return {
            ...tyrax.options,
            ...option,
            headers: {
                ...(tyrax.options.headers || {}),
                ...(option.headers || {})
            },
            request: {
                ...(tyrax.options.request || tyrax.options.data || tyrax.options.Request || {}),
                ...(option.request || option.data || option.Request || {})
            }
        };
    },

    api(option) {
        tyrux(this._mergeOptions(option));
    },

    post(option) {
        option.method = "POST";
        tyrux(this._mergeOptions(option));
    },

    put(option) {
        option.method = "PUT";
        tyrux(this._mergeOptions(option));
    },

    get(option) {
        option.method = "GET";
        tyrux(this._mergeOptions(option));
    },

    patch(option) {
        option.method = "PATCH";
        tyrux(this._mergeOptions(option));
    },

    delete(option) {
        option.method = "DELETE";
        tyrux(this._mergeOptions(option));
    },

    head(option) {
        option.method = "HEAD";
        tyrux(this._mergeOptions(option));
    },

    options(option) {
        option.method = "OPTIONS";
        tyrux(this._mergeOptions(option));
    },

    async(option) {
        return new Promise((resolve, reject) => {
            tyrux(this._mergeOptions({
                ...option,
                response: res => resolve(res),
                error: err => reject(err)
            }));
        });
    }
};

const tyrsync = { // For async/await tyrax :: CodeTazeR
    options: {},

    _mergeOptions(option) {
        return {
            ...tyrsync.options,
            ...option,
            headers: {
                ...(tyrsync.options.headers || {}),
                ...(option.headers || {})
            },
            request: {
                ...(tyrsync.options.request || tyrsync.options.data || tyrsync.options.Request || {}),
                ...(option.request || option.data || option.Request || {})
            }
        };
    },
    api(option) {
        return tyrax.async(tyrsync._mergeOptions(option));
    },
    post(option) {
        return tyrax.async(tyrsync._mergeOptions({ ...option, method: "POST" }));
    },
    put(option) {
        return tyrax.async(tyrsync._mergeOptions({ ...option, method: "PUT" }));
    },
    get(option) {
        return tyrax.async(tyrsync._mergeOptions({ ...option, method: "GET" }));
    },
    patch(option) {
        return tyrax.async(tyrsync._mergeOptions({ ...option, method: "PATCH" }));
    },
    delete(option) {
        return tyrax.async(tyrsync._mergeOptions({ ...option, method: "DELETE" }));
    },
    head(option) {
        return tyrax.async(tyrsync._mergeOptions({ ...option, method: "HEAD" }));
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

