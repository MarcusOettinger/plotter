  <div id="calculate">
  <p class="noprinting">Enter an expression and some variable values
  (comma or space-separated) and press calculate to produce sample points.</p>
  <div class="info" id="info3">
    <img class="bt" id="closeinfo3" src="images/iconClose.png">
    <p>Enter some variable values and a function term to be calculated,
    the functions used in the plot above can be copied using one of the 
    buttons labeled f(x), g(x) or h(x). Variables will usually be 
    simple numbers, separate multiple input values (x) by commas or
    spaces.</p>
    <p>Select one of the checkboxes to chose a format for output:</p>
    <ul class="lalign">
      <li>results - a vector-like string of function values, separated
          by spaces</li>
      <li>table - a html table including variable values and results</li>
      <li>csv - colon separated values including variable values</li>
      <li>latex - code to layout a table including variable values and
          results in laTeX</li>
    </ul>
  </div>
  <form method="post" action="calc_results.php" id="singleform" target="single" autocomplete="off">
    <fieldset><legend><img class="bt" id="infobutton3" src="images/iconInfo.png">
    calculate discrete values: </legend>
    <table><tr>
    <td class="malign">&nbsp; Function: </td>
    <td class="ralign"><input class="w250" id="single1" name="single1" value="" ></td><td class="ralign">
    <button class="guibutton" id="calc1"> f(x) </button>
    <button class="guibutton" id="calc2"> g(x) </button>
    <button class="guibutton" id="calc3"> h(x) </button>
    </td></tr><tr>
    <td class="malign ralign" title="Separate multiple values with spaces"> 
    Input value(s):</td>
    <td class="ralign"> <input class="w250" id="inpval" name="inpval" value="" >
    </td><td>
    <button class="guibutton" id="calcreset">clear</button>
    <button class="guibutton" id="posval">+10&nbsp;</button>
    <button class="guibutton" id="negval">-10</button>
    </td></tr>
    </table>
    <input type="hidden" name="decis" id="decis" value="3" >
      <div class="lalign">
      <div class="smalldiv"><input class="guibutton" type="submit" value=" Calculate "> &nbsp;</div>
      <div class="smalldiv">
      <label for="res">results</label>
      <input type="radio" class="check" name="format" id="res" value="0" >&nbsp;
      <label for="tbl">table</label>
      <input type="radio" class="check" name="format" id="tbl" value="1" checked="checked" >&nbsp;
      <label for="csv">csv</label>
      <input type="radio" class="check" name="format" id="csv" value="2" >&nbsp;
      <label for="latex">latex</label>
      <input type="radio" class="check" name="format" id="latex" value="3" >&nbsp;  
      </div>
      </div>
    <iframe class="calcframe" name="single" src="calc_results.php">Result display</iframe> &nbsp;
    <input type="hidden" name="c" value="2" >
    <input type="hidden" id="qqsingle" name="qqsingle" value="" >
  </fieldset></form>
  <br>
  <fieldset><legend>
    <img class="bt" id="infobutton4" src="images/iconInfo.png"> Load &amp; save:
    </legend>
    <p class="noprinting">In case you want to link to the graph or cannot use
    one of the image formats provided, the graph in the plotter can be reproduced
    any time by the following URL</p>
    <div class="info"  id="info4">
   <img class="bt" id="closeinfo4" src="images/iconClose.png">
   <p>Every image drawn by plotter can be dynamically reproduced by a simple
      call to plotter's image creating part (called graph.php). All the information
      needed is placed in the query string appended to its URL. So, wehenever you
      call this URL (including the querystring), an image file of the plot above
      will be returned.
      </p>
      <?php echo $short_msg; ?>
      <p>Of course, this Information can be used to load an existing plot, e.g. to
      modify some of its settings - that is what the button labeled <i>Load graph</i>
      is for.
     </p></div>
     <div>
     <?php if (isset($useShortening) && $useShortening == true) {  ?>
     <input type="text" value="" id="shortpath">
         <button class="guibutton" id="urlcopy">copy</button>
         <?php if ( isset($useQR) && $useQR ) {  ?>
            <img src="" id="QRcode" alt="URI QR-encoded">
          <?php } ?>
     <?php } ?>
     </div>
     <br><p class="noprinting">full URL to your image file</p>
     <textarea id="path" class="calcframe"></textarea><br>
     <button class="guibutton" id="urlclear"> clear </button>
     <button class="guibutton" id="graphcopy"> copy </button>
     <button class="guibutton" id="loadgraph"> load graph </button>
    </fieldset>
  </div><!-- accordion content -->
