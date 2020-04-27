/**
 * Helper to Generate Buttons
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-27
 */

/**
 * Class to Generate Buttons
 */
class ButtonHelper {
    ICON_CONFIG = "config";
    ICON_INFO = "info";

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
     * Generates a link button to a destination
     *
     * @param {String} url Destination
     * @param {*} typeIcon Type Icon (config, info)
     * @param {*} text Text within link
     * @param {*} tooltip Tooltip
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-24
     */
    generateLinkViewToDestination(url, typeIcon = undefined, text = undefined, tooltip = undefined) {
        let linkElement = document.createElement("a");
        linkElement.href = url;

        if (this.bootstrapVersion = 3) {
            linkElement.classList = "btn btn-default";
        }

        // @TODO add bootstrap 4
        let iconClass = "";
        let iconElement = document.createElement("i");

        switch (typeIcon) {
            case "config":
                iconClass = "fas fa-cogs";
                break;
            case "info":
                iconClass = "fas fa-info";
                break;
            default:
                iconClass = "fas fa-info";
                break;
        }

        iconElement.classList = iconClass;
        linkElement.append(iconElement);

        if (text !== undefined && text !== null) {
            let textElement = document.createElement("span");
            textElement.textContent = text;
            linkElement.append(textElement);
        }

        if (tooltip !== undefined && tooltip !== null) {
            linkElement.title = tooltip;
        }

        return linkElement;
    }

    /**
     * Generates a link button to a destination
     *
     * @param {String} url Destination
     * @param {*} text Text within link
     * @param {*} tooltip Tooltip
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-24
     */
    generateLinkEditToDestination(url, text = undefined, tooltip = undefined) {
        let linkElement = document.createElement("a");
        linkElement.href = url;

        if (this.bootstrapVersion = 3) {
            linkElement.classList = "btn btn-primary";
        }

        // @TODO add bootstrap 4
        let iconClass = "fas fa-edit";
        let iconElement = document.createElement("i");

        iconElement.classList = iconClass;
        linkElement.append(iconElement);

        if (text !== undefined && text !== null) {
            let textElement = document.createElement("span");
            textElement.textContent = text;
            linkElement.append(textElement);
        }

        if (tooltip !== undefined && tooltip !== null) {
            linkElement.title = tooltip;
        }

        return linkElement;
    }

    /**
     * Generates a link button to a destination
     *
     * @param {Boolean} booleanStatus
     * @param {String} text Text within link
     * @param {String} tooltip Tooltip
     * @param {String} customClass Custom Class
     * @param {Object} dataAttributes Object of Data Attributes
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-24
     */
    generateImgChangeStatus(booleanStatus = false, text = undefined, tooltip = undefined, customClass = undefined, dataAttributes = []) {
        let linkElement = document.createElement("a");

        if (this.bootstrapVersion = 3) {
            // @TODO add bootstrap 4
            linkElement.classList.add("btn");
            linkElement.classList.add(booleanStatus ? "btn-danger" : "btn-primary");
        }

        if (customClass !== undefined && customClass !== null) {
            linkElement.classList.add(customClass);
        }

        let iconClass = "fas fa-power-off";
        let iconElement = document.createElement("i");
        iconElement.classList = iconClass;

        // Attributes will be wrapped in linkElement because it is this dom element which will be accessed on event trigger
        let keys = Object.keys(dataAttributes);
        keys.forEach(key => {
            linkElement.setAttribute("data-" + key, dataAttributes[key]);
            iconElement.setAttribute("data-" + key, dataAttributes[key]);
        });

        linkElement.append(iconElement);

        if (text !== undefined && text !== null) {
            let textElement = document.createElement("span");
            textElement.textContent = text;
            linkElement.append(textElement);
        }

        if (tooltip !== undefined && tooltip !== null) {
            linkElement.title = tooltip;
        }

        return linkElement;
    }
}
