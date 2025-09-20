class Path {
    constructor(rootpath = "http://localhost:9999") {
        this.global_root = rootpath;
        this.frontend = "?page=";
        this.backend = "?be=";
        this.func = "?funcpage=";
    }

    page($page = "", params = {}) {
        let url = this.frontend + $page;
        if (typeof params === "object" && Object.keys(params).length > 0) {
            const query = Object.entries(params)
                .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
                .join("&");
            url += "?" + query;
        }
        return url;
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
}

window.PATH = new Path();
window.PATHS = new Path();
