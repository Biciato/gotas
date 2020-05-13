/**
 * Class to Generate Images
 */
class ImageHelper {
    /**
     * Constructor
     *
     * @param {int} bootstrapVersion Bootstrap Version that you're using in your web page
     * @param {int} fontAwesomeVersion Font Awesome Version that you're using in your web page
     */
    constructor(bootstrapVersion = 3, fontAwesomeVersion = 5) {
        this.bootstrapVersion = bootstrapVersion;
        this.fontAwesomeVersion = fontAwesomeVersion;
    }

    /**
     * Generate Image Element
     *
     * @param {String} src Src
     * @param {String} alt Alt
     * @param {String} title Title
     * @param {String} customClass Custom Class
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-08
     */
    generateDefaultImage(src, alt, title = undefined, customClass = undefined) {
        let element = document.createElement("img");
        element.src = src;
        element.alt = alt;
        element.title = title !== undefined && title !== null ? title : "Imagem";
        element.classList = customClass !== undefined && customClass !== null ? customClass : "";

        return element;
    }
}
