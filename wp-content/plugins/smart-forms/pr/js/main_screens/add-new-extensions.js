function SmartFormsAddNewExtension()
{
    rnJQuery("#tabpro").empty();
    rnJQuery("#tabpro").append('<div class="component">\
        <div class="control-group sfFileUpload">\
    </div>\
    </div>');

    var self=this;
    RedNaoEventManager.Subscribe('AddExtendedElements',self.AddExtendedElements);
}

SmartFormsAddNewExtension.prototype.AddExtendedElements=function(extensionArray)
{
    extensionArray.push('sfFileUpload');
}

var SmartFormsAddNewExtensionVar=null;
rnJQuery(function()
{
    SmartFormsAddNewExtensionVar=new SmartFormsAddNewExtension();
})
