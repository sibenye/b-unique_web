

/************************************************************************************* Base ***************************************************************************************************/
function SmartFormsBaseStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    this.FormElement=formElement;
    this.JQueryElementToStyle=jQueryElementToStyle;
    this.ElementName=elementName;
    this.AttributesCointainer=attributesCointainer;

    this.SetupElementHighlight();
}

SmartFormsBaseStyleSet.prototype.SetupElementHighlight=function()
{
    var self=this;
    this.JQueryElementToStyle.find('.'+this.ElementName).hover(
        function(event){
            event.stopPropagation();
            self.JQueryElementToStyle.find('.'+self.ElementName).addClass('redNaoStylerHighlight');
        },
        //unhover
        function(event){
            event.stopPropagation();
            self.JQueryElementToStyle.find('.'+self.ElementName).removeClass('redNaoStylerHighlight');


        }).click(function(){
            self.StartEdition();
        });

}

SmartFormsBaseStyleSet.prototype.StartEdition=function(jQueryTable)
{
    this.AttributesCointainer.empty();
    var properties=this.GetSetProperties();
    for(var i=0;i<properties.length;i++)
    {
        properties[i].AppendToElement(this.AttributesCointainer);
    }

}

SmartFormsBaseStyleSet.prototype.GetSetProperties=function()
{

}

/************************************************************************************* Title Style Set ***************************************************************************************************/
function SmartFormsTitleStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsTitleStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);


SmartFormsTitleStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoFontFamilyStyleProperty(this.FormElement,this.ElementName,"Font-Family","Font Family"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"Color","Title Color"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"border-bottom-color","Border Color"));
    return properties;
}



/************************************************************************************* Label Style Set ***************************************************************************************************/
function SmartFormsLabelStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsLabelStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);

SmartFormsLabelStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoFontFamilyStyleProperty(this.FormElement,this.ElementName,"Font-Family","Font Family"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"Color","Color"));
    return properties;
}

/************************************************************************************* Input Style Set ***************************************************************************************************/
function SmartFormsInputStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsInputStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);

SmartFormsInputStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoFontFamilyStyleProperty(this.FormElement,this.ElementName,"Font-Family","Font Family"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"Color","Color"));
    return properties;
}

/************************************************************************************* Prepend Style Set ***************************************************************************************************/
function SmartFormsPrependStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsPrependStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);

SmartFormsPrependStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoFontFamilyStyleProperty(this.FormElement,this.ElementName,"Font-Family","Font Family"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"Color","Color"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"background-color","Background Color"));
    return properties;
}

/************************************************************************************* Checkbox Style Set ***************************************************************************************************/
function SmartFormsCheckBoxStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsCheckBoxStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);

SmartFormsCheckBoxStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"background-color","Background Color"));
    return properties;
}

/************************************************************************************* Button Style Set ***************************************************************************************************/
function SmartFormsButtonStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsButtonStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);

SmartFormsButtonStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoFontFamilyStyleProperty(this.FormElement,this.ElementName,"Font-Family","Font Family"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"Color","Color"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"background-color","Background Color"));

    return properties;
}

/************************************************************************************* File Button Style Set ***************************************************************************************************/
function SmartFormsFileButtonStyleSet(formElement,jQueryElementToStyle,attributesCointainer,elementName)
{
    SmartFormsBaseStyleSet.call(this,formElement,jQueryElementToStyle,attributesCointainer,elementName);
}
SmartFormsFileButtonStyleSet.prototype=Object.create(SmartFormsBaseStyleSet.prototype);

SmartFormsFileButtonStyleSet.prototype.GetSetProperties=function()
{
    var properties=[];
    properties.push(new RedNaoFontFamilyStyleProperty(this.FormElement,this.ElementName,"Font-Family","Font Family"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"Color","Color"));
    properties.push(new RedNaoColorStyleProperty(this.FormElement,this.ElementName,"background-color","Background Color"));

    return properties;
}

