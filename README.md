# plotter
a simple function plotter based on php/javascript

### About
plotter is an empirical plotting tool to draw curves of mathematical expressions (usually these will be functions) as images (in gif, jpeg or png format). Currently, plotter draws up to 3 curves in cartesian coordinates and can do a bit of differential calculus. Here's an [example of a plotter-created curve on github.io](https://marcusoettinger.github.io/plotter).

The images can be drawn on the fly: after the desired plot is created and formatted in the UI part, plotter creates an URL (a really ugly one with a ridiculously long query string containing all required options). The plot will then be recreated whenever this URL is called - copy the URL into the src-attribute of an image-tag to use a  dynamically created plot on every page load. 

### Prerequisites
To run a plotter, a standard installation of a webserver capable of interpreting PHP (>=4.3 - that is untested) with an enabled GD extension is needed.

Plotter depends on [jQuery](https://jquery.com) and [jQuery UI](https://jqueryui.com) (which are loaded from a cdn by default) and obviously uses javaScript (mostly for user interaction).

### License
This project is published as open source licensed under the terms of the Gnu General Public License, version 2 ([GNU GPL v.2](https://www.gnu.org/licenses/gpl-2.0.html)) license.
