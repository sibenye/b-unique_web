"use strict;"


/************************************************************************************* Base  ***************************************************************************************************/

function ElementPropertiesBase(formelement,propertiesObject,propertyName,propertyTitle,additionalInformation)
{
    if(additionalInformation.ManipulatorType=='basic')
        this.Manipulator=RedNaoBasicManipulatorInstance;
    this.FormElement=formelement;
    this.AdditionalInformation=additionalInformation;
    this.PropertiesObject=propertiesObject;
    this.PropertyName=propertyName;
    this.PropertyTitle=propertyTitle;
    this.PropertyId="redNaoFormProperty"+this.PropertyName;

}

ElementPropertiesBase.prototype.FormulaExists=function(formElement,propertyName)
{
    if(RedNaoPathExists(formElement,'Options.Formulas.'+propertyName+'.Value')&&formElement.Options.Formulas[propertyName].Value!="")
        return true;

    return false;
}

ElementPropertiesBase.prototype.CreateProperty=function(jQueryObject)
{
    var newRow=rnJQuery("<tr></tr>");
    newRow.append(this.GenerateHtml());
    jQueryObject.append(newRow);
}

ElementPropertiesBase.prototype.GenerateHtml=function()
{
    throw 'Abstract Method';
}

ElementPropertiesBase.prototype.GetPropertyCurrentValue=function()
{
    return this.Manipulator.GetValue(this.PropertiesObject,this.PropertyName,this.AdditionalInformation);
}

ElementPropertiesBase.prototype.UpdateProperty=function()
{
    this.Manipulator.SetValue(this.PropertiesObject,this.PropertyName, rnJQuery("#"+this.PropertyId).val(),this.AdditionalInformation);

}

ElementPropertiesBase.prototype.RefreshElement=function()
{
    var refreshedElements=this.FormElement.RefreshElement();
    refreshedElements.find('input[type=submit]').click(function(e){e.preventDefault();e.stopPropagation();})

}

/************************************************************************************* Simple Text Property ***************************************************************************************************/


function SimpleTextProperty(formelement,propertiesObject,propertyName,propertyTitle,additionalInformation)
{
    if(typeof additionalInformation.Placeholder=='undefined')
        additionalInformation.Placeholder='Default';
    ElementPropertiesBase.call(this,formelement,propertiesObject,propertyName,propertyTitle,additionalInformation);
}

SimpleTextProperty.prototype=Object.create(ElementPropertiesBase.prototype);

SimpleTextProperty.prototype.GenerateHtml=function()
{
    var input="";
    var tdStyle="";
    if(this.AdditionalInformation.MultipleLine==true)
    {
        input='<textarea style="width:206px;" class="rednao-input-large" data-type="input" type="text" name="name" id="'+this.PropertyId+'" value="'+RedNaoEscapeHtml(this.GetPropertyCurrentValue())+'" placeholder="'+this.AdditionalInformation.Placeholder+'"/>';
        tdStyle='vertical-align:top;'
    }
    else
    {
        input='<input style="width: 206px;" class="rednao-input-large" data-type="input" type="text" name="name" id="'+this.PropertyId+'" value="'+RedNaoEscapeHtml(this.GetPropertyCurrentValue())+'" placeholder="'+this.AdditionalInformation.Placeholder+'"/>';
    }


    var newProperty=rnJQuery( '<td style="text-align: right;'+tdStyle+'"><label class="rednao-properties-control-label"> '+this.PropertyTitle+' </label></td>\
            <td style="text-align: left">'+input+'\
            <img style="width:15px;height: 20px; vertical-align: middle;cursor:pointer;cursor:hand;" title="Formula" src="'+ smartFormsRootPath+(this.FormulaExists(this.FormElement,this.PropertyName)?'images/formula_used.png' :'images/formula.png')+'"/> </td>');

    var self=this;
    newProperty.keyup(function(){
        self.Manipulator.SetValue(self.PropertiesObject,self.PropertyName, (rnJQuery("#"+self.PropertyId).val()),self.AdditionalInformation);
        self.RefreshElement();

    });
    newProperty.find('img').click(function(){RedNaoEventManager.Publish('FormulaButtonClicked',{"FormElement":self.FormElement,"PropertyName":self.PropertyName,AdditionalInformation:self.AdditionalInformation,Image:newProperty.find('img')})});
    return newProperty;
}


/************************************************************************************* Simple Numeric Property ***************************************************************************************************/


function SimpleNumericProperty(formelement,propertiesObject,propertyName,propertyTitle,additionalInformation)
{
    if(typeof additionalInformation.Placeholder=='undefined')
        additionalInformation.Placeholder='Default';
    ElementPropertiesBase.call(this,formelement,propertiesObject,propertyName,propertyTitle,additionalInformation);
}

SimpleNumericProperty.prototype=Object.create(ElementPropertiesBase.prototype);

SimpleNumericProperty.prototype.GenerateHtml=function()
{
    var input="";
    var tdStyle="";
    if(this.AdditionalInformation.MultipleLine==true)
    {
        input='<textarea style="width:206px;" class="rednao-input-large" data-type="input" type="text" name="name" id="'+this.PropertyId+'" value="'+RedNaoEscapeHtml(this.GetPropertyCurrentValue())+'" placeholder="'+this.AdditionalInformation.Placeholder+'"/>';
        tdStyle='vertical-align:top;'
    }
    else
    {
        input='<input style="width: 206px;" class="rednao-input-large" data-type="input" type="text" name="name" id="'+this.PropertyId+'" value="'+RedNaoEscapeHtml(this.GetPropertyCurrentValue())+'" placeholder="'+this.AdditionalInformation.Placeholder+'"/>';
    }


    var newProperty=rnJQuery( '<td style="text-align: right;'+tdStyle+'"><label class="rednao-properties-control-label"> '+this.PropertyTitle+' </label></td>\
            <td style="text-align: left">'+input+'\
            <img style="width:15px;height: 20px; vertical-align: middle;cursor:pointer;cursor:hand;" title="Formula" src="'+ smartFormsRootPath+(this.FormulaExists(this.FormElement,this.PropertyName)?'images/formula_used.png' :'images/formula.png')+'"/> </td>');
    newProperty.find('input').ForceNumericOnly();
    var self=this;
    newProperty.keyup(function(){
        var value=parseFloat(rnJQuery("#"+self.PropertyId).val());
        if(isNaN(value))
            value='';
        else
            value=value.toString();
        self.Manipulator.SetValue(self.PropertiesObject,self.PropertyName, value,self.AdditionalInformation);
        self.RefreshElement();

    });
    newProperty.find('img').click(function(){RedNaoEventManager.Publish('FormulaButtonClicked',{"FormElement":self.FormElement,"PropertyName":self.PropertyName,AdditionalInformation:self.AdditionalInformation,Image:newProperty.find('img')})});
    return newProperty;
}







/************************************************************************************* Check Box Property ***************************************************************************************************/



function CheckBoxProperty(formelement,propertiesObject,propertyName,propertyTitle,additionalInformation)
{
    ElementPropertiesBase.call(this,formelement,propertiesObject,propertyName,propertyTitle,additionalInformation);
}

CheckBoxProperty.prototype=Object.create(ElementPropertiesBase.prototype);

CheckBoxProperty.prototype.GenerateHtml=function()
{
    var newProperty=rnJQuery('<td style="text-align: right"><label class="checkbox control-group" style="display: block;">'+this.PropertyTitle+'</label></td>\
                <td style="text-align: left"><input type="checkbox" class="input-inline field" name="checked" id="'+this.PropertyId+'" '+(this.GetPropertyCurrentValue()=='y'? 'checked="checked"':'')+'/></td>');

    var self=this;
    newProperty.find('#'+this.PropertyId).change(function(){
        self.Manipulator.SetValue(self.PropertiesObject,self.PropertyName, (rnJQuery("#"+self.PropertyId).is(':checked')?'y':'n'),self.AdditionalInformation);
        self.RefreshElement();
    });

    return newProperty;
}





/************************************************************************************* Array Property ***************************************************************************************************/



function ArrayProperty(formelement,propertiesObject,propertyName,propertyTitle,additionalInformation)
{
    ElementPropertiesBase.call(this,formelement,propertiesObject,propertyName,propertyTitle,additionalInformation);
}

ArrayProperty.prototype=Object.create(ElementPropertiesBase.prototype);

ArrayProperty.prototype.GenerateHtml=function()
{
    var currentValues=this.GetPropertyCurrentValue();
    var self=this;
    var newProperty=rnJQuery('<td style="vertical-align: top;text-align: right;"><label class="checkbox control-group" style="display: block;vertical-align: top;">'+this.PropertyTitle+'</label></td><td style="text-align: left">'+this.GetItemList(currentValues)+'</td>');
    newProperty.find('table').append("<tr><td style='border-bottom-style: none;'><button class='redNaoPropertyClearButton' value='None'>Clear</button></td></tr></table>");
    newProperty.find('.redNaoPropertyClearButton').click(function(event)
    {
        event.preventDefault();
        newProperty.find('.itemSel').removeAttr('checked');
        self.UpdateProperty();
    });

    newProperty.find('.cloneArrayItem').click(function(){self.CloneItem(rnJQuery(this))});
    newProperty.find('.deleteArrayItem').click(function(){self.DeleteItem(rnJQuery(this))});
    newProperty.find('input[type=text],input[type=radio],input[type=checkbox]').change(function(){self.UpdateProperty();});
    newProperty.find('input[type=text]').keyup(function(){self.UpdateProperty();});


    this.ItemsList=newProperty.find('.listOfItems');
    return newProperty;
}

ArrayProperty.prototype.GetItemList=function(items)
{
    var list= '<table class="listOfItems"><tr><th style="text-align: right">Sel</th><th>Label</th><th>Amount</th></tr>';

    var isFirst=true;
    for(var i=0;i<items.length;i++)
    {
        list+=this.CreateListRow(isFirst,items[i]);
        isFirst=false;
    }
    return list;

}

ArrayProperty.prototype.DeleteItem=function(jQueryElement)
{
    var array=this.GetPropertyCurrentValue();
    var index=jQueryElement.parent().parent().index();

    array.splice(index,1);
    jQueryElement.parent().parent().remove();
    this.UpdateProperty();
}

ArrayProperty.prototype.CloneItem=function(jQueryElement)
{
    var array=this.GetPropertyCurrentValue();
    var index=jQueryElement.parent().parent().index();
    var jQueryToClone=jQueryElement.parent().parent();
    var data=this.GetRowData(jQueryToClone);

    if(this.AdditionalInformation.SelectorType=='radio')
        data.sel='n';

    var jQueryNewRow=rnJQuery(this.CreateListRow(false,data));
    jQueryToClone.after(jQueryNewRow);

    var self=this;
    jQueryNewRow.find('.cloneArrayItem').click(function(){self.CloneItem(rnJQuery(this))});
    jQueryNewRow.find('.deleteArrayItem').click(function(){self.DeleteItem(rnJQuery(this))});
    jQueryNewRow.find('input[type=text],input[type=radio],input[type=checkbox]').change(function(){self.UpdateProperty();});

    this.UpdateProperty();

}


ArrayProperty.prototype.CreateListRow=function(isFirst,item)
{
    var row= '<tr class="redNaoRowOption">' +
            '       <td style="text-align: right;">'+this.GetSelector(item)+'</td>' +
            '       <td><input type="text" class="itemText" value="'+RedNaoEscapeHtml(item.label)+'"/></td>' +
            '       <td><input type="text" class="itemValue" style="text-align: right; width: 50px;" value="'+RedNaoEscapeHtml(item.value)+'"/></td>' +
            '       <td style="text-align: center;vertical-align: middle;"><img style="cursor: hand;cursor: pointer; width:15px;height:15px;" class="cloneArrayItem" src="'+smartFormsRootPath+'images/clone.png" title="Clone"></td>';
            if(!isFirst)
                row+=' <td style="text-align: center;vertical-align: middle;"><img style="cursor: hand; cursor: pointer;width:15px;height:15px;" class="deleteArrayItem" src="'+smartFormsRootPath+'images/delete.png" title="Delete"></td>';
            row+='</tr>';
    return row;
}

ArrayProperty.prototype.GetSelector=function(item)
{
    var selected='';
    if(RedNaoGetValueOrEmpty(item.sel)=='y')
        selected='checked="checked"';
    if(this.AdditionalInformation.SelectorType=='radio')
        return '<input class="itemSel" type="radio" '+selected+' name="propertySelector"/>';
    else
        return '<input class="itemSel" type="checkbox" '+selected+'/>';
}

ArrayProperty.prototype.UpdateProperty=function()
{
    var processedValueArray=new Array();
    var self=this;
    var rows=this.ItemsList.find('tr.redNaoRowOption').each(
        function()
        {
            var jQueryRow=rnJQuery(this);
            var row=self.GetRowData(jQueryRow);
            processedValueArray.push(row);
        }
    );

    this.Manipulator.SetValue(this.PropertiesObject,this.PropertyName, processedValueArray,this.AdditionalInformation);
    this.RefreshElement();
}


ArrayProperty.prototype.GetRowData=function(jQueryRow)
{
    return {label:jQueryRow.find('.itemText').val(),value:jQueryRow.find('.itemValue').val(),sel:(jQueryRow.find('.itemSel').is(':checked')?'y':'n')};
}

/************************************************************************************* Id Property ***************************************************************************************************/


function IdProperty(formelement,propertiesObject)
{
    ElementPropertiesBase.call(this,formelement,propertiesObject,"Id","Id",{ManipulatorType:'basic'});
}

IdProperty.prototype=Object.create(ElementPropertiesBase.prototype);

IdProperty.prototype.GenerateHtml=function()
{
    this.PreviousId=this.FormElement.Id;

    var value=this.PreviousId;
    var newProperty=rnJQuery( '<td style="text-align: right"><label class="rednao-properties-control-label"> '+this.PropertyTitle+' </label></td>\
            <td style="text-align: left"><input style="width: 206px;" class="rednao-input-large" data-type="input" type="text" name="name" id="'+this.PropertyId+'" value="'+value+'" placeholder="Default"/></td>');


    var self=this;
    newProperty.change(function(){

        var jqueryElement=rnJQuery(this).find('#'+self.PropertyId);
        var fieldName=jqueryElement.val().trim();

        if(!fieldName.match(/^[a-zA-Z][\w:.-]+$/))
        {
            alert("Invalid field name, it should start with a letter and not contain spaces or symbols");
            jqueryElement.val(self.PreviousId);
            return;
        }

        self.FormElement.Id=fieldName;
        self.PropertiesObject.Id=fieldName;

        var jQueryElement=rnJQuery('#'+self.PreviousId);
        jQueryElement.attr('id',fieldName);


        var refreshedElements=self.FormElement.RefreshElement();
        refreshedElements.find('input[type=submit]').click(function(e){e.preventDefault();e.stopPropagation();})
        self.RefreshElement();

    });
    return newProperty;
}




/************************************************************************************* Combo Property ***************************************************************************************************/


function ComboBoxProperty(formelement,propertiesObject,propertyName,propertyTitle,additionalInformation)
{
    ElementPropertiesBase.call(this,formelement,propertiesObject,propertyName,propertyTitle,additionalInformation);
}

ComboBoxProperty.prototype=Object.create(ElementPropertiesBase.prototype);

ComboBoxProperty.prototype.GenerateHtml=function()
{
    var value=this.GetPropertyCurrentValue().trim();
    var selectText='<select id="'+this.PropertyId+'">'
    for(var i=0;i<this.AdditionalInformation.Values.length;i++)
    {
        var selected="";
        if(this.AdditionalInformation.Values[i].value==value)
            selected='selected="selected"';

        selectText+='<option value="'+RedNaoEscapeHtml(this.AdditionalInformation.Values[i].value)+'" '+selected+'>'+RedNaoEscapeHtml(this.AdditionalInformation.Values[i].label)+'</option>';
    }
    selectText+='</select>'

    var newProperty=rnJQuery( '<td style="text-align: right"><label class="rednao-properties-control-label"> '+RedNaoEscapeHtml(this.PropertyTitle)+' </label></td>'+
            '<td style="text-align: left">'+selectText+' </td>');

    var self=this;
    newProperty.find('select').change(function(){
        self.Manipulator.SetValue(self.PropertiesObject,self.PropertyName, (rnJQuery("#"+self.PropertyId).val()),self.AdditionalInformation);
        self.RefreshElement();

    });

    newProperty.find('img').click(function(){RedNaoEventManager.Publish('FormulaButtonClicked',{"FormElement":self.FormElement,"PropertyName":self.PropertyName,AdditionalInformation:self.AdditionalInformation,Image:null})});
    return newProperty;
};



