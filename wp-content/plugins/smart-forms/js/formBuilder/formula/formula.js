function RedNaoFormula(formElement,formula)
{
    this.FormElement=formElement;
    this.Formula=formula;
}

RedNaoFormula.prototype.FieldUsedInFormula=function(fieldName)
{
    for(var i=0;i<this.Formula.FieldsUsed.length;i++)
    {
        if(fieldName==this.Formula.FieldsUsed[i])
            return true;
    }

    return false;
}

RedNaoFormula.prototype.UpdateFieldWithValue=function(value)
{
    var calculatedValue=this.GetValueFromFormula(value);

    RedNaoBasicManipulatorInstance.SetValue(this.FormElement.Options,this.Formula.PropertyName,calculatedValue,this.Formula.additionalInformation);
    this.FormElement.RefreshElement();
}

RedNaoFormula.prototype.GetValueFromFormula=function(values)
{
    var formula=new Function('formData','return '+this.Formula.CompiledFormula);

    var calculatedValue=formula(values);
    if(typeof calculatedValue=='number'&&isNaN(calculatedValue))
        calculatedValue=0;

    return calculatedValue;
}