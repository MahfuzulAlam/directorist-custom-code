/**
 *   Add your custom JS here
 * */

jQuery(document).ready(function ($) {
  // Write your javascript code here
  (function() {
    const el = document.getElementById("directorist_qrcode");
    const homeUrl = window.location.origin;
    const text = el.getAttribute("text") || homeUrl;
    //const text = homeUrl;
    const width = parseInt(el.getAttribute("width")) || 256;
    const height = parseInt(el.getAttribute("height")) || 256;

    new QRCode(el, {
      text: text,
      width: width,
      height: height,
      colorDark : "#000000",
      colorLight : "#ffffff",
      correctLevel : QRCode.CorrectLevel.H
    });
  })();
});
