class CtrTodal {
    constructor(selector) {
        this.modal = document.querySelector(selector);
        //if (!this.modal) throw new Error(`Todal: element ${selector} not found`);
        this._injectCSS();
    }

    static init(selector) {
        return new CtrTodal(selector);
    }

    static get backdrop() {
        if (!CtrTodal._backdrop) {
            const bd = document.createElement("div");
            bd.className = "todal-backdrop";
            document.body.appendChild(bd);
            CtrTodal._backdrop = bd;
        }
        return CtrTodal._backdrop;
    }

    static bindGlobalButtons() {
        const buttons = document.querySelectorAll('[todal-type="hide"], [todal-type="show"], [todal-type="close"]');
        buttons.forEach((btn) => {
            const type = btn.getAttribute("todal-type");
            const target = btn.getAttribute("todal-target");
            if (!type || !target) return;

            const modalSelector = target.startsWith("#") ? target : `#${target}`;
            const modal = document.querySelector(modalSelector);
            if (!modal) return;

            const instance = new CtrTodal(modalSelector);
            btn.addEventListener("click", () =>
                type === "show" ? instance.show() : instance.hide()
            );
        });
    }

    _injectCSS() {
        if (document.getElementById("todal-style")) return;
        const style = document.createElement("style");
        style.id = "todal-style";
        style.textContent = `
            body.todal-open { overflow: hidden; }
            .todal {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.9);
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
                z-index: 99999;
                opacity: 0;
                transition: opacity 0.25s ease, transform 0.25s ease;
                max-width: 450px;
                width: 90%;
                display: none;
                font-family: system-ui, sans-serif;
            }
            .todal-form-control {
                display: block;
                width: 100%;
                height: calc(1.5em + .75rem + 2px);
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.5;
                color: #495057;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #ced4da;
                border-radius: .25rem;
                padding: 0px 8px;
                box-sizing: border-box;
                transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            }
        
            .todal-form-control:focus {
                border-color: #80bdff;
                outline: 0;
                box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
            }
        
            .todal-form-group {
                margin-bottom: 0.7rem;
            }
            .todal-show {
                display: block;
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
            .todal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(2px);
                z-index: 99998;
                opacity: 0;
                transition: opacity 0.25s ease;
            }
            .todal-backdrop.show { opacity: 1; }
            .todal-header {
                padding: 0.5rem 1rem;
                background: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .todal-header h3 { margin: 0; font-size: 1.1rem; }
            .todal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                color: #6c757d;
                line-height: 1;
            }
            .todal-close:hover { color: #000; }
            .todal-body { padding: 1rem; }
            .todal-footer {
                padding: 0.5rem 1rem;
                background: #f8f9fa;
                border-top: 1px solid #dee2e6;
                text-align: right;
            }
            .todal-footer button {
                background: #0d6efd;
                color: white;
                border: none;
                padding: 0.4rem 0.8rem;
                border-radius: 4px;
                font-size: 0.9rem;
                cursor: pointer;
            }
            .todal-footer button:hover { background: #0b5ed7; }
        `;
        document.head.appendChild(style);
    }

    show(attribute = {}) {
        if (attribute) {
            for (let a in attribute) {
                if (a == "id" || a == "class" || a == "todal-atr") {
                    continue;
                }
                this.modal.setAttribute(a, attribute[a]);
            }
        }
        if (CtrTodal.current && CtrTodal.current !== this) {
            CtrTodal.current.hide();
        }
        CtrTodal.current = this;

        const backdrop = CtrTodal.backdrop;
        requestAnimationFrame(() => backdrop.classList.add("show"));

        this.modal.style.display = "block";
        requestAnimationFrame(() => this.modal.classList.add("todal-show"));
        document.body.classList.add("todal-open");

        backdrop.addEventListener("click", () => this.hide(), { once: true });

        this._escHandler = (e) => e.key === "Escape" && this.hide();
        document.addEventListener("keydown", this._escHandler);
    }

    set_id(id) {
        this.modal.setAttribute("todal-atr", id);
    }

    get_id() {
        return this.modal.getAttribute("todal-atr");
    }

    hide() {
        if (!this.modal.classList.contains("todal-show")) return;

        const backdrop = CtrTodal._backdrop;

        this.modal.classList.remove("todal-show");
        document.body.classList.remove("todal-open");

        if (backdrop) {
            backdrop.classList.remove("show");
            backdrop.addEventListener(
                "transitionend",
                () => {
                    backdrop.remove();
                    CtrTodal._backdrop = null;
                },
                { once: true }
            );
            setTimeout(() => {
                if (document.body.contains(backdrop)) {
                    backdrop.remove();
                    CtrTodal._backdrop = null;
                }
            }, 300);
        }

        setTimeout(() => {
            this.modal.style.display = "none";
        }, 200);

        document.removeEventListener("keydown", this._escHandler);

        if (CtrTodal.current === this) {
            CtrTodal.current = null;
        }
    }
}

const Todal = CtrTodal;
if (typeof window !== "undefined") {
    window.Todal = Todal;
    document.addEventListener("DOMContentLoaded", () => Todal.bindGlobalButtons());
}
if (typeof module !== "undefined" && typeof module.exports !== "undefined") {
    module.exports = Todal;
}
export default Todal;


/**
 *Usage 
 * <button parent="todal" todal-type="open" todal-target="#tdl">click me</button>
    <div id="tdl" class="todal">
        <div class="todal-header">
            <h3>My Todal</h3>
            <button class="todal-close" parent="todal" todal-type="close" todal-target="#tdl">×</button>
        </div>
        <div class="todal-body">
            <p>This is a Todal modal window!</p>
        </div>
        <div class="todal-footer">
            <button parent="todal" todal-type="close" todal-target="#tdl">Close</button>
        </div>
    </div>
 */
