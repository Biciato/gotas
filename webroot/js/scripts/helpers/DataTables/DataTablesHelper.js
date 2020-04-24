/**
 * Helper to Manage DataTables library
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-04-20
 */

//#region Classes

/**
 * Class to Generate Action Buttons
 */
class DataTableActionButton {
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
}
//#endregion

// #region Functions

/**
 * Helper to generate a dynamic DataTable.
 *
 * Must Include https://datatables.net/ in your project's references
 *
 * @param {Object} element Element
 * @param {array} columns Columns array
 * @param {array} dataSource Json data
 * @param {array} lengthMenu Number of results per page
 * @param {array} languageOptions Language options
 * @param {function} callbackFunction Function to be executed when rendered
 * @returns DataTable element
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 2020-04-24
 */
function generateDataTable(element, columns, dataSource, lengthMenu, languageOptions, callbackFunction) {
    try {
        if ($.fn.DataTable.isDataTable(element)) {
            element.DataTable().clear();
            element.DataTable().destroy();
        }

        if (lengthMenu === undefined || lengthMenu === null) {
            lengthMenu = [10, 25, 50, 100];
        }

        /**
         * No array de data, é esperado uma coluna 'actions'. Se esta coluna não existe, adiciona-a vazia
         * In data source, it is expect a 'actions' column. If this column doesn't exists, it creates, only if columns data
         * contains an 'actions' data item
         */
        let dataRows = [];

        if (columns.filter(x => x.data === 'actions').length > 0) {
            dataSource.forEach(item => {
                // Create actions column if it doesn't exist
                if (item.actions === undefined) {
                    item.actions = [];
                }

                let actionsItem = [];

                item.actions.forEach(action => {
                    if (action.href.indexOf(":id") > 0) {
                        action.href = action.href.replace(":id", item.id);
                    }
                    console.log(action);
                    actionsItem.push(action.outerHTML);
                });

                item.actions = actionsItem.join("  ");

                dataRows.push(item);
            })
        } else {
            dataRows = dataSource;
        }

        let languageData = {};
        if (languageOptions === undefined || languageOptions === null) {
            languageData.url = "/webroot/js/DataTables/i18n/dataTables.pt-BR.lang";
        }

        element.DataTable({
            language: languageData,
            columns: columns,
            lengthMenu: lengthMenu,
            data: dataRows
        });

        if (callbackFunction !== undefined) {
            callbackFunction();
        }
    } catch (error) {
        window.alert("Error during DataTable initialization! \n" + error.message);
        console.log("Full Log of DataTable error: " + error);
    }
}


//#endregion
