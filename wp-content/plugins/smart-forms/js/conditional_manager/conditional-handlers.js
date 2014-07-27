"use strict";
var SmartFormsConditionalHandlerArray=[];
function SmartFormsGetConditionalHandlerByType(handlerId,options)
{
    var handlers=SmartFormsGetConditionalHandlerArray();
    for(var i=0;i<handlers.length;i++)
    {
        if(handlers[i].id==handlerId)
        {
            return handlers[i].create(options);
        }
    }
    throw ('Invalid handler');
}
function SmartFormsGetConditionalHandlerArray()
{
    SmartFormsConditionalHandlerArray=[
        {Label:"Show fields depending on a condition",id:"SfShowConditionalHandler",create:function(options){return new SfShowConditionalHandler(options)}}
    ];

    return SmartFormsConditionalHandlerArray;
}


/************************************************************************************* Base ***************************************************************************************************/
function SfConditionalHandlerBase(options)
{
    this.PreviousActionWasTrue=-1;
    if(options==null)
    {
        this.Options={};
        SfConditionalHandlerBase.prototype.ConditionId++;
        this.Options.Id=SfConditionalHandlerBase.prototype.ConditionId;
        this.IsNew=true;
    }else
        this.Options=options;

    this.Id=this.Options.Id;
}
SfConditionalHandlerBase.prototype.ConditionId=0;

SfConditionalHandlerBase.prototype.GetConditionalSteps=function()
{
    throw "method is abstract";
};

SfConditionalHandlerBase.prototype.GetOptionsToSave=function()
{
    this.Options.Label=this.Options.GeneralInfo.Name;
    return this.Options;

};

//noinspection JSUnusedLocalSymbols
SfConditionalHandlerBase.prototype.Initialize=function(form,data)
{
    throw "method is abstract";
};


SfConditionalHandlerBase.prototype.ParseConditionLogic=function(form)
{
    throw "method is abstract";
};

SfConditionalHandlerBase.prototype.SubscribeCondition=function(condition,initialData)
{
    var self=this;
    var cond=condition;
    this.ConditionFunction=new Function('formData','return '+condition.CompiledCondition);
    var fieldsInCondition=[];
    for(var i=0;i<cond.Conditions.length;i++)
        fieldsInCondition.push(cond.Conditions[i].Field);

    RedNaoEventManager.Subscribe('FormValueChanged',function(data){
        if(fieldsInCondition.indexOf(data.FieldName)>-1)
            self.ProcessCondition(data.Data);
    });

    this.ProcessCondition(initialData);

};

SfConditionalHandlerBase.prototype.ProcessCondition=function(data)
{
    if(this.ConditionFunction(data))
    {
        if(this.PreviousActionWasTrue!=1)
        {
            this.PreviousActionWasTrue=1;
            this.ExecuteTrueAction();
        }
    }
    else
    if(this.PreviousActionWasTrue!=0)
    {
        this.PreviousActionWasTrue=0;
        this.ExecuteFalseAction();
    }
};

SfConditionalHandlerBase.prototype.ExecuteTrueAction=function(form)
{
    throw "method is abstract";
};

SfConditionalHandlerBase.prototype.ExecuteFalseAction=function(form)
{
    throw "method is abstract";
};
/************************************************************************************* Show Conditional Handler ***************************************************************************************************/
function SfShowConditionalHandler(options)
{
    SfConditionalHandlerBase.call(this,options);
    this.Options.Type="SfShowConditionalHandler";
    this.Fields="";
}
SfShowConditionalHandler.prototype=Object.create(SfConditionalHandlerBase.prototype);

SfShowConditionalHandler.prototype.GetConditionalSteps=function()
{
    if(this.IsNew){
        this.Options.GeneralInfo={};
        this.Options.FieldPicker={};
        this.Options.Condition={};
    }
    return [
        {Type:"SfNamePicker",Label:'HowDoYouWantToName',Options:this.Options.GeneralInfo,Id:this.Id},
        {Type:"SfHandlerFieldPicker",Label:'typeOrSelectFieldsToBeShown',Options:this.Options.FieldPicker},
        {Type:"SfHandlerConditionGenerator",Label:'WhenDoYouWantToDisplay',Options:this.Options.Condition}
    ];
};

SfShowConditionalHandler.prototype.Initialize=function(form,data)
{
    this.Form=form;
    var self=this;
    self.HideFields();
    self.SubscribeCondition(self.Options.Condition,data);

};

SfShowConditionalHandler.prototype.HideFields=function()
{
    this.Form.JQueryForm.find(this.GetFieldIds()).css('display','none');
};

SfShowConditionalHandler.prototype.GetFieldIds=function()
{
    if(this.Fields=="")
        for(var i=0;i<this.Options.FieldPicker.AffectedItems.length;i++)
        {
            if(i>0)
                this.Fields+=",";
            this.Fields+='#'+this.Options.FieldPicker.AffectedItems[i];

        }
    return this.Fields;
};

SfShowConditionalHandler.prototype.RegisterToEvents=function()
{

};

SfShowConditionalHandler.prototype.RegisterToEvents=function()
{

};


SfShowConditionalHandler.prototype.ExecuteTrueAction=function()
{
    this.Form.JQueryForm.find(this.GetFieldIds()).slideDown();
};

SfShowConditionalHandler.prototype.ExecuteFalseAction=function()
{
    this.Form.JQueryForm.find(this.GetFieldIds()).slideUp();
};

