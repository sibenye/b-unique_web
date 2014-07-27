var smartFormsIntegrationFormula={};
smartFormsIntegrationFormula.Formulas={};
var smartFormsDesignMode=true;
function SmartFormsAddNew()
{
    if(typeof smartFormId!='undefined')
        this.id=smartFormId;
    else
        this.id=0;

    var options=null;
    this.EmailTextLoaded=false;
    this.EmailText="";
    this.RestoreDefault();
    this.Emails=[{ToEmail:""}];
    if(typeof smartFormsOptions!='undefined')
    {
        options=smartFormsOptions;
        rnJQuery('#smartFormName').val(smartFormsOptions.Name);
        rnJQuery('#smartFormDescription').val(smartFormsOptions.Description);

        if(RedNaoGetValueOrNull(smartFormsOptions.SendNotificationEmail)=='y')
            rnJQuery('#smartFormsSendNotificationEmail').attr('checked','checked');

        if(RedNaoGetValueOrNull(smartFormsOptions.Emails))
        {
            if(smartFormsOptions.Emails.length>0)
            {
                rnJQuery('#redNaoFromEmail').val(smartFormsOptions.Emails[0].FromEmail);
                rnJQuery('#redNaoFromName').val(smartFormsOptions.Emails[0].FromName);
                this.Emails[0].ToEmail=smartFormsOptions.Emails[0].ToEmail||"";
                rnJQuery('#redNaoEmailSubject').val(smartFormsOptions.Emails[0].EmailSubject);
                this.EmailText=smartFormsOptions.Emails[0].EmailText;
            }
        }
    }

    if(typeof smartFormClientOptions!='undefined')
    {
        rnJQuery('#smartFormsJavascriptText').val(smartFormClientOptions.JavascriptCode);
        rnJQuery('#redirectToInput').val(RedNaoGetValueOrEmpty(smartFormClientOptions.redirect_to));
        rnJQuery('#alertMessageInput').val(RedNaoGetValueOrEmpty(smartFormClientOptions.alert_message));
        if(RedNaoGetValueOrEmpty(smartFormClientOptions.redirect_to_cb)=='y')
            rnJQuery('#redNaoRedirectToCB').attr('checked','checked');
        if(RedNaoGetValueOrEmpty(smartFormClientOptions.alert_message_cb)=='y')
            rnJQuery('#redNaoAlertMessageCB').attr('checked','checked');
        if(RedNaoGetValueOrNull(smartFormClientOptions.Formulas)!=null)
            smartFormsIntegrationFormula.Formulas=smartFormClientOptions.Formulas;
        if(RedNaoGetValueOrNull(smartFormClientOptions.InvalidInputMessage)!=null)
            rnJQuery("#smartFormsInvalidFieldMessage").val(smartFormClientOptions.InvalidInputMessage);
    }else{
        smartFormClientOptions={};
    }


    var formElements=[];
    if(typeof smartFormsElementOptions!='undefined')
        formElements=smartFormsElementOptions;


    this.FormBuilder= new RedNaoFormBuilder(options,formElements,smartFormClientOptions);


    var self=this;
    rnJQuery('#smartFormsBasic').click(self.SmartFormsTagClicked);
    rnJQuery('#smartFormsSaveButton').click(function(e){self.SaveForm(e);});

    rnJQuery('#smartFormsSendNotificationEmail').change(function(){self.NotifyToChanged()});
    rnJQuery('#smartFormsSendNotificationEmail').change();
    rnJQuery('#redNaoEditEmailButton').click(function(e){e.preventDefault();self.EditEmailClicked();});
    rnJQuery('#redNaoRedirectToCB').change(function(){self.DisableOnEmpty(rnJQuery(this),rnJQuery('#redirectToInput'))});
    rnJQuery('#redNaoAlertMessageCB').change(function(){self.DisableOnEmpty(rnJQuery(this),rnJQuery('#alertMessageInput'))});

    rnJQuery('#redNaoRedirectToCB').change();
    rnJQuery('#redNaoAlertMessageCB').change();
    //rnJQuery('#sfAddConditionalLogic').click(function(){self.SfConditionalLogicManager.AddNew();});
    RedNaoEventManager.Subscribe('FormulaButtonClicked',function(data){self.OpenFormulaBuilder(data.FormElement,data.PropertyName,data.AdditionalInformation,data.Image)});




}
SmartFormsAddNew.prototype.DisableOnEmpty=function(checkbox,elementToDisable)
{
    if(checkbox.is(':checked'))
    {
        elementToDisable.removeAttr('disabled');
        elementToDisable.removeClass('redNaoDisabled');
    }else
    {
        elementToDisable.attr('disabled','disabled');
        elementToDisable.addClass('redNaoDisabled');
    }
};


SmartFormsAddNew.prototype.OpenFormulaBuilder=function(formElement,propertyName,additionalInformation,image)
{
    RedNaoFormulaWindowVar.OpenFormulaEditor(this.FormBuilder.RedNaoFormElements,formElement.Options,propertyName,additionalInformation,image);
};

SmartFormsAddNew.prototype.EditEmailClicked=function()
{

    if(!this.EmailTextLoaded)
    {
        try{
            tinymce.get('redNaoTinyMCEEditor').setContent(this.EmailText);
        }catch(exception)
        {
            tinymce.init(tinyMCEPreInit.mceInit["redNaoTinyMCEEditor"]);
            return;
        }

    }
    this.EmailTextLoaded=true;
    RedNaoEmailEditorVar.OpenEmailEditor(this.FormBuilder.RedNaoFormElements,this.Emails);
};

SmartFormsAddNew.prototype.NotifyToChanged=function()
{
    if(rnJQuery('#smartFormsSendNotificationEmail').is(':checked'))
        rnJQuery('#redNaoEditEmailButton').removeAttr('disabled');
    else
        rnJQuery('#redNaoEditEmailButton').attr('disabled','disabled');
};


SmartFormsAddNew.prototype.FillEmailData=function(emailOption)
{
    emailOption.FromEmail=rnJQuery('#redNaoFromEmail').val();
    emailOption.FromName=rnJQuery('#redNaoFromName').val();
    emailOption.ToEmail= this.Emails[0].ToEmail;
    emailOption.EmailSubject=rnJQuery('#redNaoEmailSubject').val();
    if(this.EmailTextLoaded)
        emailOption.EmailText=tinymce.get('redNaoTinyMCEEditor').getContent();
    else
        emailOption.EmailText=this.EmailText;
};

SmartFormsAddNew.prototype.DonationConfigurationIsValid=function()
{
    var formElements=this.FormBuilder.RedNaoFormElements;
    for(var i=0;i<formElements.length;i++)
    {
        if(formElements[i].Options.ClassName=="rednaodonationbutton")
        {
            if(rnJQuery('#redNaoCampaign').val()=='')
            {
                alert('If you are going to use a donation button, please select a campaign before saving');
                this.GoToSmartDonations();
                return false;
            }

            if(rnJQuery('#smartDonationsEmail').val()=='')
            {
                alert('Please select a paypal donation email before saving');
                this.GoToSmartDonations();
                return false;
            }

            if(typeof smartFormsIntegrationFormula.Formulas.DonationFormula=='undefined'||smartFormsIntegrationFormula.Formulas.DonationFormula=='')
            {
                alert('Please setup a donation formula before saving');
                this.GoToSmartDonations();
                return false;
            }
        }

    }

    return true;
};

SmartFormsAddNew.prototype.FormOptionsAreValid = function (formOptions) {
    rnJQuery("#redNaoEditEmailButton").removeClass('redNaoInvalidInput');
    if(formOptions.SendNotificationEmail=='y')
    {
        if(formOptions.Emails[0].EmailText.trim()=="")
        {
            rnJQuery("#redNaoEditEmailButton").addClass('redNaoInvalidInput');
            alert('Please configure the email that is going to sent before saving.');
            this.GoToAfterSubmit();
            return false;
        }
    }

    return true;
};
SmartFormsAddNew.prototype.SaveForm=function(e)
{
    e.preventDefault();
    e.stopPropagation();

    var formOptions=this.GetFormOptions();
    var clientFormOptions=this.GetClientFormOptions(formOptions.UsesCaptcha);
    var elementsOptions=this.FormBuilder.GetFormInformation();

    if(!this.DonationConfigurationIsValid())
        return;

    if(!this.FormOptionsAreValid(formOptions))
        return;

    this.ExecuteSaveRequest(formOptions,clientFormOptions,elementsOptions);

};

SmartFormsAddNew.prototype.ExecuteSaveRequest=function(formOptions,clientFormOptions,elementOptions)
{
    var data={};
    data.form_options=JSON.stringify(formOptions);
    data.element_options=JSON.stringify(elementOptions);
    data.donation_email=rnJQuery('#smartDonationsEmail').val();
    data.client_form_options=JSON.stringify(clientFormOptions);


    var self=this;
    rnJQuery('#smartFormsSaveButton').text('Saving...');
    rnJQuery('#smartFormsSaveButton').attr('disabled','disabled');
    data.id=this.id;
    data.action="rednao_smart_forms_save";
    rnJQuery.post(ajaxurl,data,function(result){
        rnJQuery('#smartFormsSaveButton').text('Save');
        rnJQuery('#smartFormsSaveButton').removeAttr('disabled');
        result=rnJQuery.parseJSON(result);
        alert(result.Message);
        if(result.Message=="saved")
            self.id=result.FormId;
    });
};

SmartFormsAddNew.prototype.GetFormOptions=function()
{
    var formOptions={};
    formOptions.Name=rnJQuery('#smartFormName').val();
    formOptions.Description=rnJQuery('#smartFormDescription').val();
    formOptions.NotifyTo=rnJQuery('#smartFormsSubmissionNotifyTo').val();
    formOptions.LatestId=sfFormElementBase.IdCounter;
    formOptions.SendNotificationEmail=(rnJQuery('#smartFormsSendNotificationEmail').is(':checked')?'y':'n');
    formOptions.Emails=[{}];
    this.FillEmailData(formOptions.Emails[0]);

    var usesCaptcha='n';
    var formElements=this.FormBuilder.RedNaoFormElements;
    for(var i=0;i<formElements.length;i++)
    {
        if(formElements[i].Id=="captcha")
        {
            usesCaptcha='y';
            break;
        }
    }
    formOptions.UsesCaptcha=usesCaptcha;
    formOptions.RedNaoSendThankYouEmail=(rnJQuery('#redNaoSendThankYouEmail').is(':checked')?'y':'n');
    return formOptions;
};

SmartFormsAddNew.prototype.GetClientFormOptions=function(usesCaptcha)
{
    return {
        JavascriptCode:this.GetJavascriptCode(),
        Conditions:this.FormBuilder.Conditions,
        UsesCaptcha:usesCaptcha,
        alert_message:rnJQuery('#alertMessageInput').val(),
        alert_message_cb:(rnJQuery('#redNaoAlertMessageCB').is(':checked')?'y':'n'),
        redirect_to:rnJQuery('#redirectToInput').val(),
        redirect_to_cb:(rnJQuery('#redNaoRedirectToCB').is(':checked')?'y':'n'),
        Campaign:rnJQuery('#redNaoCampaign').val(),
        PayPalEmail:rnJQuery('#smartDonationsEmail').val(),
        PayPalDescription:rnJQuery('#smartDonationsDescription').val(),
        PayPalCurrency:rnJQuery('#smartDonationsCurrencyDropDown').val(),
        Formulas:smartFormsIntegrationFormula.Formulas,
        InvalidInputMessage:rnJQuery("#smartFormsInvalidFieldMessage").val()
    };


};

SmartFormsAddNew.prototype.SendTestEmail=function()
{
    RedNaoEmailEditorVar.UpdateToEmails();
    var emailData={};
    emailData.action="rednao_smart_form_send_test_email";
    emailData.element_options=JSON.stringify(this.FormBuilder.GetFormInformation());
    this.FillEmailData(emailData);

    //noinspection JSUnresolvedVariable
    rnJQuery.post(ajaxurl,emailData,function(result){
        result=rnJQuery.parseJSON(result);
        alert(result.Message);

    });

};

SmartFormsAddNew.prototype.ActivateTab=function(activationName)
{
    rnJQuery('#smartFormsTopTab a').removeClass("nav-tab-active");
    rnJQuery('#smartFormsGeneralDiv,#smartFormsJavascriptDiv,#smartFormsCSSDiv,#smartFormsAfterSubmitDiv,#smartDonationsDiv').css('display','none');

    rnJQuery('#'+activationName+'Tab').addClass('nav-tab-active');
    rnJQuery('#'+activationName+'Div').css('display','block');
};

SmartFormsAddNew.prototype.GoToGeneral=function()
{
    this.ActivateTab('smartFormsGeneral');
};

SmartFormsAddNew.prototype.GoToJavascript=function()
{
    this.ActivateTab('smartFormsJavascript');
};

SmartFormsAddNew.prototype.GoToAfterSubmit=function()
{
    this.ActivateTab('smartFormsAfterSubmit');
};

SmartFormsAddNew.prototype.RestoreDefault=function()
{
    rnJQuery('#smartFormsJavascriptText').val('\
  //AUTO GENERATED CODE, DO NOT DELETE\n\
(function(){var javaObject={\n\n\n\
//YOU CAN PUT YOUR CODE BELLOW\n\n\n\
//jQueryFormReference:A jquery reference of the loaded form\n\
AfterFormLoaded:function(jQueryFormReference){\n\
     //Here you can put code that you want to be executed after the form is loades\n\
},\n\n\n\
//jQueryFormReference:A jquery reference of the loaded form\n\
//formData:An object with the information that is going to be submitted\n\
BeforeFormSubmit:function(formData,jQueryFormReference){\n\
    //Here you can put code that you want to be executed before the form is submitted\n\
}\n\n\n\n\
//MORE AUTO GENERATED CODE, DO NOT DELETE\n\
}; return javaObject;})\
    ');
};

SmartFormsAddNew.prototype.GetJavascriptCode=function()
{
    return rnJQuery('#smartFormsJavascriptText').val();
};

SmartFormsAddNew.prototype.Validate=function()
{
    var javascriptCode=this.GetJavascriptCode();
    try{
        var obj=eval(javascriptCode);
        var code=obj();
        if(typeof code.AfterFormLoaded=='undefined')
        {
            throw 'Method AfterFormLoaded was not found';
        }
        code.AfterFormLoaded(rnJQuery('<form></form>'));
        if(typeof code.BeforeFormSubmit=='undefined')
        {
            throw 'Method BeforeFormSubmit was not found';
        }
        code.BeforeFormSubmit({},rnJQuery('<form></form>'));
    }catch(exception)
    {
        alert('An error ocurred\n'+exception);
        return;
    }

    alert('Code tested successfully!!')
};

var SmartFormsAddNewVar=null;
rnJQuery(function(){SmartFormsAddNewVar=new SmartFormsAddNew();});

