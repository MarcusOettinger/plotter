<h3>Calculate, Link &amp; Save</h3><div>
  <div id="calculate">
  <p class="noprinting">Enter an expression and some variable values below, then hit calculate
  to produce sample points</p>
  <div class="info" id="info3">
    <p>Enter some variable values and a formula to be calculated, 
    functions defined for the plot above can be copied using one of 
    the buttons labeled f(x), g(x) or h(x). Variables will usually be 
    simple numbers, separate multiple input values (x) by spaces.</p>
    <p>Select one of the checkboxes to chose a format for your samples:</p>
    <ul class="lalign"><li>results - a string of function values, separated by spaces</li>
      <li>table - a simple html table including variable values</li>
      <li>csv - colon separated values including variable values</li>
      <li>latex - code to layout a table including variable values in laTeX</li>
    </ul>
  </div>
  <form method="post" action="single.php" id="singleform" target="single" autocomplete="off">
    <fieldset><legend><img class="bt" id="infobutton3" src="images/iconInfo.png">
    calculate discrete values: </legend>
    <table><tr>
    <td class="malign">&nbsp; Function: </td>
    <td class="ralign"><input class="w250" id="single1" name="single1" value="" ></td><td class="ralign">
    <button class="ui-button ui-widget ui-corner-all" id="calc1"> f(x) </button>
    <button class="ui-button ui-widget ui-corner-all" id="calc2"> g(x) </button>
    <button class="ui-button ui-widget ui-corner-all" id="calc3"> h(x) </button>
    </td></tr><tr>
    <td class=malign ralign" title="Separate multiple values with spaces"> 
    Input value(s):</td>
    <td class="ralign"> <input class="w250" id="inpval" name="inpval" value="" >
    </td><td>
    <button class="ui-button ui-widget ui-corner-all" id="calcreset">
      &nbsp;<span class="ui-icon ui-icon-close"></span>&nbsp;</button>
    <button class="ui-button ui-widget ui-corner-all" id="posval">+10&nbsp;</button>
    <button class="ui-button ui-widget ui-corner-all" id="negval">-10</button>
    </td></tr>
    </table>
    <input type="hidden" name="decis" id="decis" value="3" >
      <div class="lalign">
      <div class="smalldiv"><input class="ui-button ui-widget ui-corner-all" type="submit" value=" Calculate "> &nbsp;</div>
      <div class="smalldiv">
      <label for="res">results</label>
      <input type="radio" class="check" name="format" id="res" value="0" >
      <label for="tbl">table</label>
      <input type="radio" class="check" name="format" id="tbl" value="1" checked="checked" >
      <label for="csv">csv</label>
      <input type="radio" class="check" name="format" id="csv" value="2" >      
      <label for="latex">latex</label>
      <input type="radio" class="check" name="format" id="latex" value="3" >      
      </div>
      </div>
    <iframe class="calcframe" name="single" src="single.php">Result display</iframe> &nbsp;
    <input type="hidden" name="c" value="2" >
    <input type="hidden" id="qqsingle" name="qqsingle" value="" >
  </fieldset></form>
  <br>
  <fieldset><legend>
    <img class="bt" id="infobutton4" src="images/iconInfo.png"> Load &amp; save:
    </legend>
    <p class="noprinting">In case you want to link to the graph or cannot use
    one of the image formats provided, the graph in the plotter
    can be reproduced any time by the following URL</p>
    <div class="info"  id="info4"><p>
      Every image drawn by plotter can be dynamically reproduced by a simple
      call to plotter's image creating part (graph.php). All the information needed is placed
      in a query string appended to its URL.</p>
      <?php echo $short_msg; ?>
      <p>Of course, this Information can be used
      to load an existing plot, e.g. to modify some of its Settings - that is what the button
      labeled <i>Load graph</i> is for.
     </p></div>
     <div>
     <?php if (isset($useShortening) && $useShortening == true) {  ?>
     <input type="text" value="" id="shortpath">
         <button class="ui-button ui-widget ui-corner-all" id="urlcopy">copy</button>
         <?php if (isset($useQR) && $useQR = true) {  ?>
            <img src="" id="QRcode" alt="URI QR-encoded">
          <?php } ?>
     <?php } ?>
     </div>
     <br><p class="noprinting">full URL to your image file</p>
     <textarea id="path" class="calcframe"></textarea><br>
     <button class="ui-button ui-widget ui-corner-all" id="urlclear">
        <span class="ui-icon ui-icon-close"></span> clear</button>
     <button class="ui-button ui-widget ui-corner-all" id="urlselect"> select </button>
     <button class="ui-button ui-widget ui-corner-all" id="loadgraph"> load graph </button>
    </fieldset>
  </div>
  
</div><?php //-- end of tab 3 --- ?>
