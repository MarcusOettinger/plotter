TAR=/bin/tar
CP=/bin/cp
RM=/bin/rm
MKDIR=/bin/mkdir
include VERSION
TARBALL=plotter.$(VERSION).tar.gz

PKG_FILES=INSTALL.txt FreeSans.ttf Makefile common.inc config.inc.default diffint.inc examples.html function.inc function.php gnu_gpl.txt graph.php helpers.php images/ index.php init.php js/ manual.html manual_menu.inc openPlaG3_1.tar.gz plotstyle.css short_tinyurl.inc short_yourls.inc single.php VERSION

all: tarball clean

tarball: $(PKG_FILES)
	test -d plotter_package || $(MKDIR) plotter_package
	cp -R $(PKG_FILES) plotter_package/
	$(TAR) -cvzf $(TARBALL) plotter_package

clean:
	$(RM) -rf plotter_package

realclean: clean
	$(RM) $(TARBALL)

love:
	echo "Not war?"
