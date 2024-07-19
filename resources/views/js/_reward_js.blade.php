
<script>
$( document ).ready(function() {    
    var $lootTable  = $('#rewardTableBody');
    var $lootRow = $('#rewardRow').find('.reward-row');
    var $itemSelect = $('#rewardRowData').find('.item-select');
    var $currencySelect = $('#rewardRowData').find('.currency-select');
    var $awardSelect = $('#rewardRowData').find('.award-select');
    @if($showLootTables)
        var $tableSelect = $('#rewardRowData').find('.table-select');
    @endif
    @if($showRaffles)
        var $raffleSelect = $('#rewardRowData').find('.raffle-select');
    @endif

    $('#rewardTableBody .selectize').selectize();
    attachRemoveListener($('#rewardTableBody .remove-reward-button'));

    $('#addReward').on('click', function(e) {
        e.preventDefault();
        var $clone = $lootRow.clone();
        $lootTable.append($clone);
        attachAwardTypeListener($clone.find('.award-type'));
        attachRemoveListener($clone.find('.remove-reward-button'));
    });

    $('.award-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.reward-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'Currency') $clone = $currencySelect.clone();
        else if (val == 'Award') $clone = $awardSelect.clone();
        @if($showLootTables)
            else if (val == 'LootTable') $clone = $tableSelect.clone();
        @endif
        @if($showRaffles)
            else if (val == 'Raffle') $clone = $raffleSelect.clone();
        @endif

        $cell.html('');
        $cell.append($clone);
    });

    function attachAwardTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.reward-row-select');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'Award') $clone = $awardSelect.clone();
            @if($showLootTables)
                else if (val == 'LootTable') $clone = $tableSelect.clone();
            @endif
            @if($showRaffles)
                else if (val == 'Raffle') $clone = $raffleSelect.clone();
            @endif

            $cell.html('');
            $cell.append($clone);
            $clone.selectize();
        });
    }

    function attachRemoveListener(node) {
        node.on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    }

});
    
</script>