$(document).ready(function(){
    $("td form").submit(function($event){
        var form_id = $event.target.id;
        var form = $("#" + form_id);
        var url = form.attr("action");
        form.children("button").attr("disabled","disabled");
        if(data.actionName == "change_state")
        {
            form.siblings("span").text("loading..").css("color","black");
        }
        $.post(url, {}, function(data){
            if(data.code == 200)
            {
                if(data.actionName == "change_state")
                {
                    processActivateForm(form, data);
                }
                else if(data.actionName == "make_admin")
                {
                    form.parentsUntil("tbody", "tr").find("button").attr("disabled","disabled");
                }
                else if(data.actionName == "delete")
                {
                    form.parentsUntil("tbody", "tr").remove();
                }
            }
        });
        $event.preventDefault();
    });
})

function processActivateForm(form, data)
{
    var userState = data.userState;
    form.siblings("span").text(userState);
    var buttonText = null;
    if(userState == "Yes"){
        form.siblings("span").css("color","blue");
        buttonText = "Deactivate";
    } else if(userState == "No"){
        form.siblings("span").css("color","red");
        buttonText = "Activate";
    }
    form.children("button").text(buttonText);
    form.children("button").removeAttr("disabled");
}