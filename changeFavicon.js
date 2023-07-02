function changeFavicon(faviconPath) {
    var favicon = document.querySelector('link[rel="shortcut icon"]');
    if (!favicon) {
      favicon = document.createElement('link');
      favicon.setAttribute('rel', 'shortcut icon');
      var head = document.querySelector('head');
      head.appendChild(favicon);
    }
    favicon.setAttribute('type', 'image/x-icon');
    favicon.setAttribute('href', faviconPath);
  }