"use strict";
function SfGetConditionalStep(formBuilder,stepConfiguration)
{
    if(stepConfiguration.Type=="SfHandlerFieldPicker")
        return new SfHandlerFieldPicker(smartFormsTranslation,formBuilder,stepConfiguration);
    if(stepConfiguration.Type=="SfHandlerConditionGenerator")
        return new SfHandlerConditionGenerator(smartFormsTranslation,formBuilder,stepConfiguration);
    if(stepConfiguration.Type=="SfNamePicker")
        return new SfNamePicker(smartFormsTranslation,formBuilder,stepConfiguration);
    throw 'invalid conditional step';
}

/************************************************************************************* Conditional Designer Base ***************************************************************************************************/
function SfConditionalStepBase(translations,formBuilder,stepConfiguration)
{
    this.FormBuilder=formBuilder;
    this.Translations=translations;
    this.StepConfiguration=stepConfiguration;
    this.Width=550;
}



//noinspection JSUnusedLocalSymbols
SfConditionalStepBase.prototype.InitializeScreen=function(container)
{
    throw 'Method is abstract';
};


SfConditionalStepBase.prototype.Exit=function()
{
    throw 'Method is abstract';
};


SfConditionalStepBase.prototype.Commit=function()
{
    throw 'Method is abstract';
};


/*************************************************************************************SfNamePicker***************************************************************************************************/
function SfNamePicker(translations,formBuilder,stepConfiguration)
{
    SfConditionalStepBase.call(this,translations,formBuilder,stepConfiguration);
}
SfNamePicker.prototype=Object.create(SfConditionalStepBase.prototype);

SfNamePicker.prototype.InitializeScreen=function(container)
{
    container.css('text-align','left');
    container.css('padding-left','5px');
    container.css('padding-right','5px');

    container.append('<h2 style="text-align: left">'+this.Translations[this.StepConfiguration.Label]+'</h2>');

    var name=this.Translations["MyNewCondition"]+" "+this.StepConfiguration.Id;
    if(!this.StepConfiguration.IsNew)
    {
        name=this.StepConfiguration.Options.Name;
    }
    this.Title=rnJQuery('<input type="text" style="width: 100%;height: 40px;font-size: 20px;padding: 10px;">');
    this.Title.val(name);
    container.append(this.Title);

};

SfNamePicker.prototype.Exit=function()
{

};

SfNamePicker.prototype.Commit=function()
{
    if(this.Title.val().trim()=="")
    {
        alert(this.Translations["TheTitleCantBeEmpty"]);
        return false;
    }

    this.StepConfiguration.Options.Name=this.Title.val();
    return true;
};



/*************************************************************************************Field Picker***************************************************************************************************/
function SfHandlerFieldPicker(translations,formBuilder,stepConfiguration)
{
    SfConditionalStepBase.call(this,translations,formBuilder,stepConfiguration);
}
SfHandlerFieldPicker.prototype=Object.create(SfConditionalStepBase.prototype);

SfHandlerFieldPicker.prototype.InitializeScreen=function(container)
{
    this.FormBuilder.Disable();
    container.css('text-align','left');
    container.css('padding-left','5px');
    container.css('padding-right','5px');
    var jQueryDocument=rnJQuery(document);
    var self=this;
    rnJQuery('.rednao-control-group').bind("click.FieldPicker",function(){self.FormElementClicked(rnJQuery(this));});
    rnJQuery('body').append('<div class="smartFormsSlider smartFormsFieldPickerOverlay"><div class="ui-widget-overlay" style="z-index: 1001;width:'+jQueryDocument.width()+'px;height:'+jQueryDocument.height()+'" ></div></div>');
    rnJQuery('.rednaoformbuilder').addClass('smartFormsFieldPick');
    var pickerInterface=rnJQuery('<div class="fieldPickContainer" style="margin:10px;"></div>');

    var options="";
    var selectedFields=[];
    if(!this.StepConfiguration.IsNew)
        selectedFields=this.StepConfiguration.Options.AffectedItems;


    for(var i=0;i<this.FormBuilder.RedNaoFormElements.length;i++)
    {
            options+='<option '+(selectedFields.indexOf(this.FormBuilder.RedNaoFormElements[i].Options.Id)>=0?'selected="selected"':'')+'  value="'+this.FormBuilder.RedNaoFormElements[i].Options.Id+'">'+this.FormBuilder.RedNaoFormElements[i].GetFriendlyName()+'</option>';
    }
    this.Select=rnJQuery('<select size="margin-left:10px;" multiple="multiple" id="redNaoFieldPicked" style="width:100%">'+options+'</select>');
    pickerInterface.append(this.Select);
    this.Select.select2({
        allowClear: true
    }).on("change", function() {
        self.SelectChanged();
    });
    container.append('<h2 style="text-align: left">'+this.Translations[this.StepConfiguration.Label]+'</h2>');
    container.append(pickerInterface);



};

SfHandlerFieldPicker.prototype.Exit=function()
{
    this.FormBuilder.Enable();
    rnJQuery('.rednao-control-group').unbind("click.FieldPicker");
    rnJQuery('.fieldPickerSelected').removeClass('fieldPickerSelected');
    rnJQuery('.rednaoformbuilder').removeClass('smartFormsFieldPick');
    rnJQuery('.smartFormsFieldPickerOverlay').remove();
};

SfHandlerFieldPicker.prototype.Commit=function()
{
    var selectedValues=this.Select.select2('val');
    if(selectedValues.length==0)
    {
        alert(this.Translations["SelectAtLeastOneField"]);
        return false;
    }
    this.StepConfiguration.Options.AffectedItems=selectedValues;
    return true;
};

SfHandlerFieldPicker.prototype.FormElementClicked=function(elementClickedJQuery)
{
    var fieldId=this.FormBuilder.RedNaoFormElements[elementClickedJQuery.index()].Id;
    var selectedFields=this.Select.select2('val');
    if(rnJQuery.inArray(fieldId,selectedFields)>=0)
        return;
    selectedFields.push(fieldId);
    this.Select.select2('val',selectedFields).change();
};

SfHandlerFieldPicker.prototype.SelectChanged=function()
{
    var selectedFields=this.Select.select2('val');
    rnJQuery('.fieldPickerSelected').removeClass('fieldPickerSelected');
    for(var i=0;i<selectedFields.length;i++)
    {
        rnJQuery('#'+selectedFields[i]).addClass('fieldPickerSelected');
    }
};


/*************************************************************************************Condition Generator ***************************************************************************************************/
function SfHandlerConditionGenerator(translations,formBuilder,stepConfiguration)
{
    SfConditionalStepBase.call(this,translations,formBuilder,stepConfiguration);
    this.Width=700;
}
SfHandlerConditionGenerator.prototype=Object.create(SfConditionalStepBase.prototype);

SfHandlerConditionGenerator.prototype.InitializeScreen=function(container)
{
    container.css('padding-left','5px');
    container.css('padding-right','5px');

    this.Table=rnJQuery("<table>" +
        "<th>(</th>" +
        "<th>Field</th>" +
        "<th>Operation</th>" +
        "<th>Value</th>" +
        "<th>)</th>" +
        "<th>Join</th>" +
        "<th></th>" +
        "<th></th>" +
        "</table>");

    this.Conditions=[];

    if(!this.StepConfiguration.IsNew)
        this.FillDefaultValues();
    else
        this.CreateConditionalRow();

    container.append('<h2 style="text-align: left">'+this.Translations[this.StepConfiguration.Label]+'</h2>');
    container.append(this.Table);



};

SfHandlerConditionGenerator.prototype.FillDefaultValues=function()
{
    var conditions=this.StepConfiguration.Options.Conditions;
    for(var i=0;i<conditions.length;i++)
    {
        var row=this.CreateConditionalRow();
        var formElements=this.FormBuilder.RedNaoFormElements;
        for(var h=0;h<formElements.length;h++)
            if(formElements[h].Id==conditions[i].Field)
                row.find('.rnConditionField').val(h).change();

        row.find('.rnConditionOper').val(conditions[i].Op);
        row.find('.operType').val(conditions[i].OpType);
        if(conditions[i].OpType=="text")
            row.find('.rnConditionVal').val(conditions[i].Value);
        else
            row.find('.rnConditionVal').select2('val',conditions[i].Value);

        if(conditions[i].IsOpeningPar=='y')
            row.find('.leftPar').attr('checked','checked');

        if(conditions[i].IsClosingPar=='y')
            row.find('.rightPar').attr('checked','checked');

        row.find('.conditionJoin').val(conditions[i].Join);



    }
};

SfHandlerConditionGenerator.prototype.CreateConditionalRow=function()
{
    var condition={};
    this.Conditions.push(condition);
    var row=rnJQuery('<tr class="sfConditionRow">' +
        '   <td><input class="leftPar" type="checkbox" name="condition'+this.Table.find('tr').length+'"/></td>' +
        '   <td><select class="rnConditionField" style="width: 200px;">'+this.GetFieldItems()+'</select></td>' +
        '   <td><select class="rnConditionOper" style="width: 100px;"></select><input type="hidden" class="operType"/></td>' +
        '   <td class="tdValue"><input type="text" style="width: 200px;"/></td>' +
        '   <td><input  class="rightPar" type="checkbox" name="condition'+this.Table.find('tr').length+'"/></td>' +
        '   <td><select class="conditionJoin"><option></option><option value="and">And</option><option value="or">Or</option></select></td>' +
        '   <td><button class="conditionAdd" value="+">+</button></td>'+
        (this.Table.find('tr').length>1?'   <td><button class="conditionRemove" value="-">-</button></td>':'')+
        '</tr>');

    var self=this;
    row.find('.leftPar').change(function(){
        if(rnJQuery(this).is(':checked'))
            row.find('.rightPar').removeAttr('checked');
    });

    row.find('.rightPar').change(function(){
        if(rnJQuery(this).is(':checked'))
            row.find('.leftPar').removeAttr('checked');
    });
    row.find('.conditionAdd').click(function(e){
                e.preventDefault();
                if(row.find('.conditionJoin').val()=='')
                    row.find('.conditionJoin').val('and');
                self.CreateConditionalRow()});
    row.find('.conditionRemove').click(function(e){e.preventDefault();row.remove();});
    row.find('.rnConditionField').change(function(){self.FieldSelected(row,rnJQuery(this).val(), condition)});
    this.Table.append(row);
    return row;
};

SfHandlerConditionGenerator.prototype.FieldSelected=function(row,selectedField,condition)
{
    row.find('.rnConditionOper').empty();
    if(selectedField==-1)
    {
        condition.Field="";
        return;
    }

    selectedField=this.FormBuilder.RedNaoFormElements[selectedField];
    condition.Field=selectedField.Id;
    var options="";
    if(typeof selectedField.Options.Options=='undefined')
    {
        row.find('.operType').val('text');
        options="<option value='eq'>equal</option>" +
                "<option value='neq'>not equal</option>" +
                "<option value='contains'>contains</option>" +
                "<option value='ncontains'>not contains</option>";/*+
                "<option value='empty'>Is Empty</option>"+
                "<option value='nempty'>Is Not Empty</option>"*/

        row.find('.tdValue').empty().append('<input class="rnConditionVal" type="text" style="width: 200px;"/>');
    }else
    {
        row.find('.operType').val('list');
        options="<option value='contains'>contains</option>" +
                "<option value='ncontains'>not contains</option>"/*+
                "<option value='empty'>Is Empty</option>"+
                "<option value='nempty'>Is Not Empty</option>"*/;

        var fieldAvailableOptions="";
        for(var i=0;i<selectedField.Options.Options.length;i++)
        {
            fieldAvailableOptions+="<option value='"+RedNaoEscapeHtml(selectedField.Options.Options[i].label)+"'>"+RedNaoEscapeHtml(selectedField.Options.Options[i].label)+"</option>";
        }

        var select=rnJQuery('<select class="rnConditionVal" multiple="multiple" style="width: 200px;">'+fieldAvailableOptions+'</select>');
        row.find('.tdValue').empty().append(select);
        select.select2();
    }


    row.find('.rnConditionOper').append(options);



};

SfHandlerConditionGenerator.prototype.GetFieldItems=function()
{
    var formElements=this.FormBuilder.RedNaoFormElements;
    var options="<option value='-1'></option>";
    for(var i=0;i<formElements.length;i++)
        if(formElements[i].StoresInformation())
            options+="<option value='"+ i.toString()+"'>"+formElements[i].GetFriendlyName()+"</option>";

    return options;
};

SfHandlerConditionGenerator.prototype.Exit=function()
{

};

SfHandlerConditionGenerator.prototype.Commit=function()
{
    var rows=this.Table.find('.sfConditionRow');
    var data=this.GetRowData(rows);
    if(this.IsValid(data))
    {
        this.StepConfiguration.Options.Conditions=data;
        this.StepConfiguration.Options.CompiledCondition=this.CompileCondition(data);
        return true;
    }
    return false;
};

SfHandlerConditionGenerator.prototype.CompileCondition=function(conditions)
{
    var conditionTxt="";
    for(var i=0;i<conditions.length;i++){
        var formElement=null;
        for(var h=0;h<this.FormBuilder.RedNaoFormElements.length;h++)
        {
            if(this.FormBuilder.RedNaoFormElements[h].Id==conditions[i].Field)
                formElement=this.FormBuilder.RedNaoFormElements[h];
        }
        if(formElement==null)
            continue;

        if(conditions[i].IsOpeningPar=='y')
            conditionTxt+='(';

        if(conditions[i].OpType=='list')
        {

           conditionTxt+=(conditions[i].Op=="contains"?"":"!")+"RedNaoListContainsValue("+JSON.stringify(conditions[i].Value)+",formData."+formElement.Id+") ";

        }else{
            switch(conditions[i].Op)
            {
                case 'eq':
                    conditionTxt+=formElement.GetLabelPath()+".toLowerCase()=='"+conditions[i].Value.toLowerCase()+"' ";
                    break;
                case 'neq':
                    conditionTxt+=formElement.GetLabelPath()+".toLowerCase()!='"+conditions[i].Value.toLowerCase()+"' ";
                    break;
                case 'contains':
                    conditionTxt+=formElement.GetLabelPath()+".toLowerCase().indexOf('"+conditions[i].Value.toLowerCase()+"')>-1 ";
                    break;
                case 'ncontains':
                    conditionTxt+=formElement.GetLabelPath()+".toLowerCase().indexOf('"+conditions[i].Value.toLowerCase()+"')==-1 ";
                    break;
            }
        }

        if(conditions[i].IsClosingPar=='y')
            conditionTxt+=") ";

        if(conditions.length-1>i)
            conditionTxt+=' '+(conditions[i].Join=='and'?'&&':'||')+' ';
    }

   // alert(conditionTxt);
    return conditionTxt;
};


SfHandlerConditionGenerator.prototype.IsValid=function(data)
{
    var openPar=0;
    var closePar=0;

    for(var i=0;i<data.length;i++)
    {
        if(data[i].Field.trim()==""||data[i].Op.trim()==""||data[i].OpType.trim()==""||(data[i].OpType=="text"&&data[i].Value.trim()=="")||(data[i].OpType=="list"&&data[i].Value.length<=0)||
                                                                                                     (i<(data[i].length-1)&&data[i].Join.trim()==""))
        {
            alert(this.Translations["PleaseFillAllFields"]);
            return false;
        }

        if(data[i].IsOpeningPar)
            openPar++;

        if(data[i].IsClosingPar)
        {
            if(closePar>=openPar)
            {
                alert(this.Translations['YouAreClosingOneParenthesis']);
                return false;
            }
            closePar++;
        }
    }

    if(openPar!=closePar)
    {
        alert(this.Translations['ParenthesisDontMatch']);
        return false;
    }

    return true;


};

SfHandlerConditionGenerator.prototype.GetRowData=function(rows)
{
    var data=[];
    for(var i=0;i<rows.length;i++)
    {
        var row=rnJQuery(rows[i]);
        data.push(
            {
                Field:(row.find('.rnConditionField').val()>=0? this.FormBuilder.RedNaoFormElements[row.find('.rnConditionField').val()].Id:""),
                Op:row.find('.rnConditionOper').val(),
                OpType:row.find('.operType').val(),
                Value:(row.find('.operType').val()=='list'?row.find('.rnConditionVal').select2('val'):row.find('.rnConditionVal').val()),
                IsOpeningPar:(row.find('.leftPar').is(':checked')?'y':'n'),
                IsClosingPar:(row.find('.rightPar').is(':checked')?'y':'n'),
                Join:row.find('.conditionJoin').val()
            }
        );
    }
    return data;
};