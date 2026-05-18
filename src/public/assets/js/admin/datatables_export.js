/**
 * Tema PDF para exportaciones DataTables (pdfMake).
 * Colores alineados con app.css; cabecera con logotipo a la derecha.
 */
const DataTablesPdfTheme = (function () {
    const COLORS = {
        primary: '#0F4C81',
        primaryDark: '#0c3c66',
        rowAlt: '#e0f2fe',
        rowBase: '#ffffff',
        border: '#e2e8f0',
        text: '#334155',
        muted: '#64748b',
        white: '#ffffff',
    };

    const DEFAULT_LOGO_PATH = 'img/isotype.png';
    let cachedLogoDataUrl = null;
    let logoLoadPromise = null;

    function getLogoUrl(customPath) {
        const path = customPath || DEFAULT_LOGO_PATH;
        const base = typeof ASSETS_URL !== 'undefined' ? ASSETS_URL : '/assets/';
        return base + path.replace(/^\//, '');
    }

    function loadLogoDataUrl(customPath) {
        return fetch(getLogoUrl(customPath))
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Logo no encontrado');
                }
                return response.blob();
            })
            .then(function (blob) {
                return new Promise(function (resolve, reject) {
                    const reader = new FileReader();
                    reader.onloadend = function () {
                        resolve(reader.result);
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });
            })
            .catch(function () {
                return null;
            });
    }

    function ensureLogoLoaded(customPath) {
        if (cachedLogoDataUrl) {
            return Promise.resolve(cachedLogoDataUrl);
        }
        if (!logoLoadPromise) {
            logoLoadPromise = loadLogoDataUrl(customPath).then(function (dataUrl) {
                cachedLogoDataUrl = dataUrl;
                return dataUrl;
            });
        }
        return logoLoadPromise;
    }

    document.addEventListener('DOMContentLoaded', function () {
        ensureLogoLoaded();
    });

    function findTableBlock(doc) {
        if (!doc.content) {
            return null;
        }
        for (let i = 0; i < doc.content.length; i++) {
            if (doc.content[i].table) {
                return doc.content[i];
            }
        }
        return null;
    }

    function removeDefaultPdfTitle(doc) {
        if (!doc.content || !doc.content.length) {
            return;
        }
        const first = doc.content[0];
        if (
            first &&
            typeof first.text === 'string' &&
            !first.table &&
            !first.columns &&
            !first.stack &&
            first.text.indexOf('Gestión de Guardias') !== -1
        ) {
            doc.content.shift();
        }
    }

    function buildReportHeader(title, opts) {
        const logo = opts.logoDataUrl || cachedLogoDataUrl;
        const generatedAt = {
            text: 'Generado el ' + new Date().toLocaleString('es-ES'),
            style: 'reportMeta',
            margin: [0, 4, 0, 0],
        };
        const titleBlock = {
            text: title,
            style: 'reportTitle',
        };

        if (logo) {
            return {
                columns: [
                    {
                        width: '*',
                        stack: [titleBlock, generatedAt],
                    },
                    {
                        width: 88,
                        image: logo,
                        fit: [72, 48],
                        alignment: 'right',
                    },
                ],
                columnGap: 16,
                margin: [0, 0, 0, 14],
            };
        }

        return {
            stack: [titleBlock, generatedAt],
            margin: [0, 0, 0, 14],
        };
    }

    function applyTheme(doc, options) {
        const opts = options || {};
        const title =
            opts.title ||
            (document.querySelector('.text-title') &&
                document.querySelector('.text-title').textContent.trim()) ||
            'Informe';

        doc.pageOrientation = opts.orientation || 'portrait';
        doc.pageSize = opts.pageSize || 'A4';
        doc.pageMargins = [40, 48, 40, 56];

        doc.defaultStyle = {
            fontSize: 9,
            color: COLORS.text,
        };

        doc.styles = doc.styles || {};
        doc.styles.reportTitle = {
            fontSize: 18,
            bold: true,
            color: COLORS.primary,
        };
        doc.styles.reportMeta = {
            fontSize: 8,
            color: COLORS.muted,
        };
        doc.styles.tableHeader = {
            bold: true,
            fontSize: 9,
            color: COLORS.white,
            fillColor: COLORS.primary,
        };
        doc.styles.footer = {
            fontSize: 8,
            color: COLORS.muted,
        };

        removeDefaultPdfTitle(doc);

        doc.content.unshift(buildReportHeader(title, opts));

        const tableBlock = findTableBlock(doc);
        if (tableBlock && tableBlock.table) {
            const colCount = tableBlock.table.body[0]
                ? tableBlock.table.body[0].length
                : 1;
            tableBlock.table.widths = Array(colCount).fill('*');

            tableBlock.layout = {
                hLineWidth: function () {
                    return 0.5;
                },
                vLineWidth: function () {
                    return 0.5;
                },
                hLineColor: function () {
                    return COLORS.border;
                },
                vLineColor: function () {
                    return COLORS.border;
                },
                paddingLeft: function () {
                    return 10;
                },
                paddingRight: function () {
                    return 10;
                },
                paddingTop: function () {
                    return 8;
                },
                paddingBottom: function () {
                    return 8;
                },
                fillColor: function (rowIndex) {
                    if (rowIndex === 0) {
                        return COLORS.primary;
                    }
                    return rowIndex % 2 === 1 ? COLORS.rowBase : COLORS.rowAlt;
                },
            };

            const headerRow = tableBlock.table.body[0];
            if (headerRow) {
                headerRow.forEach(function (cell, index) {
                    if (typeof cell === 'object' && cell !== null) {
                        cell.fillColor = COLORS.primary;
                        cell.color = COLORS.white;
                        cell.bold = true;
                        return;
                    }
                    headerRow[index] = {
                        text: String(cell),
                        fillColor: COLORS.primary,
                        color: COLORS.white,
                        bold: true,
                    };
                });
            }
        }

        doc.footer = function (currentPage, pageCount) {
            return {
                columns: [
                    {
                        text: opts.footerLabel || 'Gestión de Guardias',
                        style: 'footer',
                        alignment: 'left',
                    },
                    {
                        text: currentPage + ' / ' + pageCount,
                        style: 'footer',
                        alignment: 'right',
                    },
                ],
                margin: [40, 12, 40, 0],
            };
        };
    }

    /**
     * @param {object} buttonConfig Config del botón DataTables (extend, text, exportOptions…)
     * @param {object} themeOptions   { title, orientation, footerLabel, logoPath }
     */
    function pdfButton(buttonConfig, themeOptions) {
        const base = Object.assign({ title: '' }, buttonConfig);
        const theme = themeOptions || {};
        const userCustomize = base.customize;
        const pdfHtml5 = DataTable.ext.buttons.pdfHtml5;
        const defaultAction = pdfHtml5.action;

        base.extend = 'pdfHtml5';
        base.title = '';
        base.customize = function (doc) {
            applyTheme(doc, theme);
            if (typeof userCustomize === 'function') {
                userCustomize(doc);
            }
        };
        base.action = function (e, dt, node, config) {
            const self = this;
            ensureLogoLoaded(theme.logoPath).then(function () {
                defaultAction.call(self, e, dt, node, config);
            });
        };

        return base;
    }

    return {
        COLORS: COLORS,
        applyTheme: applyTheme,
        pdfButton: pdfButton,
        ensureLogoLoaded: ensureLogoLoaded,
    };
})();
