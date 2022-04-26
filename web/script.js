
$('#search-btn').click(function () {
    var search = $('input[name="BookForm[search]"]:checked').val();
    var book = $('input[name="BookForm[book]"]').val();
    var title = $('input[name="BookForm[title]"]').val();
    $.ajax({
        type: 'post',
        url: urlSearchBook,
        data: {
            search: search,
            book: book,
            title: title
        },
        success: function (data) {
            $('#content').html(data);
        },
    });
    return false;
});

$('input[name="BookForm[title]"]').blur(function(){
    var search = $('input[name="BookForm[search]"]:checked').val();
    var book = $('input[name="BookForm[book]"]').val();
    var title = $('input[name="BookForm[title]"]').val();
    if(title.length == 0){
        return false;
    }
    $.ajax({
        type: 'post',
        url: urlSearchBook,
        data: {
            search: search,
            book: book,
            title: title
        },
        success: function (data) {
            $('#content').html(data);
        },
    });
})

$('body').on('click', '.spoiler', function(){
    console.log($(this).attr('spoiler'));
    $(this).css("display","none");
    $('#spoiler-'+$(this).attr('spoiler')).css("display","block");
});