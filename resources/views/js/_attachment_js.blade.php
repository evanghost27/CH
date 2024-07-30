<script>
    $(document).ready(function() {
        var $attachments = $('#attachmentsBody');
        var $attachmentRow = $('#attachmentRow').find('.attachment-row');
        var $itemSelect = $('#attachmentRowData').find('.item-select');
        var $newsSelect = $('#attachmentRowData').find('.news-select');
        var $figureSelect = $('#attachmentRowData').find('.figure-select');
        var $faunaSelect = $('#attachmentRowData').find('.fauna-select');
        var $floraSelect = $('#attachmentRowData').find('.flora-select');
        var $factionSelect = $('#attachmentRowData').find('.faction-select');
        var $conceptSelect = $('#attachmentRowData').find('.concept-select');
        var $promptSelect = $('#attachmentRowData').find('.prompt-select');
        var $locationSelect = $('#attachmentRowData').find('.location-select');
        var $eventSelect = $('#attachmentRowData').find('.event-select');

        $('#attachmentsBody .selectize').selectize();
        attachRemoveListener($('#attachmentsBody .remove-attachment-button'));

        $('#addAttachment').on('click', function(e) {
            e.preventDefault();
            var $clone = $attachmentRow.clone();
            $attachments.append($clone);
            attachAttachmentTypeListener($clone.find('.attachment-type'));
            attachRemoveListener($clone.find('.remove-attachment-button'));
        });

        $('.attachment-type').on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.attachment-row-select');

            var $clone = null;
            if (val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Prompt') $clone = $promptSelect.clone();
            else if (val == 'Figure') $clone = $figureSelect.clone();
            else if (val == 'Fauna') $clone = $faunaSelect.clone();
            else if (val == 'Flora') $clone = $floraSelect.clone();
            else if (val == 'Faction') $clone = $factionSelect.clone();
            else if (val == 'Concept') $clone = $conceptSelect.clone();
            else if (val == 'Location') $clone = $locationSelect.clone();
            else if (val == 'Event') $clone = $eventSelect.clone();
            else if (val == 'News') $clone = $newsSelect.clone();

            $cell.html('');
            $cell.append($clone);
        });

        function attachAttachmentTypeListener(node) {
            node.on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.attachment-row-select');

                var $clone = null;
                if (val == 'Item') $clone = $itemSelect.clone();
                else if (val == 'Prompt') $clone = $promptSelect.clone();
                else if (val == 'Figure') $clone = $figureSelect.clone();
                else if (val == 'Fauna') $clone = $faunaSelect.clone();
                else if (val == 'Flora') $clone = $floraSelect.clone();
                else if (val == 'Faction') $clone = $factionSelect.clone();
                else if (val == 'Concept') $clone = $conceptSelect.clone();
                else if (val == 'Location') $clone = $locationSelect.clone();
                else if (val == 'Event') $clone = $eventSelect.clone();
                else if (val == 'News') $clone = $newsSelect.clone();

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
