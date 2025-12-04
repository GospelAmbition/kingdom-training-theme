import type { Plugin } from 'vite';

/**
 * Vite plugin to make CSS load asynchronously to prevent render blocking
 */
export function asyncCss(): Plugin {
  return {
    name: 'async-css',
    transformIndexHtml(html) {
      // Make main CSS file load asynchronously
      // Note: Google Fonts is handled directly in the HTML template
      html = html.replace(
        /<link\s+rel="stylesheet"[^>]*href="([^"]*main-[^"]*\.css)"[^>]*>/g,
        (match, href) => {
          // Preload the CSS and load it asynchronously
          return `<link rel="preload" href="${href}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="${href}"></noscript>`;
        }
      );

      // Add a polyfill for browsers that don't support onload on link tags
      // This should be added before the closing </head> tag
      if (html.includes('onload=')) {
        html = html.replace(
          '</head>',
          `<script>
/*! loadCSS polyfill for browsers that don't support onload on link tags */
(function(w){"use strict";if(!w.loadCSS){var loadCSS=function(href,before,media){var doc=w.document;var ss=doc.createElement("link");var ref;if(before){ref=before}else{var refs=(doc.body||doc.getElementsByTagName("head")[0]).childNodes;ref=refs[refs.length-1]}var sheets=doc.styleSheets;ss.rel="stylesheet";ss.href=href;ss.media="only x";function ready(cb){if(doc.body){return cb()}setTimeout(function(){ready(cb)})}ready(function(){ref.parentNode.insertBefore(ss,before?ref:ref.nextSibling)});var onloadcssdefined=function(cb){var resolvedHref=ss.href;var i=sheets.length;while(i--){if(sheets[i].href===resolvedHref){return cb()}}setTimeout(function(){onloadcssdefined(cb)})};function onloadcss(){ss.onloadcss=function(){};ss.media=media||"all"};if(ss.addEventListener){ss.addEventListener("load",onloadcss)}ss.onloadcssdefined=onloadcssdefined;onloadcssdefined(onloadcss);return ss};w.loadCSS=loadCSS}})(typeof global!=="undefined"?global:this);
</script>
</head>`
        );
      }

      return html;
    },
  };
}

