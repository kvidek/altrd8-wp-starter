/**
 * MAIN JS FILE
 */

/**
 * Helpers
 */
import Grid from "./helpers/Grid";

/**
 * Components
 */
import "instant.page";
import Lazy from "./components/common/Lazy";
import ResponsiveVideo from "./components/common/ResponsiveVideo";
import VideoOnScroll from "./components/common/VideoOnScroll";
import VideoPlayButton from "./components/common/VideoPlayButton";
import ScrollToAnimation from "./components/animations/ScrollToAnimation";
import AccordionAnimation from "./components/animations/AccordionAnimation";
import Navigation from "./components/common/Navigation";
import Modal from "./components/modals/Modal";

/**
 * Check if the document is ready cross-browser
 * @param callback
 */
const ready = (callback) => {
    if (document.readyState !== "loading") {
        /**
         * The document is already ready, call the callback directly
         */
        callback();
    } else if (document.addEventListener) {
        /**
         * All modern browsers to register DOMContentLoaded
         */
        document.addEventListener("DOMContentLoaded", callback);
    } else {
        /**
         * Old IE browsers
         */
        document.attachEvent("onreadystatechange", function () {
            if (document.readyState === "complete") {
                callback();
            }
        });
    }
};

/**
 * Document ready callback
 */
ready(() => {
    /**
     * CREDITS
     */
    const credits = [
        "background-color: #000000",
        "color: white",
        "display: block",
        "line-height: 24px",
        "text-align: center",
        "border: 1px solid #ffffff",
        "font-weight: bold",
    ].join(";");
    console.info("dev by: %c Bornfight Studio ", credits);

    /**
     * HELPERS
     */

    /**
     * Grid
     * @type {Grid}
     */
    const grid = new Grid();
    grid.init();

    /**
     * COMPONENTS
     */

    /**
     * Lazy
     * @type {Lazy}
     */
    const lazy = new Lazy();
    lazy.init();

    /**
     * Responsive video
     * @type {ResponsiveVideo}
     */
    const responsiveVideo = new ResponsiveVideo();
    responsiveVideo.init();

    /**
     * Video on scroll
     * @type {VideoOnScroll}
     */
    const videoOnScroll = new VideoOnScroll();
    videoOnScroll.init();

    /**
     * Video play button
     * @type {VideoPlayButton}
     */
    const videoPlayButton = new VideoPlayButton();
    videoPlayButton.init();

    /**
     * Scroll to animation
     * @type {ScrollToAnimation}
     */
    const scrollToAnimation = new ScrollToAnimation();
    scrollToAnimation.init();

    /**
     * Navigation
     * @type {Navigation}
     */
    const navigation = new Navigation();
    navigation.init();

    /**
     * Footer accordion initial state — only the first column starts
     * expanded on mobile, but every column must start expanded on desktop
     * (where the toggle is CSS-hidden — see .c-footer__column-toggle's
     * mq(lg) in _components.footer.scss), since AccordionAnimation has no
     * breakpoint awareness of its own: a column not marked is-initially-active
     * gets aria-hidden="true" on every viewport, not just the one it's meant
     * for. Must run before accordionAnimation.init() below, which only reads
     * is-initially-active once, at init time.
     * 1140px matches $breakpoint-lg in _settings.breakpoint.scss.
     */
    const footerColumns = document.querySelectorAll(".c-footer .js-accordion-single");
    if (footerColumns.length) {
        const isMobile = window.matchMedia("(max-width: 1140px)").matches;
        footerColumns.forEach((column, index) => {
            if (!isMobile || index === 0) {
                column.classList.add("is-initially-active");
            }
        });
    }

    /**
     * Accordion animation
     * Also powers the mobile navigation's submenu expand/collapse
     * (.js-accordion-* on partials/layout/mobile-navigation.php) and the
     * footer's collapsible columns (see the viewport check above).
     * @type {AccordionAnimation}
     */
    const accordionAnimation = new AccordionAnimation();
    accordionAnimation.init();

    /**
     * Modal
     * Also powers the mobile navigation dialog (#mobile-navigation,
     * opened via the header hamburger's data-modal-open attribute) and
     * any CTA with an "Open modal" action (see App\helpers\CtaHelper).
     * @type {Modal}
     */
    const hamburger = document.querySelector(".js-hamburger");
    const modal = new Modal({
        afterOpen: (modalEl) => {
            if (modalEl.id === "mobile-navigation" && hamburger) {
                hamburger.classList.add("is-active");
            }
        },
        afterClose: (modalEl) => {
            if (modalEl.id === "mobile-navigation" && hamburger) {
                hamburger.classList.remove("is-active");
            }
        },
    });
    modal.init();
});
