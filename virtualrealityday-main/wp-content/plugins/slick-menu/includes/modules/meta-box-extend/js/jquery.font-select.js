/* GWFSelect for jQuery UI, version 1.1 (2012-05-14) */
(function ($) {
    var FSFonts = [];
    var loadedFonts = {};
    var fontFetch = (function () {
        var fontURL = 'https://www.googleapis.com/webfonts/v1/webfonts?key={key}',
            callbacks = [],
            fetching = false;
        var fetch = function (key) {
	        fontURL = fontURL.replace('{key}', key); 
	        
			setTimeout(function() {
			
	            $.getJSON(fontURL, function (data, status, req) {
	                $.each(data.items, function () { 
		                var font = this;
		                FSFonts.push({
		                	family: font.family,
		                	variants: font.variants
			            }); 
		            });
	                $.each(callbacks, function () { this.call(null); });
	            });
            }, 800);
            
            fetching = true;
        };
        return {
            init: function (key, fn) {
                callbacks.push(fn);
                fetching || fetch(key);
            }
        };
    })();
    $.widget('mlg.fontSelect', {
        options: {
           api_key: ''
        },
        destroy: function () {
            this.fontList.remove();
            this.toggle.remove();
            this.element
                .removeClass('fs-input')
                .removeAttr('readonly')
                .unbind('click.fs')
                .unwrap();
            $('html').unbind('click.fs');
            $.Widget.prototype.destroy.call(this);
        },
        randomize: function () {
            var fonts = this.fontList.find('li');
            var index = Math.floor(Math.random() * fonts.length);
            var randomFont = $(fonts.get(index));
            this._selectFontListItem(randomFont);
        },
        _create: function () {
            var self = this,
                opt = this.options,
                el = this.element,
                useFonts = opt.fonts || FSFonts;
            el.addClass('fs-input')
            .attr('readonly', true)
                .wrap($('<div/>').addClass('fs-wrapper'));
            this.wrapper = el.closest('.fs-wrapper');
            
            this.selectedLabel = $('<div/>').addClass('fs-selected-name');
			this.wrapper.append(this.selectedLabel);
				
			this.toggle = $('<span/>')
                    .addClass('fs-toggle dashicons dashicons-arrow-down-alt2')
                    .insertAfter(el);
                    	
            var createFontList = function () {
                el.after(self._createFontList(useFonts).hide());
                self._selectFontById(el.val());
                
                self._bindHandlers();
            };
            useFonts.length ?
                createFontList(useFonts) :
                fontFetch.init(opt.api_key, createFontList);
        },
        _bindHandlers: function () {
            var self = this,
                loadTimeout = null;
            $('html').bind('click.fs', function (event) {
                self._toggleFontList(false);
            });
            var openFontList = function (event) {
	            if(self.fontList.is(':visible')) {
                	self._toggleFontList(false);
                }else{
	               	self._toggleFontList(true); 
                }
                event.stopPropagation();
            };
            this.element.bind('click.fs', openFontList);
            this.toggle.bind('click.fs', openFontList);
            this.fontList.bind('scroll.fs', function (event) {
                window.clearTimeout(loadTimeout);
                loadTimeout = window.setTimeout(function () {
                    self._loadVisibleFonts();
                }, 250);
            }).bind('click.fs', function (event) {
                var target = $(event.target);
                if (!target.is('li')) { return; }
                self._selectFontListItem(target);
            });
            

            this.element.bind('change', function(event, font) {
	            
	            var val = $(this).val();
	            if(val !== '') {
	            	self._selectFontById(val);
	            }else{
		            self.selectedLabel.empty();
	            }
            });
            
            this.selectedLabel.bind('click.fs', openFontList);
            
        },
        _selectFontListItem: function (li) {
            if (li.hasClass('selected')) { return; }
            this.fontList.find('li.selected').removeClass('selected');
            li = $(li).addClass('selected');
            var fontID = li.data('fontID');
            var fontName = li.data('fontName');
            var fontVariant = li.data('fontVariant');
            var fontLabel = fontName + ' - ' + fontVariant;
 
            var styles = this._fontNameToStyle(fontName, fontVariant);
            this.element.css(styles);
            if (this.element.val() != fontID) {
                this.element
                    .val(fontID)
                    .trigger('change', {
	                    id: fontID, 
	                    name: fontName, 
	                    variant: fontVariant, 
	                    label: fontLabel
	                });
            }
            this._trigger('change', null, styles);
            this._loadFonts([fontID]);
            this._toggleFontList(false);
            
			this.selectedLabel.html(fontLabel);
			this.selectedLabel.css(styles);
            
        },
        _selectFontById: function (id) {
            var fonts = this.fontList.find('li');
            var match = $.grep(fonts, function (li, i) {
                return ($(li).data('fontID') == id);
            });
            if (match.length) {
                this._selectFontListItem($(match).first());
                return true;
            }
            return false;
        },
        _createFontList: function (useFonts) {
            this.fontList = $('<ul/>').addClass('fs-list');
            var self = this;
            $.each(useFonts, function (i, font) {

				$.each(font.variants, function (v, variant) {
					if(variant.search('italic') === -1) {
						
						variant = variant.replace('regular', '400');
						var fontLabel = font.family+' - '+variant;
		
						$('<li/>')
	                    .html(fontLabel)
	                    .data('fontName', font.family)
	                    .data('fontID', font.family+':'+variant)
	                    .data('fontVariant', variant)
	                    .css(self._fontNameToStyle(font.family, variant))
	                    .appendTo(self.fontList);
                    
                    }
                });    
            });
            return this.fontList;
        },
        _fontNameReadable: function (fontName) {
            return fontName.replace(/[\+|:]/g, ' ');
        },
        _fontNameToStyle: function (fontName, variant) {
            return {
                'font-family': fontName,
                'font-weight': variant
            };
        },
        _toggleFontList: function (bool) {
            if (bool) {
                this.toggle.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
                this.wrapper.css({ 'z-index': 999999 });
                this.fontList.show();
                this._loadVisibleFonts();
                var selectedFont = this.fontList.find('li.selected');
                if (selectedFont.length) {
                    this.fontList.scrollTop(selectedFont.position().top);
                }
            } else {
	            this.toggle.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
                this.wrapper.css({ 'z-index': 'auto' });
                this.fontList.scrollTop(0);
                this.fontList.hide();
                
            }
        },
        _loadVisibleFonts: function () {
            if (!this.fontList.is(':visible')) { return; }
            var self = this,
                listTop = this.fontList.scrollTop(),
                listHeight = this.fontList.height(),
                listBottom = listTop + listHeight,
                fonts = this.fontList.find('li'),
                fontsToLoad = [];
            $.each(fonts, function (i, font) {
                font = $(font);
                var fontTop = font.position().top,
                    fontBottom = fontTop + font.outerHeight();
                if ((fontBottom >= 0) && (fontTop < listHeight)) {
                    fontsToLoad.push(font.data('fontID'));
                }
            });
            this._loadFonts(fontsToLoad);
        },
        _loadFonts: function (fontArray) {
            fontArray = $.grep(fontArray, function (fontID) {
                return loadedFonts[fontID];
            }, true);
            
            if (!fontArray.length) { return; }
            
            $.each(fontArray, function (i, fontID) {
                loadedFonts[fontID] = true;
            });
      
            WebFont.load({ google: { families: fontArray } });
        }
    });
})(jQuery);
