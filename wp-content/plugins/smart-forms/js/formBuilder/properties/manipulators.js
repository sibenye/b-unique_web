"use strict;"


/************************************************************************************* Manipulstors ***************************************************************************************************/
function RedNaoBasicManipulator()
{

}

RedNaoBasicManipulator.prototype.GetValue=function(propertiesObject,propertyName,additionalInformation)
{
    return propertiesObject[propertyName];
}

RedNaoBasicManipulator.prototype.SetValue=function(propertiesObject,propertyName,value,additionalInformation)
{
    propertiesObject[propertyName]=value;
}





var RedNaoBasicManipulatorInstance=new RedNaoBasicManipulator();
