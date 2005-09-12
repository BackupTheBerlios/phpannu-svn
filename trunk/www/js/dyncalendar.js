/**
* Filename.......: calendar.js
* Project........: Popup Calendar
* Last Modified..: $Date: 2004/10/07 10:13:11 $
* CVS Revision...: $Revision: 1.4 $
* Copyright......: 2001, 2002 Richard Heyes
* hacked by Laurent Jouanneau For Copix Framework, http://copix.aston.fr
* -> full use of CSS
* -> more XHTML 1.0/strict web standard respect
* -> no collision with other event callback
*/

/**
* Global variables
*/
	dynCalendar_layers          = new Array();

/**
* The calendar constructor
*
* @access public
* @param string objName      Name of the object that you create
* @param string callbackFunc Name of the callback function
* @param string OPTIONAL     Optional layer name
* @param string OPTIONAL     Optional images path
*/
	function dynCalendar(objName, callbackFunc)
	{
		/**
        * Properties
        */
		// Todays date
		this.today          = new Date();
		this.date           = this.today.getDate();
		this.month          = this.today.getMonth();
		this.year           = this.today.getFullYear();

		this.objName        = objName;
		this.callbackFunc   = callbackFunc;
		this.imagesPath     = arguments[2] ? arguments[2] : 'img/dyncalendar/';
		this.layerID        = arguments[3] ? arguments[3] : 'dynCalendar_layer_' + dynCalendar_layers.length;

		this.offsetX        = 5;
		this.offsetY        = 5;

		this.useMonthCombo  = true;
		this.useYearCombo   = true;
		this.yearComboRange = 5;

		this.currentMonth   = this.month;
		this.currentYear    = this.year;

		/**
        * Public Methods
        */
		this.show              = dynCalendar_show;
		this.writeHTML         = dynCalendar_writeHTML;

		// Accessor methods
      this.setXY         = dynCalendar_setXY;
		this.setOffset         = dynCalendar_setOffset;
		this.setOffsetX        = dynCalendar_setOffsetX;
		this.setOffsetY        = dynCalendar_setOffsetY;
		this.setImagesPath     = dynCalendar_setImagesPath;
		this.setMonthCombo     = dynCalendar_setMonthCombo;
		this.setYearCombo      = dynCalendar_setYearCombo;
		this.setCurrentMonth   = dynCalendar_setCurrentMonth;
		this.setCurrentYear    = dynCalendar_setCurrentYear;
		this.setYearComboRange = dynCalendar_setYearComboRange;

		/**
        * Private methods
        */
		// Layer manipulation
		this._getLayer         = dynCalendar_getLayer;
		this._hideLayer        = dynCalendar_hideLayer;
		this._showLayer        = dynCalendar_showLayer;
		this._setLayerPosition = dynCalendar_setLayerPosition;
		this._setHTML          = dynCalendar_setHTML;

      this._x = 0;
      this._y = 0;

		// Miscellaneous
		this._getDaysInMonth   = dynCalendar_getDaysInMonth;

		/**
        * Constructor type code
        */
		dynCalendar_layers[dynCalendar_layers.length] = this;
		this.writeHTML();

      this.ie4 = (document.all)? true:false;		// check if ie4
      this.ie5 = false;
      if(this.ie4) this.ie5 = (navigator.userAgent.indexOf('MSIE 5')>0
                  || navigator.userAgent.indexOf('MSIE 6')>0);
      this.dom2 = ((document.getElementById) && !(this.ie4||this.ie5))? true:false; // check the W3C DOM level2 compliance. ie4, ie5, ns4 are not dom level2 compliance !! grrrr >:-(

	}

/**
* Shows the calendar, or updates the layer if
* already visible.
*
* @access public
* @param integer month Optional month number (0-11)
* @param integer year  Optional year (YYYY format)
*/
	function dynCalendar_show()
	{
		// Variable declarations to prevent globalisation
		var month, year, monthnames, numdays, thisMonth, firstOfMonth;
		var ret, row, i, cssClass, linkHTML, previousMonth, previousYear;
		var nextMonth, nextYear, prevImgHTML, prevLinkHTML, nextImgHTML, nextLinkHTML;
		var monthComboOptions, monthCombo, yearComboOptions, yearCombo, html;

		this.currentMonth = month = arguments[0] != null ? arguments[0] : this.currentMonth;
		this.currentYear  = year  = arguments[1] != null ? arguments[1] : this.currentYear;

		//monthnames = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
      monthnames = new Array('Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre');
		numdays    = this._getDaysInMonth(month, year);

		thisMonth    = new Date(year, month, 1);
		firstOfMonth = thisMonth.getDay();

		// First few blanks up to first day
		ret = new Array(new Array());
		for(i=0; i<firstOfMonth; i++){
			ret[0][ret[0].length] = '<td>&nbsp;</td>';
		}

		// Main body of calendar
		row = 0;
		i   = 1;
		while(i <= numdays){
			if(ret[row].length == 7){
				ret[++row] = new Array();
			}

			/**
            * Generate this cells' HTML
            */
			cssClass = (i == this.date && month == this.month && year == this.year) ? 'dynCalendar_today' : 'dynCalendar_day';
			linkHTML = '<a href="javascript: ' + this.callbackFunc + '(' + i + ', ' + (Number(month) + 1) + ', ' + year + '); ' + this.objName + '._hideLayer()">' + (i++) + '</a>';
			ret[row][ret[row].length] = '<td class="' + cssClass + '">' + linkHTML + '</td>';
		}

		// Format the HTML
		for(i=0; i<ret.length; i++){
			ret[i] = ret[i].join('\n') + '\n';
		}

		previousYear  = thisMonth.getFullYear();
		previousMonth = thisMonth.getMonth() - 1;
		if(previousMonth < 0){
			previousMonth = 11;
			previousYear--;
		}
		
		nextYear  = thisMonth.getFullYear();
		nextMonth = thisMonth.getMonth() + 1;
		if(nextMonth > 11){
			nextMonth = 0;
			nextYear++;
		}

		prevImgHTML  = '<img src="' + this.imagesPath + '/prev.gif" alt="&lt;&lt;" />';
		prevLinkHTML = '<a href="javascript: ' + this.objName + '.show(' + previousMonth + ', ' + previousYear + ');">' + prevImgHTML + '</a>';
		nextImgHTML  = '<img src="' + this.imagesPath + '/next.gif" alt="&gt;&gt;" />';
		nextLinkHTML = '<a href="javascript: ' + this.objName + '.show(' + nextMonth + ', ' + nextYear + ');">' + nextImgHTML + '</a>';

		/**
        * Build month combo
        */
		if (this.useMonthCombo) {
			monthComboOptions = '';
			for (i=0; i<12; i++) {
				selected = (i == thisMonth.getMonth() ? 'selected="selected"' : '');
				monthComboOptions += '<option value="' + i + '" ' + selected + '>' + monthnames[i] + '</option>';
			}
			monthCombo = '<select name="months" onchange="' + this.objName + '.show(this.options[this.selectedIndex].value, ' + this.objName + '.currentYear)">' + monthComboOptions + '</select>';
		} else {
			monthCombo = monthnames[thisMonth.getMonth()];
		}

		/**
        * Build year combo
        */
		if (this.useYearCombo) {
			yearComboOptions = '';
			for (i = thisMonth.getFullYear() - this.yearComboRange; i <= (thisMonth.getFullYear() + this.yearComboRange); i++) {
				selected = (i == thisMonth.getFullYear() ? 'selected="selected"' : '');
				yearComboOptions += '<option value="' + i + '" ' + selected + '>' + i + '</option>';
			}
			yearCombo = '<select name="years" onchange="' + this.objName + '.show(' + this.objName + '.currentMonth, this.options[this.selectedIndex].value)">' + yearComboOptions + '</select>';
		} else {
			yearCombo = thisMonth.getFullYear();
		}

		html = '<table>';
		html += '<tr class="dynCalendar_header"><th>' + prevLinkHTML + '</td><th colspan="5">' + monthCombo + ' ' + yearCombo + '</td><th>' + nextLinkHTML + '</td></tr>';
		html += '<tr>';
		html += '<th>Dim</th>';
		html += '<th>Lun</th>';
		html += '<th>Mar</th>';
		html += '<th>Mer</th>';
		html += '<th>Jeu</th>';
		html += '<th>Ven</th>';
		html += '<th>Sam</th></tr>';
		/*html += '<th>Sun</th>';
		html += '<th>Mon</th>';
		html += '<th>Tue</th>';
		html += '<th>Wed</th>';
		html += '<th>Thu</th>';
		html += '<th>Fri</th>';
		html += '<th>Sat</th></tr>';*/
		html += '<tr>' + ret.join('</tr>\n<tr>') + '</tr>';
		html += '</table><p><a href="javascript:' + this.objName + '._hideLayer()">fermer le calendrier</a></p>';

		this._setHTML(html);
		if (!arguments[0] && !arguments[1]) {
			this._showLayer();
			this._setLayerPosition();
		}
	}

/**
* Writes HTML to document for layer
*
* @access public
*/
	function dynCalendar_writeHTML()
	{
		if (document.getElementById || document.all) {
			document.write('<span class="dynCalendar-button"><a href="javascript: ' + this.objName + '.show()" onclick="' + this.objName + '.setXY(event)"><img src="' + this.imagesPath + 'dynCalendar.gif" alt="bouton affichage calendrier" /></a></span>');
			document.write('<div class="dynCalendar" id="' + this.layerID + '"></div>');   // onmouseover="' + this.objName + '._mouseover(true)" onmouseout="' + this.objName + '._mouseover(false)"
		}
	}


   function dynCalendar_setXY(e){

      if(this.ie4 || this.dom2){

         if(this.dom2){
            this._x = e.pageX;
            this._y = e.pageY;
         }else{
            if(this.ie4) { this._x = e.x; this._y = e.y; }
            if(this.ie5) { this._x = e.x + document.body.scrollLeft;
                  this._y = e.y + document.body.scrollTop; }
         }
      }
   }
/**
* Sets the offset to the mouse position
* that the calendar appears at.
*
* @access public
* @param integer Xoffset Number of pixels for vertical
*                        offset from mouse position
* @param integer Yoffset Number of pixels for horizontal
*                        offset from mouse position
*/
	function dynCalendar_setOffset(Xoffset, Yoffset)
	{
		this.setOffsetX(Xoffset);
		this.setOffsetY(Yoffset);
	}

/**
* Sets the X offset to the mouse position
* that the calendar appears at.
*
* @access public
* @param integer Xoffset Number of pixels for horizontal
*                        offset from mouse position
*/
	function dynCalendar_setOffsetX(Xoffset)
	{
		this.offsetX = Xoffset;
	}

/**
* Sets the Y offset to the mouse position
* that the calendar appears at.
*
* @access public
* @param integer Yoffset Number of pixels for vertical
*                        offset from mouse position
*/
	function dynCalendar_setOffsetY(Yoffset)
	{
		this.offsetY = Yoffset;
	}
	
/**
* Sets the images path
*
* @access public
* @param string path Path to use for images
*/
	function dynCalendar_setImagesPath(path)
	{
		this.imagesPath = path;
	}

/**
* Turns on/off the month dropdown
*
* @access public
* @param boolean useMonthCombo Whether to use month dropdown or not
*/
	function dynCalendar_setMonthCombo(useMonthCombo)
	{
		this.useMonthCombo = useMonthCombo;
	}

/**
* Turns on/off the year dropdown
*
* @access public
* @param boolean useYearCombo Whether to use year dropdown or not
*/
	function dynCalendar_setYearCombo(useYearCombo)
	{
		this.useYearCombo = useYearCombo;
	}

/**
* Sets the current month being displayed
*
* @access public
* @param boolean month The month to set the current month to
*/
	function dynCalendar_setCurrentMonth(month)
	{
		this.currentMonth = month;
	}

/**
* Sets the current month being displayed
*
* @access public
* @param boolean year The year to set the current year to
*/
	function dynCalendar_setCurrentYear(year)
	{
		this.currentYear = year;
	}

/**
* Sets the range of the year combo. Displays this number of
* years either side of the year being displayed.
*
* @access public
* @param integer range The range to set
*/
	function dynCalendar_setYearComboRange(range)
	{
		this.yearComboRange = range;
	}

/**
* Returns the layer object
*
* @access private
*/
	function dynCalendar_getLayer()
	{
		var layerID = this.layerID;

		if (document.getElementById(layerID)) {

			return document.getElementById(layerID);

		} else if (document.all(layerID)) {
			return document.all(layerID);
		}
	}

/**
* Hides the calendar layer
*
* @access private
*/
	function dynCalendar_hideLayer()
	{
		this._getLayer().style.visibility = 'hidden';
	}

/**
* Shows the calendar layer
*
* @access private
*/
	function dynCalendar_showLayer()
	{
		this._getLayer().style.visibility = 'visible';
	}

/**
* Sets the layers position
*
* @access private
*/
	function dynCalendar_setLayerPosition()
	{
		this._getLayer().style.top  =  (this._y + this.offsetY) + 'px';
		this._getLayer().style.left = (this._x + this.offsetX) + 'px';

	}

/**
* Sets the innerHTML attribute of the layer
*
* @access private
*/
	function dynCalendar_setHTML(html)
	{
		this._getLayer().innerHTML = html;
	}

/**
* Returns number of days in the supplied month
*
* @access private
* @param integer month The month to get number of days in
* @param integer year  The year of the month in question
*/
	function dynCalendar_getDaysInMonth(month, year)
	{
		monthdays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		if (month != 1) {
			return monthdays[month];
		} else {
			return ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0 ? 29 : 28);
		}
	}

