/**
 * Prefer native POS selects (same as Sales/Receiving Stock Source).
 * Only multi-select filters keep bootstrap-select; search is always hidden.
 */
(function ($) {
    'use strict';

    function hideSearch($root) {
        ($root && $root.length ? $root : $(document))
            .find('.bootstrap-select .bs-searchbox')
            .attr('hidden', true)
            .hide();
    }

    function toNative($el) {
        if (!$el.length || !$el.is('select')) {
            return;
        }
        if ($el.is('[multiple]')) {
            return;
        }
        if ($el.closest('.lp-search-select').length || $el.hasClass('lp-search-select__native')) {
            return;
        }

        try {
            if ($el.data('selectpicker') && $.fn.selectpicker) {
                $el.selectpicker('destroy');
            }
        } catch (err) {
            // ignore
        }

        $el.removeClass('selectpicker show-menu-arrow bs-select-hidden');
        if (!$el.hasClass('form-control')) {
            $el.addClass('form-control');
        }
        if (!$el.hasClass('input-sm') && !$el.hasClass('input-lg')) {
            $el.addClass('input-sm');
        }
        $el.addClass('pos-native-select');

        var $parent = $el.parent();
        if ($parent.hasClass('bootstrap-select')) {
            $el.insertBefore($parent);
            $parent.remove();
        }
    }

    function normalize(context) {
        var $ctx = context ? $(context) : $(document);
        var $scope = $ctx.find('.neo-global-content, .bootstrap-dialog, .modal');

        if ($ctx.is('.neo-global-content, .bootstrap-dialog, .modal')) {
            $scope = $scope.add($ctx);
        }
        if (!$scope.length && $ctx.is(document)) {
            $scope = $('.neo-global-content, .bootstrap-dialog, .modal');
        }

        $scope.find('select').each(function () {
            toNative($(this));
        });

        hideSearch($ctx);
    }

    $(function () {
        if ($.fn.selectpicker && $.fn.selectpicker.Constructor && $.fn.selectpicker.Constructor.DEFAULTS) {
            $.fn.selectpicker.Constructor.DEFAULTS.liveSearch = false;
        }

        normalize(document);

        window.setTimeout(function () {
            normalize(document);
        }, 80);

        $(document).on('shown.bs.modal', function (e) {
            normalize(e.target);
        });

        $(document).on('loaded.bs.select shown.bs.select', function () {
            hideSearch($(document));
        });
    });
})(jQuery);
