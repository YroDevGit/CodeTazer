(() => {
  /**
   * CodeTazer importer, import script at the head of the document
   * this is attached using import_script
   */
  queueMicrotask(() => {
    const current = Array.from(document.querySelectorAll('script[type="module"]'))
      .find(s => s.src.split('#')[0].split('?')[0] === import.meta.url.split('#')[0].split('?')[0]);
    if (!current) return;

    const imports = JSON.parse(current.getAttribute('data-import') || '[]');
    const head = document.head || document.getElementsByTagName("head")[0];
    window.__loadedModules ??= new Set();

    imports.forEach(src => {
      if (!window.__loadedModules.has(src)) {
        window.__loadedModules.add(src);
        const s = document.createElement("script");
        s.type = "module";
        s.src = src;
        s.onerror = () => console.error(`[importer.js] Failed to load: ${src}`);
        head.appendChild(s);
      }
    });

    current.remove();
  });
})();
