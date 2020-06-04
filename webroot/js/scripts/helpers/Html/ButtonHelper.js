/**
 * Class to Generate Buttons
 */
class ButtonHelper {

    /**
     * Helper to Generate Buttons
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-27
     */
    ICON_DELETE = "delete";
    ICON_CONFIG = "config";
    ICON_INFO = "info";
    ICON_DELETE_V4 = "fa fa-trash";
    ICON_DELETE_V5 = "fas fa-trash";
    ICONS_DELETE = [{
            key: 4,
            value: this.ICON_DELETE_V4,
        },
        {
            key: 5,
            value: this.ICON_DELETE_V5
        }
    ];

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
     * Generates a button to toggle Add / Remove Status
     *
     * @param {Object} dataAttributes Object of Data Attributes
     * @param {Boolean} booleanStatus If true, shows plus sign. If False, shows remove sign
     * @param {String} text Text within link
     * @param {String} tooltip Tooltip
     * @param {String} customClass Custom Class
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-20
     */
    generateAddRemovBtn(dataAttributes = [], booleanStatus = false, text = undefined, tooltip = undefined, customClass = undefined) {
        let linkElement = document.createElement("a");

        if (this.bootstrapVersion = 3) {
            // @TODO add bootstrap 4
            linkElement.classList.add("btn");
            linkElement.classList.add(booleanStatus ? "btn-primary" : "btn-danger");
        }

        if (customClass !== undefined && customClass !== null) {
            let classArray = [];
            if (typeof (customClass) === "string") {
                customClass.split(" ").filter(x => x.length > 0).forEach(x => classArray.push(x));
            } else {
                classArray = customClass;
            }
            classArray.forEach(c => linkElement.classList.add(c));
        }

        let iconClass = "";

        if (this.fontAwesomeVersion = 4) {
            iconClass = (booleanStatus) ? "fa fa-plus" : "fa fa-minus";
        } else if (this.fontAwesomeVersion = 5) {
            iconClass = (booleanStatus) ? "fas fa-plus" : "fas fa-minus";
        }

        let iconElement = document.createElement("em");
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

    /**
     * Generates a link button to a destination
     *
     * @param {String} url Destination
     * @param {String} typeIcon Type Icon (config, info)
     * @param {String} text Text within link
     * @param {String} tooltip Tooltip
     * @param {String} customClass Custom Class
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-24
     */
    generateLinkViewToDestination(url, typeIcon = undefined, text = undefined, tooltip = undefined, customClass = undefined, id = null) {
        let linkElement = document.createElement("a");

        // Sets data to open modal e get id to show user data
        if (url.includes('usuarios')) {
            linkElement.classList.add(customClass);
            linkElement.setAttribute('data-id', id);
            linkElement.setAttribute('data-target', '#modal_info');
            linkElement.setAttribute('data-toggle', 'modal');
        } else {
            linkElement.href = url;
        }

        if (this.bootstrapVersion = 3) {
            linkElement.classList.add("btn");
            linkElement.classList.add("btn-default");
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
            linkElement.classList = "btn btn-info";
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
     * Generates a button to change Status (On / Off)
     *
     * @param {Object} dataAttributes Object of Data Attributes
     * @param {Boolean} booleanStatus
     * @param {String} text Text within link
     * @param {String} tooltip Tooltip
     * @param {String} customClass Custom Class
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-24
     */
    generateImgChangeStatus(dataAttributes = [], booleanStatus = false, text = undefined, tooltip = undefined, customClass = undefined) {
        let linkElement = document.createElement("a");

        if (this.bootstrapVersion = 3) {
            // @TODO add bootstrap 4
            linkElement.classList.add("btn");
            linkElement.classList.add(booleanStatus ? "btn-primary" : "btn-danger");
        }

        if (customClass !== undefined && customClass !== null) {
            let classArray = [];
            if (typeof (customClass) === "string") {
                customClass.split(" ").filter(x => x.length > 0).forEach(x => classArray.push(x));
            } else {
                classArray = customClass;
            }
            classArray.forEach(c => linkElement.classList.add(c));
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

    /**
     * Generates generic danger image button
     *
     * @param {Array} dataAttributes List of Data Attributes
     * @param {string} text Text to display
     * @param {string} tooltip Title
     * @param {string} customClass Custom Class of anchor element
     * @param {string} imgClass Custom Class of i element (icon)
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-28
     */
    genericImgDangerButton(dataAttributes = [], text = undefined, tooltip = undefined, customClass = undefined, imgClass = undefined) {
        let linkElement = document.createElement("a");

        if (this.bootstrapVersion = 3) {
            // @TODO add bootstrap 4
            linkElement.classList.add("btn");
            linkElement.classList.add("btn-danger");
            linkElement.setAttribute('data-target', '#modal_confirmar');
            linkElement.setAttribute('data-toggle', 'modal');
        }

        if (customClass !== undefined && customClass !== null) {
            linkElement.classList.add(customClass);
        }

        let iconClass = imgClass !== undefined && imgClass !== null ? imgClass : this.ICONS_DELETE.filter(x => x.key === this.fontAwesomeVersion)[0].value;
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

    /**
     * Generates a link button to a destination
     *
     * @param {Object} dataAttributes Object of Data Attributes
     * @param {String} btnClass Type Icon (config, info)
     * @param {String} text Text within link
     * @param {String} tooltip Tooltip
     * @param {String} iconClass Type Icon (config, info)
     * @param {String} customClass Custom Class
     * @returns Element HTML
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-04-24
     */
    generateSimpleButton(dataAttributes = [], btnClass = undefined, text = undefined, tooltip = undefined, iconClass = undefined, customClass = undefined) {
        let divElement = document.createElement("div");
        divElement.href = "#";

        if (this.bootstrapVersion = 3) {
            divElement.classList.add("btn");

            if (btnClass !== undefined) {
                if (btnClass === this.ICON_INFO) {
                    divElement.classList.add("btn-default");
                } else if (btnClass === this.ICON_CONFIG) {
                    divElement.classList.add("btn-primary");
                } else if (btnClass === this.ICON_DELETE_V4) {
                    divElement.classList.add("btn-danger");
                } else {
                    divElement.classList.add("btn-default");
                }
            } else {
                divElement.classList.add("btn-default");
            }
        }

        // @TODO add bootstrap 4
        let iconElement = document.createElement("i");

        switch (iconClass) {
            case "config":
                iconClass = "fas fa-cogs";
                break;
            case "delete":
                iconClass = "fas fa-trash";
                break;
            case "alert":
                iconClass = "fas fa-exclamation";
                break;
            case "info":
                iconClass = "fas fa-info";
                break;
            default:
                break;
        }

        iconElement.classList = iconClass;

        if (customClass !== undefined && customClass !== null) {
            customClass.split(" ").forEach(classItem => {
                iconElement.classList.add(classItem);
                divElement.classList.add(classItem);
            });
        }

        // Attributes will be wrapped in linkElement because it is this dom element which will be accessed on event trigger
        let keys = Object.keys(dataAttributes);
        keys.forEach(key => {
            divElement.setAttribute("data-" + key, dataAttributes[key]);
            iconElement.setAttribute("data-" + key, dataAttributes[key]);
        });

        divElement.append(iconElement);

        if (text !== undefined && text !== null) {
            let textElement = document.createElement("span");
            textElement.textContent = " " + text;
            divElement.append(textElement);
        }

        if (tooltip !== undefined && tooltip !== null) {
            divElement.title = tooltip;
        }

        return divElement;
    }
}
