<!DOCTYPE html>
<html>
	<head>
		<title></title>
				
		<script type='text/javascript' src='js/jquery.js'></script>
		<script type='text/javascript' src='js/ui.core.js'></script>
		<script type='text/javascript' src='js/ui.widget.js'></script>
		<script type='text/javascript' src='js/ui.mouse.js'></script>
		<script type='text/javascript' src='js/ui.slider.js'></script>
		<script type='text/javascript' src='js/jquery.jplayer.min.js'></script>
		
		<script type='text/javascript' src='js/mp3-jplayer-1.8.js'></script>
		
<script type='text/javascript'>

var MP3J;
	
function loadcss(filename) {
	var fileref = document.createElement("link");
	fileref.setAttribute("rel", "stylesheet");
	fileref.setAttribute("type", "text/css");
	fileref.setAttribute("href", filename);
	if (typeof fileref !== "undefined") { 
		document.getElementsByTagName("head")[0].appendChild(fileref); 
	}
}

if(window.opener && !window.opener.closed) {
	loadcss(window.opener.MP3_JPLAYER.vars.stylesheet_url);
	MP3J = create_mp3_jplayer();
	MP3J.vars.play_f = false; // always set false!
	MP3J.vars.pathto_swf = window.opener.MP3_JPLAYER.vars.pathto_swf;
	MP3J.vars.silence = window.opener.MP3_JPLAYER.vars.silence;
	MP3J.vars.launched_ID = window.opener.MP3_JPLAYER.vars.launched_ID;
	MP3J.vars.dload_text = window.opener.MP3_JPLAYER.vars.dload_text;
	
	var pl_info_wo = window.opener.MP3_JPLAYER.pl_info;
	MP3J.pl_info = [{ 	
		list:pl_info_wo[MP3J.vars.launched_ID].list, 
		tr:pl_info_wo[MP3J.vars.launched_ID].tr,
		type:'MI', 
		lstate:pl_info_wo[MP3J.vars.launched_ID].lstate, 
		loop:pl_info_wo[MP3J.vars.launched_ID].loop, 
		play_txt:pl_info_wo[MP3J.vars.launched_ID].play_txt, 
		pause_txt:pl_info_wo[MP3J.vars.launched_ID].pause_txt, 
		pp_title:pl_info_wo[MP3J.vars.launched_ID].pp_title, 
		autoplay:pl_info_wo[MP3J.vars.launched_ID].autoplay,
		download:pl_info_wo[MP3J.vars.launched_ID].download, 
		vol:pl_info_wo[MP3J.vars.launched_ID].vol,
		height:pl_info_wo[MP3J.vars.launched_ID].height,
		cssclass:pl_info_wo[MP3J.vars.launched_ID].cssclass
	}];
	
	MP3J.vars.pp_playerheight = window.opener.MP3_JPLAYER.vars.pp_playerheight;
	MP3J.vars.pp_windowheight = window.opener.MP3_JPLAYER.vars.pp_windowheight;
	MP3J.vars.pp_maxheight = window.opener.MP3_JPLAYER.vars.pp_windowheight;
	//MP3J.vars.pp_fixedcss = window.opener.MP3_JPLAYER.vars.pp_fixedcss;
	
	MP3J.togglelist = function (j) {
		var winwidth = jQuery(window).width();
		if (this.pl_info[j].lstate === true) {
			jQuery(this.eID.plwrap + j).fadeOut(300);
			jQuery(this.eID.toglist + j).text('SHOW');
			window.resizeTo( winwidth+24 , this.vars.pp_playerheight );
			this.pl_info[j].lstate = false;
		} else if (this.pl_info[j].lstate === false) {
			jQuery(this.eID.plwrap + j).fadeIn("slow");
			jQuery(this.eID.toglist + j).text('HIDE');
			window.resizeTo( winwidth+24 , this.vars.pp_windowheight );
			this.pl_info[j].lstate = true;
		}
	};
	
}

jQuery(document).ready(function () {
	if ( typeof MP3J === "undefined" ) { 
		jQuery("body").empty();
		jQuery("body").css("background", '#333333');
		jQuery("*").css("color", '#cccccc');
		jQuery("body").append("<h4 style='margin-left:10px;'>Please launch a playlist from the site to use me,<br />I've been refreshed and can't find my parent window.</h4>");
		return; 
	} else {
		MP3J.init();
	}
});
			
</script>

		<style type="text/css"> div.wrap-MI { min-width:350px; } </style>

	</head>
	<body style="padding:5px 4px 0px 4px; margin:0px;">
		<div style="position:relative;overflow:hidden;"><div id="jquery_jplayer" style="left:-999em;"></div></div>
		<div class="wrap-MI" style="position:relative; padding:0; margin:0px auto 0px auto; width:100%;">
			<div class="jp-innerwrap">
				<div class="innerx"></div>
				<div class="innerleft"></div>
				<div class="innerright"></div>
				<div class="innertab"></div>
				<div class="jp-interface">
					<div class="MI-image" id="MI_image_0"></div>
					<div id="T_mp3j_0" class="player-track-title" style="padding-left:16px;"></div>
					<div class="MIsliderVolume" id="vol_mp3j_0"></div>
					<div class="bars_holder">
						<div class="loadMI_mp3j" id="load_mp3j_0"></div>
						<div class="poscolMI_mp3j" id="poscol_mp3j_0"></div>
						<div class="posbarMI_mp3j" id="posbar_mp3j_0"></div>
					</div>
					<div id="P-Time-MI_0" class="jp-play-time"></div>
					<div id="T-Time-MI_0" class="jp-total-time"></div>
					<div id="statusMI_0" class="statusMI"></div>
					<div class="transport-MI"><div class="buttons_mp3j" id="playpause_mp3j_0">Play Pause</div><div class="stop_mp3j" id="stop_mp3j_0">Stop</div><div class="Next_mp3j" id="Next_mp3j_0">Next&raquo;</div><div class="Prev_mp3j" id="Prev_mp3j_0">&laquo;Prev</div></div>
					<div id="download_mp3j_0" class="dloadmp3-MI" style="visibility: visible;"></div>
					<div class="playlist-toggle-MI" id="playlist-toggle_0"></div>
				</div>
			</div>
			<div class="listwrap_mp3j" id="L_mp3j_0">
				<div class="playlist-colour"></div>
				<div class="playlist-wrap-MI"><ul class="UL-MI_mp3j" id="UL_mp3j_0"><li></li></ul></div>				
			</div>
		</div>
		
<script type="text/javascript">
	
if(window.opener && !window.opener.closed) {				
	if ( MP3J.pl_info[0].height !== false ) {
		jQuery("div.jp-interface").css( "height", MP3J.pl_info[0].height+"px" );
	}
		
	if ( !MP3J.pl_info[0].download ) { 
		jQuery("div.dloadmp3-MI").hide(); 
	}
	if ( MP3J.pl_info[0].list.length < 2 ) {
		jQuery("#Prev_mp3j_0").hide();
		jQuery("#Next_mp3j_0").hide();
		jQuery("#playlist-toggle_0").hide(); 
	}
	if ( MP3J.pl_info[0].lstate ) {
		jQuery("#playlist-toggle_0").append("HIDE PLAYLIST");
	} else {
		jQuery("#playlist-toggle_0").append("SHOW PLAYLIST");
	}
	
	jQuery("div.wrap-MI").addClass(MP3J.pl_info[0].cssclass);
	
	if ( window.opener.MP3_JPLAYER.popout_css !== "undefined" ) {
		MP3J.popout_css = window.opener.MP3_JPLAYER.popout_css;
		jQuery("body").css( "background" , MP3J.popout_css.body_col + " url('" + MP3J.popout_css.body_img + "')");
		jQuery("div.player-track-title, div.player-artist, div.jp-play-time, div.jp-total-time, div.statusMI").css( "color" , MP3J.popout_css.screen_text );
		jQuery("ul.UL-MI_mp3j").css( "background" , MP3J.popout_css.list_img );
		jQuery("div.playlist-colour").css({ "background" : MP3J.popout_css.list_col, opacity : MP3J.popout_css.list_opac });
		jQuery("div.innertab").css({ "background" : MP3J.popout_css.screen_bg, opacity : MP3J.popout_css.screen_opac });
		jQuery("div.loadMI_mp3j").css({ "background" : MP3J.popout_css.loader_col, opacity : MP3J.popout_css.loader_opac });
		jQuery("div.poscolMI_mp3j").css({ "background" : MP3J.popout_css.posbar_col, opacity : MP3J.popout_css.posbar_opac });		
		
		jQuery('<style type="text/css"> .MI-image a:hover img { background:' + MP3J.popout_css.list_current_text + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> span.mp3-tint { background:' + MP3J.popout_css.indi_tint + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> ul.UL-MI_mp3j li { background:' + MP3J.popout_css.list_divider + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> ul.UL-MI_mp3j li a { color:' + MP3J.popout_css.list_text + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> ul.UL-MI_mp3j li a:hover { color:' + MP3J.popout_css.list_hover_text + '; background:' + MP3J.popout_css.list_hover_bg + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> ul.UL-MI_mp3j li a.mp3j_A_current { color:' + MP3J.popout_css.list_current_text + '; background:' + MP3J.popout_css.list_current_bg + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> div.MIsliderVolume .ui-widget-header { background:' + MP3J.popout_css.vol_slider_bg + '; } </style>').appendTo('head');
		jQuery('<style type="text/css"> div.transport-MI div:hover { background-color:' + MP3J.popout_css.list_current_text + '; } </style>').appendTo('head');
		
		jQuery("div.transport-MI div").css("color", MP3J.popout_css.list_hover_text );
		jQuery("div.transport-MI div").mouseover(function () {
			 jQuery(this).css( "color" , MP3J.popout_css.list_current_text );
		});
		jQuery("div.transport-MI div").mouseout(function () {
			 jQuery(this).css("color", MP3J.popout_css.list_hover_text );
		});
	}
	jQuery("title").text(MP3J.pl_info[0].pp_title);
}

</script>

	</body>
</html>