
$(document).ready(function(){


    $( "#signup_role_select" ).on( "change", function() {
        
        $( ".signup_role_added_div" ).remove();
        switch( $(" #signup_role_select option:selected ").val() )
        {
            case "reviewer":
            case "author":
            {
                $.ajax({
                    type: "GET",
                    url: "functions.php?getExpertises=true",             
                    dataType: "json",                
                    success: function(response){
                        var arr = ("" + response).split(",");

                        if(arr.length <= 0 )
                        {
                            alert("No expertises in Database!");
                        }
                        else
                        {
                            $(".role_select_div").after( '<div class="form-group signup_role_added_div">' + 
                            '<label for="expertise">Select your expertise</label>' + 
                            '<select class="form-control" name="expertise[]" required multiple></select></div>');
                            $pubSel = $(".signup_role_added_div > select");
                            arr.forEach(element => {
                                $pubSel.append('<option value="'+ element +'">'+ element +'</option>');
                            });
                        }
                    }
                });
            }
            break;
            case "editor":
            {
                
                $.ajax({
                    type: "GET",
                    url: "functions.php?getPublishers=true",             
                    dataType: "json",                
                    success: function(response){
                        var arr = ("" + response).split(",");

                        if(arr.length <= 0 )
                        {
                            alert("No publishers in Database!");
                        }
                        else
                        {
                            $(".role_select_div").after( '<div class="form-group signup_role_added_div">' + 
                            '<label for="publisher">Select your publisher(s)</label>' + 
                            '<select class="form-control" name="publisher[]" required multiple></select></div>');
                            $pubSel = $(".signup_role_added_div > select");
                            arr.forEach(element => {
                                $pubSel.append('<option value="'+ element +'">'+ element +'</option>');
                            });
                        }
                    }
                });
            }
            break;
        }
    });
});


