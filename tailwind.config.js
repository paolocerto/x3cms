/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./INSTALL/index.php",
    "./INSTALL/fix_tailwind_missing_classes.html",
    "./cms/forms/admin/**/*._form.php",
    "./cms/view/admin/**/*._view.php",
    "./plugins/**/forms/**/*._form.php",
    "./plugins/**/view/**/*._view.php",
    "./system/helpers/*._helper.php",
    "./themes/**/*.php",
    "./themes/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [

  ],
}