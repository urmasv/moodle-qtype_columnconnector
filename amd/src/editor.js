// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * columnconnector visuaalne autoritoimetaja.
 * Õpetaja lisab tulpi, lahtreid ja valib igal 2.–4. tulba lahtril
 * märkeruutudega õiged ühendused eelmise tulba lahtritega — nagu H5P-s.
 * Kokku pandud mudel kirjutatakse peidetud väljale.
 *
 * @module     qtype_columnconnector/editor
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /**
     * Eemalda HTML-sildid tekstist.
     *
     * @param {String} html
     * @return {String}
     */
    function stripTags(html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html || '';
        return (tmp.textContent || tmp.innerText || '').trim();
    }

    /**
     * Lihtne elemendi looja.
     *
     * @param {String} tag
     * @param {Object} attrs
     * @param {String|Node|Array} children
     * @return {Element}
     */
    function el(tag, attrs, children) {
        var node = document.createElement(tag);
        attrs = attrs || {};
        Object.keys(attrs).forEach(function(k) {
            if (k === 'class') {
                node.className = attrs[k];
            } else if (k === 'text') {
                node.textContent = attrs[k];
            } else {
                node.setAttribute(k, attrs[k]);
            }
        });
        if (children === undefined || children === null) {
            return node;
        }
        (Array.isArray(children) ? children : [children]).forEach(function(c) {
            if (c === null || c === undefined) {
                return;
            }
            node.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
        });
        return node;
    }

    /**
     * Toimetaja konstruktor.
     *
     * @param {Object} config
     */
    function Editor(config) {
        this.config = config;
        this.s = config.strings || {};
        this.root = document.querySelector(config.rootSelector);
        this.input = document.getElementById(config.inputId)
            || document.querySelector('[name="contentjson"]');
        this.numSelect = document.getElementById(config.numColumnsId)
            || document.querySelector('[name="numcolumns"]');
        if (!this.root) {
            return;
        }
        if (!this.input) {
            this.root.textContent = this.s.loadError || 'Editor could not load.';
            return;
        }

        this.columns = this.loadModel();
        this.ensureColumnCount(this.getNumColumns());

        // Üleslaaditud lahtripiltide olek.
        this.imageFiles = [];
        this.imageSelects = [];

        var self = this;
        if (this.numSelect) {
            this.numSelect.addEventListener('change', function() {
                self.ensureColumnCount(self.getNumColumns());
                self.render();
                self.serialize();
            });
        }

        this.render();
        this.serialize();
        this.loadImageFiles();
    }

    /**
     * @return {String} tõlge
     */
    Editor.prototype.t = function(key, replacements) {
        var out = this.s[key] || key;
        Object.keys(replacements || {}).forEach(function(p) {
            out = out.replace(p, replacements[p]);
        });
        return out;
    };

    /**
     * @return {Number} valitud tulpade arv
     */
    Editor.prototype.getNumColumns = function() {
        var n = this.numSelect ? parseInt(this.numSelect.value, 10) : this.columns.length;
        return Math.max(2, Math.min(7, n || 2));
    };

    /**
     * Laadi mudel peidetud väljalt ja rekonstrueeri lahtrite _prev valikud.
     *
     * @return {Array}
     */
    Editor.prototype.loadModel = function() {
        var data = {};
        try {
            data = JSON.parse(this.input.value || '{}');
        } catch (e) {
            data = {};
        }
        var columns = (data.columns || []).map(function(col) {
            return {
                title: col.title || '',
                cells: (col.cells || []).map(function(cell) {
                    return {
                        text: cell.text || '',
                        image: cell.image || '',
                        imageurl: cell.imageurl || '',
                        imageposition: cell.imageposition === 'left' ? 'left' : 'above',
                        imageleftsize: (cell.imageleftsize === 'medium' || cell.imageleftsize === 'large') ? cell.imageleftsize : 'small',
                        imagealign: (cell.imagealign === 'left') ? 'left' : 'center',
                        alt: cell.alt || '',
                        prev: []
                    };
                })
            };
        });

        // Rekonstrueeri _prev ühendused vastusevõtmest.
        (data.answerkey || []).forEach(function(item) {
            if (!item || !item.from || !item.to) {
                return;
            }
            var lo = item.from;
            var hi = item.to;
            if (hi.col < lo.col) {
                lo = item.to;
                hi = item.from;
            }
            if (hi.col === lo.col + 1 && columns[hi.col] && columns[hi.col].cells[hi.row]) {
                columns[hi.col].cells[hi.row].prev.push(lo.row);
            }
        });

        return columns;
    };

    /**
     * Taga, et tulpi oleks vähemalt nõutud arv; igas vähemalt üks lahter.
     *
     * @param {Number} n
     */
    Editor.prototype.ensureColumnCount = function(n) {
        while (this.columns.length < n) {
            this.columns.push({title: '', cells: []});
        }
        if (this.columns.length > n) {
            this.columns = this.columns.slice(0, n);
            // Eemalda ühendused, mis viitavad kadunud tulpadele.
            this.columns.forEach(function(col, ci) {
                col.cells.forEach(function(cell) {
                    if (ci === 0) {
                        cell.prev = [];
                    }
                });
            });
        }
        this.columns.forEach(function(col) {
            if (col.cells.length === 0) {
                col.cells.push({text: '', image: '', imageurl: '', imageposition: 'above',
                    imageleftsize: 'small', imagealign: 'center', alt: '', prev: []});
            }
        });
    };

    /**
     * @return {Element} tühi lahter
     */
    Editor.prototype.blankCell = function() {
        return {text: '', image: '', imageurl: '', imageposition: 'above',
            imageleftsize: 'small', imagealign: 'center', alt: '', prev: []};
    };

    /**
     * Renderda kogu toimetaja.
     */
    Editor.prototype.render = function() {
        var self = this;
        var n = this.getNumColumns();
        this.root.innerHTML = '';
        this.imageSelects = [];

        var grid = el('div', {'class': 'cc-editor-grid'});
        for (var c = 0; c < n; c++) {
            grid.appendChild(this.renderColumn(c));
        }
        this.root.appendChild(grid);

        // Kohanda ridade arv nii, et kadunud tulba lahtrite valikud ei jääks.
        self.pruneConnections();
    };

    /**
     * Renderda üks tulp.
     *
     * @param {Number} c
     * @return {Element}
     */
    Editor.prototype.renderColumn = function(c) {
        var self = this;
        var column = this.columns[c];

        var head = el('div', {'class': 'cc-ed-colhead'}, [
            el('strong', {text: this.t('column') + ' ' + (c + 1)})
        ]);

        var title = el('input', {
            type: 'text', 'class': 'form-control cc-ed-title',
            placeholder: this.t('columnTitle'), value: column.title
        });
        title.addEventListener('input', function() {
            column.title = title.value;
            self.serialize();
        });

        var cells = el('div', {'class': 'cc-ed-cells'});
        column.cells.forEach(function(cell, r) {
            cells.appendChild(self.renderCell(c, r));
        });

        var add = el('button', {
            type: 'button', 'class': 'btn btn-secondary btn-sm cc-ed-addcell'
        }, this.t('addCell'));
        add.addEventListener('click', function() {
            column.cells.push(self.blankCell());
            self.render();
            self.serialize();
        });

        return el('div', {'class': 'cc-ed-column', 'data-col': c},
            [head, title, cells, add]);
    };

    /**
     * Renderda üks lahter.
     *
     * @param {Number} c
     * @param {Number} r
     * @return {Element}
     */
    Editor.prototype.renderCell = function(c, r) {
        var self = this;
        var cell = this.columns[c].cells[r];

        var rte = this.makeRichText(cell.text, function(html) {
            cell.text = html;
            // Uuenda järgmise tulba ühenduste silte, sest need viitavad sellele.
            self.refreshConnectionsForColumn(c + 1);
            self.serialize();
        });
        var text = rte.wrapper;

        var url = el('input', {
            type: 'text', 'class': 'form-control cc-ed-url',
            placeholder: this.t('imageUrl'), value: cell.imageurl
        });
        url.addEventListener('input', function() {
            cell.imageurl = url.value;
            self.serialize();
        });

        // Üleslaaditud pildi valik (rippmenüü + eelvaade + värskendus).
        var imageSelect = el('select', {'class': 'form-control cc-ed-imgsel'});
        var imagePreview = el('img', {'class': 'cc-ed-imgpreview', alt: ''});
        var refreshBtn = el('button', {
            type: 'button', 'class': 'btn btn-secondary btn-sm cc-ed-imgrefresh',
            title: this.t('refreshImages'), 'aria-label': this.t('refreshImages')
        }, '\u21bb');
        this.imageSelects.push({select: imageSelect, cell: cell, preview: imagePreview});
        this.populateImageSelect(imageSelect, cell);
        this.updateImagePreview(imagePreview, cell);
        imageSelect.addEventListener('change', function() {
            cell.image = imageSelect.value;
            self.updateImagePreview(imagePreview, cell);
            self.serialize();
        });
        refreshBtn.addEventListener('click', function() {
            self.loadImageFiles();
        });
        var imageRow = el('div', {'class': 'cc-ed-imgrow'}, [imageSelect, refreshBtn, imagePreview]);

        var position = this.select(['above', 'left'],
            [this.t('imageAbove'), this.t('imageLeft')], cell.imageposition, function(v) {
                cell.imageposition = v;
                alignHalf.style.display = (v === 'above') ? '' : 'none';
                self.serialize();
            });

        var size = this.select(['small', 'medium', 'large'],
            [this.t('sizeSmall'), this.t('sizeMedium'), this.t('sizeLarge')], cell.imageleftsize, function(v) {
                cell.imageleftsize = v;
                self.serialize();
            });

        // Joondus (ainult teksti kohal oleva pildi jaoks; pealkirjata).
        var align = this.select(['left', 'center'],
            [this.t('imageAlignLeft'), this.t('imageAlignCenter')],
            cell.imagealign === 'left' ? 'left' : 'center', function(v) {
                cell.imagealign = v;
                self.serialize();
            });
        var alignHalf = el('div', {'class': 'cc-ed-half'}, [align]);
        alignHalf.style.display = (cell.imageposition === 'above') ? '' : 'none';

        var alt = el('input', {
            type: 'text', 'class': 'form-control cc-ed-alt',
            placeholder: this.t('alt'), value: cell.alt
        });
        alt.addEventListener('input', function() {
            cell.alt = alt.value;
            self.serialize();
        });

        var fields = el('div', {'class': 'cc-ed-fields'}, [
            el('label', {'class': 'cc-ed-flabel', text: this.t('cellText')}), text,
            el('label', {'class': 'cc-ed-flabel', text: this.t('uploadedImage')}), imageRow,
            el('label', {'class': 'cc-ed-flabel', text: this.t('imageUrl')}), url,
            el('div', {'class': 'cc-ed-row'}, [
                el('div', {'class': 'cc-ed-half'},
                    [el('label', {'class': 'cc-ed-flabel', text: this.t('imagePosition')}), position]),
                alignHalf,
                el('div', {'class': 'cc-ed-half'},
                    [el('label', {'class': 'cc-ed-flabel', text: this.t('imageLeftSize')}), size])
            ]),
            el('label', {'class': 'cc-ed-flabel', text: this.t('alt')}), alt
        ]);

        // Ühenduste valik (ainult 2.–4. tulbas, viitab eelmisele tulbale).
        var connections = el('div', {'class': 'cc-ed-conn'});
        if (c >= 1) {
            this.fillConnections(connections, c, r);
        }

        // Tööriistariba.
        var tools = el('div', {'class': 'cc-ed-tools'}, [
            this.toolButton('▲', this.t('moveUp'), function() {
                self.moveCell(c, r, -1);
            }),
            this.toolButton('▼', this.t('moveDown'), function() {
                self.moveCell(c, r, 1);
            }),
            this.toolButton('✕', this.t('removeCell'), function() {
                self.removeCell(c, r);
            })
        ]);

        var head = el('div', {'class': 'cc-ed-cellhead'}, [
            el('span', {'class': 'cc-ed-cellnum', text: (r + 1) + '.'}),
            tools
        ]);

        return el('div', {'class': 'cc-ed-cell', 'data-col': c, 'data-row': r},
            [head, fields, connections]);
    };

    /**
     * Täida ühenduste valiku plokk märkeruutudega eelmise tulba lahtrite jaoks.
     *
     * @param {Element} container
     * @param {Number} c
     * @param {Number} r
     */
    Editor.prototype.fillConnections = function(container, c, r) {
        var self = this;
        var cell = this.columns[c].cells[r];
        var prevCells = this.columns[c - 1] ? this.columns[c - 1].cells : [];

        container.innerHTML = '';
        container.appendChild(el('div', {'class': 'cc-ed-conn-label',
            text: this.t('correctConnections')}));

        if (!prevCells.length) {
            container.appendChild(el('div', {'class': 'cc-ed-conn-empty',
                text: this.t('noNeighbourCells')}));
            return;
        }

        var list = el('div', {'class': 'cc-ed-conn-list'});
        prevCells.forEach(function(pcell, pidx) {
            var checked = cell.prev.indexOf(pidx) !== -1;
            var box = el('input', {type: 'checkbox'});
            box.checked = checked;
            box.value = pidx;
            box.addEventListener('change', function() {
                var i = cell.prev.indexOf(pidx);
                if (box.checked && i === -1) {
                    cell.prev.push(pidx);
                } else if (!box.checked && i !== -1) {
                    cell.prev.splice(i, 1);
                }
                self.serialize();
            });
            var label = el('label', {'class': 'cc-ed-conn-item'},
                [box, el('span', {text: self.cellLabel(pcell, pidx)})]);
            list.appendChild(label);
        });
        container.appendChild(list);
    };

    /**
     * Uuenda antud tulba kõigi lahtrite ühendusplokid (nt kui eelmise tulba
     * tekst muutus).
     *
     * @param {Number} c
     */
    Editor.prototype.refreshConnectionsForColumn = function(c) {
        if (c < 1 || c >= this.getNumColumns()) {
            return;
        }
        var self = this;
        var cards = this.root.querySelectorAll('.cc-ed-cell[data-col="' + c + '"]');
        cards.forEach(function(card) {
            var r = parseInt(card.getAttribute('data-row'), 10);
            var conn = card.querySelector('.cc-ed-conn');
            if (conn) {
                self.fillConnections(conn, c, r);
            }
        });
    };

    /**
     * Lahtri silt: "(nr). tekst" nagu H5P-s.
     *
     * @param {Object} cell
     * @param {Number} idx
     * @return {String}
     */
    Editor.prototype.cellLabel = function(cell, idx) {
        var content = stripTags(cell.text) || (cell.alt || '').trim();
        return (idx + 1) + '. ' + (content || this.t('cellFallback'));
    };

    /**
     * Rikastekstitoimetaja lahtri sisu jaoks (H5P html-vidiku vaste).
     * Iseseisev kontroll: nupurida + contenteditable ala, salvestab HTML-i.
     *
     * @param {String} html algne HTML
     * @param {Function} onchange
     * @return {Object} {wrapper, area}
     */
    Editor.prototype.makeRichText = function(html, onchange) {
        var s = this.s;
        var area = el('div', {
            'class': 'form-control cc-ed-rte-area',
            contenteditable: 'true',
            role: 'textbox',
            'aria-multiline': 'true'
        });
        area.innerHTML = html || '';

        var commit = function() {
            onchange(area.innerHTML);
        };
        area.addEventListener('input', commit);
        area.addEventListener('blur', commit);

        var exec = function(command, value) {
            area.focus();
            try {
                document.execCommand(command, false, value || null);
            } catch (e) {
                return;
            }
            commit();
        };

        var mkbtn = function(label, title, handler) {
            var b = el('button', {
                type: 'button', 'class': 'btn btn-light btn-sm cc-ed-rte-btn',
                title: title, 'aria-label': title
            }, label);
            b.addEventListener('mousedown', function(e) {
                // Väldi fookuse kadu enne käsu täitmist.
                e.preventDefault();
            });
            b.addEventListener('click', handler);
            return b;
        };

        var toolbar = el('div', {'class': 'cc-ed-rte-toolbar'}, [
            mkbtn('B', s.rteBold || 'Bold', function() {
                exec('bold');
            }),
            mkbtn('I', s.rteItalic || 'Italic', function() {
                exec('italic');
            }),
            mkbtn('U', s.rteUnderline || 'Underline', function() {
                exec('underline');
            }),
            mkbtn('•', s.rteBullet || 'Bulleted list', function() {
                exec('insertUnorderedList');
            }),
            mkbtn('1.', s.rteNumbered || 'Numbered list', function() {
                exec('insertOrderedList');
            }),
            mkbtn('\u2630\u2190', s.rteAlignLeft || 'Align left', function() {
                exec('justifyLeft');
            }),
            mkbtn('\u2630', s.rteAlignCenter || 'Align center', function() {
                exec('justifyCenter');
            }),
            mkbtn('\u2192\u2630', s.rteAlignRight || 'Align right', function() {
                exec('justifyRight');
            }),
            mkbtn('🔗', s.rteLink || 'Insert link', function() {
                var href = window.prompt(s.rteLinkPrompt || 'Link URL:', 'https://');
                if (href) {
                    exec('createLink', href);
                }
            }),
            mkbtn('⌫', s.rteClear || 'Clear formatting', function() {
                exec('removeFormat');
            })
        ]);

        var wrapper = el('div', {'class': 'cc-ed-rte'}, [toolbar, area]);
        return {wrapper: wrapper, area: area};
    };

    /**
     * Täida üks pildivaliku rippmenüü üleslaaditud failinimedega.
     *
     * @param {Element} select
     * @param {Object} cell
     */
    Editor.prototype.populateImageSelect = function(select, cell) {
        var current = cell.image || '';
        select.innerHTML = '';
        var none = el('option', {value: ''}, this.t('noImage'));
        select.appendChild(none);
        var found = false;
        this.imageFiles.forEach(function(f) {
            var opt = el('option', {value: f.name}, f.name);
            if (f.name === current) {
                opt.setAttribute('selected', 'selected');
                found = true;
            }
            select.appendChild(opt);
        });
        // Kui salvestatud pilti nimekirjas veel pole, hoia see siiski valikus alles.
        if (current && !found) {
            var opt2 = el('option', {value: current}, current);
            opt2.setAttribute('selected', 'selected');
            select.appendChild(opt2);
        }
        select.value = current;
    };

    /**
     * Uuenda lahtri pildi eelvaadet.
     *
     * @param {Element} img
     * @param {Object} cell
     */
    Editor.prototype.updateImagePreview = function(img, cell) {
        var match = null;
        this.imageFiles.forEach(function(f) {
            if (f.name === cell.image) {
                match = f;
            }
        });
        if (cell.image && match && match.url) {
            img.setAttribute('src', match.url);
            img.style.display = '';
        } else {
            img.removeAttribute('src');
            img.style.display = 'none';
        }
    };

    /**
     * Täida kõik pildivalikud uuesti pärast failinimekirja laadimist.
     */
    Editor.prototype.populateImageSelects = function() {
        var self = this;
        this.imageSelects.forEach(function(entry) {
            self.populateImageSelect(entry.select, entry.cell);
            self.updateImagePreview(entry.preview, entry.cell);
        });
    };

    /**
     * Loe üleslaaditud lahtripiltide nimekiri mustandialast (Moodle draftfiles AJAX).
     */
    Editor.prototype.loadImageFiles = function() {
        var self = this;
        var inputId = this.config.cellImagesInputId || 'id_cellimages';
        var input = document.getElementById(inputId);
        var itemid = input ? input.value : null;
        if (!itemid || typeof window === 'undefined' || !window.M || !window.M.cfg
                || !window.fetch) {
            return;
        }
        var cfg = window.M.cfg;
        var body = 'sesskey=' + encodeURIComponent(cfg.sesskey)
            + '&client_id=cc&itemid=' + encodeURIComponent(itemid)
            + '&filepath=%2F&draftpath=%2F';
        window.fetch(cfg.wwwroot + '/repository/draftfiles_ajax.php?action=list', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body
        }).then(function(r) {
            return r.json();
        }).then(function(data) {
            var list = (data && data.list) || [];
            self.imageFiles = list.filter(function(f) {
                return f && !f.children && (f.filename || f.fullname);
            }).map(function(f) {
                return {name: f.filename || f.fullname, url: f.url || f.thumbnail || ''};
            });
            self.populateImageSelects();
        }).catch(function() {
            // Vaikselt: kui nimekirja ei saa, jäävad väljad URL-i ja käsitsi valiku peale.
        });
    };

    /**
     * Loo select-element.
     *
     * @param {Array} values
     * @param {Array} labels
     * @param {String} current
     * @param {Function} onchange
     * @return {Element}
     */
    Editor.prototype.select = function(values, labels, current, onchange) {
        var sel = el('select', {'class': 'form-control cc-ed-select'});
        values.forEach(function(v, i) {
            var opt = el('option', {value: v, text: labels[i]});
            if (v === current) {
                opt.selected = true;
            }
            sel.appendChild(opt);
        });
        sel.addEventListener('change', function() {
            onchange(sel.value);
        });
        return sel;
    };

    /**
     * Väike tööriistanupp.
     *
     * @param {String} glyph
     * @param {String} title
     * @param {Function} onclick
     * @return {Element}
     */
    Editor.prototype.toolButton = function(glyph, title, onclick) {
        var b = el('button', {
            type: 'button', 'class': 'btn btn-link btn-sm cc-ed-tool',
            title: title, 'aria-label': title
        }, glyph);
        b.addEventListener('click', onclick);
        return b;
    };

    /**
     * Nihuta lahtrit üles/alla ja teisenda ühendusviited.
     *
     * @param {Number} c
     * @param {Number} r
     * @param {Number} delta
     */
    Editor.prototype.moveCell = function(c, r, delta) {
        var cells = this.columns[c].cells;
        var target = r + delta;
        if (target < 0 || target >= cells.length) {
            return;
        }
        var tmp = cells[r];
        cells[r] = cells[target];
        cells[target] = tmp;

        // Järgmise tulba viited sellele tulbale tuleb r <-> target vahetada.
        if (c + 1 < this.columns.length) {
            this.columns[c + 1].cells.forEach(function(cell) {
                cell.prev = cell.prev.map(function(p) {
                    if (p === r) {
                        return target;
                    }
                    if (p === target) {
                        return r;
                    }
                    return p;
                });
            });
        }
        this.render();
        this.serialize();
    };

    /**
     * Eemalda lahter ja korrasta viited.
     *
     * @param {Number} c
     * @param {Number} r
     */
    Editor.prototype.removeCell = function(c, r) {
        var cells = this.columns[c].cells;
        if (cells.length <= 1) {
            return;
        }
        cells.splice(r, 1);

        // Järgmise tulba viited: eemalda r, nihuta suuremad ühe võrra alla.
        if (c + 1 < this.columns.length) {
            this.columns[c + 1].cells.forEach(function(cell) {
                cell.prev = cell.prev.filter(function(p) {
                    return p !== r;
                }).map(function(p) {
                    return p > r ? p - 1 : p;
                });
            });
        }
        this.render();
        this.serialize();
    };

    /**
     * Eemalda ühendusviited, mis jäävad väljapoole olemasolevaid lahtreid.
     */
    Editor.prototype.pruneConnections = function() {
        var self = this;
        this.columns.forEach(function(col, ci) {
            col.cells.forEach(function(cell) {
                if (ci === 0) {
                    cell.prev = [];
                    return;
                }
                var prevCount = self.columns[ci - 1].cells.length;
                cell.prev = cell.prev.filter(function(p) {
                    return p >= 0 && p < prevCount;
                });
            });
        });
    };

    /**
     * Kirjuta kanooniline mudel peidetud väljale.
     */
    Editor.prototype.serialize = function() {
        var n = this.getNumColumns();
        var columns = [];
        var answerkey = [];

        for (var c = 0; c < n; c++) {
            var col = this.columns[c] || {title: '', cells: []};
            var cells = col.cells.map(function(cell) {
                return {
                    text: cell.text || '',
                    image: cell.image || '',
                    imageurl: cell.imageurl || '',
                    imageposition: cell.imageposition === 'left' ? 'left' : 'above',
                    imageleftsize: (cell.imageleftsize === 'medium' || cell.imageleftsize === 'large') ? cell.imageleftsize : 'small',
                        imagealign: (cell.imagealign === 'left') ? 'left' : 'center',
                    alt: cell.alt || ''
                };
            });
            columns.push({title: col.title || '', cells: cells});
        }

        var seen = {};
        for (var k = 1; k < n; k++) {
            this.columns[k].cells.forEach(function(cell, r) {
                (cell.prev || []).forEach(function(p) {
                    if (p < 0 || p >= (this.columns[k - 1] ? this.columns[k - 1].cells.length : 0)) {
                        return;
                    }
                    var key = (k - 1) + ':' + p + '-' + k + ':' + r;
                    if (seen[key]) {
                        return;
                    }
                    seen[key] = true;
                    answerkey.push({
                        from: {col: k - 1, row: p},
                        to: {col: k, row: r}
                    });
                }.bind(this));
            }.bind(this));
        }

        this.input.value = JSON.stringify({columns: columns, answerkey: answerkey});
    };

    return {
        /**
         * Käivita toimetaja.
         *
         * @param {Object} config
         */
        init: function(config) {
            var attempts = 0;
            var start = function() {
                var root = document.querySelector(config.rootSelector);
                if (!root) {
                    if (attempts++ < 50) {
                        window.setTimeout(start, 100);
                    }
                    return;
                }
                new Editor(config);
            };
            if (document.readyState !== 'loading') {
                start();
            } else {
                document.addEventListener('DOMContentLoaded', start);
            }
        }
    };
});
