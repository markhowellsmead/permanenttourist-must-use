(function () {
    const darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    if (typeof _paq !== 'undefined') {
        console.log('_paq', _paq);
        _paq.push(['trackEvent', 'VisitorPreference', 'DarkModeOS', darkMode]);
    }
})();
