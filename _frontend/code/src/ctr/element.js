class CtrElement {
    constructor(element, attribute = {}) {
        this.elem = document.createElement(element);
        let txt = attribute.text ?? element;
        if (attribute) {
            for (let a in attribute) {
                if (a == 'text') {
                    continue;
                }
                this.elem.setAttribute(a, attribute[a]);
            }
        }
        if (element == "input" || element == "textarea") {
            this.elem.value = txt;
        } else {
            this.elem.innerHTML = txt;
        }
        this._injectCSS();
        return this.elem;
    }
    static make(element, attribute = {}) {
        return new CtrElement(element, attribute);
    }

    static _button(attribute, actions) {
        return new CtrElement("button", attribute);
    }

    _injectCSS() {
        if (document.getElementById("CtrElement-style")) return;
        const style = document.createElement("style");
        style.id = "CtrElement-style";
        style.textContent = `
        .ctr-element-btn {
            display: inline-block;
            font-size: 0.95rem;
            padding: 0.3rem 0.8rem;
            border-radius: 0.3rem;
            border: none;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            user-select: none;
            text-decoration: none;
          }
          .ctr-element-btn-default {
            background: #e0e0e0;
            color: #333;
          }
          .ctr-element-btn-default:hover {
            background: #d5d5d5;
          }

          .ctr-element-btn-primary {
            background: #007bff;
            color: white;
          }
          .ctr-element-btn-primary:hover {
            background: #0069d9;
          }
          
          .ctr-element-btn-success {
            background: #28a745;
            color: white;
          }
          .ctr-element-btn-success:hover {
            background: #218838;
          }
          
          .ctr-element-btn-warning {
            background: #ffc107;
            color: #212529;
          }
          .ctr-element-btn-warning:hover {
            background: #e0a800;
          }
          
          .ctr-element-btn-danger {
            background: red;
            color: white;
          }
          .ctr-element-btn-danger:hover {
            background: #c82333;
          }
          
          .ctr-element-btn-info {
            background: #17a2b8;
            color: white;
          }
          .ctr-element-btn-info:hover {
            background: #138496;
          }
          
          .ctr-element-btn-dark {
            background: #343a40;
            color: white;
          }
          .ctr-element-btn-dark:hover {
            background: #23272b;
          }
          
          .ctr-element-btn-light {
            background: #f8f9fa;
            color: #212529;
            border: 1px solid #ced4da;
          }
          .ctr-element-btn-light:hover {
            background: #e2e6ea;
          }
          
          .ctr-element-btn-outline-primary {
            background: transparent;
            color: #007bff;
            border: 1px solid #007bff;
          }
          .ctr-element-btn-outline-primary:hover {
            background: #007bff;
            color: white;
          }
          
          .ctr-element-btn:disabled,
          .ctr-element-btn.disabled {
            opacity: 0.65;
            cursor: not-allowed;
            pointer-events: none;
          }
          
          .ctr-element-btn:hover {
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
          }
        `;
        document.head.appendChild(style);
    }


    static button(attribute, actions = {}) {
        let attr = {};
        if (typeof attribute == "string") {
            attr = { ...attr, text: attribute };
        } else {
            attr = attribute;
        }
        let btn = new CtrElement("button", attr);
        if (attr.color) {
            btn.style.color = attr.color;
        }
        if (!attr.class) {
            btn.className = "ctr-element-btn";
        }
        if (attr.bg) {
            let bg = attr.bg;
            if (bg == "primary" || bg == "info" || bg == "warning" || bg == "success" || bg == "danger" || bg == "dark") {
                btn.classList.add(`ctr-element-btn-${bg}`);
            } else {
                btn.style.background = bg;
            }
        }
        if (actions) {
            console.log(typeof actions);
            if (typeof actions == "function") {
                btn.addEventListener("click", () => {
                    actions();
                });
            } else if (typeof actions == "object") {
                for (let a in actions) {
                    let call = actions[a];
                    if (typeof call !== "function") {
                        continue;
                    }
                    btn.addEventListener(a, () => {
                        call();
                    });
                }
            } else {
                console.error("ctr button actions should only be object or function");
                return;
            }
        }
        return btn;
    }

    static dropdown(attr = {}, items = []) {
        if (!CtrElement._dropdowns) CtrElement._dropdowns = [];

        if (!CtrElement._dropdownCSSInjected) {
            const style = document.createElement("style");
            style.textContent = `
                .ctr-dropdown-toggle.ctr-dropdown-active {
                    font-weight:bold;
                    color:red;
                }
            `;
            document.head.appendChild(style);
            CtrElement._dropdownCSSInjected = true;
        }

        const wrapper = document.createElement("div");
        wrapper.classList.add("ctr-dropdown");
        wrapper.style.position = "relative";
        wrapper.style.display = "inline-block";

        const btn = document.createElement("span");
        btn.classList.add("ctr-dropdown-toggle");
        btn.textContent = attr.text || "⋮";
        btn.style.cursor = "pointer";
        Object.entries(attr).forEach(([k, v]) => {
            if (k !== "text") btn.setAttribute(k, v);
        });
        wrapper.appendChild(btn);

        const menu = document.createElement("div");
        menu.classList.add("ctr-dropdown-menu");
        Object.assign(menu.style, {
            position: "absolute",
            top: "0",
            left: "0",
            background: "#fff",
            boxShadow: "0 2px 6px rgba(0,0,0,0.15)",
            borderRadius: "6px",
            display: "none",
            zIndex: 9999,
            minWidth: "120px",
            overflow: "hidden"
        });

        items.forEach(item => {
            const menuItem = document.createElement("div");
            menuItem.classList.add("ctr-dropdown-item");
            menuItem.innerHTML = item.text;
            Object.assign(menuItem.style, {
                padding: "8px 12px",
                cursor: "pointer",
                userSelect: "none"
            });
            menuItem.addEventListener("click", e => {
                e.stopPropagation();
                menu.style.display = "none";
                btn.classList.remove("ctr-dropdown-active"); // remove highlight
                if (typeof item.action === "function") item.action();
            });
            menuItem.addEventListener("mouseover", () => menuItem.style.background = "#f2f2f2");
            menuItem.addEventListener("mouseout", () => menuItem.style.background = "");
            menu.appendChild(menuItem);
        });

        document.body.appendChild(menu);
        CtrElement._dropdowns.push({ menu, btn });

        btn.addEventListener("click", e => {
            e.stopPropagation();
            CtrElement._dropdowns.forEach(({ menu: m, btn: b }) => {
                if (m !== menu) {
                    m.style.display = "none";
                    b.classList.remove("ctr-dropdown-active");
                }
            });

            const isVisible = menu.style.display === "block";
            menu.style.display = isVisible ? "none" : "block";
            btn.classList.toggle("ctr-dropdown-active", !isVisible);

            const rect = btn.getBoundingClientRect();
            const menuHeight = menu.offsetHeight;
            const menuWidth = menu.offsetWidth;
            const viewportHeight = window.innerHeight;

            let left = rect.right - menuWidth;
            if (left < 0) left = 0;
            menu.style.left = left + "px";

            if (rect.bottom + menuHeight > viewportHeight) {
                menu.style.top = (rect.top - menuHeight) + "px";
            } else {
                menu.style.top = rect.bottom + "px";
            }
        });

        document.addEventListener("click", () => {
            menu.style.display = "none";
            btn.classList.remove("ctr-dropdown-active");
        });

        return wrapper;
    }

    static menu(attr = {}, items = []) {
        if (!CtrElement._menuCSSInjected) {
            const style = document.createElement("style");
            style.textContent = `
                .ctr-menu-modal {
                    position: fixed;
                    top: 0; left: 0;
                    width: 100%; height: 100%;
                    background: rgba(0,0,0,0.4);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 10000;
                }
                .ctr-menu-content {
                    background: #fff;
                    border-radius: 8px;
                    min-width: 200px;
                    max-width: 90%;
                    max-height: 80%;
                    overflow-y: auto;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                    position: relative;
                }
                .ctr-menu-close {
                    display: block;
                    width: 100%;
                    text-align: right;
                    padding: 10px 16px;
                    font-weight: bold;
                    font-size: 18px;
                    cursor: pointer;
                    color: #0d6efd; /* primary light blue */
                    border-bottom: 1px solid #ddd;
                    box-sizing: border-box;
                }
                .ctr-menu-close:hover {
                    color: #084298;
                }
                .ctr-menu-item {
                    padding: 10px 16px;
                    cursor: pointer;
                    user-select: none;
                }
                .ctr-menu-item:hover {
                    background: #f2f2f2;
                }
                .ctr-menu-separator {
                    height: 1px;
                    width: 100%;
                    background: #ddd;
                    margin: 0;
                }
                .ctr-menu-toggle-active {
                    font-weight: bold;
                    color: red;
                }
            `;
            document.head.appendChild(style);
            CtrElement._menuCSSInjected = true;
        }

        const wrapper = document.createElement("div");
        wrapper.classList.add("ctr-menu-wrapper");
        wrapper.style.display = "inline-block";

        const btn = document.createElement("span");
        btn.classList.add("ctr-menu-toggle");
        btn.textContent = attr.text || "⋮";
        btn.style.cursor = "pointer";
        Object.entries(attr).forEach(([k, v]) => {
            if (k !== "text") btn.setAttribute(k, v);
        });
        wrapper.appendChild(btn);

        btn.addEventListener("click", e => {
            e.stopPropagation();
            btn.classList.add("ctr-menu-toggle-active");

            const modal = document.createElement("div");
            modal.classList.add("ctr-menu-modal");

            const content = document.createElement("div");
            content.classList.add("ctr-menu-content");

            const closeBtn = document.createElement("div");
            closeBtn.classList.add("ctr-menu-close");
            closeBtn.innerHTML = "&times;";
            closeBtn.addEventListener("click", () => {
                document.body.removeChild(modal);
                btn.classList.remove("ctr-menu-toggle-active");
            });
            content.appendChild(closeBtn);

            items.forEach((item, index) => {
                if (index > 0) {
                    const sep = document.createElement("div");
                    sep.classList.add("ctr-menu-separator");
                    content.appendChild(sep);
                }

                const menuItem = document.createElement("div");
                menuItem.classList.add("ctr-menu-item");
                menuItem.innerHTML = item.text;
                menuItem.addEventListener("click", ev => {
                    ev.stopPropagation();
                    if (typeof item.action === "function") item.action();
                    document.body.removeChild(modal);
                    btn.classList.remove("ctr-menu-toggle-active");
                });
                content.appendChild(menuItem);
            });

            modal.appendChild(content);
            document.body.appendChild(modal);

            modal.addEventListener("click", ev => {
                if (ev.target === modal) {
                    document.body.removeChild(modal);
                    btn.classList.remove("ctr-menu-toggle-active");
                }
            });
        });

        return wrapper;
    }

    set_attribute(array) {
        if (array) {
            for (let a in array) {
                this.elem.setAttribute(a, array[a]);
            }
        }
    }
}

export default CtrElement;