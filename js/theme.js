// Theme Management
class ThemeManager {
    setupTheme() {
        // Get the themeToggle id
        const themeToggle = document.getElementById('themeToggle');
        // Get the theme stored in the local storage / browser
        const currentTheme = localStorage.getItem('theme') || 'light';

        // Set the root element class data-theme into current theme
        document.documentElement.setAttribute('data-theme', currentTheme);
        
        // Add event listener if the theme toggle button is triggered
        themeToggle?.addEventListener('click', () => {
            // If the current theme is light then it will become dark
            // If the current theme is dark it will become light
            const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            // Set again a new theme to the root element
            document.documentElement.setAttribute('data-theme', newTheme);
            // Set the theme in the local storage 
            localStorage.setItem('theme', newTheme);
            // Update the new theme icon
            this.updateThemeIcon(newTheme);
        });

        // Update the current theme icon
        this.updateThemeIcon(currentTheme);
    }

    updateThemeIcon(theme) {
        // Get the theme toggle id
        const themeIcon = document.querySelector('#themeToggle i');
        // Return if there's no such id existed
        if (!themeIcon) return;

        // Get the desired class
        const desiredClass = theme === 'dark' ? 'fas fa-sun' :'fas fa-moon';
        // Change only the class of the class name is completely opposite the the desired class
        if (themeIcon.className !== desiredClass) {
            themeIcon.className = desiredClass;
        }
    }
}