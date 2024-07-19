<script>
    $(document).ready(function() {
        var $userAwardCategory = $('#userAwardCategory');
        $userAwardCategory.on('change', function(e) {
            refreshCategory();
        });
        $('.awardcase-select-all').on('click', function(e) {
            e.preventDefault();
            selectVisible();
        });
        $('.awardcase-clear-selection').on('click', function(e) {
            e.preventDefault();
            deselectVisible();
        });
        $('.awardcase-checkbox').on('change', function() {
            $checkbox = $(this);
            var rowId = "#awardRow" + $checkbox.val()
            if($checkbox.is(":checked")) {
                $(rowId).addClass('category-selected');
                $(rowId).find('.quantity-select').prop('name', 'stack_quantity[]')
            }
            else {
                $(rowId).removeClass('category-selected');
                $(rowId).find('.quantity-select').prop('name', '')
            }
        });
        $('#toggle-checks').on('click', function() {
            ($(this).is(":checked")) ? selectVisible() : deselectVisible();
        });
        
        function refreshCategory() {
            var display = $userAwardCategory.val();
            $('.user-award').addClass('hide');
            $('.user-awards .category-' + display).removeClass('hide');
            $('#toggle-checks').prop('checked', false);
        }
        function selectVisible() {
            var $target = $('.user-award:not(.hide)');
            $target.addClass('category-selected');
            $target.find('.awardcase-checkbox').prop('checked', true);
            $('#toggle-checks').prop('checked', true);
            $target.find('.quantity-select').prop('name', 'stack_quantity[]');
        }
        function deselectVisible() {
            var $target = $('.user-award:not(.hide)');
            $target.removeClass('category-selected');
            $target.find('.awardcase-checkbox').prop('checked', false);
            $('#toggle-checks').prop('checked', false);
            $target.find('.quantity-select').prop('name', '');
        }
    });
</script>