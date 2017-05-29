/*----------------------------------------------------
 *
 * Karma Builder - A Visual Composer Extension Plugin for Karma WP-Theme
 * by TrueThemes
 *
 * http://themeforest.net/user/TrueThemes/portfolio
 *
 * Back-end Editor JavaScript
 * provides "live preview" of elements within the
 * wordpress content editor
 *
 * @since Karma Builder 1.0
*
----------------------------------------------------*/


/*
====== Notes:

- here's the default "VcButtonView" from Visual Composer....
- VcButtonView is called within vc_map() function like so:  'js_view' => 'VcButtonView'
- Lets rename VcButtonView to: OrbitButtonView
- 'js_view' => 'OrbitButtonView' is already added to vc_map() for button in orbit.php

========


*/
window.OrbitButtonView = vc.shortcode_view.extend({events:function () {
        return _.extend({'click a':'buttonClick'
        }, window.VcToggleView.__super__.events);
    },
        buttonClick:function (e) {
            e.preventDefault();
        },
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitButtonView.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                //Does not know why this original codes, does not seem to work, but serves as good example!
                //var el_class = params.color + ' ' + params.size + ' ';
                //this.$el.find('.wpb_element_wrapper').removeClass(el_class);
                //this.$el.find('a.content').attr({ "class":"true-button content " + el_class });
                
                //construct button html, which is a tag and includes size, color, custom css, true-button as the class names.
                var button_content = "<a href='#' class='ka_button " + params.size + "_button " + params.size + "_" + params.style + "'>" + params.content + "</a>";
                //write the button html to .wpb_element_wrapper
                this.$el.find('.wpb_element_wrapper').html(button_content);
            }
        }
    });
window.OrbitAlertBox = vc.shortcode_view.extend({events:function () {
        return _.extend({'click div':'buttonClick'
        }, window.VcToggleView.__super__.events);
    },
        buttonClick:function (e) {
            e.preventDefault();
        },
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            var closeable = '';
            var closeable_x = '';
            window.OrbitButtonView.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                if (params.closeable == 'true') {
                    closeable = 'closeable';
                    closeable_x = 'closeable-x';
                }
                var el_class = params.style + ' ' + closeable + ' ';
                this.$el.find('.wpb_element_wrapper').removeClass(el_class);
                this.$el.find('div.content').attr({ "class":"true-notification content " + el_class });
                this.$el.find('p').css('font-size', params.font_size).wrap('<div class="' + closeable_x + '"></div>');
            }
        }
    });
window.OrbitContentBox = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitContentBox.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var el_class = params.style + ' ';

                this.$el.find('.wpb_element_wrapper').removeClass(el_class);
                this.$el.find('div.title').attr({ "class":"title true-contentbox-title true-cb-title-" + el_class });
                this.$el.find('div.content').attr({ "class":"content true-contentbox-content true-content-style-" + el_class });

                if (!this.$el.find('.true-contentbox').length) {
                    var wrapper = document.createElement('div');

                    wrapper.className = "true-contentbox";

                    wrapper.appendChild(this.$el.find('div.title').get(0));
                    wrapper.appendChild(this.$el.find('div.content').get(0));
                    this.$el.find('.wpb_element_wrapper').append(wrapper);
                } else {
                    this.$el.find('div.title').html(params.title);
                    this.$el.find('div.content').html(params.content);
                }
            }
        }
    });
window.OrbitDropcap = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitDropcap.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var el_class = params.color + ' ' + params.style + ' ';

                this.$el.find('.wpb_element_wrapper').removeClass(el_class);
                this.$el.find('span.title').attr('class', 'title ' + "true-dropcap-" + params.color);
                this.$el.find('span.letter').attr('class', "letter true-dropcap-" + params.style);

                if (!this.$el.find('.true-dropcap-wrap').length) {
                    var wrapper = document.createElement('div');
                    var inner_wrapper = document.createElement('span');
                    var dropcap_el = document.createElement('span');

                    wrapper.className = "true-dropcap-wrap";
                    inner_wrapper.className = "title true-dropcap-" + params.color;
                    dropcap_el.className = "letter true-dropcap-" + params.style;

                    dropcap_el.innerHTML = this.$el.find('div.dropcap').html();
                    inner_wrapper.appendChild(dropcap_el);

                    wrapper.appendChild(inner_wrapper);
                    wrapper.appendChild(this.$el.find('div.content').get(0));
                    this.$el.find('.wpb_element_wrapper').append(wrapper);
                } else {
                    this.$el.find('span.letter').html(params.dropcap);
                    this.$el.find('div.content').html(params.content);
                }
            }
        }
    });
window.OrbitFeatureListItem = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitFeatureListItem.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var icon_fa = params.icon_fontawesome,
                   content = params.content,
                   icon_color = params.icon_color,
                   border_width = params.border_width,
                   border_color = params.border_color,
                   bg_color = params.bg_color;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-features"></div>');
                wrapper.append('<i class="true-custom-icon fa ' + icon_fa + '"></i>');
                wrapper.append('<div class="true-description">' + content + '</div>');

                this.$('.wpb_element_wrapper').append(wrapper);
                
                this.$('.true-custom-icon').css({
                    'color': icon_color,
                    'border-color': border_color,
                    'border-width': border_width,
                    'background-color': bg_color
                });
            }
        }
    });
window.OrbitIconBox = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitIconBox.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var icon_fa       = params.icon_fontawesome,
                    /* icon_op       = params.icon_openiconic,
                    icon_ty       = params.icon_typicons,
                    icon_ent      = params.icon_entypo,
                    icon_lin      = params.icon_linecons, */
                    icon_size     = params.icon_size,
                    content       = params.content,
                    box_bg_color  = params.box_bg_color,
                    icon_color    = params.icon_color,
                    icon_bg_color = params.icon_bg_color;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-icon-box" style="background-color: ' + box_bg_color + ';"></div>');
                this.$('.wpb_element_wrapper').append(wrapper);

                wrapper.append('<span class="fa-stack ' + icon_size + '"><i class="fa fa-circle fa-stack-2x" style="color: ' + icon_bg_color + ';"/><i class="fa ' + icon_fa + ' fa-stack-1x fa-inverse" style="color: ' + icon_color + ';"/></span>');
                wrapper.append(content);
                wrapper.css('float', 'none');
            }
        }
    });
window.OrbitIconText = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitIconBox.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var icon_fa = params.icon_fontawesome,
                    icon_color = params.icon_color,
                    icon_bg_color = params.icon_bg_color;
                    content = params.content;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-icon-wrap"></div>');
                this.$('.wpb_element_wrapper').append(wrapper);

                wrapper.append('<span class="fa ' + icon_fa + ' true-icon"></span>');
                wrapper.append('<div class="true-icon-text">' + content + '</div>');
                wrapper.css('overflow', 'hidden');

                this.$('.true-icon').css({
                    'background-color': icon_bg_color,
                    'color': icon_color
                });
            }
        }
    });
window.OrbitIconImage = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitIconImage.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var icon = params.icon,
                    content = params.content;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }
				
				//original codes, doesn't seem to work.
                //var wrapper = jQuery('<a class="preview_element true-icon-link true-icon ' + icon + '" href="#nogo">' + content + '</a>');
                //this.$('.wpb_element_wrapper').append(wrapper);
                //rewrite, tested.. works..
                var wrapper = jQuery('<a class="preview_element true-icon true-' + icon + '" href="#nogo">' + content +'</a>');
                this.$el.find('.wpb_element_wrapper').append(wrapper);
            }
        }
    });
window.OrbitNumberCounter = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitNumberCounter.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var number = params.number,
                    divider_height = params.divider_height,
                    title = params.title,
                    number_color = params.number_color,
                    divider_color = params.divider_color,
                    title_color = params.title_color;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-counter-wrap"></div>');
                wrapper.append('<h3 class="true-counter true-zero">' + number + '</h3>');
                wrapper.append('<h4>' + title + '</h4>');
                this.$('.wpb_element_wrapper').append(wrapper);
                this.$('h4').css('color', title_color);
                this.$('h3').css('color', number_color);
            }
        }
    });
window.OrbitPricingBox = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitPricingBox.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var style = params.style,
                    color = params.color,
                    price = params.price,
                    currency = params.currency,
                    plan = params.plan,
                    term = params.term,
                    content = params.content,
                    button_label = params.button_label,
                    button_color = params.button_color,
                    button_size = params.button_size;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                if (style == "style-1") {
                    var wrapper = jQuery('<div class="preview_element true-pricing-column true-pricing-' + style + '"></div>');
                    wrapper.append('<div class="true-pricing-top true-cb-title-' + color + '"><p class="true-pricing-plan">' + plan + '</p><p class="true-pricing-price"><sup>' + currency + '</sup>' + price + '</p><p class="true-pricing-term">' + term + '</p>');
                    wrapper.append(content + '<hr />');
                    wrapper.append('<a class="' + button_size + '_button ' + button_size + '_' + button_color + ' ka_button" href="#nogo">' + button_label + '</a>');
                    wrapper.css('float', 'none');
                } else {
                    var wrapper = jQuery('<div class="preview_element true-pricing-column true-pricing-' + style + '"></div>');
                    wrapper.append('<div class="true-pricing-top true-cb-title-' + color + '"><p class="true-pricing-plan">' + plan + '</p>');
                    wrapper.append(content + '<hr />');
                    wrapper.append('<p class="true-pricing-price"><sup>' + currency + '</sup>' + price + '</p><p class="true-pricing-term">' + term + '</p>');
                    wrapper.append('<a class="' + button_size + '_button ' + button_size + '_' + button_color + ' ka_button" href="#nogo">' + button_label + '</a>');
                    wrapper.css('float', 'none');
                    this.$('.preview_element h2').css('margin-top', '0px');
                }

                this.$('.wpb_element_wrapper').append(wrapper);
            }
        }
    });
window.OrbitProgressBar = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitProgressBar.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var title    = params.title,
                number       = params.number,
                symbol       = params.symbol,
                title_color  = params.title_color,
                number_color = params.number_color,
                bar_color    = params.bar_color,
                bar_height   = params.progress_height,
                track_color  = params.track_color;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-progress-section"></div>');
                wrapper.append('<div class="progress-title clearfix"><h4 class="pull-left">' + title + '</h4><h4 class="pull-right"><span class="true-progress-number">' + number + '</span>' + symbol + '</h4></div>');
                wrapper.append('<div class="progress"><div class="progress-bar" style="width: ' + number + '%;"></div></div>');

                this.$('.wpb_element_wrapper').append(wrapper);
                
                this.$('h4.pull-left').css('color', title_color);
                this.$('h4.pull-right').css('color', number_color);
                this.$('.progress-bar').css('background-color', bar_color);
                this.$('.progress').css('background-color', track_color);
                this.$('.progress').css('height', bar_height);
                this.$('.progress-bar').css('height', bar_height);
            }
        }
    });
window.OrbitProgressBar2 = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitProgressBar2.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var title = params.title,
                    number = params.number,
                    symbol = params.symbol,
                    title_color = params.title_color,
                    number_color = params.number_color,
                    bar_color = params.bar_color,
                    track_color = params.track_color;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-progress-section-vertical"></div>');
                wrapper.append('<div class="progress-wrapper"><div class="progress-bar-vertical" style="height: ' + number + '%;"></div></div>');
                wrapper.append('<h4 class="true-progress-title">' + title + '</h4><h4 class="true-progress-text"><span class="true-progress-number">' + number + '</span>' + symbol + '</h4>');

                this.$('.wpb_element_wrapper').append(wrapper);
                
                this.$('.true-progress-title').css('color', title_color);
                this.$('.true-progress-text').css('color', number_color);
                this.$('.progress-bar-vertical').css('background-color', bar_color);
                this.$('.progress-wrapper').css('background-color', track_color);
            }
        }
    });
window.OrbitServiceListItem = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitServiceListItem.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var icon = params.icon,
                   content = params.content,
                   icon_color = params.icon_color,
                   border_width = params.border_width,
                   border_color = params.border_color,
                   bg_color = params.bg_color;

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                var wrapper = jQuery('<div class="preview_element true-services"></div>');
                wrapper.append('<i class="true-custom-icon fa ' + icon + '"></i>');
                wrapper.append('<div class="true-description">' + content + '</div>');

                this.$('.wpb_element_wrapper').append(wrapper);
                
                this.$('.true-custom-icon').css({
                    'color': icon_color,
                    'border-color': border_color,
                    'border-width': border_width,
                    'background-color': bg_color
                });
            }
        }
    });
window.OrbitSocialIcons = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitSocialIcons.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var design = params.design,
                    icon_style = params.icon_style,
                    networks = [
                        'twitter',
                        'facebook',
                        'dribbble',
                        'flickr',
                        'google',
                        'instagram',
                        'vc_link',
                        'pinterest',
                        'rss',
                        'skype',
                        'vimeo',
                        'wordpress',
                        'youtube'
                    ];

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }

                if (icon_style == "round") {
                    design += " true-round";
                }

                var wrapper = jQuery('<ul class="preview_element true-social true-' + design + '"></ul>');

                for (var i = 0; networks[i]; i += 1) {
                    if (params[networks[i]]) {
                        if (networks[i] == 'vimeo') {
                            networks[i] = 'vimeo-square';
                        }
                        wrapper.append('<li><a class="fa fa-' + networks[i] + '" href="#nogo"></a></li>');
                    }
                }

                this.$('.wpb_element_wrapper').append(wrapper);
            }
        }
    });
window.OrbitImage_1 = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitImage_1.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var attachment_id = params.attachment_id,
                    main_title = params.main_title,
                    sub_title = params.sub_title,
                    content = params.content,
                    box_bg_color = params.box_bg_color,
                    img_border_width = params.img_border_width,
                    img_border_color = params.img_border_color,
                    main_title_color = params.main_title_color,

                    wrapper = jQuery('<div class="preview_element ' +
                        'true-image-box-1" style="width: 474px; max-width: 100%; ' +
                        'overflow: hidden;"></div>');

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }
                wrapper.append('<div class="true-img-wrap">' +
                    '<img class="attachment-large" width="474" ' +
                    'src="//placehold.it/474?text=Image"></div>');
                wrapper.append('<div class="true-text-wrap">' +
                    '<div class="callout-heading-wrap">' +
                    '<h4>' + sub_title + '</h4>' +
                    '<h3>' + main_title + '</h3>' +
                    '</div>' +
                    '<div class="callout-details-wrap">' +
                    content +
                    '</div>' +
                    '</div>');

                wrapper.find('.true-text-wrap').css({
                    'background-color': box_bg_color
                });
                wrapper.find('.true-img-wrap').css({
                    'border-width': img_border_width + 'px',
                    'border-color': img_border_color
                });
                wrapper.find('h3').css('color', main_title_color);
                karma_get_image_url(attachment_id, function(url) {
                   if(attachment_id){
                    wrapper.find('.attachment-large').attr('src', url);
                    }
                });

                this.$('.wpb_element_wrapper').append(wrapper);
            }
        }
    });
window.OrbitImage_2 = vc.shortcode_view.extend({
        changeShortcodeParams:function (model) {
            var params = model.get('params');
            window.OrbitImage_2.__super__.changeShortcodeParams.call(this, model);
            if (_.isObject(params)) {
                var icon = params.icon,
                    custom_icon = params.custom_icon,
                    attachment_id = params.attachment_id,
                    content = params.content,
                    box_bg_color = params.box_bg_color,
                    icon_bg_color = params.icon_bg_color,
                    icon_color = params.icon_color,
                    link_color = params.link_color,

                    wrapper = jQuery('<div class="preview_element ' +
                        'true-image-box-2" style="width: 474px; max-width: 100%; ' +
                        'overflow: hidden;"></div>');

                if (this.$('.preview_element').length) {
                    this.$('.preview_element').remove();
                }
                wrapper.append('<div class="true-img-wrap">' +
                    '<img class="attachment-large" width="474" ' +
                    'src="//placehold.it/474?text=Image"></div>');
                wrapper.append('<div class="true-text-wrap">' +
                    '<span class="icon-circ-wrap">' +
                    '<i class="fa ' + (typeof custom_icon !== 'undefined' ? custom_icon : icon) + '"></i>' +
                    '</span>' +
                    '<div class="callout-details-wrap">' +
                    content +
                    '</div>' +
                    '</div>');

                wrapper.find('.true-text-wrap').css({
                    'background-color': box_bg_color
                });
                wrapper.find('.icon-circ-wrap').css('background-color', icon_bg_color);
                wrapper.find('.' + icon).css('color', icon_color);
                karma_get_image_url(attachment_id, function(url) {
                   if(attachment_id){
                    wrapper.find('.attachment-large').attr('src', url);
                    }
                });

                this.$('.wpb_element_wrapper').append(wrapper);
            }
        }
    });
/* window.OrbitGoogleMap = vc.shortcode_view.extend({
    changeShortcodeParams:function (model) {
        var params = model.get('params');
        window.OrbitGoogleMap.__super__.changeShortcodeParams.call(this, model);
        if (_.isObject(params)) {
            var id = 'orbit_map_' + parseInt(Math.random() * 1000),
                map_element = document.createElement('div');

            // remove existing map if any.
            this.$el.find('[id^="orbit_map_"]').remove();

            map_element.id = id;
            map_element.style.width = (params.google_map_width || 600) + 'px';
            map_element.style.height = (params.google_map_height || 500) + 'px';

            this.$el.append(map_element);

            if (!params.google_map_api_key || params.google_map_api_key.match(/^\s*$/)) {
                alert('The Google Maps element needs an api key');
            } else {
                mapIt();
            }
        }

        function mapIt() {
            var center = {
                    lat: parseFloat(params.google_map_center_lat) || 37.803003,
                    lng: parseFloat(params.google_map_center_long) || -122.277719
                },
                 map_options = {
                    center: center,
                    zoom: parseInt(params.google_map_zoom) || 13
                },
                map = new google.maps.Map(map_element, map_options);

            if (params.google_map_center_marker) {
                var marker_options = {
                        position: center,
                        map: map
                    },
                    info_window,
                    center_marker;

                if (params.google_map_center_marker_custom_icon) {
                    marker_options.icon = params.google_map_center_marker_custom_icon;
                }

                center_marker = new google.maps.Marker(marker_options);

                if (params.google_map_center_marker_info_window_content) {
                    info_window = new google.maps.InfoWindow({
                        content: params.google_map_center_marker_info_window_content
                    });

                    google.maps.event.addListener(center_marker, 'click', function () {
                        info_window.open(map, center_marker);
                    });
                }
            }
        }
    }
}); */