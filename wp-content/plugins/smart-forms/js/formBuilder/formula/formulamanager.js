function RedNaoFormulaManager()
{
    this.Formulas=[];
    this.Data={};

    var self=this;
    RedNaoEventManager.Subscribe('formPropertyChanged',function(data){self.PropertyChanged(data)});
}

RedNaoFormulaManager.prototype.PropertyChanged=function(data)
{
    this.SetFormulaValue(data.FieldName,data.Value);
    this.UpdateFormulaFieldsIfNeeded(data.FieldName,data.Value);
    RedNaoEventManager.Publish('FormValueChanged',{FieldName:data.FieldName,Data:this.Data});
}


RedNaoFormulaManager.prototype.SetFormulaValue=function(fieldName,data)
{
    if(RedNaoPathExists(data,'value'))
    {
        data.label=data.value.toString();
        if(data.value=='')
            data.value=0;
        else
            if(!isNaN(data.value))
                data.value=parseFloat(data.value);
    }

    this.Data[fieldName]=data;
}

RedNaoFormulaManager.prototype.UpdateFormulaFieldsIfNeeded=function(fieldName)
{
    for(var i=0;i<this.Formulas.length;i++)
    {
        if(this.Formulas[i].FieldUsedInFormula(fieldName))
           this.Formulas[i].UpdateFieldWithValue(this.Data);
    }
}

RedNaoFormulaManager.prototype.RefreshAllFormulasAndConditionalLogic=function()
{
    for(var i=0;i<this.Formulas.length;i++)
        this.Formulas[i].UpdateFieldWithValue(this.Data);


}

RedNaoFormulaManager.prototype.AddFormula=function(formElement,formula)
{
    this.Formulas.push(new RedNaoFormula(formElement,formula))
}

var RedNaoFormulaManagerVar=new RedNaoFormulaManager();