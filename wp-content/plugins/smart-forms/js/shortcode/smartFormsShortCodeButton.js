(function() {
    javascriptUrl='';
    tinymce.create('tinymce.plugins.rednao_smart_forms', {
        init : function(ed, url) {
            javascriptUrl=url.substring(0,url.length-13);
            // Register command for when button is clicked
            ed.addCommand('rednao_smart_forms_short_code_clicked', function(a,donationId) {
                tinymce.execCommand('mceInsertContent', false, '[sform]'+donationId+'[/sform]');

                rnJQuery("#smartformsShortCodeDialog").dialog('close');
                rnJQuery("#smartformsShortCodeDialog").remove();
            }),

            // Register buttons - trigger above command when clicked
            ed.addButton('rednao_smart_forms_button', {title : 'Smart Forms', image: javascriptUrl + '/images/smartFormsIcon.png',
            onclick:function()
            {
                var data={
                    action:"rednao_smart_form_list"
                };

                rnJQuery.post(ajaxurl,data,smartFormsAjaxCompleted);

            }});
        }
    });


    tinymce.PluginManager.add('rednao_smart_forms_button', tinymce.plugins.rednao_smart_forms);
})();
var smartFormsShortCodeDialog;
function smartFormsAjaxCompleted(result,status)
{

    if(smartFormsShortCodeDialog==null)
    {
        var smartFormsPopUpForm='<div style=""><div id="smartformsShortCodeDialog"  title="Basic modal dialog"><select id="smartFormList">';
        smartFormsPopUpForm+='</select></div></div>'
        var dialog=rnJQuery(smartFormsPopUpForm);

        smartFormsShortCodeDialog=dialog.dialog({
        modal:true,
        draggable:false,
        title:'Select a Form ',
        resizable:false,
        buttons:[
            {text: "Apply", click: function() {tinymce.execCommand('rednao_smart_forms_short_code_clicked', false, rnJQuery('#smartFormList').val())}},
            {text: "Cancel", click: function() {rnJQuery(this).dialog("close")}}
        ],
            create: function(event, ui){
                rnJQuery('.ui-dialog').wrap('<div class="smartFormsSlider" />');
            },
            open: function(event, ui){
                rnJQuery('.ui-widget-overlay').wrap('<div class="smartFormsSlider" />');
                rnJQuery(".smartFormsConfigurationFields").val('');
            }
        });
    }else{
        smartFormsShortCodeDialog.dialog('open');
        rnJQuery('#redNaoSelection').val('button');
    }

    ajaxFormCompleted(result);

    function ajaxFormCompleted(result) {
        var formArray=rnJQuery.parseJSON(result);
        var options="";
        for(var i=0;i<formArray.length;i++)
        {
            options+=' <option value="'+formArray[i].Id+'">'+formArray[i].Name+'</option>';
        }

        rnJQuery('#smartFormList').empty().append(options);
    }





}


