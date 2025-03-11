/**
 * Composant HotTable - Wrapper pour Handsontable
 * 
 * Ce composant permet de simplifier l'utilisation de Handsontable dans l'application
 * avec des méthodes utilitaires et une configuration par défaut.
 */
class HotTable {
    /**
     * Initialise un nouveau tableau Handsontable
     * 
     * @param {HTMLElement} element - L'élément DOM où initialiser le tableau
     * @param {Object} options - Options de configuration pour Handsontable
     */
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            // Options par défaut
            licenseKey: 'non-commercial-and-evaluation',
            height: 'auto',
            stretchH: 'all',
            rowHeaders: true,
            colHeaders: true,
            contextMenu: true,
            manualColumnResize: true,
            manualRowResize: true,
            ...options
        };
        this.hot = null;
    }

    /**
     * Initialise le tableau Handsontable
     * 
     * @returns {Handsontable} - L'instance Handsontable créée
     */
    init() {
        this.hot = new Handsontable(this.element, this.options);
        return this.hot;
    }

    /**
     * Récupère les données du tableau
     * 
     * @returns {Array} - Les données du tableau
     */
    getData() {
        return this.hot.getData();
    }

    /**
     * Charge des données dans le tableau
     * 
     * @param {Array} data - Les données à charger
     */
    loadData(data) {
        this.hot.loadData(data);
    }

    /**
     * Met à jour les paramètres du tableau
     * 
     * @param {Object} settings - Les paramètres à mettre à jour
     */
    updateSettings(settings) {
        this.hot.updateSettings(settings);
    }

    /**
     * Ajoute un rendererr personnalisé pour les cellules spécifiques
     * 
     * @param {Function} renderer - La fonction de rendu personnalisée
     */
    addRenderer(renderer) {
        this.updateSettings({
            cells: renderer
        });
    }

    /**
     * Ajoute une validation personnalisée pour les cellules
     * 
     * @param {Function} validator - La fonction de validation
     */
    addValidator(validator) {
        this.hot.addHook('beforeValidate', validator);
    }

    /**
     * Nettoie les ressources quand le tableau n'est plus nécessaire
     */
    destroy() {
        if (this.hot) {
            this.hot.destroy();
            this.hot = null;
        }
    }

    /**
     * Crée un renderer spécial pour les jours du mois
     * 
     * @param {Array} weekends - Liste des indices des colonnes qui sont des weekends
     * @param {Array} holidays - Liste des indices des colonnes qui sont des jours fériés
     * @param {String} hourType - Type d'heures (jour/nuit)
     * @returns {Function} - La fonction renderer
     */
    static createMonthRenderer(weekends = [], holidays = [], hourType = 'day') {
        return function(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.NumericRenderer.apply(this, arguments);
            
            // Fonctionnalité de base : afficher 'abs' pour 0
            if (value === 0) {
                td.textContent = 'abs';
                td.style.backgroundColor = '#ffcccc'; // Rouge pour absence
            } else if (value > 0 && value <= 12) {
                // Couleur selon le type d'heures
                if (hourType === 'day') {
                    td.style.backgroundColor = '#ccffcc'; // Vert clair pour heures de jour
                } else if (hourType === 'night') {
                    td.style.backgroundColor = '#e6ccff'; // Violet clair pour heures de nuit
                }
            }
            
            // Style pour les weekends
            if (weekends.includes(col)) {
                td.classList.add('weekend');
                if (!value || value === 0) {
                    td.style.backgroundColor = '#f0f0f0'; // Gris clair pour les weekends sans valeur
                }
            }
            
            // Style pour les jours fériés
            if (holidays.includes(col)) {
                td.classList.add('holiday');
                if (!value || value === 0) {
                    td.style.backgroundColor = '#ffe8e8'; // Rouge très clair pour les jours fériés sans valeur
                }
            }
            
            return td;
        };
    }
}

// Rendre disponible globalement
window.HotTable = HotTable;