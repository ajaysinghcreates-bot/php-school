$(document).ready(function() {
    // Theme switcher
    const themes = ['theme-corporate-blue', 'theme-academic-green', 'theme-modern-dark', 'theme-vibrant-orange', 'theme-clean-purple'];
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        $('body').removeClass(themes.join(' ')).addClass(currentTheme);
    }

    $('.theme-switcher').on('click', function(e) {
        e.preventDefault();
        const theme = $(this).data('theme');
        $('body').removeClass(themes.join(' ')).addClass(theme);
        localStorage.setItem('theme', theme);
    });

    // Initialize DataTables
    $('#dataTable').DataTable();
    $('#dataTable2').DataTable();
});