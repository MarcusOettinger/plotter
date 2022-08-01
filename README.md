[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://stand-with-ukraine.pp.ua)

# plotter
a simple function plotter based on php/javascript

### About
plotter is an empirical plotting tool to draw curves of mathematical expressions (usually these will be functions) and some additional points as an image (in gif, jpeg or png format). Currently, plotter can draw up to 3 curves and 10 named points in cartesian coordinates and can do a bit of differential calculus. Here are some [examples of plotter-created curves on github.io](https://marcusoettinger.github.io/plotter).

The images can be drawn on the fly: after a plot is created and formatted in the UI part, plotter will create an URL (a really ugly one with a ridiculously long query string containing all the required definitions and settings to draw the image). The plot will then be recreated whenever this URL is called - it can be copied into an image-tag to use a dynamically created plot on every page load or can be used in links.

Because the URL is long and ugly, the possibility to use an URL-shortener (using [tinyURL](https://tinyurl.com/) or [YOURLS](https://yourls.org/)) is built in - although this might need some fiddling to get it to work.

The plotter itself is free of cookies or any analytical stuff and uses reasonable CSP ([Content-Security-Policy](https://content-security-policy.com/)) headers to enhance the security of the page in modern browsers.

### Prerequisites
To run a plotter, a standard installation of a webserver capable of interpreting PHP (>=4.3 - that is untested) with an enabled GD extension (at least version 2) is needed.

Plotter uses the [katex math typesetting library](https://katex.org/) (which is loaded from a cdn by default) to print maths on the manual and example page and obviously uses javaScript (mostly for user interaction).

### License
This project is published as open source licensed under the terms of the Gnu General Public License, version 2 ([GNU GPL v.2](https://www.gnu.org/licenses/gpl-2.0.html)) license.
