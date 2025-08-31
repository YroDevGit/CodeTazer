class Loading {
    constructor() {
        this.bodyColor = "#f3f3f3";
        this.spinnerColor = "#3498db";
        this.zindex = "999999";
        this.loaderId = "custom-loader-overlay";
        this.styleId = "custom-loader-style";
        this.ensureStyle();
    }

    ensureStyle() {
        if (!document.getElementById(this.styleId)) {
            const style = document.createElement("style");
            style.id = this.styleId;
            style.innerHTML = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                #${this.loaderId} {
                    transition: opacity 0.3s ease;
                }
            `;
            document.head.appendChild(style);
        }
    }

    load(show = true) {
        if (show) {
            if (document.getElementById(this.loaderId)) return;

            const overlay = document.createElement("div");
            overlay.id = this.loaderId;
            overlay.style.position = "fixed";
            overlay.style.top = "0";
            overlay.style.left = "0";
            overlay.style.width = "100%";
            overlay.style.height = "100%";
            overlay.style.background = "rgba(0,0,0,0.5)";
            overlay.style.display = "flex";
            overlay.style.alignItems = "center";
            overlay.style.justifyContent = "center";
            overlay.style.zIndex = this.zindex;
            overlay.style.opacity = "0";

            const spinner = document.createElement("div");
            spinner.style.width = "60px";
            spinner.style.height = "60px";
            spinner.style.border = "6px solid "+this.bodyColor;
            spinner.style.borderTop = "6px solid "+this.spinnerColor;
            spinner.style.borderRadius = "50%";
            spinner.style.animation = "spin 1s linear infinite";

            overlay.appendChild(spinner);
            document.body.appendChild(overlay);

            requestAnimationFrame(() => {
                overlay.style.opacity = "1";
            });

        } else {
            const overlay = document.getElementById(this.loaderId);
            if (overlay) {
                overlay.style.opacity = "0";
                setTimeout(() => overlay.remove(), 300);
            }
        }
    }
}

window.LOADING = new Loading();
