function registerIframeResizer(iframe) {
    iFrameResize({
        heightCalculationMethod: 'documentElementOffset'
    }, iframe);
}

const matomoWidgetIframes = document.querySelectorAll('iframe.matomo-widget');
matomoWidgetIframes.forEach(iframe => registerIframeResizer(iframe));

const MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
const dashboard = document.getElementById('dashboard-grid');
const observer = new MutationObserver((mutations, observer) => {
    for (const mutation of mutations) {
        if (mutation.target === dashboard) return;

        const matomoWidget = mutation.type === 'childList' ? mutation.target.querySelector('.matomo-widget')
            : mutation.oldValue === 'submit btn loading' ? mutation.target.closest('.widget').querySelector('.matomo-widget')
                : null;

        if (!matomoWidget) return;

        registerIframeResizer(matomoWidget);
    }
});
observer.observe(dashboard, {
    subtree: true,
    childList: true,
    attributes: true,
    attributeOldValue: true,
    attributeFilter: [
        'class'
    ]
})