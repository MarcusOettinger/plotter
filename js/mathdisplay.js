/*
 * mathdisplay.js: start the katex auto-renderer on pages using
 * inline maths.
 * M. Oettinger 06/2020
 *

$(function(){ */
document.addEventListener("DOMContentLoaded", function () {
	renderMathInElement(document.body, {delimiters: [
	  {left: '$', right: '$', display: false},
	  {left: '\\(', right: '\\)', display: false},
	  {left: '\\[', right: '\\]', display: true}
	]});
});
