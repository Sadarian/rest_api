$(document).ready(function() {
    
    //------------ GET ---------------------------------------------------------
    $('#category_get').on('submit', function(e) {
        e.preventDefault();
        let url = '/get';

        let dataInput = {};
        let $id = $("#get_id").val();
        let $slug = $("#get_slug").val();
        if ($id > 0) {
            dataInput.id = $id;
        } else if ($slug){
            dataInput.slug = $slug;
        } else {
            return;
        }

        $.ajax({
            method: 'POST',
            url: url,
            data: dataInput
        }).done(function(data) {
            let out = "";
            out += "<h2>" + data.status + " Catagory not found</h2>";
            if (data.category) {
                out += getCategory(data.category);
            }
            $('#out').html(out);
        })
    });

    //------------ CREATE ---------------------------------------------------------
    $('#category_create').on('submit', function(e) {
        e.preventDefault();
        let url = '/create';

        $.ajax({
            method: 'POST',
            url: url,
            data: {name: $("#create_name").val(), parent: $("#create_parent").val(), isVisible: $("#create_visible").prop( 'checked' )}
        }).done(function(data) {
            $('#out').html("<h2>" + data.status + "</h2>");
        })
    });

    //----------- TREE ---------------------------------------------------------
    $('#category_tree').on('submit', function(e) {
        e.preventDefault();
        let url = '/tree';
        $.ajax({
            method: 'POST',
            url: url,
            data: {name: $("#tree_name").val()}
        }).done(function(data) {
            let out = "";
            out += "<h2>" + data.status + "</h2>";
            for (let index = 0; index < data.list.length; index++) {
                var category = data.list[index];
                out += getCategory(category);
            }
            $('#out').html(out);
        })
    });

    function getCategory(category) {
        let out = "<ul>";
        out += "<li>Id: " + category.id + "</li>";
        out += "<li>Name: " + category.name + "</li>";
        out += "<li>Slug: " + category.slug + "</li>";
        out += "<li>Parent: " + category.parent + "</li>";
        let visible = (category.isVisible) ? "true" : "false";
        out += "<li>Visible: " + "<a href='/hide/" + category.id + "' title='Hide'>" + visible + "</a>" + "</li>";
        out += "</ul>";
        return out;
    }
});