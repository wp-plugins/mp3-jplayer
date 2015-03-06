/*
	MP3-jPlayer 1.8
	Admin-Settings js
*/

var MP3jP = {
	
	openTab: 1,
	
	add_tab_listener: function ( j ) {
		var that = this;
		jQuery('#mp3j_tabbutton_' + j).click( function (e) {
			that.changeTab( j );
		});
	},
	
	changeTab: function ( j ) {
		if ( j !== this.openTab ) {
			jQuery('#mp3j_tab_' + this.openTab).hide();
			jQuery('#mp3j_tabbutton_' + this.openTab).removeClass('active-tab');
			jQuery('#mp3j_tab_' + j).show();
			jQuery('#mp3j_tabbutton_' + j).addClass('active-tab');
			this.openTab = j;
		}
	},
	
	counterpartsFeedback: function () {
		var noCP = [ 'ogg', 'webm', 'wav' ],
			isTicked = false,
			message = 'Auto-counterparting is switched off.',
			l = noCP.length,
			j;
		
		for ( j = 0; j < l; j += 1 ) {	
			if ( jQuery('#audioFormats_' + noCP[j]).prop( 'checked' ) === true ) {
				isTicked = true;
				break;
			}
		}
		
		if ( jQuery('#autoCounterpart').prop( 'checked' ) ) {
			if ( isTicked ) {
				message = 'Bulk auto-counterparting is not available with this format selection.';
			} else {
				message = '<span class="tick">&nbsp;</span>Bulk auto-counterparting is active.';
			}
		}
		jQuery('#feedCounterpartInfo').empty().append( message );
	},
	
	init: function () {
		jQuery( '.mp3j-tabbutton').each( function ( j ) {
			MP3jP.add_tab_listener( j );
			if ( j !== MP3jP.openTab ) {
				jQuery('#mp3j_tab_' + j ).hide();
			}
		});
		jQuery('#mp3j_tabbutton_' + this.openTab ).addClass('active-tab');
		
		jQuery('.formatChecker, #autoCounterpart').on( 'change', function ( e ) {
			MP3jP.counterpartsFeedback();
		});
		MP3jP.counterpartsFeedback();
	}
};



function HextoRGB(hexString) {  
	  if(hexString === null || typeof(hexString) != "string") {
		SetRGB(0,0,0);
		return;
	  }
	  if (hexString.substr(0, 1) == '#')
		hexString = hexString.substr(1);
	  if(hexString.length != 6) {
		SetRGB(0,0,0);
		return;
	  }  
	  var r = parseInt(hexString.substr(0, 2), 16);
	  var g = parseInt(hexString.substr(2, 2), 16);
	  var b = parseInt(hexString.substr(4, 2), 16);
	  if (isNaN(r) || isNaN(g) || isNaN(b)) {
		SetRGB(0,0,0);
		return;
	  }
	  SetRGB(r,g,b);  
}
function SetRGB(r, g, b){
	  red = r/255.0;
	  green = g/255.0;
	  blue = b/255.0;
}
function RGBtoHSV(){
	  var max = Math.max(Math.max(red, green), blue);
	  var min = Math.min(Math.min(red, green), blue);
	  value = max;
	  saturation = 0;
	  if(max !== 0)
		saturation = 1 - min/max;
	  hue = 0;
	  if(min == max)
		return;
	 
	  var delta = (max - min);
	  if (red == max)
		hue = (green - blue) / delta;
	  else if (green == max)
		hue = 2 + ((blue - red) / delta);
	  else
		hue = 4 + ((red - green) / delta);
	  hue = hue * 60;
	  if(hue < 0)
		hue += 360;
}




jQuery(document).ready( function () {
	MP3jP.init();
});



