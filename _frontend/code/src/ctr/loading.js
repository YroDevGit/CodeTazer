class CtrLoading {
    constructor(theme = "dark") {
        this.theme = theme;
        this.loaderId = "s-loader-overlay";
        this.styleId = "s-loader-style";
        this.ensureStyle();
    }

    ensureStyle() {
        if (document.getElementById(this.styleId)) return;

        const style = document.createElement("style");
        style.id = this.styleId;
        style.innerHTML = `
            :root {
                --s-color-primary: #00FFFF;
                --s-color-secondary: #ff0;
            }

            #${this.loaderId} {
                position: fixed;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                opacity: 0;
                background: ${this.theme === 'dark' ? 'rgba(0, 0, 0, 0.9)' : 'rgba(255, 255, 255, 0.9)'};
                transition: opacity 0.3s ease;
            }

            .svg-frame {
                position: relative;
                width: 344px; /* Use the size of the SVG for centering */
                height: 344px;
                transform-style: preserve-3d;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            /* --- SVG Styles and Animation Setup --- */
            .svg-frame svg {
                position: absolute;
                transition: .5s;
                z-index: calc(1 - (0.2 * var(--j)));
                transform-origin: center;
                width: 344px;
                height: 344px;
                fill: none;
                /* Initial transform for a subtle start */
                transform: rotate(0deg) skew(0deg) translateX(0px) translateY(0px);
            }

            .svg-frame:hover svg {
                 transform: rotate(-80deg) skew(30deg) translateX(calc(45px * var(--i))) translateY(calc(-35px * var(--i))); */
            }

            .svg-frame svg #center {
                transition: .5s;
                transform-origin: center;
            }

            .svg-frame:hover svg #center {
                 transform: rotate(-30deg) translateX(45px) translateY(-3px); 
            }

            /* --- Individual Animation Durations --- */
            #out1 path:nth-child(2) {
                /* Stroke for the main circle outline */
                stroke: var(--s-color-primary);
            }
            #out2 path { fill: var(--s-color-primary); }

            #out2 {
                animation: rotateS 7s ease-in-out infinite alternate;
                transform-origin: center;
            }

            #out3 {
                animation: rotateS 3s ease-in-out infinite alternate;
                transform-origin: center;
                stroke: var(--s-color-secondary);
            }

            #inner3,
            #inner1 {
                animation: rotateS 4s ease-in-out infinite alternate;
                transform-origin: center;
            }

            #center1 {
                fill: var(--s-color-secondary);
                animation: rotateS 2s ease-in-out infinite alternate;
                transform-origin: center;
            }

            #center {
                fill: var(--s-color-secondary);
                animation: rotateS 1.5s ease-in-out infinite alternate;
                transform-origin: center;
            }

            /* --- Keyframes --- */
            @keyframes rotateS {
                to {
                    transform: rotate(360deg);
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Creates and inserts the SVG HTML structure
    createSVG() {
        const svgHTML = `
            <div class="svg-frame">
                <svg style="--i:0;--j:0;">
                    <g id="out1">
                        <path d="M72 172C72 116.772 116.772 72 172 72C227.228 72 272 116.772 272 172C272 227.228 227.228 272 172 272C116.772 272 72 227.228 72 172ZM197.322 172C197.322 158.015 185.985 146.678 172 146.678C158.015 146.678 146.678 158.015 146.678 172C146.678 185.985 158.015 197.322 172 197.322C185.985 197.322 197.322 185.985 197.322 172Z"></path>
                        <path stroke-miterlimit="16" stroke-width="2" d="M72 172C72 116.772 116.772 72 172 72C227.228 72 272 116.772 272 172C272 227.228 227.228 272 172 272C116.772 272 72 227.228 72 172ZM197.322 172C197.322 158.015 185.985 146.678 172 146.678C158.015 146.678 146.678 158.015 146.678 172C146.678 185.985 158.015 197.322 172 197.322C185.985 197.322 197.322 185.985 197.322 172Z"></path>
                    </g>
                </svg>

                <svg style="--i:1;--j:1;">
                    <g id="out2">
                        <path d="M102.892 127.966C93.3733 142.905 88.9517 160.527 90.2897 178.19L94.3752 177.88C93.1041 161.1 97.3046 144.36 106.347 130.168L102.892 127.966Z"></path>
                        <path d="M93.3401 194.968C98.3049 211.971 108.646 226.908 122.814 237.541L125.273 234.264C111.814 224.163 101.99 209.973 97.2731 193.819L93.3401 194.968Z"></path>
                        <path d="M152.707 92.3592C140.33 95.3575 128.822 101.199 119.097 109.421L121.742 112.55C130.981 104.739 141.914 99.1897 153.672 96.3413L152.707 92.3592Z"></path>
                        <path d="M253.294 161.699C255.099 175.937 253.132 190.4 247.59 203.639L243.811 202.057C249.075 189.48 250.944 175.74 249.23 162.214L253.294 161.699Z"></path>
                        <path d="M172 90.0557C184.677 90.0557 197.18 92.9967 208.528 98.6474C219.875 104.298 229.757 112.505 237.396 122.621L234.126 125.09C226.869 115.479 217.481 107.683 206.701 102.315C195.921 96.9469 184.043 94.1529 172 94.1529V90.0557Z"></path>
                        <path d="M244.195 133.235C246.991 138.442 249.216 143.937 250.83 149.623L246.888 150.742C245.355 145.34 243.242 140.12 240.586 135.174L244.195 133.235Z"></path>
                        <path d="M234.238 225.304C223.932 237.338 210.358 246.126 195.159 250.604C179.961 255.082 163.79 255.058 148.606 250.534L149.775 246.607C164.201 250.905 179.563 250.928 194.001 246.674C208.44 242.42 221.335 234.071 231.126 222.639L234.238 225.304Z"></path>
                    </g>
                </svg>

                <svg style="--i:0;--j:2;">
                    <g id="inner3">
                        <path d="M195.136 135.689C188.115 131.215 179.948 128.873 171.624 128.946C163.299 129.019 155.174 131.503 148.232 136.099L148.42 136.382C155.307 131.823 163.368 129.358 171.627 129.286C179.886 129.213 187.988 131.537 194.954 135.975L195.136 135.689Z"></path>
                        <path d="M195.136 208.311C188.115 212.784 179.948 215.127 171.624 215.054C163.299 214.981 155.174 212.496 148.232 207.901L148.42 207.618C155.307 212.177 163.368 214.642 171.627 214.714C179.886 214.786 187.988 212.463 194.954 208.025L195.136 208.311Z"></path>
                    </g>
                    <path stroke="#00FFFF" d="M240.944 172C240.944 187.951 235.414 203.408 225.295 215.738C215.176 228.068 201.095 236.508 185.45 239.62C169.806 242.732 153.567 240.323 139.5 232.804C125.433 225.285 114.408 213.12 108.304 198.384C102.2 183.648 101.394 167.25 106.024 151.987C110.654 136.723 120.434 123.537 133.696 114.675C146.959 105.813 162.884 101.824 178.758 103.388C194.632 104.951 209.472 111.97 220.751 123.249" id="out3"></path>
                </svg>

                <svg style="--i:1;--j:3;">
                    <g id="inner1">
                        <path fill="#00FFFF" d="M145.949 124.51L148.554 129.259C156.575 124.859 165.672 122.804 174.806 123.331C183.94 123.858 192.741 126.944 200.203 132.236C207.665 137.529 213.488 144.815 217.004 153.261C220.521 161.707 221.59 170.972 220.09 179.997L224.108 180.665L224.102 180.699L229.537 181.607C230.521 175.715 230.594 169.708 229.753 163.795L225.628 164.381C224.987 159.867 223.775 155.429 222.005 151.179C218.097 141.795 211.628 133.699 203.337 127.818C195.045 121.937 185.266 118.508 175.118 117.923C165.302 117.357 155.525 119.474 146.83 124.037C146.535 124.192 146.241 124.349 145.949 124.51ZM224.638 164.522C224.009 160.091 222.819 155.735 221.082 151.563C217.246 142.352 210.897 134.406 202.758 128.634C194.62 122.862 185.021 119.496 175.06 118.922C165.432 118.367 155.841 120.441 147.311 124.914L148.954 127.91C156.922 123.745 165.876 121.814 174.864 122.333C184.185 122.87 193.166 126.019 200.782 131.421C208.397 136.822 214.339 144.257 217.928 152.877C221.388 161.188 222.526 170.276 221.23 179.173L224.262 179.677C224.998 174.671 225.35 169.535 224.638 164.522Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        <path fill="#00FFFF" d="M139.91 220.713C134.922 217.428 130.469 213.395 126.705 208.758L130.983 205.286L130.985 205.288L134.148 202.721C141.342 211.584 151.417 217.642 162.619 219.839C173.821 222.036 185.438 220.232 195.446 214.742L198.051 219.491C197.759 219.651 197.465 219.809 197.17 219.963C186.252 225.693 173.696 227.531 161.577 225.154C154.613 223.789 148.041 221.08 142.202 217.234L139.91 220.713ZM142.752 216.399C148.483 220.174 154.934 222.833 161.769 224.173C173.658 226.504 185.977 224.704 196.689 219.087L195.046 216.09C185.035 221.323 173.531 222.998 162.427 220.82C151.323 218.643 141.303 212.747 134.01 204.122L131.182 206.5C134.451 210.376 138.515 213.607 142.752 216.399Z" clip-rule="evenodd" fill-rule="evenodd"></path>
                    </g>
                </svg>

                <svg style="--i:2;--j:4;">
                    <path fill="var(--s-color-secondary)" d="M180.956 186.056C183.849 184.212 186.103 181.521 187.41 178.349C188.717 175.177 189.013 171.679 188.258 168.332C187.503 164.986 185.734 161.954 183.192 159.65C180.649 157.346 177.458 155.883 174.054 155.46C170.649 155.038 167.197 155.676 164.169 157.288C161.14 158.9 158.683 161.407 157.133 164.468C155.582 167.528 155.014 170.992 155.505 174.388C155.997 177.783 157.524 180.944 159.879 183.439L161.129 182.259C159.018 180.021 157.648 177.186 157.207 174.141C156.766 171.096 157.276 167.989 158.667 165.245C160.057 162.5 162.261 160.252 164.977 158.806C167.693 157.36 170.788 156.788 173.842 157.167C176.895 157.546 179.757 158.858 182.037 160.924C184.317 162.99 185.904 165.709 186.581 168.711C187.258 171.712 186.992 174.849 185.82 177.694C184.648 180.539 182.627 182.952 180.032 184.606L180.956 186.056Z" id="center1"></path>
                    <path fill="var(--s-color-secondary)" d="M172 166.445C175.068 166.445 177.556 168.932 177.556 172C177.556 175.068 175.068 177.556 172 177.556C168.932 177.556 166.444 175.068 166.444 172C166.444 168.932 168.932 166.445 172 166.445ZM172 177.021C174.773 177.021 177.021 174.773 177.021 172C177.021 169.227 174.773 166.979 172 166.979C169.227 166.979 166.979 169.227 166.979 172C166.979 174.773 169.227 177.021 172 177.021Z" id="center"></path>
                </svg>
            </div>
        `;
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = svgHTML.trim();
        return tempDiv.firstChild;
    }

    loadss() {
        if (document.getElementById(this.loaderId)) return;

        const overlay = document.createElement("div");
        overlay.id = this.loaderId;
        
        overlay.appendChild(this.createSVG());
        document.body.appendChild(overlay);

        requestAnimationFrame(() => overlay.style.opacity = "1");
    }

    load(bool = true){
        if(bool){
            this.loadss();
        }else{
            this.unload();
        }
    }

    unload() {
        const existing = document.getElementById(this.loaderId);
        if (existing) {
            existing.style.opacity = "0";
            setTimeout(() => existing.remove(), 300);
        }
    }
}

const LOADING = new CtrLoading("dark");
const Loading = LOADING;

if (typeof window !== "undefined") {
    window.LOADING = LOADING;
    window.Loading = LOADING;
}

if (typeof module !== "undefined" && typeof module.exports !== "undefined") {
    module.exports = LOADING;
    module.exports = Loading;
}
export {Loading};
export default LOADING;