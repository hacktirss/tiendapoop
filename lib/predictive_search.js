/**
*  Omicrom: Predictive Search Toolset
*  (c) 2016 DETI Desarrollo y Transferencia de Informatica S.A. de C.V.
*  @author Rolando Esquivel Villafaña (REV@Softcoatl)
*/

(function ($) {

    $.fn.activeComboBox = function(form, querySentence, field) {
        this.autocomplete({
            serviceUrl: 'ajax_combo_tool.php',
            onSelect: function () {
                //form.submit();
            },
            showNoSuggestionNotice : true,
            noSuggestionNotice : 'No se econtraron resultados',
            params: {
                sQuery: querySentence,
                sField: field
            },
            minChars: 3,
            orientation: 'auto'
        });
    };
    $.fn.suggestionTool = function(form, fromsSentence, searchField) {
        this.autocomplete({
            serviceUrl: 'ajax_search_tool.php',
            onSelect: function () {
                //form.submit();
            },
            showNoSuggestionNotice : true,
            noSuggestionNotice : 'No se econtraron resultados',
            params: {
            sTable: fromsSentence,
            sSearch: searchField
            },
            minChars: 3,
            orientation: 'auto'
        });
    };
})(jQuery);