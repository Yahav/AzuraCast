import "~/scss/style.scss";

import * as bootstrap from "bootstrap";

const ready = (callback) => {
    if (document.readyState !== "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
}

ready(() => {
    // If in a frame, notify the parent frame of the frame dimensions.
    if (window.self !== window.top) {
        let docHeight = 0;
        let docWidth = 0;

        const postSizeToParent = () => {
            if (document.body.scrollHeight !== docHeight || document.body.scrollWidth !== docWidth) {
                docHeight = document.body.scrollHeight;
                docWidth = document.body.scrollWidth;

                const message = {height: docHeight, width: docWidth};
                window.top.postMessage(message, "*");
            }
        }

        postSizeToParent();
        document.addEventListener("vue-ready", postSizeToParent);

        const mainElem = document.querySelector('main');
        const resizeObserver = new ResizeObserver(postSizeToParent);
        resizeObserver.observe(mainElem);
    }
});

export default bootstrap;
