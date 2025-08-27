$(document).ready(function() {
    // Theme switcher
    $('.theme-switcher').on('click', function(e) {
        e.preventDefault();
        var theme = $(this).data('theme');
        $('body').removeClass().addClass(theme);
    });

    // Sidebar toggle
    $('.navbar-toggler').on('click', function() {
        $('#sidebar').toggleClass('show');
        $('#content').toggleClass('active');
    });
});
