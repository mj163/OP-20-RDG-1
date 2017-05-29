/* ------------------------------------------------------------------------

This has been modified from it's original version to be HTML5 compatible

Developers: To uncompress this file check out: http://jsbeautifier.org/

 * ------------------------------------------------------------------------- */
 

/* ------------------------------------------------------------------------
 * prettySociable plugin.
 * Version: 1.2.1
 * Description: Include this plugin in your webpage and let people
 * share your content like never before.
 * Website: http://no-margin-for-errors.com/projects/prettySociable/
 * 						
 * Thank You: 
 * Chris Wallace, for the nice icons
 * http://www.chris-wallace.com/2009/05/28/free-social-media-icons-socialize/
 * ------------------------------------------------------------------------- */
 
(function(a){a.prettySociable={version:1.21};a.prettySociable=function(f){a.prettySociable.settings=a.extend({animationSpeed:"fast",opacity:0.8,share_label:"Drag to share",label_position:"top",share_on_label:"Share on ",hideflash:false,hover_padding:0,websites:{facebook:{active:true,encode:true,title:"Facebook",url:"http://www.facebook.com/share.php?u=",icon:social_data.facebook,sizes:{width:70,height:70}},twitter:{active:true,encode:true,title:"Twitter",url:"http://twitter.com/home?status=",icon:social_data.twitter,sizes:{width:70,height:70}},delicious:{active:true,encode:true,title:"Delicious",url:"https://delicious.com/save?v=5&url=",icon:social_data.delicious,sizes:{width:70,height:70}},digg:{active:true,encode:true,title:"Digg",url:"http://digg.com/submit?phase=2&url=",icon:social_data.digg,sizes:{width:70,height:70}},linkedin:{active:false,encode:true,title:"LinkedIn",url:"http://www.linkedin.com/shareArticle?mini=true&ro=true&url=",icon:social_data.linkedin,sizes:{width:70,height:70}},reddit:{active:false,encode:true,title:"Reddit",url:"http://reddit.com/submit?url=",icon:social_data.reddit,sizes:{width:70,height:70}},stumbleupon:{active:false,encode:false,title:"StumbleUpon",url:"http://stumbleupon.com/submit?url=",icon:social_data.stumbleupon,sizes:{width:70,height:70}},tumblr:{active:false,encode:true,title:"tumblr",url:"http://www.tumblr.com/share?v=3&u=",icon:social_data.tumblr,sizes:{width:70,height:70}}},urlshortener:{bitly:{active:false}},tooltip:{offsetTop:0,offsetLeft:15},popup:{width:900,height:500},callback:function(){}},f);var c,f=a.prettySociable.settings,d,h;a.each(f.websites,function(m){var l=new Image();l.src=this.icon});a("a[data-gal^=prettySociable]").hover(function(){_self=this;_container=this;if(a(_self).find("img").size()>0){_self=a(_self).find("img")}else{if(a.browser.msie){if(a(_self).find("embed").size()>0){_self=a(_self).find("embed");a(_self).css({display:"block"})}}else{if(a(_self).find("object").size()>0){_self=a(_self).find("object");a(_self).css({display:"block"})}}}a(_self).css({cursor:"move",position:"relative","z-index":1005});offsetLeft=(parseFloat(a(_self).css("borderLeftWidth")))?parseFloat(a(_self).css("borderLeftWidth")):0;offsetTop=(parseFloat(a(_self).css("borderTopWidth")))?parseFloat(a(_self).css("borderTopWidth")):0;offsetLeft+=(parseFloat(a(_self).css("paddingLeft")))?parseFloat(a(_self).css("paddingLeft")):0;offsetTop+=(parseFloat(a(_self).css("paddingTop")))?parseFloat(a(_self).css("paddingTop")):0;h=a('<div id="ps_hover">         <div class="ps_hd">          <div class="ps_c"></div>         </div>         <div class="ps_bd">          <div class="ps_c">           <div class="ps_s">           </div>          </div>         </div>         <div class="ps_ft">          <div class="ps_c"></div>         </div>         <div id="ps_title">          <div class="ps_tt_l">           '+f.share_label+"          </div>         </div>        </div>").css({width:a(_self).width()+(f.hover_padding+8)*2,top:a(_self).position().top-f.hover_padding-8+parseFloat(a(_self).css("marginTop"))+offsetTop,left:a(_self).position().left-f.hover_padding-8+parseFloat(a(_self).css("marginLeft"))+offsetLeft}).hide().insertAfter(_container).fadeIn(f.animationSpeed);a("#ps_title").animate({top:-15},f.animationSpeed);a(h).find(">.ps_bd .ps_s").height(a(_self).height()+f.hover_padding*2);i("ps_hover",this);k.attach(a(this)[0]);a(this)[0].dragBegin=function(l){_self=this;d=window.setTimeout(function(){a("object,embed").css("visibility","hidden");a(_self).animate({opacity:0},f.animationSpeed);a(h).remove();g.show();j.show(_self);j.follow(l.mouseX,l.mouseY);b.show()},200)};a(this)[0].drag=function(l){j.follow(l.mouseX,l.mouseY)};a(this)[0].dragEnd=function(m,l,n){a("object,embed").css("visibility","visible");a(this).attr("style",0);g.hide();j.checkCollision(m.mouseX,m.mouseY)}},function(){a(h).fadeOut(f.animationSpeed,function(){a(this).remove()})}).click(function(){clearTimeout(d)});var j={show:function(l){j.link_to_share=(a(l).attr("href")!="#")?a(l).attr("href"):location.href;if(f.urlshortener.bitly.active){if(window.BitlyCB){BitlyCB.myShortenCallback=function(p){var n;for(var o in p.results){n=p.results[o];n.longUrl=o;break}j.link_to_share=n.shortUrl};BitlyClient.shorten(j.link_to_share,"BitlyCB.myShortenCallback")}}attributes=a(l).attr("data-gal").split(";");for(var m=1;m<attributes.length;m++){attributes[m]=attributes[m].split(":")}desc=(a("meta[name=Description]").attr("content"))?a("meta[name=Description]").attr("content"):"";if(attributes.length==1){attributes[1]=["title",document.title];attributes[2]=["excerpt",desc]}ps_tooltip=a('<div id="ps_tooltip">          <div class="ps_hd">           <div class="ps_c"></div>          </div>          <div class="ps_bd">           <div class="ps_c">            <div class="ps_s">            </div>           </div>          </div>          <div class="ps_ft">           <div class="ps_c"></div>          </div>             </div>').appendTo("body");a(ps_tooltip).find(".ps_s").html("<p><strong>"+attributes[1][1]+"</strong><br />"+attributes[2][1]+"</p>");i("ps_tooltip")},checkCollision:function(l,m){collision="";scrollPos=e();a.each(c,function(n){if((l+scrollPos.scrollLeft>a(this).offset().left&&l+scrollPos.scrollLeft<a(this).offset().left+a(this).width())&&(m+scrollPos.scrollTop>a(this).offset().top&&m+scrollPos.scrollTop<a(this).offset().top+a(this).height())){collision=a(this).find("a")}});if(collision!=""){a(collision).click()}b.hide();a("#ps_tooltip").remove()},follow:function(l,m){scrollPos=e();f.tooltip.offsetTop=(f.tooltip.offsetTop)?f.tooltip.offsetTop:0;f.tooltip.offsetLeft=(f.tooltip.offsetLeft)?f.tooltip.offsetLeft:0;a("#ps_tooltip").css({top:m+f.tooltip.offsetTop+scrollPos.scrollTop,left:l+f.tooltip.offsetLeft+scrollPos.scrollLeft})}};var b={show:function(){websites_container=a("<ul />");a.each(f.websites,function(m){var l=this;if(l.active){link=a("<a />").attr({href:"#"}).html('<img src="'+l.icon+'" alt="'+l.title+'" width="'+l.sizes.width+'" height="'+l.sizes.height+'" />').hover(function(){b.showTitle(l.title,a(this).width(),a(this).position().left,a(this).height(),a(this).position().top)},function(){b.hideTitle()}).click(function(){shareURL=(l.encode)?encodeURIComponent(j.link_to_share):j.link_to_share;popup=window.open(l.url+shareURL,"prettySociable","location=0,status=0,scrollbars=1,width="+f.popup.width+",height="+f.popup.height)});a("<li>").append(link).appendTo(websites_container)}});a('<div id="ps_websites"><p class="ps_label"></p></div>').append(websites_container).appendTo("body");i("ps_websites");scrollPos=e();a("#ps_websites").css({top:a(window).height()/2-a("#ps_websites").height()/2+scrollPos.scrollTop,left:a(window).width()/2-a("#ps_websites").width()/2+scrollPos.scrollLeft});c=a.makeArray(a("#ps_websites li"))},hide:function(){a("#ps_websites").fadeOut(f.animationSpeed,function(){a(this).remove()})},showTitle:function(p,m,o,l,n){jQuerylabel=a("#ps_websites .ps_label");jQuerylabel.text(f.share_on_label+p);jQuerylabel.css({left:o-jQuerylabel.width()/2+m/2,opacity:0,display:"block"}).stop().animate({opacity:1,top:n-l+45},f.animationSpeed)},hideTitle:function(){a("#ps_websites .ps_label").stop().animate({opacity:0,top:10},f.animationSpeed)}};var g={show:function(){a('<div id="ps_overlay" />').css("opacity",0).appendTo("body").height(a(document).height()).fadeTo(f.animationSpeed,f.opacity)},hide:function(){a("#ps_overlay").fadeOut(f.animationSpeed,function(){a(this).remove()})}};var k={_oElem:null,attach:function(l){l.onmousedown=k._dragBegin;l.dragBegin=new Function();l.drag=new Function();l.dragEnd=new Function();return l},_dragBegin:function(m){var n=k._oElem=this;if(isNaN(parseInt(n.style.left))){n.style.left="0px"}if(isNaN(parseInt(n.style.top))){n.style.top="0px"}var l=parseInt(n.style.left);var o=parseInt(n.style.top);m=m?m:window.event;n.mouseX=m.clientX;n.mouseY=m.clientY;n.dragBegin(n,l,o);document.onmousemove=k._drag;document.onmouseup=k._dragEnd;return false},_drag:function(m){var n=k._oElem;var l=parseInt(n.style.left);var o=parseInt(n.style.top);m=m?m:window.event;n.style.left=l+(m.clientX-n.mouseX)+"px";n.style.top=o+(m.clientY-n.mouseY)+"px";n.mouseX=m.clientX;n.mouseY=m.clientY;n.drag(n,l,o);return false},_dragEnd:function(){var m=k._oElem;var l=parseInt(m.style.left);var n=parseInt(m.style.top);m.dragEnd(m,l,n);document.onmousemove=null;document.onmouseup=null;k._oElem=null}};function e(){if(self.pageYOffset){scrollTop=self.pageYOffset;scrollLeft=self.pageXOffset}else{if(document.documentElement&&document.documentElement.scrollTop){scrollTop=document.documentElement.scrollTop;scrollLeft=document.documentElement.scrollLeft}else{if(document.body){scrollTop=document.body.scrollTop;scrollLeft=document.body.scrollLeft}}}return{scrollTop:scrollTop,scrollLeft:scrollLeft}}function i(m,l){if(a.browser.msie&&a.browser.version==6){if(typeof DD_belatedPNG!="undefined"){if(m=="ps_websites"){a("#"+m+" img").each(function(){DD_belatedPNG.fixPng(a(this)[0])})}else{DD_belatedPNG.fixPng(a("#"+m+" .ps_hd .ps_c")[0]);DD_belatedPNG.fixPng(a("#"+m+" .ps_hd")[0]);DD_belatedPNG.fixPng(a("#"+m+" .ps_bd .ps_c")[0]);DD_belatedPNG.fixPng(a("#"+m+" .ps_bd")[0]);DD_belatedPNG.fixPng(a("#"+m+" .ps_ft .ps_c")[0]);DD_belatedPNG.fixPng(a("#"+m+" .ps_ft")[0])}}}}}})(jQuery);jQuery(document).ready(function(){jQuery.prettySociable();jQuery.prettySociable.settings.urlshortener.bitly.active=true});