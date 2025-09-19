class Path {
    constructor(rootpath = "http://localhost:9999") {
        this.global_root = rootpath;
        this.frontend = "?page=";
        this.backend = "?be=";
        this.func = "?funcpage=";
    }

    page($page = "") {
        return this.frontend + $page;
    }

    funcpage($page = "") {
        return this.func + $page;
    }

    backend($be) {
        return this.frontend + $be;
    }
}

window.PATH = new Path();