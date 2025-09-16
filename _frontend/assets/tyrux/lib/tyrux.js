export class Tyrux {
    /**
     * @Author : Tyrone Limen Malocon
     * @Created : Aug 6 2025
     * @Country : Philippines
     * @Email : tyronemalocon@gmail.com
     */
    #defaultHeaders = {

    };
    #baseURL = "";
    #config = {};

    constructor(config = {}) {
        if (config.headers) {
            let headers = config.headers;
            this.#defaultHeaders = { ...this.#defaultHeaders, ...headers };
        }
        if (config.baseURL && config.baseURL != null && config.baseURL !== "") {
            this.#baseURL = config.baseURL;
        }
        this.#config = config;
    }

    request(options) {
        const xhr = new XMLHttpRequest();
        const method = options.method ? options.method.toUpperCase() : "GET";
        let link = options.url ?? options.route ?? options.backend;
        let url = this.#baseURL + link;
        let data = null;

        const headers = options.headers
            ? { ...this.#defaultHeaders, ...options.headers }
            : this.#defaultHeaders;
        const contentType = headers["Content-Type"] || "";

        options.before?.(xhr);
        options.wait?.(xhr);
        options.pending?.(xhr);

        if (options.progress) {
            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    options.progress(percent, "upload", event, xhr);
                }
            };

            xhr.onprogress = (event) => {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    options.progress(percent, "download", event, xhr);
                }
            };
        } else {
            if (options.uploading) {
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percent = Math.round((event.loaded / event.total) * 100);
                        options.uploading(percent, "upload", event, xhr);
                    }
                };
            }
            if (options.downloading) {
                xhr.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percent = Math.round((event.loaded / event.total) * 100);
                        options.downloading(percent, "download", event, xhr);
                    }
                };
            }
        }

        options.data = options.data ?? options.request ?? null;
        if (options.data instanceof FormData) {
            data = options.data;
            delete headers["Content-Type"];
        } else if (options.data && typeof options.data === "object") {
            if (method === "GET") {
                const params = Object.keys(options.data)
                    .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(options.data[key])}`)
                    .join("&");
                url += (url.includes("?") ? "&" : "?") + params;
            } else {
                if (contentType.includes("application/json")) {
                    data = JSON.stringify(options.data);
                } else if (contentType.includes("application/x-www-form-urlencoded")) {
                    data = Object.keys(options.data)
                        .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(options.data[key])}`)
                        .join("&");
                } else {
                    data = options.data;
                }
            }
        }

        if (options.test && options.test == true) {
            this.createModal({ url, method, headers }, data);
            return;
        }

        xhr.open(method, url, true);

        for (const h in headers) {
            xhr.setRequestHeader(h, headers[h]);
        }

        xhr.onreadystatechange = () => {
            let status = xhr.readyState;
            if (status === 4) {
                let responseData = xhr.responseText;
                const respContentType = xhr.getResponseHeader("Content-Type") || "";

                if (respContentType.includes("application/json")) {
                    try {
                        responseData = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.warn("Failed to parse JSON response:", e);
                    }
                }

                if (xhr.status >= 200 && xhr.status < 300) {
                    options.success?.(responseData, xhr);
                    options.response?.(responseData, xhr);
                    options.ok?.(responseData, xhr);
                } else {
                    if (this.#config?.error) {
                        if (this.#config.error === "console") {
                            console.error(responseData.message ?? xhr.statusText);
                        }
                        if (this.#config.error === "alert") {
                            alert(responseData.message ?? xhr.statusText);
                        }
                        if (this.#config.error === "log") {
                            console.log(responseData.message ?? xhr.statusText);
                        }
                    }
                    options.error?.(responseData, xhr);
                }
                options.finally?.(responseData, xhr);
                options.ready?.(responseData, xhr);
                options.done?.(responseData, xhr);
            }
        };

        xhr.send(data);
    }


    createModal({ url, method, headers }, body) {
        const old = document.getElementById("reqModal");
        if (old) old.remove();

        const modal = document.createElement("div");
        modal.id = "reqModal";
        modal.style.cssText = `
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999;
    font-family: Arial, sans-serif;
    color: #222; /* dark text */
  `;

        let bodyContent = "";
        let queryParams = null;
        if (body) {
            if (typeof body === "string") {
                try {
                    bodyContent = JSON.stringify(JSON.parse(body), null, 2);
                } catch {
                    bodyContent = body;
                }
            } else if (body instanceof FormData) {
                const obj = {};
                body.forEach((v, k) => {
                    if (v instanceof File) {
                        obj[k] = {
                            filename: v.name,
                            size: v.size + " bytes",
                            type: v.type || "unknown"
                        };
                    } else {
                        obj[k] = v;
                    }
                });
                bodyContent = JSON.stringify(obj, null, 2);
            } else if (body instanceof URLSearchParams) {
                const obj = {};
                body.forEach((v, k) => (obj[k] = v));
                bodyContent = JSON.stringify(obj, null, 2);
            } else if (typeof body === "object") {
                try {
                    bodyContent = JSON.stringify(body, null, 2);
                } catch {
                    bodyContent = String(body);
                }
            }
        } else if (method.toUpperCase() === "GET") {
            try {
                const u = new URL(url, window.location.origin);
                const obj = {};
                u.searchParams.forEach((v, k) => (obj[k] = v));
                if (Object.keys(obj).length > 0) {
                    queryParams = JSON.stringify(obj, null, 2);
                }
                bodyContent = JSON.parse(queryParams);

                bodyContent = JSON.stringify(bodyContent, null, 2);
                url = url.replace(/\s+/g, "");

            } catch {

            }
        }
        let prettyBody = bodyContent;

        let prettyHeaders = "";
        if (headers) {
            if (headers instanceof Headers) {
                const obj = {};
                headers.forEach((v, k) => (obj[k] = v));
                prettyHeaders = JSON.stringify(obj, null, 2);
            } else {
                prettyHeaders = JSON.stringify(headers, null, 2);
            }
        } else {
            prettyHeaders = "—";
        }

        modal.innerHTML = `
    <div style="background: #fff; padding: 20px; width: 600px; max-height: 80vh; overflow:auto; border-radius:10px; box-shadow: 0 5px 25px rgba(0,0,0,0.3); position:relative;">
        <!-- X button -->
        <button id="closexModax2f3787473b847b8v3tyroneleeemzmodal23" 
            style="position:absolute; top:10px; right:10px; background:transparent; border:none; font-size:20px; font-weight:bold; cursor:pointer; color:#666;">
            ✖
        </button>
        <h2 style="margin-bottom: 10px; color:#111;">📡 Tyrux Request</h2>
        <div style="margin-bottom:10px;">
            <strong style="color:#444;">Method:</strong>
            <code style="background:#eee; padding:2px 6px; border-radius:4px;">${method}</code>
        </div>
        <div style="margin-bottom:10px;">
            <strong style="color:#444;">URL/Route:</strong>
            <div style="background:#f6f6f6; padding:8px; border-radius:4px; font-family:monospace; overflow-wrap:anywhere;">${url}</div>
        </div>
        <div style="margin-bottom:10px;">
            <strong style="color:#444;">Headers:</strong>
            <pre style="background:#f6f6f6; padding:8px; border-radius:4px; font-family:monospace; white-space:pre-wrap; color:#222; max-height:140px; overflow-y:scroll;">${prettyHeaders}</pre>
        </div>
        <div style="margin-bottom:10px;">
            <strong style="color:blue;">Request body:</strong>
            <pre style="background:#f6f6f6; padding:8px; border-radius:4px; font-family:monospace; white-space:pre-wrap; color:#222; max-height:210px; height:200px; overflow-y:scroll; color:blue;">${prettyBody}</pre>
        </div>
    </div>
`;

        document.body.appendChild(modal);

        document.getElementById("closexModax2f3787473b847b8v3tyroneleeemzmodal23").onclick = () => { modal.remove(); location.reload() };
    }

    post(option) {
        option.method = "POST";
        this.request(option);
    }

    put(option) {
        option.method = "PUT";
        this.request(option);
    }

    get(option) {
        option.method = "GET";
        this.request(option);
    }

    patch(option) {
        option.method = "PATCH";
        this.request(option);
    }

    delete(option) {
        option.method = "DELETE";
        this.request(option);
    }

    head(option) {
        option.method = "HEAD";
        this.request(option);
    }

    option(option) {
        option.method = "OPTIONS";
        this.request(option);
    }

}
