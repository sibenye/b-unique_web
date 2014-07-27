function RedNaoEmailEditor()
{
    rnJQuery( "#redNaoAccordion" ).accordion();
    var self=this;
    //noinspection JSUnusedLocalSymbols
    this.Dialog=rnJQuery("#redNaoEmailEditor").dialog(
        {   width:"800",
            height:"750",
            modal:true,
            autoOpen:false,
            create: function(event, ui){
                rnJQuery('.ui-dialog').wrap('<div class="smartFormsSlider" />');
            },
            open: function(event, ui){
                rnJQuery('.ui-widget-overlay').wrap('<div class="smartFormsSlider" />');

            },
            beforeClose:function(event,ui)
            {
               return self.UpdateToEmails();
            }


        });


}
RedNaoEmailEditor.prototype.UpdateToEmails=function()
{
    var selectedToEmails=rnJQuery('#redNaoToEmail').select2('val');

    var selectedEmailsString="";
    for(var i=0;i<selectedToEmails.length;i++)
    {
        if(selectedToEmails[i].indexOf("[field")==0)
        {
            if(!RedNaoLicensingManagerVar.LicenseIsValid('Sorry, you can\'t add fields to the "To Email" box in this version, please use only emails '))
            {
                return false;
            }
        }
        selectedEmailsString+=selectedToEmails[i]+",";
    }
    this.Emails[0].ToEmail=selectedEmailsString;
    return true;
};
RedNaoEmailEditor.prototype.SetupEmailTo=function(emailToOptions,alreadySelectedEmails)
{
    var selectOptions='<optgroup label="'+smartFormsTranslation.SelectAField+'">';
    selectOptions+=emailToOptions;

    for(var i=0;i<alreadySelectedEmails.length;i++)
    {
        if(alreadySelectedEmails[i]=="")
        {
            alreadySelectedEmails.splice(i,1);
            i--;
            continue;
        }
        if(alreadySelectedEmails.indexOf("[field")!=0)
        {
            selectOptions+='<option value="'+alreadySelectedEmails[i]+'">'+alreadySelectedEmails[i]+'</option>'
        }
    }

    selectOptions+='</optgroup>';

    var self=this;
    rnJQuery('#redNaoToEmail').empty();
    rnJQuery('#redNaoToEmail').append(selectOptions);
    rnJQuery('#redNaoToEmail').select2(
        {
            placeholder: "Type email or field (e.g. example@gmail.com)",
            allowClear: true
        }
        ).unbind("dropdown-closed")
        .on("dropdown-closed", function(event) {
            self.AddEmailIfValid(event.val);
        });

    rnJQuery('#redNaoToEmail').select2('val',alreadySelectedEmails);
    rnJQuery('#redNaoEmailEditor .select2-input').on('keyup', function(e) {
        if(e.which==13||e.which == 32)
        {
            var text=rnJQuery(this).val().trim();
            self.AddEmailIfValid(text);
        }
    });
};

RedNaoEmailEditor.prototype.AddEmailIfValid=function(text)
{
    if(text.trim()=="")
        return;
    if(sfRedNaoEmail.prototype.EmailIsValid(text))
        this.AddEmail(text);
    else
        alert('Please type a valid email');
};

RedNaoEmailEditor.prototype.AddEmail=function(email)
{
    rnJQuery('#redNaoToEmail').append(rnJQuery('<option>', {value:email, text: email}));
    var selectedValues=rnJQuery('#redNaoToEmail').select2('val');
    selectedValues.push(email);
    rnJQuery('#redNaoToEmail').select2('val',selectedValues);

};

RedNaoEmailEditor.prototype.OpenEmailEditor=function(redNaoFormElements,emails)
{
    this.Emails=emails;
    this.Dialog.dialog('open');
    var formList=rnJQuery('#redNaoEmailFormFields');
    formList.empty();
    var emailToOptions="";
    for(var i=0;i<redNaoFormElements.length;i++)
    {
        if(redNaoFormElements[i].StoresInformation())
        {
            formList.append('<li><button onclick="RedNaoEmailEditorVar.AddFieldToEmail(\''+redNaoFormElements[i].Options.Id+'\');">'+redNaoFormElements[i].Options.Label+'</button></li>');
            if(redNaoFormElements[i].Options.ClassName=="rednaoemail")
                emailToOptions+='<option value="[field '+redNaoFormElements[i].Options.Id+']">'+redNaoFormElements[i].Options.Label+'</option>';
        }
    }

    var selectedToEmails=emails[0].ToEmail;
    this.SetupEmailTo(emailToOptions,RedNaoGetValueOrEmpty(selectedToEmails).split(','));
};


RedNaoEmailEditor.prototype.CloseEmailEditor=function()
{
    this.Dialog.dialog('close');
};

RedNaoEmailEditor.prototype.AddFieldToEmail=function(id)
{
    tinymce.activeEditor.execCommand('mceInsertContent', false, "[field "+id.trim()+"]");
};



var RedNaoEmailEditorVar=null;
rnJQuery(function(){
    RedNaoEmailEditorVar=new RedNaoEmailEditor();
});