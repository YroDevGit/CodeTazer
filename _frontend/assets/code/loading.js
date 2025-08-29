class loading {
    constructor() {
        this.loaderId = "custom-loader-overlay";
    }

    load(show = true) {
        if (show) {
            // Prevent multiple loaders
            if (document.getElementById(this.loaderId)) return;

            // Create overlay
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
            overlay.style.zIndex = "9999";

            // Spinner
            const spinner = document.createElement("div");
            spinner.style.width = "60px";
            spinner.style.height = "60px";
            spinner.style.border = "6px solid #f3f3f3";
            spinner.style.borderTop = "6px solid #3498db";
            spinner.style.borderRadius = "50%";
            spinner.style.animation = "spin 1s linear infinite";

            // Spinner animation
            const style = document.createElement("style");
            style.innerHTML = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);

            overlay.appendChild(spinner);
            document.body.appendChild(overlay);

        } else {
            const overlay = document.getElementById(this.loaderId);
            if (overlay) overlay.remove();
        }
    }
}