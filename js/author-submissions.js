
$(document).ready(function(){

    window.onclick = function(event) 
    {
        var feedback_div = $(".popup_div")[0];
        if (event.target == feedback_div) 
        {
            feedback_div.style.display = "none";
        }
    }

    $( ".see_feedback_btn" ).on( "click", function() {
      
        $(".added_div").remove();
        $(".heading").text("Feedback");

        var parentTD = $(this).parent();
        var linkTD = parentTD.prev().prev().prev();
        var link = linkTD.find("a");
        var s_id = link.attr('id');

        $.ajax({
            type: "GET",
            url: "functions.php?getFeedback=true",
            data: {id:s_id},
            dataType: "json",
            success: function(response){
                var arr = ("" + response).split(",");

                if(arr.length > 0 )
                {
                    $(".popup-content").append( '<div class="added_div">' +  
                    '<table class="table table-striped"><tbody class="reviews_tbody"></tbody></table></div>');
                    $table = $(".reviews_tbody");
                    arr.forEach(element => {
                        $table.append('<tr><td>'+ element +'</td></tr>');
                    });
                }
            }
        });

        $(".popup_div")[0].style.display = "block";

    });

    $(".close").on('click',function(){

        $(".popup_div")[0].style.display = "none";

    });


    $( ".publish_btn" ).on( "click", function() {

        $(".added_div").remove();
        $(".heading").text("Add Citations");

        var parentTD = $(this).parent();
        var linkTD = parentTD.prev().prev().prev().prev();
        var link = linkTD.find("a");
        var s_id = link.attr('id');

        
        $(".popup-content").append( '<div class="added_div"><form method="POST" action="functions.php">' +  
        '<div class="form-group"><select style="width: 500px" class="form-control citations_select" name="citations[]" multiple></select></div>'+
        '<input type="hidden" name="publishID" value="'+ s_id +'">'+
        '<div class="form-group"><button type="submit" name="publish" class=" publish_popup_btn btn btn-primary">Publish</button></div></form></div>');

        $(".citations_select").select2({
            placeholder: 'Type cited publication title',
            ajax: {
                url: 'functions.php?getPublications=true',
                //type: "GET",
                delay: 250,
                dataType: 'json',
                minimumInputLength: 1,
                data: function (term) {
                    //alert(term.term);
                    return {
                        searchTerm: term.term // search term
                    };
                }
            }
    
        });
/*
        $(".publish_popup_btn").on("click", function(){

            $.ajax({
                type: "GET",
                url: "functions.php?publish=true",
                data: {publishID:s_id},
                //dataType: "html",
                success: function(response){
                    location.reload();
                }
            });

        });
*/

        $(".popup_div")[0].style.display = "block";
    });

    $( ".cancel_btn" ).on( "click", function() {

        var parentTD = $(this).parent();
        var linkTD = parentTD.prev().prev().prev().prev().prev();
        var link = linkTD.find("a");
        var s_id = link.attr('id');

        $.ajax({
            type: "POST",
            url: "functions.php?cancelSub=true",
            data: {id:s_id},
            //dataType: "text",
            success: function(response){
                location.reload();
            }
        });

    });

    $(".coauthors_select").select2({
        placeholder: 'Type an author email',
        ajax: {
            url: 'functions.php?getAuthorsEmails=true',
            //type: "GET",
            delay: 250,
            dataType: 'json',
            minimumInputLength: 1,
            data: function (term) {
                var author = $("i").text();
                return {
                    searchTerm: term.term, // search term
                    authEmail: author
                };
            }
        }

    });
    
});
