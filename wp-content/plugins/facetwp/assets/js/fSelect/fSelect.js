(function($) {

    $.fn.fSelect = function(options) {

        if (typeof options == 'string' ) {
            var settings = options;
        }
        else {
            var settings = $.extend({
                placeholder: 'Select some options',
                numDisplayed: 3,
                overflowText: '{n} selected',
                searchText: 'Search',
                showSearch: true
            }, options);
        }


        /**
         * Constructor
         */
        function fSelect(select, settings) {
            this.$select = $(select);
            this.settings = settings;
            this.create();
        }


        /**
         * Prototype class
         */
        fSelect.prototype = {
            create: function() {
                this.settings.multiple = this.$select.is('[multiple]');
                var multiple = this.settings.multiple ? ' multiple' : '';
                this.$select.wrap('<div class="fs-wrap' + multiple + '" tabindex="0"></div>');
                this.$select.before('<div class="fs-label-wrap"><div class="fs-label">' + this.settings.placeholder + '</div><span class="fs-arrow"></span></div>');
                this.$select.before('<div class="fs-dropdown hidden"><div class="fs-options"></div></div>');
                this.$select.addClass('hidden');
                this.$wrap = this.$select.closest('.fs-wrap');
                this.reload();
            },

            reload: function() {
                if (this.settings.showSearch) {
                    var search = '<div class="fs-search"><input type="search" placeholder="' + this.settings.searchText + '" /></div>';
                    this.$wrap.find('.fs-dropdown').prepend(search);
                }
                this.selected = [].concat(this.$select.val()); // force an array
                var choices = this.buildOptions(this.$select);
                this.$wrap.find('.fs-options').html(choices);
                this.reloadDropdownLabel();
            },

            destroy: function() {
                this.$wrap.find('.fs-label-wrap').remove();
                this.$wrap.find('.fs-dropdown').remove();
                this.$select.unwrap().removeClass('hidden');
            },

            buildOptions: function($element) {
                var $this = this;

                var choices = '';
                $element.children().each(function(i, el) {
                    var $el = $(el);

                    if ('optgroup' == $el.prop('nodeName').toLowerCase()) {
                        choices += '<div class="fs-optgroup">';
                        choices += '<div class="fs-optgroup-label">' + $el.prop('label') + '</div>';
                        choices += $this.buildOptions($el);
                        choices += '</div>';
                    }
                    else {
                        var val = $el.prop('value');

                        // exclude the first option in multi-select mode
                        if (0 < i || '' != val || ! $this.settings.multiple) {
                            var disabled = $el.is(':disabled') ? ' disabled' : '';
                            var selected = -1 < $.inArray(val, $this.selected) ? ' selected' : '';
                            choices += '<div class="fs-option' + selected + disabled + '" data-value="' + val + '"><span class="fs-checkbox"><i></i></span><div class="fs-option-label">' + $el.html() + '</div></div>';
                        }
                    }
                });

                return choices;
            },

            reloadDropdownLabel: function() {
                var settings = this.settings;
                var labelText = [];

                this.$wrap.find('.fs-option.selected').each(function(i, el) {
                    labelText.push($(el).find('.fs-option-label').text());
                });

                if (labelText.length < 1) {
                    labelText = settings.placeholder;
                }
                else if (labelText.length > settings.numDisplayed) {
                    labelText = settings.overflowText.replace('{n}', labelText.length);
                }
                else {
                    labelText = labelText.join(', ');
                }

                this.$wrap.find('.fs-label').html(labelText);
                this.$select.change();
            }
        }


        /**
         * Loop through each matching element
         */
        return this.each(function() {
            var data = $(this).data('fSelect');

            if (!data) {
                data = new fSelect(this, settings);
                $(this).data('fSelect', data);
            }

            if (typeof settings == 'string') {
                data[settings]();
            }
        });
    }


    /**
     * Events
     */
    window.fSelect = {
        'active': null,
        'idx': -1
    };

    $(document).on('click', '.fs-option:not(.disabled)', function() {
        var $wrap = $(this).closest('.fs-wrap');
        var do_close = false;

        if ($wrap.hasClass('multiple')) {
            var selected = [];

            $(this).toggleClass('selected');
            $wrap.find('.fs-option.selected').each(function(i, el) {
                selected.push($(el).attr('data-value'));
            });
        }
        else {
            var selected = $(this).attr('data-value');
            $wrap.find('.fs-option').removeClass('selected');
            $(this).addClass('selected');
            do_close = true;
        }

        $wrap.find('select').val(selected);
        $wrap.find('select').fSelect('reloadDropdownLabel');

        if (do_close) {
            closeDropdown($wrap);
        }
    });

    $(document).on('keyup', '.fs-search input', function(e) {
        if (40 == e.which) { // down
            $(this).blur();
            return;
        }

        var $wrap = $(this).closest('.fs-wrap');
        var keywords = $(this).val();

        $wrap.find('.fs-option, .fs-optgroup-label').removeClass('hidden');

        if ('' != keywords) {
            $wrap.find('.fs-option').each(function() {
                var regex = new RegExp(keywords, 'gi');
                if (null === $(this).find('.fs-option-label').text().match(regex)) {
                    $(this).addClass('hidden');
                }
            });

            $wrap.find('.fs-optgroup-label').each(function() {
                var num_visible = $(this).closest('.fs-optgroup').find('.fs-option:not(.hidden)').length;
                if (num_visible < 1) {
                    $(this).addClass('hidden');
                }
            });
        }

        setIndexes($wrap);
    });

    $(document).on('click', function(e) {
        var $el = $(e.target);
        var $wrap = $el.closest('.fs-wrap');

        if (0 < $wrap.length) {
            if ($el.hasClass('fs-label')) {
                var is_hidden = $wrap.find('.fs-dropdown').hasClass('hidden');

                if (is_hidden) {
                    openDropdown($wrap);
                }
                else {
                    closeDropdown($wrap);
                }
            }
        }
        // clicked outside, close all fSelect boxes
        else {
            closeDropdown(/* null */);
        }
    });

    $(document).on('keydown', function(e) {
        var $wrap = window.fSelect.active;
        var $target = $(e.target);

        // toggle the dropdown on space
        if ($target.hasClass('fs-wrap')) {
            if (32 == e.which) {
                $target.find('.fs-label').trigger('click');
                return;
            }
        }
        // preserve spaces during search
        else if (0 < $target.closest('.fs-search').length) {
            if (32 == e.which) {
                return;
            }
        }
        else if (null === $wrap) {
            return;
        }

        if (38 == e.which) { // up
            e.preventDefault();

            $wrap.find('.fs-option').removeClass('hl');

            if (window.fSelect.idx > 0) {
                window.fSelect.idx--;
                $wrap.find('.fs-option[data-index=' + window.fSelect.idx + ']').addClass('hl');
                setScroll($wrap);
            }
            else {
                window.fSelect.idx = -1;
                $wrap.find('.fs-search input').focus();
            }
        }
        else if (40 == e.which) { // down
            e.preventDefault();

            var last_index = $wrap.find('.fs-option:last').attr('data-index');
            if (window.fSelect.idx < parseInt(last_index)) {
                window.fSelect.idx++;
                $wrap.find('.fs-option').removeClass('hl');
                $wrap.find('.fs-option[data-index=' + window.fSelect.idx + ']').addClass('hl');
                setScroll($wrap);
            }
        }
        else if (32 == e.which || 13 == e.which) { // space, enter
            e.preventDefault();

            $wrap.find('.fs-option.hl').click();
        }
        else if (27 == e.which) { // esc
            closeDropdown($wrap);
        }
    });

    function setIndexes($wrap) {
        $wrap.find('.fs-option:not(.hidden,.disabled)').each(function(i, el) {
            $(el).attr('data-index', i);
            $wrap.find('.fs-option').removeClass('hl');
        });
        $wrap.find('.fs-search input').focus();
        window.fSelect.idx = -1;
    }

    function setScroll($wrap) {
        var $container = $wrap.find('.fs-options');
        var $selected = $wrap.find('.fs-option.hl');

        var itemMin = $selected.offset().top + $container.scrollTop();
        var itemMax = itemMin + $selected.outerHeight();
        var containerMin = $container.offset().top + $container.scrollTop();
        var containerMax = containerMin + $container.outerHeight();

        if (itemMax > containerMax) { // scroll down
            var to = $container.scrollTop() + itemMax - containerMax;
            $container.scrollTop(to);
        }
        else if (itemMin < containerMin) { // scroll up
            var to = $container.scrollTop() - containerMin - itemMin;
            $container.scrollTop(to);
        }
    }

    function openDropdown($wrap) {
        window.fSelect.active = $wrap;
        window.fSelect.initial_values = $wrap.find('select').val();
        $wrap.find('.fs-dropdown').removeClass('hidden');
        setIndexes($wrap);
    }

    function closeDropdown($wrap) {
        if ('undefined' == typeof $wrap && null != window.fSelect.active) {
            $wrap = window.fSelect.active;
        }
        if ('undefined' !== typeof $wrap) {
            // only trigger if the values have changed
            var initial_values = window.fSelect.initial_values;
            var current_values = $wrap.find('select').val();
            if (JSON.stringify(initial_values) != JSON.stringify(current_values)) {
                $(document).trigger('fs:changed', $wrap);
            }
        }

        $('.fs-dropdown').addClass('hidden');
        window.fSelect.active = null;
    }

})(jQuery);