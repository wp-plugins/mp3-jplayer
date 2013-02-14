/* 
	MP3-jPlayer 1.8
   	www.sjward.org 
*/

function create_mp3_jplayer() {
	var mp3j = {
		
		tID: '',		//id current
		state: '',		//state current 
		pl_info: [],	//players info
		load_pc: 0,		//loaded percent
		played_t: 0,	//played time

		vars: { // Setup vars
			pathto_swf: '',
			play_f: false,
			popout_url: '',
			jpID: '#jquery_jplayer',
			silence: '',
			stylesheet_url: '',
			launched_ID: '',
			dload_text: 'DOWNLOAD MP3',
			pp_width: 280,
			pp_maxheight: 350,
			pp_bodycolour: '#fff',
			pp_bodyimg: '',
			pp_fixedcss: false,
			pp_playerheight: 100+142,
			pp_windowheight: 600
		},
		
		eID: { //html element ID bases
			play: '#playpause_mp3j_',
			playW: '#playpause_wrap_mp3j_',
			stp: '#stop_mp3j_',
			prev: '#Prev_mp3j_',
			next: '#Next_mp3j_',
			vol: '#vol_mp3j_',
			loader: '#load_mp3j_',
			pos: '#posbar_mp3j_',
			poscol: '#poscol_mp3j_',
			title: '#T_mp3j_',
			caption: '#C_mp3j_',
			pT: '#P-Time-MI_',
			tT: '#T-Time-MI_',
			dload: '#download_mp3j_',
			plwrap: '#L_mp3j_',
			ul: '#UL_mp3j_',
			a: 'mp3j_A_',		//No hash!
			indiM: '#statusMI_',
			toglist: '#playlist-toggle_',
			lPP: '#lpp_mp3j_',
			pplink: '#mp3j_popout_',
			img: '#MI_image_'
		},
		
		init: function () {
			var that = this;
			this.unwrap();
			jQuery(this.vars.jpID).jPlayer({
				ready: function () {
					that.write_controls();
					that.startup();
				},
				volume: 100,
				swfPath: that.vars.pathto_swf
			})
				.jPlayer("onProgressChange", function (lp, ppR, ppA, pt, tt) {
					if (that.state !== '') {
						that.E_progress(that.tID, lp, ppR, ppA, pt, tt);
					}
				})
				.jPlayer("onSoundComplete", function () {
					that.E_complete(that.tID);
				});
		},
		write_controls: function () { //Sets up each player
			var j;
			for (j = 0; j < this.pl_info.length; j += 1) {
				this.setup_a_player(j);
			}
		},
		startup: function () { //Looks if there's something to play
			var j;
			for (j = 0; j < this.pl_info.length; j += 1) {
				if (this.pl_info[j].autoplay) {
					this.pl_info[j].autoplay = false;
					this.E_change_track(j, this.pl_info[j].tr);
					return;
				}
			}
		},
		setup_a_player: function (j) { //Sets up control clicks, lists, text for a player
			var i, li, sel, that = this, p = this.pl_info[j];
			// VOL SLIDER
			jQuery(this.eID.vol + j).slider({
				value : p.vol,
				max: 100,
				range: 'min',
				animate: false,
				slide: function (event, ui) {
					p.vol = ui.value;
					if (j === that.tID) {
						jQuery(that.vars.jpID).jPlayer("volume", ui.value);
					}
				}
			});
			//PLAY-PAUSE CLICKS
			sel = ('MI' === p.type) ? this.eID.play : this.eID.playW;
			jQuery(sel + j).click(function () {
				that.E_change_track(j, p.tr);
				jQuery(this).blur();
			});
			jQuery(sel + j).dblclick(function () {
				if (that.state !== "playing") {
					that.E_change_track(j, p.tr);
				}
				jQuery(this).blur();
			});
			//TEXT	
			this.titles(j, p.tr);

			if ('MI' === p.type) {
				jQuery(this.eID.pT + j).text('00:00');
				jQuery(this.eID.indiM + j).text('Ready');
			//STOP CLICKS
				jQuery(this.eID.stp + j).click(function () {
					that.E_stop(j);
					jQuery(this).blur();
				});
				jQuery(this.eID.stp + j).dblclick(function () {
					that.E_dblstop(j);
					jQuery(this).blur();
				});
			//PREV & NEXT CLICKS
				jQuery(this.eID.plwrap + j).hide();
				if (p.list.length > 1) {
					jQuery(this.eID.next + j).click(function () {
						that.E_change_track(j, 'next');
						jQuery(this).blur();
					});
					jQuery(this.eID.prev + j).click(function () {
						that.E_change_track(j, 'prev');
						jQuery(this).blur();
					});
			//UL ITEMS	
					jQuery(this.eID.ul + j).empty();
					for (i = 0; i < p.list.length; i += 1) {
						li = '<li>';
						li += '<a href="#" id="' + this.eID.a + j + '_' + i + '">' + p.list[i].name + '</a></li>';
						jQuery(this.eID.ul + j).append(li);
			//UL <a> CLICKS
						this.add_ul_click(j, i);
					}
					jQuery('#' + this.eID.a + j + '_' + p.tr).addClass('mp3j_A_current');
			//LIST TOGGLE
					jQuery(this.eID.toglist + j).click(function () {
						that.togglelist(j);
						jQuery(this).blur();
					});
					if (p.lstate === true) {
						jQuery(this.eID.plwrap + j).show();
					}
				}
			//DOWNLOAD
				this.writedownload(j, p.tr);
			//POPOUT BUTTON
				jQuery(this.eID.lPP + j).click(function () {
					jQuery(this).blur();
					return that.launchPP(j);
				});
			}
			
			if ('popout' === p.type) {
				jQuery(this.eID.pplink + j).click(function () {
					jQuery(this).blur();
					return that.launchPP(j);
				});
			}
			
		},
		add_ul_click: function (j, i) { //Creates the list item click on the <a>
			var that = this;
			jQuery('#' + this.eID.a + j + "_" + i).click(function () {
				that.E_change_track(j, i);
				jQuery(this).blur();
				return false;
			});
		},
		
		unwrap: function () {
			var i, j, arr;
			if (this.vars.play_f === true && typeof this.lists !== "undefined" && this.lists.length > 0) {
				for (i = 0; i < this.lists.length; i += 1) {
					arr = this.lists[i];
					for (j = 0; j < arr.length; j += 1) { 
						arr[j].mp3 = this.f_undo.f_con(arr[j].mp3);
					}
				}
			}
		},
		f_undo: {
			keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
			f_con : function (input) {
				var output = "", i = 0, chr1, chr2, chr3, enc1, enc2, enc3, enc4;
				input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
				while (i < input.length) {
					enc1 = this.keyStr.indexOf(input.charAt(i++)); enc2 = this.keyStr.indexOf(input.charAt(i++));
					enc3 = this.keyStr.indexOf(input.charAt(i++)); enc4 = this.keyStr.indexOf(input.charAt(i++));
					chr1 = (enc1 << 2) | (enc2 >> 4); chr2 = ((enc2 & 15) << 4) | (enc3 >> 2); chr3 = ((enc3 & 3) << 6) | enc4;
					output = output + String.fromCharCode(chr1);
					if (enc3 !== 64) { output = output + String.fromCharCode(chr2); }
					if (enc4 !== 64) { output = output + String.fromCharCode(chr3); }
				}
				output = this.utf8_f_con(output);
				return output;
			},
			utf8_f_con : function (utftext) {
				var string = "", i = 0, c, c1, c2, c3;
				while (i < utftext.length) {
					c = utftext.charCodeAt(i);
					if (c < 128) {
						string += String.fromCharCode(c); i++;
					} else if ((c > 191) && (c < 224)) {
						c2 = utftext.charCodeAt(i + 1); string += String.fromCharCode(((c & 31) << 6) | (c2 & 63)); i += 2;
					} else {
						c2 = utftext.charCodeAt(i + 1); c3 = utftext.charCodeAt(i + 2); string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63)); i += 3;
					}
				}
				return string;
			}
		},
		
		setit: function (file) {
			jQuery(this.vars.jpID).jPlayer("setFile", file);
			this.state = 'set';
		},
		playit: function () {
			jQuery(this.vars.jpID).jPlayer("play");
			this.state = 'playing';
		},
		pauseit: function () {
			jQuery(this.vars.jpID).jPlayer("pause");
			this.state = 'paused';
		},
		clearit: function () {
			jQuery(this.vars.jpID).jPlayer("clearFile");
			this.state = '';
		},
		
		Tformat: function (msec) { 
			var t = msec/1000,
				s = Math.floor((t)%60),
				m = Math.floor((t/60)%60),
				h = Math.floor(t/3600);
			return ((h > 0) ? h+':' : '') + ((m > 9) ? m : '0'+m) + ':' + ((s > 9) ? s : '0'+s);
		}
	}; //Close var mp3j


	mp3j.E_stop = function (j) {
		if (j === this.tID && j !== '') {
			this.clearit();
			if ( jQuery(this.eID.pos + j + ' div.ui-widget-header').length > 0 ) {
				jQuery(this.eID.pos + j).slider('destroy');
			}
			this.button(j, 'play');
			jQuery(this.eID.tT + j).empty();
			if (this.pl_info[j].type === 'MI') {
				jQuery(this.eID.indiM + j).text('Stopped');
			} else {
				jQuery(this.eID.indiM + j).empty();
			}
			this.load_pc = 0;
			this.played_t = 0;
		}
	};
	
	mp3j.E_dblstop = function (j) {
		this.listclass(j, this.pl_info[j].tr, 0);
		if ( this.pl_info[j].tr !== 0 ) {
			this.titles(j, 0);
		}
		this.writedownload(j, 0);
		this.E_stop(j);
		jQuery(this.eID.indiM + j).text('Ready');
		this.pl_info[j].tr = 0;
	};
	
	mp3j.E_change_track = function (j, change) {
		var track, txt, p = this.pl_info[j];
		if (j === this.tID && change === p.tr) {
			if ('playing' === this.state) {
				if (this.load_pc === 0) {
					this.E_stop(j);
				} else {
					this.pauseit();
					this.button(j, 'play');
					if ('MI' === p.type) {
						jQuery(this.eID.indiM + j).text('Paused');
					}
				}
				return;
			} else if ('paused' === this.state || 'set' === this.state) {
				this.playit();
				this.button(j, 'pause');
				return;
			}
		}
		this.E_stop(this.tID);
		
		if ('prev' === change) {
			track = (p.tr-1 < 0) ? p.list.length-1 : p.tr-1;
		} else if ('next' === change) {
			track = (p.tr+1 < p.list.length) ? p.tr+1 : 0;
		} else {
			track = change;
		}
		jQuery(this.vars.jpID).jPlayer("volume", 100 ); //Vol scaling fix
		this.setit(p.list[track].mp3);
		this.playit();
		jQuery(this.vars.jpID).jPlayer("volume", p.vol ); //Reset to correct vol

		txt = ('MI' === p.type) ? '<span class="mp3-finding"></span><span class="mp3-tint"></span>Connecting' : '<span class="Smp3-finding"></span><span class="mp3-gtint"></span>';
		jQuery(this.eID.indiM + j).empty().append(txt);
		this.button(j, 'pause');
		this.makeslider(j);
		if ('MI' === p.type) {
			this.listclass(j, p.tr, track);
			if ( p.tr !== track ) {
				this.titles(j, track);
			}
			if (p.download) {
				this.writedownload(j, track);
				jQuery(this.eID.dload + j).hide().addClass('whilelinks').fadeIn(400);
			}
		}
		p.tr = track;
		this.tID = j;
	};
	
	mp3j.E_complete = function (j) {
		var p = this.pl_info[j];
		if ('MI' === p.type) {
			if (p.loop || p.tr+1 < p.list.length) {
				this.E_change_track(j, 'next');
			} else {
				this.E_dblstop(j);
				this.startup();
			}
		}
		if ('single' === p.type) {
			if (p.loop) {
				//this.E_change_track(j, p.tr);
				this.E_change_track(j, 'next');
			} else {
				this.E_stop(j);
				this.startup();
			}
		}
	};
	
	mp3j.E_progress = function (j, lp, ppR, ppA, pt, tt) {
		if (j !== '') {
			this.load_pc = lp;
			jQuery(this.eID.loader + j).css( "width", lp + '%' );
			jQuery(this.eID.poscol + j).css( "width", ppA + '%' );
			if ( jQuery(this.eID.pos + j + ' div.ui-widget-header').length > 0 ) {
				jQuery(this.eID.pos + j).slider('option', 'value', 10*ppA);
			}
			if (pt > 0) { jQuery(this.eID.pos + j).css( 'visibility', 'visible' ); }
			if (this.pl_info[j].type === 'MI') {
				jQuery(this.eID.pT + j).text(this.Tformat(pt));
				if (tt > 0) { jQuery(this.eID.tT + j).text(this.Tformat(tt)); }
			}
			if ('playing' === this.state) {
				if ('MI' === this.pl_info[j].type) {
					if (tt > 0 && this.played_t === pt) {
						jQuery(this.eID.indiM + j).empty().append('<span class="mp3-finding"></span><span class="mp3-tint"></span>Buffering');
					} else if (pt > 0) {
						jQuery(this.eID.indiM + j).empty().append('Playing');
					}
				}
				if ('single' === this.pl_info[j].type){
					if (pt > 0 ) {
						if (this.played_t === pt) {
							jQuery(this.eID.indiM + j).empty().append('<span class="Smp3-finding"></span><span class="mp3-gtint"></span> ' + this.Tformat(pt));
						} else {
							jQuery(this.eID.indiM + j).empty().append('<span class="mp3-tint tintmarg"></span> ' + this.Tformat(pt));
						}
					}
				}
			}
			this.played_t = pt;
		}
	};
	
	mp3j.launchPP = function (j) {
		var li_height = 28;
		if ( this.pl_info[j].height !== false ) {
			this.vars.pp_playerheight = 100 + this.pl_info[j].height;
		}
		this.vars.pp_windowheight = ( this.pl_info[j].list.length > 1 ) ? this.vars.pp_playerheight + ( this.pl_info[j].list.length * li_height) : this.vars.pp_playerheight;
		if ( this.vars.pp_windowheight > this.vars.pp_maxheight ) {
			this.vars.pp_windowheight = this.vars.pp_maxheight;
		}
		this.vars.launched_ID = j;
		if ( this.state === "playing" ) {
			this.pl_info[j].autoplay = true;	
		}
		this.E_stop(this.tID);
		this.setit(this.vars.silence); 
		this.playit(); //Chrome let go of last track (incase it didn't finish loading)
		this.clearit();
		
		var newwindow = window.open(this.vars.popout_url, 'mp3jpopout', 'height=300, width=600, location=1, status=1, scrollbars=1, resizable=1, left=25, top=25');
		if ( this.pl_info[j].lstate === true ) {
			newwindow.resizeTo( this.vars.pp_width, this.vars.pp_windowheight );
		} else {
			newwindow.resizeTo( this.vars.pp_width, this.vars.pp_playerheight );
		}
		if (window.focus) { 
			newwindow.focus(); 
		}
		return false;
	};
	
	mp3j.button = function (j, type) {
		if (j === '') { return; }
		if ('pause' === type) {
			if (this.pl_info[j].play_txt === '#USE_G#') { 
				jQuery(this.eID.play + j).removeClass('buttons_mp3j').addClass('buttons_mp3jpause');
			} else {
				jQuery(this.eID.play + j).text(this.pl_info[j].pause_txt);
			}
		}
		if ('play' === type) {
			if (this.pl_info[j].play_txt === '#USE_G#') {
				jQuery(this.eID.play + j).removeClass('buttons_mp3jpause').addClass('buttons_mp3j');
			} else {
				jQuery(this.eID.play + j).text(this.pl_info[j].play_txt);
			}
		}
	};
	
	mp3j.listclass = function (j, rem, add) {
		jQuery('#'+ this.eID.a + j +'_'+ rem).removeClass('mp3j_A_current');
		jQuery('#'+ this.eID.a + j +'_'+ add).addClass('mp3j_A_current');
	};
	
	mp3j.titles = function (j, track) {
		var p = this.pl_info[j], Olink = '', Clink = '';	
		if (p.type === "MI") {
			jQuery(this.eID.title + j).empty().append(p.list[track].name).append('<br /><span>' + p.list[track].artist + '</span>');
			if (p.list[track].image !== '') {
				if (p.list[track].imgurl !== '') {
					Olink = '<a href="' + p.list[track].imgurl + '">';
					Clink = '</a>';
				}
				jQuery(this.eID.img + j).empty().hide().append(Olink + '<img src="' + p.list[track].image + '" />' + Clink).fadeIn(300);
			}
		}
	};
	
	mp3j.writedownload = function (j, track) {
		var p = this.pl_info[j];
		if (p.download) {
			jQuery(this.eID.dload + j).empty().removeClass('whilelinks').append('<a href="' + p.list[track].mp3 + '">' + this.vars.dload_text + '</a>');
		}
	};
	
	mp3j.togglelist = function (j) {
		if (this.pl_info[j].lstate === true) {
			jQuery(this.eID.plwrap + j).fadeOut(300);
			jQuery(this.eID.toglist + j).text('SHOW');
			this.pl_info[j].lstate = false;
		} else if (this.pl_info[j].lstate === false) {
			jQuery(this.eID.plwrap + j).fadeIn("slow");
			jQuery(this.eID.toglist + j).text('HIDE');
			this.pl_info[j].lstate = true;
		}
	};
	
	mp3j.makeslider = function (j) {
		var phmove, cssmove, that = this;
		jQuery(this.eID.pos + j).css( 'visibility', 'hidden' );
		jQuery(this.eID.pos + j).slider({
			max: 1000,
			range: 'min',
			animate: false,
			slide: function (event, ui) { 
				if (that.state === 'paused') { 
					that.button(j, 'pause');
				}
				if ((ui.value/10) <= that.load_pc) {
					cssmove = ui.value/10;
					phmove = ui.value*(10.0/that.load_pc);
				} else {
					cssmove = that.load_pc;
					phmove = 100;
				}
				jQuery(that.eID.poscol + j).css('width', cssmove + '%');
				jQuery(that.vars.jpID).jPlayer("playHead", phmove );
				that.state = 'playing';
			}
		});
	};
	
	return mp3j;
}