$(document).ready(function() {
    loadContent();

    $('.menu a').click(function() {
        loadContent($(this).data('content'));
    });

    function loadContent(content = 'users') {
        $.ajax({
            url: 'admin.php',
            type: 'GET',
            data: { content: content },
            success: function(response) {
                $('#mainContent').html(response);
            },
            error: function() {
                alert('Error loading content.');
            }
        });
    }
});