"use strict";
/************************************************************************************* Formula Methods ***************************************************************************************************/
var SmartFormsStyleScopeField=1;
//used in an string that is evaluated as function
//noinspection JSUnusedGlobalSymbols
function RedNaoGetValueFromArray(array)
{
    var value=0;

    for(var i=0;i<array.length;i++)
    {
        var aux=parseFloat(array[i].amount);
        if(isNaN(aux))
           aux=0;
        value+=aux;
    }

    return value;
}


function RedNaoListContainsValue(options,value)
{
    var arrayOfValues;
    if(typeof value.selectedValues !='undefined')
        arrayOfValues=value.selectedValues;
    else
    {
        arrayOfValues=[];
        arrayOfValues.push(value);
    }

    for(var i=0;i<arrayOfValues.length;i++)
    {
        for(var h=0;h<options.length;h++)
            if(options[h].toLowerCase().trim()==arrayOfValues[i].label.toLowerCase().trim())
                return true;
    }

    return false;
}


/************************************************************************************* End or formula methods ***************************************************************************************************/




function RedNaoFormElementEscape(property)
{
    return property.replace(' ','_');
}

function sfRedNaoCreateFormElementByName(elementName,options)
{
    if(elementName=='rednaotextinput')
        return new sfTextInputElement(options);
    if(elementName=='rednaoprependedtext')
        return new sfPrependTexElement(options);
    if(elementName=='rednaoappendedtext')
        return new sfAppendedTexElement(options);
    if(elementName=='rednaoprependedcheckbox')
        return new sfPrependCheckBoxElement(options);
    if(elementName=='rednaoappendedcheckbox')
        return new sfAppendCheckBoxElement(options);
    if(elementName=='rednaobuttondropdown')
        return 'rednaobuttondropdown';
    if(elementName=='tabradioscheckboxes')
        return 'tabradioscheckboxes';
    if(elementName=='rednaomultiplecheckboxes')
        return new sfMultipleCheckBoxElement(options);
    if(elementName=='rednaoselectbasic')
        return new sfSelectBasicElement(options);
    if(elementName=='rednaodonationamount')
        return new sfDonationAmountElement(options);

    if(elementName=='rednaofilebutton')
        return 'rednaofilebutton';
    if(elementName=='rednaosinglebutton')
        return 'rednaosinglebutton';
    if(elementName=='rednaodoublebutton')
        return 'rednaodoublebutton';
    if(elementName=='rednaotitle')
        return new sfTitleElement(options);
    if(elementName=='rednaotextarea')
        return  new sfTextAreaElement(options);
    if(elementName=='rednaomultipleradios')
        return new sfMultipleRadioElement(options);
    if(elementName=='rednaodonationbutton')
        return new sfDonationButtonElement(options);
    if(elementName=='rednaodonationrecurrence')
        return new sfRecurrenceElement(options);
    if(elementName=='rednaosubmissionbutton')
        return new sfRedNaoSubmissionButton(options);

    if(elementName=='rednaodatepicker')
        return new sfRedNaoDatePicker(options);
    if(elementName=='rednaoname')
        return new sfRedNaoName(options);
    if(elementName=='rednaoaddress')
        return new sfRedNaoAddress(options);
    if(elementName=='rednaophone')
        return new sfRedNaoPhone(options);
    if(elementName=='rednaoemail')
        return new sfRedNaoEmail(options);
    if(elementName=='rednaonumber')
        return new sfRedNaoNumber(options);
    if(elementName=='rednaocaptcha')
        return new sfRedNaoCaptcha(options);

    for(var i=0;i<sfFormElementBase.Extensions.length;i++)
        if(sfFormElementBase.Extensions[i].Name==elementName)
            return sfFormElementBase.Extensions[i].Create(options);

    throw 'Element Type Not Found';
}

/************************************************************************************* Base Class  ***************************************************************************************************/
function sfFormElementBase(options)
{
    //Variable declared in another javascript;
    //noinspection JSUnresolvedVariable
    this.Translations=SmartFormsElementsTranslation;
    this.StyleTags={};
    if(options==null)
    {
        this.Options={};
        this.Options.ClassName="";
        this.Options.IsRequired='n';
        this.Options.Formulas={};
        this.Options.Styles={};

        sfFormElementBase.IdCounter++;
        this.Id='rnField'+sfFormElementBase.IdCounter;
        while(!SmartFormsFieldIsAvailable(this.Id))
        {
            sfFormElementBase.IdCounter++;
            this.Id='rnField'+sfFormElementBase.IdCounter;
        }

        this.Options.Id=this.Id;
        this.IsNew=true;
        this.GenerateDefaultStyle();
    }
    else
    {
        this.Id=options.Id;
        this.IsNew=false;
        if(typeof options.IsRequired=='undefined')
            options.IsRequired='n';
        this.Options=options;
        if(typeof this.Options.Styles=='undefined')
            this.Options.Styles={};
        if(typeof this.Options.Formulas=='undefined')
            this.Options.Formulas={};
        else{
            if(typeof RedNaoFormulaManagerVar!='undefined')
                for(var property in this.Options.Formulas)
                {
                    RedNaoFormulaManagerVar.AddFormula(this,this.Options.Formulas[property]);
                }
        }


    }
    this.Properties=null;
    this.amount=0;
}
sfFormElementBase.Extensions=[];
sfFormElementBase.IdCounter=0;

sfFormElementBase.prototype.FirePropertyChanged=function(val){

    RedNaoEventManager.Publish('formPropertyChanged',{FieldName:this.Id, Value:val});

};

sfFormElementBase.prototype.SetDefaultIfUndefined=function(propertyName,defaultValue)
{
    if(typeof this.Options[propertyName]=='undefined')
        this.Options[propertyName]=defaultValue;
};


sfFormElementBase.prototype.GenerateDefaultStyle=function()
{
};

sfFormElementBase.prototype.GetRootContainer=function()
{
    return rnJQuery("#"+this.Id);
};

sfFormElementBase.prototype.GetElementByClassName=function(className)
{
    return this.GetRootContainer().find("."+className);
};

sfFormElementBase.prototype.RefreshElement=function()
{
    var element=rnJQuery("#"+this.Id);
    var labelWidth=element.find('.rednao_label_container').width();
    var controlWidth=element.find('.redNaoControls').width();
    element.find(".rednao_label_container, .redNaoControls").remove();
    element.find(".redNaoOneColumn").remove();
    var generatedElement=rnJQuery(this.GenerateInlineElement());
    element.append(generatedElement);
    this.JQueryElement=generatedElement;
    this.GenerationCompleted(element);
    if(!smartFormsDesignMode)
    {
        element.find('.rednao_label_container').width(labelWidth);
        element.find('.redNaoControls').width(controlWidth);
    }
    return element;
};
sfFormElementBase.prototype.GenerateHtml=function(jqueryElement)
{
    var newElement=rnJQuery('<div class="rednao-control-group '+this.Options.ClassName+'" id="'+this.Id+'" style="margin-bottom:15px;clear:both;">'+this.GenerateInlineElement()+'</div>');
    jqueryElement.replaceWith(newElement );
    this.JQueryElement=newElement;
    this.GenerationCompleted(newElement);
    this.ApplyAllStyles();
    return newElement;

};

sfFormElementBase.prototype.AppendElementToContainer=function(jqueryElement)
{
    var JQueryElement=rnJQuery( '<div class="rednao-control-group '+this.Options.ClassName+'" id="'+this.Id+'" style="margin-bottom:15px;">'+this.GenerateInlineElement()+'</div>');
    jqueryElement.append(JQueryElement);
    this.JQueryElement=JQueryElement;
    this.GenerationCompleted(JQueryElement);
    this.ApplyAllStyles();

};

sfFormElementBase.prototype.GetFriendlyName=function()
{
    return this.Options.Label;
};

sfFormElementBase.prototype.StoresInformation=function()
{
    return true;
};


sfFormElementBase.prototype.CreateProperties=function()
{
    throw 'Abstract method';
};

sfFormElementBase.prototype.GenerateInlineElement=function()
{
    throw 'Abstract method';
};

sfFormElementBase.prototype.GetProperties=function()
{
    if(this.Properties==null)
    {
        this.Properties=[];
        this.CreateProperties();
    }

    return this.Properties;
};


sfFormElementBase.prototype.GetPropertyName=function()
{
    return RedNaoEscapeHtml(RedNaoFormElementEscape(this.Options.Label));
};


sfFormElementBase.prototype.GeneratePropertiesHtml=function(jQueryObject)
{
    var properties=this.GetProperties();

    for(var i=0;i<properties.length;i++)
    {
        properties[i].CreateProperty(jQueryObject);
    }
};


sfFormElementBase.prototype.GetValueString=function()
{

};

sfFormElementBase.prototype.GenerationCompleted=function(jQueryElement)
{

};

sfFormElementBase.prototype.IsValid=function()
{
    return true;
};

sfFormElementBase.prototype.ClearInvalidStyle=function()
{

};

sfFormElementBase.prototype.Clone=function()
{
   var newObject=  jQuery.extend(true, {}, this);
    sfFormElementBase.IdCounter++;
    newObject.Id='rnField'+sfFormElementBase.IdCounter;
    newObject.Options.Id=newObject.Id;
    newObject.Properties=[];
    newObject.CreateProperties();

    return newObject;
};

sfFormElementBase.prototype.GetValuePath=function()
{
    return '';
};

sfFormElementBase.prototype.GetLabelPath=function()
{
    return 'formData.'+this.Id+'.label';;
};

sfFormElementBase.prototype.GetStyleTagForElement=function(elementName)
{
    if(typeof this.StyleTags[elementName]=='undefined')
    {
        this.StyleTags[elementName]=rnJQuery("<style type='text/css'></style>");
        rnJQuery("head").append(this.StyleTags[elementName]);
    }

    return this.StyleTags[elementName];
};

sfFormElementBase.prototype.ApplyAllStyles=function()
{
    for(var elements in this.Options.Styles)
        this.ApplyTagStyleForElement(elements);
};

sfFormElementBase.prototype.ApplyTagStyleForElement=function(elementName)
{
    if(typeof this.Options.Styles[elementName]=='undefined')
        return;

    var style="";
    var elementProperties=this.Options.Styles[elementName];
    for(var styleName in elementProperties.Properties)
    {
        //noinspection JSUnfilteredForInLoop
        style+=styleName+":"+elementProperties.Properties[styleName]+" !important;";
    }
    //noinspection JSUnresolvedVariable
    var selector=this.GetSelectorByScope(elementProperties.Scope,elementName);

    var tag=this.GetStyleTagForElement(elementName);
    tag.empty();
    tag.append( selector+' {'+style+'}')


};

sfFormElementBase.prototype.GetSelectorByScope=function(scope,elementName)
{
    if(scope==SmartFormsStyleScopeField)
        return '#'+this.Id + " ."+elementName;

    throw ("Undefined scope");
};


/************************************************************************************* Title Element ***************************************************************************************************/

function sfTitleElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Title";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaotitle";
        this.Options.Title="Title";
    }
}
sfTitleElement.prototype=Object.create(sfFormElementBase.prototype);

sfTitleElement.prototype.GetFriendlyName=function()
{
    return this.Options.Title;
};


sfTitleElement.prototype.CreateProperties=function()
{

    this.Properties.push(new SimpleTextProperty(this,this.Options,"Title","Title",{ManipulatorType:'basic'}));
};

sfTitleElement.prototype.GenerateInlineElement=function()
{
    return '<legend class="redNaoLegend redNaoOneColumn">'+RedNaoEscapeHtml(this.Options.Title)+'</legend>';
};




sfTitleElement.prototype.GetValueString=function()
{
    return '';

};

sfTitleElement.prototype.StoresInformation=function()
{
    return false;
};

/************************************************************************************* Text Element ***************************************************************************************************/

function sfTextInputElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Text Input";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaotextinput";
        this.Options.Label="Text Input";
        this.Options.Placeholder="Placeholder";
        this.Options.Value="";
        this.Options.ReadOnly='n';
        this.Options.Width="";
    }else{
        this.SetDefaultIfUndefined('Value','');
        this.SetDefaultIfUndefined('ReadOnly','n');
        this.SetDefaultIfUndefined('Width','');
    }





}

sfTextInputElement.prototype=Object.create(sfFormElementBase.prototype);

sfTextInputElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Value","Value",{ManipulatorType:'basic',RefreshFormData:true}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ReadOnly","Read Only",{ManipulatorType:'basic'}));

};

sfTextInputElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <input style="'+additionalStyle+'" '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" class="redNaoInputText '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'" value="'+RedNaoEscapeHtml(this.Options.Value)+'">'+
                '</div>';
};


sfTextInputElement.prototype.GetValueString=function()
{
    return {value:rnJQuery('#'+this.Id+ ' .redNaoInputText').val()};
};

sfTextInputElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};


sfTextInputElement.prototype.IsValid=function()
{
     if(rnJQuery('#'+this.Id+ ' .redNaoInputText').val()==""&&this.Options.IsRequired=='y')
     {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
     }

    return true;
};

//noinspection JSUnusedLocalSymbols
sfTextInputElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputText').change(function(){self.FirePropertyChanged(self.GetValueString());});
};

/************************************************************************************* Prepend Text Element ***************************************************************************************************/

function sfPrependTexElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Prepend Text";

    if(this.IsNew)
    {
        this.Options.Label="Prepend Text";
        this.Options.ClassName="rednaoprependedtext";
        this.Options.Placeholder="Placeholder";
        this.Options.Prepend="Prepend";
        this.Options.Value='';
        this.Options.Checked='y';
        this.Options.Width='';
    }else{
        this.SetDefaultIfUndefined('Value','');
        this.SetDefaultIfUndefined('Width','');

    }



}

sfPrependTexElement.prototype=Object.create(sfFormElementBase.prototype);

sfPrependTexElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Prepend","Prepend",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Value","Value",{ManipulatorType:'basic',RefreshFormData:true}));

};

sfPrependTexElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';


    return '<div class="rednao_label_container"><label class="rednao_control_label" for="prependedtext">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
            <div class="redNaoControls">\
               <div class="rednao-input-prepend">\
                    <span class="redNaoPrepend">'+RedNaoEscapeHtml(this.Options.Prepend)+'</span>\
                    <input style="'+additionalStyle+'"  name="prependedtext" class="redNaoInputText" placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" type="text" value="'+RedNaoEscapeHtml(this.Options.Value)+'">\
                </div>\
            </div>';
};




sfPrependTexElement.prototype.GetValueString=function()
{
    return {value:rnJQuery('#'+this.Id+ ' .redNaoInputText').val()};
};

sfPrependTexElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};


sfPrependTexElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id+ ' .redNaoInputText').val()==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }
    return true;
};

//noinspection JSUnusedLocalSymbols
sfPrependTexElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputText').change(function(){self.FirePropertyChanged(self.GetValueString());});
};

/************************************************************************************* Appended Text Element ***************************************************************************************************/

function sfAppendedTexElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Appended Text";

    if(this.IsNew)
    {
        this.Options.Label="Appended Text";
        this.Options.ClassName="rednaoappendedtext";
        this.Options.Placeholder="Placeholder";
        this.Options.Append="Append";
        this.Options.Value="";
        this.Options.Width='';
    }else{
        this.SetDefaultIfUndefined('Value','');
        this.SetDefaultIfUndefined('Width','');

    }
}

sfAppendedTexElement.prototype=Object.create(sfFormElementBase.prototype);

sfAppendedTexElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Append","Append",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Value","Value",{ManipulatorType:'basic',RefreshFormData:true}));

};

sfAppendedTexElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';


    return '<div class="rednao_label_container"><label class="rednao_control_label" for="appendedtext">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
            <div class="redNaoControls">\
                <div class="rednao-input-append">\
                    <input style="'+additionalStyle+'" name="appendedtext"  placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" type="text" class="redNaoInputText" value="'+RedNaoEscapeHtml(this.Options.Value)+'">\
                    <span class="redNaoAppend">'+RedNaoEscapeHtml(this.Options.Append)+'</span>\
                </div>\
            </div>';


};




sfAppendedTexElement.prototype.GetValueString=function()
{
    return  {value:rnJQuery('#'+this.Id+ ' .redNaoInputText').val()};
};

sfAppendedTexElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};

sfAppendedTexElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id+ ' .redNaoInputText').val()==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }
    return true;
};

//noinspection JSUnusedLocalSymbols
sfAppendedTexElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputText').change(function(){self.FirePropertyChanged(self.GetValueString());});
};


/************************************************************************************* Prepend Checkbox Element ***************************************************************************************************/

function sfPrependCheckBoxElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Prepend Checkbox";

    if(this.IsNew)
    {
        this.Options.Label="Prepend Checkbox";
        this.Options.ClassName="rednaoprependedcheckbox";
        this.Options.Placeholder="Placeholder";
        this.Options.IsChecked='n';
        this.Options.Value="";
        this.Options.Width='';
    }else{
        this.SetDefaultIfUndefined('Value','');
        this.SetDefaultIfUndefined('Width','');

    }
}

sfPrependCheckBoxElement.prototype=Object.create(sfFormElementBase.prototype);

sfPrependCheckBoxElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsChecked","Is Checked",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Value","Value",{ManipulatorType:'basic',RefreshFormData:true}));

};

sfPrependCheckBoxElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';


    return '<div class="rednao_label_container"><label class="rednao_control_label" for="prependedcheckbox">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <div class="input-prepend">\
                    <span class="redNaoPrepend">\
                        <label class="rednao_checkbox">\
                            <input  type="checkbox" class="redNaoRealCheckBox"  '+(this.Options.IsChecked=='y'? 'checked="checked"':'')+'/>\
                        </label>\
                    </span>\
                    <input style="'+additionalStyle+'" id="prependedcheckbox" name="prependedcheckbox"  class="redNaoInputText" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" value="'+RedNaoEscapeHtml(this.Options.Value)+'"/>\
                    </div>\
                </div>';


};



sfPrependCheckBoxElement.prototype.GetValueString=function()
{
    return  {checked:(rnJQuery('#'+this.Id).find('.redNaoRealCheckBox').is(':checked')?'Yes':'No'),value:rnJQuery('#'+this.Id+ ' .redNaoInputText').val()};
};

sfPrependCheckBoxElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};


sfPrependCheckBoxElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id+ ' .redNaoInputText').val()==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }
    return true;
};

//noinspection JSUnusedLocalSymbols
sfPrependCheckBoxElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputText,#'+this.Id+' .redNaoRealCheckBox').change(function(){self.FirePropertyChanged(self.GetValueString());});
};
/************************************************************************************* Append Checkbox Element ***************************************************************************************************/

function sfAppendCheckBoxElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Append Checkbox";

    if(this.IsNew)
    {
        this.Options.Label="Append Checkbox";
        this.Options.ClassName="rednaoappendedcheckbox";
        this.Options.Placeholder="Placeholder";
        this.Options.IsChecked='n';
        this.Options.Value="";
        this.Options.Width='';
    }else{
        this.SetDefaultIfUndefined('Value','');
        this.SetDefaultIfUndefined('Width','');

    }
}

sfAppendCheckBoxElement.prototype=Object.create(sfFormElementBase.prototype);

sfAppendCheckBoxElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsChecked","Is Checked",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Value","Value",{ManipulatorType:'basic',RefreshFormData:true}));

};

sfAppendCheckBoxElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';


    return '<div class="rednao_label_container"><label class="rednao_control_label" for="appendedcheckbox">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
            <div class="redNaoControls">\
                <div class="rednao-input-append">\
                    <input style="'+additionalStyle+'" id="appendedcheckbox" class="redNaoInputText" name="appendedcheckbox" class="span2" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" value="'+RedNaoEscapeHtml(this.Options.Value)+'"/>\
                        <span class="redNaoAppend">\
                            <input type="checkbox" class="redNaoRealCheckBox"   '+(this.Options.IsChecked=='y'? 'checked="checked"':'')+'/>\
                        </span>\
                </div>\
            </div>';


};



sfAppendCheckBoxElement.prototype.GetValueString=function()
{
    return  {checked:(rnJQuery('#'+this.Id).find('.redNaoRealCheckBox').is(':checked')?'Yes':'No'),value:rnJQuery('#'+this.Id+ ' .redNaoInputText').val()};
};

sfAppendCheckBoxElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};

sfAppendCheckBoxElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id+ ' .redNaoInputText').val()==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }
    return true;
};

//noinspection JSUnusedLocalSymbols
sfAppendCheckBoxElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputText,#'+this.Id+' .redNaoRealCheckBox').change(function(){self.FirePropertyChanged(self.GetValueString());});
};
/************************************************************************************* Text Area Element ***************************************************************************************************/

function sfTextAreaElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Text Area";

    if(this.IsNew)
    {
        this.Options.Label="Text Area";
        this.Options.DefaultText="";
        this.Options.ClassName="rednaotextarea";
        this.Options.Value="";
        this.Options.Width='';
        this.Options.Height='';
        this.Options.Placeholder='Placeholder';
        this.Options.Disabled="n";
        this.Options.MaxLength=''
    }else{
        this.SetDefaultIfUndefined('Value','');
        this.SetDefaultIfUndefined('Width','');
        this.SetDefaultIfUndefined('Height','');
        this.SetDefaultIfUndefined('Placeholder','');
        this.SetDefaultIfUndefined('Disabled','n');
        this.SetDefaultIfUndefined('MaxLength','');
    }

    this.MaxLength=parseFloat(this.Options.MaxLength);
}

sfTextAreaElement.prototype=Object.create(sfFormElementBase.prototype);

sfTextAreaElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"DefaultText","Value",{ManipulatorType:'basic',RefreshFormData:true,MultipleLine:true}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic',RefreshFormData:true}));
    this.Properties.push(new SimpleNumericProperty(this,this.Options,"MaxLength","Max Character",{ManipulatorType:'basic',Placeholder:"No Limit"}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Height","Height",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"Disabled","Disabled",{ManipulatorType:'basic'}));



};

sfTextAreaElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';

    if(!isNaN(parseFloat(this.Options.Height)))
        additionalStyle+='height:'+this.Options.Height+'px'+' !important;';

    var disabled="";
    if(this.Options.Disabled=='y')
        disabled='disabled="disabled"';


    var html=  '<div class="rednao_label_container"><label class="rednao_control_label" for="textarea">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                <textarea '+(!isNaN(this.MaxLength)?'maxlength="'+this.MaxLength.toString()+'"':'')+'  '+disabled+' placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" style="'+additionalStyle+'" name="textarea" class="redNaoTextAreaInput '+(!isNaN(this.MaxLength)?'redNaoTextAreaInputWordCount':'')+'">'+RedNaoEscapeHtml(this.Options.DefaultText)+'</textarea>';
    if(!isNaN(this.MaxLength))
        html+='<span class="smartFormsCharacterCount">'+this.MaxLength.toString()+'</span>';
    html+='</div>';

    return html;
};



sfTextAreaElement.prototype.GetValueString=function()
{
    return  {value:rnJQuery('#'+this.Id+ ' .redNaoTextAreaInput').val()};
};

sfTextAreaElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};


sfTextAreaElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id+ ' .redNaoTextAreaInput').val()==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextAreaInput').addClass('redNaoInvalid');
        return false;
    }
    return true;
};

//noinspection JSUnusedLocalSymbols
sfTextAreaElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoTextAreaInput').change(function(){self.FirePropertyChanged(self.GetValueString());});
    if(!isNaN(this.MaxLength))
        rnJQuery('#'+this.Id+ ' .redNaoTextAreaInput').bind('keyup keydown',function(){
            var length=rnJQuery(this).val().length;
            var charactersRemaining=self.MaxLength-length;
            var wordCounter=self.GetElementByClassName('smartFormsCharacterCount');
            wordCounter.text(charactersRemaining.toString());

            if(charactersRemaining<=20)
                wordCounter.addClass("smartFormsAlmostFull");
            else
                wordCounter.removeClass("smartFormsAlmostFull");

        });
};

/*************************************************************************************Multiple Radio Element ***************************************************************************************************/

function sfMultipleRadioElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Multiple Radio";
    var i=undefined;
    if(this.IsNew)
    {
        this.Options.Label="Multiple Radio";
        this.Options.ClassName="rednaomultipleradios";
        this.Options.Orientation='v';
        this.Options.Options=[{label:'Option 1',value:0,sel:'n'},{label:'Option 2',value:0,sel:'n'},{label:'Option 3',value:0,sel:'n'}];
    }else
    {
        if(RedNaoGetValueOrNull(this.Options.Orientation)==null)
            this.Options.Orientation='v';
        if(this.Options.Options.length>0&&typeof this.Options.Options[0].sel=='undefined')
        {
            this.Options.Options[0].sel='y';
            for(i=1;i<this.Options.Options.length;i++)
                this.Options.Options[i].sel='n';
        }
        if(this.Options.Options.length>0&&typeof this.Options.Options[i]=='string')
        {
            var aux=[];
            for(i=0;i<this.Options.Options.length;i++)
            {
                aux.push({label:this.Options.Options[i]});
            }

            this.Options.Options=aux;
        }
    }


}

sfMultipleRadioElement.prototype=Object.create(sfFormElementBase.prototype);

sfMultipleRadioElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new ArrayProperty(this,this.Options,"Options","Options",{ManipulatorType:'basic',SelectorType:'radio'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new ComboBoxProperty(this,this.Options,"Orientation","Orientation",{ManipulatorType:'basic',Values:[{label:'Vertical',value:'v'},{label:'Horizontal',value:'h'}]}));



};

sfMultipleRadioElement.prototype.GenerateInlineElement=function()
{
    var orientationClass='';
    if(this.Options.Orientation=='h')
        orientationClass='redNaoHorizontalElements';

    var html=  '<div class="rednao_label_container"><label class="rednao_control_label">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
        <div class="redNaoControls">';

    var checked='';
    for(var i=0;i<this.Options.Options.length;i++)
    {
        if(this.Options.Options[i].sel=='y')
            checked='checked="checked"';
        else
            checked='';

        html+='<label class="redNaoRadio '+orientationClass+'" for="radios-0">\
                    <input '+checked+' class="redNaoInputRadio" type="radio" name="'+this.GetPropertyName()+'"  value="'+RedNaoEscapeHtml(this.Options.Options[i].value)+'" '+checked+'>'+RedNaoEscapeHtml(rnJQuery.trim(this.Options.Options[i].label))+'</input>\
                </label>';

        checked="";

    }

    html+='</div>';
    return html;
};





sfMultipleRadioElement.prototype.GetValueString=function()
{
    var jQueryElement=rnJQuery('#'+this.Id).find(':checked');
    if(jQueryElement.length>0)
        this.amount=parseFloat(jQueryElement.val());
    if(isNaN(this.amount))
        this.amount=0;
    return  {value:rnJQuery.trim(jQueryElement.parent().parent().text().trim()),amount:this.amount};
};


sfMultipleRadioElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.amount';
};


sfMultipleRadioElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id).find(':checked').length<=0&&this.Options.IsRequired=='y')
    {
        this.SetUpICheck('iradio_minimal-red');
        return false;
    }

    return true;
};

sfMultipleRadioElement.prototype.ClearInvalidStyle=function()
{
    this.SetUpICheck('iradio_minimal');
};

sfMultipleRadioElement.prototype.SetUpICheck=function(style)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputRadio').iCheck({radioClass: style});
    rnJQuery('#'+this.Id+ ' .redNaoInputRadio').on('ifChecked', function(event){
        if(event.type ==="ifChecked"){
            self.FirePropertyChanged(self.GetValueString());
        }
    });
};

//noinspection JSUnusedLocalSymbols
sfMultipleRadioElement.prototype.GenerationCompleted=function(jQueryElement)
{
    this.SetUpICheck('iradio_minimal');
};

/*************************************************************************************Multiple Checkbox Element ***************************************************************************************************/

function sfMultipleCheckBoxElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Multiple Checkboxes";
    var i=undefined;
    if(this.IsNew)
    {
        this.Options.Label="Multiple Checkbox";
        this.Options.ClassName="rednaomultiplecheckboxes";
        this.Options.Orientation='v';
        this.Options.Options=[{label:'Check 1',value:0,sel:'n'},{label:'Check 2',value:0,sel:'n'},{label:'Check 3',value:0,sel:'n'}];
    }else
    {
        if(RedNaoGetValueOrNull(this.Options.Orientation)==null)
            this.Options.Orientation='v';
        if(this.Options.Options.length>0&&typeof this.Options.Options[0].sel=='undefined')
        {
            for(i=0;i<this.Options.Options.length;i++)
                this.Options.Options[i].sel='n';
        }

        if(this.Options.Options.length>0&&typeof this.Options.Options[i]=='string')
        {
            var aux=[];
            for(i=0;i<this.Options.Options.length;i++)
            {
                aux.push({label:this.Options.Options[i]});
            }

            this.Options.Options=aux;
        }
    }


}

sfMultipleCheckBoxElement.prototype=Object.create(sfFormElementBase.prototype);

sfMultipleCheckBoxElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new ArrayProperty(this,this.Options,"Options","Options",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new ComboBoxProperty(this,this.Options,"Orientation","Orientation",{ManipulatorType:'basic',Values:[{label:'Vertical',value:'v'},{label:'Horizontal',value:'h'}]}));
};

sfMultipleCheckBoxElement.prototype.GenerateInlineElement=function()
{
    var orientationClass='';
    if(this.Options.Orientation=='h')
        orientationClass='redNaoHorizontalElements';

    var html=  '<div class="rednao_label_container"><label class="rednao_control_label">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
        <div class="redNaoControls">';

    var checked='';
    for(var i=0;i<this.Options.Options.length;i++)
    {
        if(this.Options.Options[i].sel=='y')
            checked='checked="checked"';
        else
            checked='';

        html+='<label class="redNaoCheckBox '+orientationClass+'" for="radios-0">\
                    <input type="checkbox" class="redNaoInputCheckBox" name="'+this.GetPropertyName()+'"  value="'+RedNaoEscapeHtml(this.Options.Options[i].value)+'" '+checked+'/>'+RedNaoEscapeHtml(this.Options.Options[i].label)+'\
                </label>';

        checked="";

    }

    html+='</div>';
    return html;
};






sfMultipleCheckBoxElement.prototype.GetValueString=function()
{
    this.amount=0;
    var jQueryElement=rnJQuery('#'+this.Id).find(':checked');
    var data={};
    data.selectedValues=[];
    if(jQueryElement.length>0)
    {
        for(var i=0;i<jQueryElement.length;i++)
        {
            if(jQueryElement.length>0)
                this.amount=parseFloat(rnJQuery(jQueryElement[i]).val());
            if(isNaN(this.amount))
                this.amount=0;
            data.selectedValues.push({value:rnJQuery(jQueryElement[i]).parent().parent().text().trim(),amount:this.amount,label:rnJQuery(jQueryElement[i]).parent().parent().text().trim()})
        }
    }

    return data;

};


sfMultipleCheckBoxElement.prototype.GetValuePath=function()
{
    return 'RedNaoGetValueFromArray(formData.'+this.Id+'.selectedValues)';
};


sfMultipleCheckBoxElement.prototype.IsValid=function()
{
    if(rnJQuery('#'+this.Id).find(':checked').length<=0&&this.Options.IsRequired=='y')
    {
        this.SetUpICheck('icheckbox_minimal-red');
        return false;
    }
    return true;
};

sfMultipleCheckBoxElement.prototype.ClearInvalidStyle=function()
{
    this.SetUpICheck('icheckbox_minimal');
};

sfMultipleCheckBoxElement.prototype.SetUpICheck=function(style)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputCheckBox').iCheck({checkboxClass: style});
    rnJQuery('#'+this.Id+ ' .redNaoInputCheckBox').on('ifChanged', function(event){
        if(event.type ==="ifChanged"){
            self.FirePropertyChanged(self.GetValueString());
        }
    });
};



//noinspection JSUnusedLocalSymbols
sfMultipleCheckBoxElement.prototype.GenerationCompleted=function(jQueryElement)
{
      this.SetUpICheck('icheckbox_minimal');
};


/*************************************************************************************Select Basic Element ***************************************************************************************************/

function sfSelectBasicElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Select Basic";
    var i=undefined;
    if(this.IsNew)
    {
        this.Options.Label="Select Basic";
        this.Options.ClassName="rednaoselectbasic";
        this.Options.DefaultText="Select a value";
        this.Options.Options=[{label:'Option 1',value:0,sel:'n'},{label:'Option 2',value:0,sel:'n'},{label:'Option',value:0,sel:'n'}];
        this.SetDefaultIfUndefined('Width','');

    }else
    {
        this.SetDefaultIfUndefined('Width','');
        this.SetDefaultIfUndefined("DefaultText","");
        if(this.Options.Options.length>0&&typeof this.Options.Options[0].sel=='undefined')
        {
            this.Options.Options[0].sel='y';
            for(i=1;i<this.Options.Options.length;i++)
                this.Options.Options[i].sel='n';
        }
        if(this.Options.Options.length>0&&typeof this.Options.Options[i]=='string')
        {
            var aux=[];
            for(i=0;i<this.Options.Options.length;i++)
            {
                aux.push({label:this.Options.Options[i]});
            }

            this.Options.Options=aux;
        }
    }


}

sfSelectBasicElement.prototype=Object.create(sfFormElementBase.prototype);

sfSelectBasicElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"DefaultText","Default text",{ManipulatorType:'basic'}));
    this.Properties.push(new ArrayProperty(this,this.Options,"Options","Options",{ManipulatorType:'basic',SelectorType:'radio'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Width","Width",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));




};

sfSelectBasicElement.prototype.GenerateInlineElement=function()
{
    var additionalStyle='';
    if(!isNaN(parseFloat(this.Options.Width)))
        additionalStyle='width:'+this.Options.Width+'px'+' !important;';


    var html=  '<div class="rednao_label_container"><label class="rednao_control_label">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
        <div class="redNaoControls">\
        <select style="'+additionalStyle+'" name="'+this.GetPropertyName()+'" class="redNaoSelect">';

    var selected='';
    var i=undefined;
    if(this.Options.DefaultText!="")
    {
        selected='selected="selected"';
        for(i=0;i<this.Options.Options.length;i++)
        {
            if(this.Options.Options[i].sel=='y')
                selected='';
        }
        html+='<option   value="redNaoNone" '+selected+'>'+RedNaoEscapeHtml(this.Options.DefaultText)+'</opton>';
    }



    selected='';
    for(i=0;i<this.Options.Options.length;i++)
    {
        if(this.Options.Options[i].sel=='y')
            selected='selected="selected"';
        else
            selected='';

        html+='<option   value="'+RedNaoEscapeHtml(this.Options.Options[i].value)+'" '+selected+'>'+RedNaoEscapeHtml(this.Options.Options[i].label)+'</opton>';

        selected="";

    }
    html+='</select></div>';
    return html;
};



sfSelectBasicElement.prototype.GetValueString=function()
{
    var jQueryElement=rnJQuery('#'+this.Id+ ' .redNaoSelect option:selected');
    if(jQueryElement.length>0)
        this.amount=parseFloat(jQueryElement.val());
    if(isNaN(this.amount))
        this.amount=0;
    return  {value:jQueryElement.text(),amount:this.amount};
};

sfSelectBasicElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.amount';
};


sfSelectBasicElement.prototype.IsValid=function()
{
    if(this.Options.IsRequired=='y'&&(this.GetValueString().value==this.Options.DefaultText||rnJQuery('#'+this.Id+ ' .redNaoSelect option:selected').length==0))
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }
    return true;
};


//noinspection JSUnusedLocalSymbols
sfSelectBasicElement.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoSelect').change(function(){self.FirePropertyChanged(self.GetValueString());});
};

/*************************************************************************************Donation Button***************************************************************************************************/



function sfDonationButtonElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Donation Button";

    if(this.IsNew)
    {
        this.Options.Label="Donation Button";
        this.Options.ClassName="rednaodonationbutton";
        this.Options.Image='https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif';
    }else
    {
        if(typeof this.Options.Image=='undefined')
            this.Options.Image='https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif';
    }


}

sfDonationButtonElement.prototype=Object.create(sfFormElementBase.prototype);

sfDonationButtonElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Image","Image Url",{ManipulatorType:'basic'}));
};

sfDonationButtonElement.prototype.GenerateInlineElement=function()
{
    return '<div class="rednao_label_container"></div>\
            <div class="redNaoControls">' +
            '<div class="redNaoControls">' +
                '<input type="image" class="redNaoDonationButton" src="'+this.Options.Image+'" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' +
           '</div>';
};


sfDonationButtonElement.prototype.GetValueString=function()
{
    return '';

};

sfDonationButtonElement.prototype.StoresInformation=function()
{
    return false;
};


/************************************************************************************* Recurrence Element  ***************************************************************************************************/



function sfRecurrenceElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Donation Button";

    if(this.IsNew)
    {
        this.Options.Label="Recurrence";
        this.Options.ClassName="rednaodonationrecurrence";
        this.Options.ShowOneTime='y';
        this.Options.ShowDaily='y';
        this.Options.ShowWeekly='y';
        this.Options.ShowMonthly='y';
        this.Options.ShowYearly='y';

    }


}

sfRecurrenceElement.prototype=Object.create(sfFormElementBase.prototype);

sfRecurrenceElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowOneTime","Show one time option",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowDaily","Show daily option",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowWeekly","Show weekly option",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowMonthly","Show monthly option",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowYearly","Show yearly option",{ManipulatorType:'basic'}));
};

sfRecurrenceElement.prototype.GenerateInlineElement=function()
{
    var html= '<div class="rednao_label_container">' +
                    '<label class="rednao_control_label">'+RedNaoEscapeHtml(this.Options.Label)+'</label>' +
                    '</div>' +
              '<div class="redNaoControls">' +
              '<select class="redNaoSelect redNaoRecurrence">';
    var selected='selected="selected"';

    if(this.Options.ShowOneTime=='y')
    {
         html+='<option value="OT" '+selected+'>One Time</option>';
        selected='';
    }

    if(this.Options.ShowDaily=='y')
    {
        html+='<option value="D" '+selected+'>Daily</option>';
        selected='';
    }

    if(this.Options.ShowWeekly=='y')
    {
        html+='<option value="W" '+selected+'>Weekly</option>';
        selected='';
    }

    if(this.Options.ShowMonthly=='y')
    {
        html+='<option value="M" '+selected+'>Monthly</option>';
        selected='';
    }

    if(this.Options.ShowYearly=='y')
    {
        html+='<option value="Y" '+selected+'>Yearly</option>';
        selected='';
    }

    html+='</select></div>';

    return html;
};



sfRecurrenceElement.prototype.GetValueString=function()
{
    var jQueryElement=rnJQuery('#'+this.Id+ ' .redNaoSelect option:selected');
    return {value:jQueryElement.val()};

};


sfRecurrenceElement.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};



/************************************************************************************* Submission Button ***************************************************************************************************/



function sfRedNaoSubmissionButton(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Submit Button";

    if(this.IsNew)
    {
        this.Options.ClassName="rednaosubmissionbutton";
        this.Options.ButtonText="Submit";


    }
}
sfRedNaoSubmissionButton.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoSubmissionButton.prototype.GetFriendlyName=function()
{
    return this.Options.ButtonText;
};

sfRedNaoSubmissionButton.prototype.CreateProperties=function()
{
    this.Properties.push(new SimpleTextProperty(this,this.Options,"ButtonText","Button Text",{ManipulatorType:'basic'}));
};

sfRedNaoSubmissionButton.prototype.GenerateInlineElement=function()
{
    return '<div class="rednao_label_container"></div><div class="redNaoControls"><input type="submit" class="redNaoSubmitButton" value="'+RedNaoEscapeHtml(this.Options.ButtonText)+'" /></div>';
};


sfRedNaoSubmissionButton.prototype.GetValueString=function()
{
    return '';

};

sfRedNaoSubmissionButton.prototype.StoresInformation=function()
{
    return false;
};



/************************************************************************************* Date Picker ***************************************************************************************************/



function sfRedNaoDatePicker(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Date Picker";

    if(this.IsNew)
    {
        this.Options.ClassName="rednaodatepicker";
        this.Options.Label="Date";
        this.Options.DateFormat="MM-dd-yy";
        this.Options.Value='';
    }

}

sfRedNaoDatePicker.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoDatePicker.prototype.CreateProperties=function()
{
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"DateFormat","Date Format",{ManipulatorType:'basic'}));

};

sfRedNaoDatePicker.prototype.GenerateInlineElement=function()
{
    return '<div class="rednao_label_container"><label class="rednao_control_label">'+RedNaoEscapeHtml(this.Options.Label)+'</label></div><div class="redNaoControls"><input type="text" class="redNaoDatePicker"  /></div>';

};


sfRedNaoDatePicker.prototype.GetValueString=function()
{
    var selectedDate= rnJQuery('#'+this.Id).find('.redNaoDatePicker').datepicker('getDate');
    if(selectedDate==null)
        selectedDate ="";
    else
        selectedDate=selectedDate.getFullYear()+'-'+(selectedDate.getMonth()+1)+'-'+selectedDate.getDate();
    return {value:selectedDate};

};

sfRedNaoDatePicker.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};



sfRedNaoDatePicker.prototype.StoresInformation=function()
{
    return true;
};

//noinspection JSUnusedLocalSymbols
sfRedNaoDatePicker.prototype.GenerationCompleted=function(jQueryElement)
{
    rnJQuery('#'+this.Id).find('.redNaoDatePicker').datepicker({
        dateFormat:this.Options.DateFormat,
        beforeShow: function() {
            rnJQuery('#ui-datepicker-div').wrap('<div class="smartFormsSlider"></div>')
        },
        onClose: function() {
            rnJQuery('#ui-datepicker-div').unwrap();
        }
    });

    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoDatePicker').change(function(){self.FirePropertyChanged(self.GetValueString());});


};

sfRedNaoDatePicker.prototype.IsValid=function()
{
    return true;
};

/************************************************************************************* Name ***************************************************************************************************/

function sfRedNaoName(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Text Input";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaoname";
        this.Options.Label="Name";
        this.Options.FirstNamePlaceholder="First Name";
        this.Options.LastNamePlaceholder="Last Name";
        this.Options.FirstNameValue="";
        this.Options.LastNameValue="";
        this.Options.ReadOnly='n'
    }





}

sfRedNaoName.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoName.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"FirstNamePlaceholder","First name placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"LastNamePlaceholder","Last name placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ReadOnly","Read Only",{ManipulatorType:'basic'}));

};

sfRedNaoName.prototype.GenerateInlineElement=function()
{
    var firstNameLabel='';
    var lastNameLabel='';

    if(this.Options.FirstNamePlaceholder!='')
        firstNameLabel='<label class="redNaoHelper">'+this.Options.FirstNamePlaceholder+'</label>';
    if(this.Options.LastNamePlaceholder!='')
        lastNameLabel='<label class="redNaoHelper">'+this.Options.LastNamePlaceholder+'</label>';

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <div class="redNaoFirstNameDiv redNaoTwoColumnsDiv">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_firstname" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.FirstNamePlaceholder)+'" class="redNaoInputText redNaoTwoColumns redNaoInputFirstName '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                        '+firstNameLabel+'\
                    </div>    \
                    <div class="redNaoLastNameDiv redNaoTwoColumnsDiv">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_lastname" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.LastNamePlaceholder)+'" class="redNaoInputText redNaoTwoColumns redNaoInputLastName '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'">\
                        '+lastNameLabel+'\
                    </div>\
               <div>     \
    </div>';
};


sfRedNaoName.prototype.GetValueString=function()
{
    return {
        firstName:rnJQuery('#'+this.Id+ ' .redNaoInputFirstName').val(),
        lastName:rnJQuery('#'+this.Id+ ' .redNaoInputLastName').val()

    };


};


sfRedNaoName.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.firstName+" "'+'formData.'+this.Id+'.lastName';
};


sfRedNaoName.prototype.IsValid=function()
{
    if(this.Options.IsRequired=='y'&&(rnJQuery('#'+this.Id+ ' .redNaoInputFirstName').val()==""||rnJQuery('#'+this.Id+ ' .redNaoInputLastName').val()==""))
    {
        var firstNameJQuery=rnJQuery('#'+this.Id+ ' .redNaoInputFirstName');
        var lastNameJQuery=rnJQuery('#'+this.Id+ ' .redNaoInputLastName');

        if(firstNameJQuery.val()=="")
            firstNameJQuery.addClass('redNaoInvalid');

        if(lastNameJQuery.val()=="")
            lastNameJQuery.addClass('redNaoInvalid');

        return false;
    }

    return true;
};

//noinspection JSUnusedLocalSymbols
sfRedNaoName.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputFirstName,#'+this.Id+ ' .redNaoInputLastName').change(function(){self.FirePropertyChanged(self.GetValueString());});
};



/************************************************************************************* Address ***************************************************************************************************/

function sfRedNaoAddress(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Text Input";
    this.Countries=["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burma", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic", "Congo, Republic of the", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Mongolia", "Morocco", "Monaco", "Mozambique", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Samoa", "San Marino", " Sao Tome", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"];
    if(this.IsNew)
    {
        this.Options.ClassName="rednaoaddress";
        this.Options.Label="Address";
        this.Options.StreetAddress1Label="Street Address";
        this.Options.StreetAddress2Label="Street Address 2";
        this.Options.CityLabel="City";
        this.Options.StateLabel="State";
        this.Options.ZipLabel='Zip';
        this.Options.CountryLabel='Country';
        this.Options.DefaultCountry="United States";

        this.Options.ShowStreetAddress1="y";
        this.Options.ShowStreetAddress2="y";
        this.Options.ShowCity="y";
        this.Options.ShowState="y";
        this.Options.ShowZip='y';
        this.Options.ShowCountry='y';
    }else
        this.SetDefaultIfUndefined("DefaultCountry","United States");
}

sfRedNaoAddress.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoAddress.prototype.GetCountriesForPropertyConfiguration = function () {
    var countriesToReturn=new Array(this.Countries.length);
    for(var i=0;i<this.Countries.length;i++)
    {
        countriesToReturn[i]={value:this.Countries[i],label:this.Countries[i]};
    }

    return countriesToReturn;
};
sfRedNaoAddress.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"StreetAddress1Label","Street Address Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"StreetAddress2Label","Street Address 2 Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"CityLabel","City Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"StateLabel","State Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"ZipLabel","Zip Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"CountryLabel","Country Label",{ManipulatorType:'basic'}));
    this.Properties.push(new ComboBoxProperty(this,this.Options,"DefaultCountry",this.Translations.DefaultCountry,{ManipulatorType:'basic',Values:this.GetCountriesForPropertyConfiguration()}));

    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowStreetAddress1","Show Street Address",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowStreetAddress2","Show Street Address 2",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowCity","Show City",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowState","Show State",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowZip","Show Zip",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"ShowCountry","Show Country",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));

};

sfRedNaoAddress.prototype.GenerateInlineElement=function()
{
    var StreetAddress1Label='';
    var StreetAddress2Label='';
    var CityLabel='';
    var StateLabel='';
    var ZipLabel='';
    var CountryLabel='';
    var isFirstElement=true;

    if(this.Options.StreetAddress1Label!='')
        StreetAddress1Label='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.StreetAddress1Label)+'</label>';

    if(this.Options.StreetAddress2Label!='')
        StreetAddress2Label='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.StreetAddress2Label)+'</label>';

    if(this.Options.CityLabel!='')
        CityLabel='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.CityLabel)+'</label>';

    if(this.Options.StateLabel!='')
        StateLabel='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.StateLabel)+'</label>';

    if(this.Options.ZipLabel!='')
        ZipLabel='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.ZipLabel)+'</label>';

    if(this.Options.CountryLabel!='')
        CountryLabel='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.CountryLabel)+'</label>';




    var html= '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">';
                if(this.Options.ShowStreetAddress1=='y')
                    html+='<div class="redNaoStreetAddress1Div">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_streetaddress1" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.StreetAddress1Label)+'" class="redNaoInputText redNaoOneColumn redNaoStreetAddress1 '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                        '+StreetAddress1Label+'\
                    </div>';
                if(this.Options.ShowStreetAddress2=='y')
                    html+='<div class="redNaoStreetAddress2Div">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_streetaddress2" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.StreetAddress2Label)+'" class="redNaoInputText redNaoOneColumn redNaoStreetAddress2 '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                        '+StreetAddress2Label+'\
                    </div>';

                if(this.Options.ShowCity=='y')
                    html+='<div class="redNaoCityDiv redNaoTwoColumnsDiv">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_city" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.CityLabel)+'" class="redNaoInputText redNaoTwoColumns redNaoCity '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                        '+CityLabel+'\
                    </div>';
                if(this.Options.ShowState=='y')
                    html+='<div class="redNaoStateDiv redNaoTwoColumnsDiv">\
                          <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'state" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.StateLabel)+'" class="redNaoInputText redNaoTwoColumns redNaoState '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                        '+StateLabel+'\
                    </div>';
                    html+='<div class="redNaoClearDiv"></div>';

                if(this.Options.ShowZip=='y')
                    html+='<div class="redNaoZipDiv redNaoTwoColumnsDiv">\
                                    <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'zip" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.ZipLabel)+'" class="redNaoInputText redNaoTwoColumns redNaoZip '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                                    '+ZipLabel+'\
                                </div>';

                if(this.Options.ShowCountry=='y')
                {
                    html+='<div class="redNaoCountryDiv redNaoTwoColumnsDiv">\
                        <select '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_country"  class="redNaoTwoColumns redNaoSelect redNaoCountry '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'">';
                        for(var i=0;i<this.Countries.length;i++)
                        {
                            html+='<option '+((this.Countries[i]==this.Options.DefaultCountry)?'selected="selected"':'')+'  value="'+RedNaoEscapeHtml(this.Countries[i])+'">'+RedNaoEscapeHtml(this.Countries[i])+'</option>';
                            isFirstElement=false;
                        }
                html+="</select>"+CountryLabel+'\
                    </div>';
                }
    html+='</div>';
    return html;
};


sfRedNaoAddress.prototype.GetValueString=function()
{
    return {
        streetAddress1:rnJQuery('#'+this.Id+ ' .redNaoStreetAddress1').val(),
        streetAddress2:rnJQuery('#'+this.Id+ ' .redNaoStreetAddress2').val(),
        city:rnJQuery('#'+this.Id+ ' .redNaoCity').val(),
        state:rnJQuery('#'+this.Id+ ' .redNaoState').val(),
        zip:rnJQuery('#'+this.Id+ ' .redNaoZip').val(),
        country:rnJQuery('#'+this.Id+ ' .redNaoCountry').val()

    };


};


sfRedNaoAddress.prototype.GetValuePath=function()
{
    return  "formData."+this.Id+'.streetAddress1'+
            " "+'formData.'+this.Id+'.streetAddress2'+
            " "+'formData.'+this.Id+'.city'+
            " "+'formData.'+this.Id+'.state'+
            " "+'formData.'+this.Id+'.zip'+
            " "+'formData.'+this.Id+'.country';
};


sfRedNaoAddress.prototype.IsValid=function()
{
    if(this.Options.IsRequired=='n')
        return true;



    var streetAddress1JQuery=rnJQuery('#'+this.Id+ ' .redNaoStreetAddress1');
    var streetAddress2JQuery=rnJQuery('#'+this.Id+ ' .redNaoStreetAddress2');
    var cityJQuery=rnJQuery('#'+this.Id+ ' .redNaoCity');
    var stateJQuery=rnJQuery('#'+this.Id+ ' .redNaoState');
    var zipJQuery=rnJQuery('#'+this.Id+ ' .redNaoZip');
    var countryJQuery=rnJQuery('#'+this.Id+ ' .redNaoCountry');

    var isValid=true;
    if(this.Options.ShowStreetAddress1&&streetAddress1JQuery.val()=='')
    {
        isValid=false;
        streetAddress1JQuery.addClass('redNaoInvalid');
    }

    if(this.Options.ShowStreetAddress2&&streetAddress2JQuery.val()=='')
    {
        isValid=false;
        streetAddress2JQuery.addClass('redNaoInvalid');
    }

    if(this.Options.ShowCity&&cityJQuery.val()=='')
    {
        isValid=false;
        cityJQuery.addClass('redNaoInvalid');
    }

    if(this.Options.ShowState&&stateJQuery.val()=='')
    {
        isValid=false;
        stateJQuery.addClass('redNaoInvalid');
    }

    if(this.Options.ShowZip&&zipJQuery.val()=='')
    {
        isValid=false;
        zipJQuery.addClass('redNaoInvalid');
    }

    if(this.Options.ShowCountry&&countryJQuery.val()=='')
    {
        isValid=false;
        countryJQuery.addClass('redNaoInvalid');
    }

    return isValid;

};

//noinspection JSUnusedLocalSymbols
sfRedNaoAddress.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoStreetAddress1,#'+this.Id+ ' .redNaoStreetAddress2,#'+this.Id+ ' .redNaoCity,#'+this.Id+ ' .redNaoState,#'+this.Id+ ' .redNaoZip,#'+this.Id+ ' .redNaoCountry').change(function(){self.FirePropertyChanged(self.GetValueString());});
};


/************************************************************************************* Phone ***************************************************************************************************/

function sfRedNaoPhone(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Phone";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaophone";
        this.Options.Label="Phone";
        this.Options.AreaLabel="Area";
        this.Options.PhoneLabel="Phone";
    }
}

sfRedNaoPhone.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoPhone.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"AreaLabel","Area",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"PhoneLabel","Phone",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));

};

sfRedNaoPhone.prototype.GenerateInlineElement=function()
{
    var areaLabel='';
    var phoneLabel='';

    if(this.Options.FirstNamePlaceholder!='')
        areaLabel='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.AreaLabel)+'</label>';
    if(this.Options.LastNamePlaceholder!='')
        phoneLabel='<label class="redNaoHelper">'+RedNaoEscapeHtml(this.Options.PhoneLabel)+'</label>';

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <div class="redNaoFirstNameDiv redNaoTwoColumnsDiv">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_area" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.AreaLabel)+'" class="redNaoInputText redNaoTwoColumns redNaoInputArea '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'"/>\
                        '+areaLabel+'\
                    </div>    \
                    <div class="redNaoLastNameDiv redNaoTwoColumnsDiv">\
                        <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'_phone" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.PhoneLabel)+'" class="redNaoInputText redNaoTwoColumns redNaoInputPhone '+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'">\
                        '+phoneLabel+'\
                    </div>\
               <div>     \
    </div>';
};


sfRedNaoPhone.prototype.GetValueString=function()
{
    return {
        area:rnJQuery('#'+this.Id+ ' .redNaoInputArea').val(),
        phone:rnJQuery('#'+this.Id+ ' .redNaoInputPhone').val()

    };


};



sfRedNaoPhone.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.area+" "'+'formData.'+this.Id+'.phone';
};


sfRedNaoPhone.prototype.IsValid=function()
{
    if(this.Options.IsRequired=='n')
        return true;

    if(rnJQuery('#'+this.Id+ ' .redNaoInputArea').val()==""||rnJQuery('#'+this.Id+ ' .redNaoInputPhone').val()=="")
    {
        var areaJQuery=rnJQuery('#'+this.Id+ ' .redNaoInputArea');
        var phoneJQuery=rnJQuery('#'+this.Id+ ' .redNaoInputPhone');

        if(areaJQuery.val()=="")
            areaJQuery.addClass('redNaoInvalid');

        if(phoneJQuery.val()=="")
            phoneJQuery.addClass('redNaoInvalid');

        return false;
    }

    return true;
};


//noinspection JSUnusedLocalSymbols
sfRedNaoPhone.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoInputArea,#'+this.Id+ ' .redNaoInputPhone').change(function(){
        self.FirePropertyChanged(self.GetValueString());
    });

    rnJQuery('#'+this.Id+ ' .redNaoInputArea,#'+this.Id+ ' .redNaoInputPhone').ForceNumericOnly();

};


/************************************************************************************* Email Element ***************************************************************************************************/

function sfRedNaoEmail(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Text Input";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaoemail";
        this.Options.Label="Email";
        this.Options.Placeholder="Placeholder"
    }
}

sfRedNaoEmail.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoEmail.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));

};

sfRedNaoEmail.prototype.GenerateInlineElement=function()
{

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" class="redNaoInputText redNaoEmail'+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'">'+
    '</div>';
};


sfRedNaoEmail.prototype.GetValueString=function()
{
    return {value:rnJQuery('#'+this.Id+ ' .redNaoEmail').val()};
};

sfRedNaoEmail.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};


sfRedNaoEmail.prototype.IsValid=function()
{
    var email=rnJQuery('#'+this.Id+ ' .redNaoEmail').val();
    if(email==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }


    if(email!=''&&!this.EmailIsValid(email))
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }

    return true;
};

sfRedNaoEmail.prototype.EmailIsValid=function(email)
{
    var reg=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return reg.test(email);
};

//noinspection JSUnusedLocalSymbols
sfRedNaoEmail.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoEmail').change(function(){self.FirePropertyChanged(self.GetValueString());});
};


/************************************************************************************* Number Element ***************************************************************************************************/

function sfRedNaoNumber(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Text Input";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaonumber";
        this.Options.Label="Number";
        this.Options.Placeholder="Placeholder";
        this.Options.NumberOfDecimals=0;
        this.Options.MaximumValue="";
        this.Options.MinimumValue="";
    }else
    {
        this.SetDefaultIfUndefined("MaximumValue","");
        this.SetDefaultIfUndefined("MinimumValue","");
    }
}

sfRedNaoNumber.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoNumber.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Placeholder","Placeholder",{ManipulatorType:'basic'}));
    //this.Properties.push(new SimpleTextProperty(this,this.Options,"NumberOfDecimals","Number of decimals",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"MinimumValue","Minimum Value",{ManipulatorType:'basic',Placeholder:'No Minimum'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"MaximumValue","Maximum Value",{ManipulatorType:'basic',Placeholder:'No Maximum'}));

};

sfRedNaoNumber.prototype.GenerateInlineElement=function()
{

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <input '+(this.Options.ReadOnly=='y'?'disabled="disabled"':"")+' name="'+this.GetPropertyName()+'" type="text" placeholder="'+this.Options.Placeholder+'" class="redNaoInputText redNaoNumber'+(this.Options.ReadOnly=='y'?'redNaoDisabledElement':"")+'">'+
    '</div>';
};


sfRedNaoNumber.prototype.GetValueString=function()
{
    return {value:rnJQuery('#'+this.Id+ ' .redNaoNumber').val()};
};

sfRedNaoNumber.prototype.GetValuePath=function()
{
    return 'formData.'+this.Id+'.value';
};


sfRedNaoNumber.prototype.IsValid=function()
{
    var number=rnJQuery('#'+this.Id+ ' .redNaoNumber').val();
    if(number==""&&this.Options.IsRequired=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText,.redNaoRealCheckBox,.redNaoInputRadio,.redNaoInputCheckBox,.redNaoSelect,.redNaoTextArea').addClass('redNaoInvalid');
        return false;
    }
    return true;
};

sfRedNaoNumber.prototype.InputIsValid=function()
{
    var inputText=rnJQuery('#'+this.Id+ ' .redNaoNumber').val();

    if(isNaN(inputText))
        return false;

    var inputNumber=parseFloat(inputText);
    if(!isNaN(this.Options.MaximumValue))
    {
        var maximumValue=parseFloat(this.Options.MaximumValue);
        if(inputNumber>maximumValue)
            return false;
    }

    if(!isNaN(parseInt(this.Options.MinimumValue)))
    {
        var minimumValue=parseFloat(this.Options.MinimumValue);
        if(inputNumber<minimumValue)
            return false;
    }
    return true;
};

//noinspection JSUnusedLocalSymbols
sfRedNaoNumber.prototype.GenerationCompleted=function(jQueryElement)
{
    var self=this;
    rnJQuery('#'+this.Id+ ' .redNaoNumber').change(function(){
        if(!self.InputIsValid())
            rnJQuery('#'+self.Id+ ' .redNaoNumber').val('');

        self.FirePropertyChanged(self.GetValueString());});
    rnJQuery('#'+this.Id+ ' .redNaoNumber').ForceNumericOnly();
};


/************************************************************************************* Recaptcha Element ***************************************************************************************************/

function sfRedNaoCaptcha(options)
{
    sfFormElementBase.call(this,options);
    this.Title="captcha";
    if(this.IsNew)
    {
        this.Options.ClassName="rednaocaptcha";
        this.Options.Label="Captcha";
        this.Options.Theme="red";
    }

    this.Options.Id="captcha";
    this.Id="captcha";
}

sfRedNaoCaptcha.prototype=Object.create(sfFormElementBase.prototype);

sfRedNaoCaptcha.prototype.CreateProperties=function()
{
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));

};

sfRedNaoCaptcha.prototype.GenerateInlineElement=function()
{

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls redNaoCaptcha" id="captchaComponent">\
    </div>';
};
sfRedNaoCaptcha.prototype.StoresInformation=function()
{
    return false;
};


//noinspection JSUnusedLocalSymbols
sfRedNaoCaptcha.prototype.GenerationCompleted=function(jQueryElement)
{
    rnJQuery.getScript("http://www.google.com/recaptcha/api/js/recaptcha_ajax.js", function(){
        //noinspection JSUnresolvedVariable
        Recaptcha.create("6Lf2J-wSAAAAACCijq50oACQRuvrsmNt9DeUsE-7",
            'captchaComponent',
            {
                theme: "red"
            }
        );
    });
};



/************************************************************************************* Donation Amount ***************************************************************************************************/

function sfDonationAmountElement(options)
{
    sfFormElementBase.call(this,options);
    this.Title="Donation Amount";

    if(this.IsNew)
    {
        this.Options.ClassName="rednaodonationamount";
        this.Options.Label="Donation Amount";
        this.Options.Placeholder="Amount";
        this.Options.DefaultValue="0";
        this.Options.Disabled='n';
    }

    if(typeof  this.Options.DefaultValue=='undefined')
        this.Options.DefaultValue=0;
}

sfDonationAmountElement.prototype=Object.create(sfFormElementBase.prototype);

sfDonationAmountElement.prototype.CreateProperties=function()
{
    this.Properties.push(new IdProperty(this,this.Options));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"Label","Label",{ManipulatorType:'basic'}));
    this.Properties.push(new SimpleTextProperty(this,this.Options,"DefaultValue","Default Value",{ManipulatorType:'basic'}));

    this.Properties.push(new CheckBoxProperty(this,this.Options,"IsRequired","Required",{ManipulatorType:'basic'}));
    this.Properties.push(new CheckBoxProperty(this,this.Options,"Disabled","Read Only",{ManipulatorType:'basic'}));



};

sfDonationAmountElement.prototype.GenerateInlineElement=function()
{

    return '<div class="rednao_label_container"><label class="rednao_control_label" >'+RedNaoEscapeHtml(this.Options.Label)+'</label></div>\
                <div class="redNaoControls">\
                    <input  class="redNaoInputText redNaoNumber" type="text" placeholder="'+RedNaoEscapeHtml(this.Options.Placeholder)+'" class="redNaoInputText" value="'+RedNaoEscapeHtml(this.Options.DefaultValue)+'"  >'+
        '</div>';
};



sfDonationAmountElement.prototype.GetValueString=function()
{
    try
    {
        this.amount=parseFloat(rnJQuery('#'+this.Id+ ' .redNaoInputText').val());
    }catch(exception)
    {

    }

    return  encodeURI(this.Options.Label)+"="+encodeURI(rnJQuery('#'+this.Id+ ' .redNaoInputText').val());
};

//noinspection JSUnusedLocalSymbols
sfDonationAmountElement.prototype.GenerationCompleted=function(jQueryElement)
{
    if(this.Options.Disabled=='y')
    {
        rnJQuery('#'+this.Id).find('.redNaoInputText').attr('readonly','readonly').css('background-color','#eeeeee');
    }
};

sfDonationAmountElement.prototype.IsValid=function()
{
    try{
        var number=parseFloat(rnJQuery('#'+this.Id+ ' .redNaoInputText').val());
        return number>0;
    }catch(exception)
    {
        return false;
    }
};
