
$('#search-btn').click(function () {
    var search = $('input[name="BookForm[search]"]:checked').val();
    var book = $('input[name="BookForm[book]"]').val();
    var title = $('input[name="BookForm[title]"]').val();
    var genre = [];
    var author = [];
    $('#genre-all input:checkbox:checked').each(function(){
        genre.push($(this).attr('name'));
    });
    $('#author-all input:checkbox:checked').each(function(){
        author.push($(this).attr('name'));
    });
    console.log(JSON.stringify(author));
    console.log(JSON.stringify(genre));
    $.ajax({
        type: 'post',
        url: urlSearchBook,
        data: {
            search: search,
            book: book,
            title: title,
            genre: JSON.stringify(genre),
            author: JSON.stringify(author),
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

$('body').on('click', '#genre', function(){
    $('#genre-all').show("slow");
});

$('body').on('click', '#author', function(){
    $('#author-all').show("slow");
});
