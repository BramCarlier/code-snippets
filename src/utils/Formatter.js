export default class Formatter {
    static capitalizeFirstLetter(string) {
        if (string !== null) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    }
}
