class Path {
    constructor(rootpath = "http://localhost:9999") {
        this.global_root = rootpath;
        this.frontend = "?page=";
        this.backend = "?be=";
        this.func = "?funcpage=";
    }

    page($page = "", params = {}) {
        let url = this.frontend + encodeURIComponent($page);

        const query = Object.entries(params)
            .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
            .join("&");

        if (query) {
            url += "&" + query;
        }

        return url;
    }

    funcpage($page = "", params = {}) {
        let url = this.func + encodeURIComponent($page);
        const query = Object.entries(params)
            .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
            .join("&");

        if (query) {
            url += "&" + query;
        }

        return url;
    }

    backend($be = "", params = {}) {
        let url = this.backend + encodeURIComponent($be);
        const query = Object.entries(params)
            .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
            .join("&");

        if (query) {
            url += "&" + query;
        }
        return url;
    }
}

window.PATH = new Path();
window.PATHS = new Path();
