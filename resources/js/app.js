import './bootstrap';
import './echo';

import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/animations/scale.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.tippy = tippy;

// Initialize tippy globally for any element with class 'has-tooltip'
document.addEventListener('alpine:init', () => {
    // Basic shared config
    const tippyConfig = {
        animation: 'scale',
        theme: 'light-border',
        content(reference) {
            return reference.getAttribute('tooltip-content') ||
                   reference.getAttribute('data-tippy-content') ||
                   reference.getAttribute('title') ||
                   reference.getAttribute('data-title');
        },
    };

    // Initialize for existing elements
    tippy('.has-tooltip', tippyConfig);

    // Observer for dynamic elements
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    if (node.classList.contains('has-tooltip')) {
                        tippy(node, tippyConfig);
                    }
                    // Also check children
                    const children = node.querySelectorAll('.has-tooltip');
                    if (children.length > 0) {
                        tippy(children, tippyConfig);
                    }
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

Alpine.start();
