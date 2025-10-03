class CtrNotify {
    constructor() {
        if (Notification.permission === "default") {
            Notification.requestPermission();
        }
    }

    fire(options) {
        if (Notification.permission !== "granted") {
            console.warn("Notifications are blocked or not granted.");
            return;
        }
        let config = {};

        if (typeof options === "string") {
            config = { title: "Notification", text: options };
        } else if (typeof options === "object") {
            config = {
                title: options.title || "Notification",
                text: options.text || "",
                icon: options.icon || ""
            };
        }

        const notification = new Notification(config.title, {
            body: config.text,
            icon: config.icon
        });

        if (options?.click) {
            if(typeof options.click != "function"){
                console.error("NOTIFY click should be a function");
                return;
            }
            notification.onclick = () => {
                options?.click(window);
            };
        }

        return notification;
    }
}

window.NOTIFY = new CtrNotify();