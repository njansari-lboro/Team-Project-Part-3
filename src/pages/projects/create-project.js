$(() => {
    // const urlParams = new URLSearchParams(window.location.search);

    // if (urlParams.has("id")) {
    //     const projectName = urlParams.get("id");
    //     //$("#pname").val(projectName);
    //     console.log("ajax called");
    //     $.ajax({
    //         type: "POST",
    //         url: "../pages/projects/create-project-sql.php",
    //         data: {project_name: projectName },
    //         //datatype: "json",
    //         success: function (response) {
    //             console.log();
    //             $("#pleader").val(response.lead_id);
    //             $("#pbrief").val(response.brief);
    //             $("#project-deadline").val(response.deadline);
    //             $("#resource-hours").val(response.resource_hours);

    //         }
    //     });
    // }

    //var input = document.getElementById("members-entry");
    document.getElementById("members-entry").addEventListener("keyup", function() {
        //When the input box 'members-entry' has had a key entered in it
        const input = document.getElementById("members-entry").value
        if (input !== "") {
            //if input is not empty
            $.ajax({
                //ajax is called to get the names from the database and display them in div 'results-box'
                type: "POST",
                url: "projects/create-project-sql.php",
                data: { search: input },
                success: function (response) {
                    $(".results-box").html(response);
                }
            });
        } else {
            $(".results-box").html("");
        }
    });

    document.getElementById("pleader").addEventListener("keyup", function() {
        //When the input box 'pleader' has had a key entered in it
        const input = document.getElementById("pleader").value
        if (input !== "") {
            $.ajax({
                //ajax is called to get the names from the database and display them in div 'leader-names'
                type: "POST",
                url: "projects/create-project-sql.php",
                data: { search_leader: input },
                success: function (response) {
                    $(".leader-names").html(response);
                }
            });
        } else {
            $(".leader-names").html("");
        }
    });

    $("#closebtn").click(() => {
        //pressing the button takes you back to projects screen
        console.log("hello");
        window.location.href = "?page=projects"
    });

    //Can choose project deadline as a datepicker
    $("#project-deadline").datepicker({ dateFormat: "dd/mm/yy" });
});
