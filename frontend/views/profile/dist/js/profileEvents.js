$(function () {
    console.log('news news news');

    $("#avatr-edit").change(function(){
        readURL(this);
    });
})

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            // $(this).css("background-image", "url('/css/images/css.jpg')");
            $('#roundAvatarEdit').css("background-image", "url('"+e.target.result+"')" );
        }

        reader.readAsDataURL(input.files[0]);
    }
}


function updateImageDisplay() {
    console.log('news news news22');
}