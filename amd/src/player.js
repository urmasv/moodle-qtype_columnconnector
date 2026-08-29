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
 * columnconnector interaktiivne mängija: lahtrite ühendamine joontega.
 *
 * @module     qtype_columnconnector/player
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    var NS = 'http://www.w3.org/2000/svg';

    /**
     * Positsiooni võti.
     *
     * @param {Object} pos
     * @return {String}
     */
    function posKey(pos) {
        return pos.col + ':' + pos.row;
    }

    /**
     * Suunast sõltumatu ühenduse võti.
     *
     * @param {Object} conn
     * @return {String}
     */
    function connKey(conn) {
        var a = conn.from;
        var b = conn.to;
        if (b.col < a.col || (b.col === a.col && b.row < a.row)) {
            a = conn.to;
            b = conn.from;
        }
        return a.col + ':' + a.row + '-' + b.col + ':' + b.row;
    }

    /**
     * Normaliseeri ühendus nii, et from on väiksem positsioon.
     *
     * @param {Object} from
     * @param {Object} to
     * @return {Object}
     */
    function makeConnection(from, to) {
        if (to.col < from.col || (to.col === from.col && to.row < from.row)) {
            return {from: {col: to.col, row: to.row}, to: {col: from.col, row: from.row}};
        }
        return {from: {col: from.col, row: from.row}, to: {col: to.col, row: to.row}};
    }

    /**
     * Mängija konstruktor.
     *
     * @param {Object} config
     */
    function Player(config) {
        this.config = config;
        this.container = document.getElementById(config.containerId);
        if (!this.container) {
            return;
        }
        this.input = document.querySelector('[name="' + config.inputName + '"]');
        this.svg = this.container.querySelector('.cc-lines');
        this.stage = this.container.querySelector('.cc-stage');
        this.status = this.container.querySelector('.cc-sr-status');
        this.layoutMode = config.layoutMode;
        this.lineStyle = config.lineStyle;
        this.readonly = config.readonly || config.reveal;
        this.active = null;

        this.connections = [];
        this.seen = {};
        (config.connections || []).forEach(function(conn) {
            this.addConnection(conn.from, conn.to, true);
        }.bind(this));

        this.correctMap = {};
        (config.answerKey || []).forEach(function(conn) {
            this.correctMap[connKey(makeConnection(conn.from, conn.to))] = true;
        }.bind(this));

        this.bind();
        this.redraw();
    }

    /**
     * Lisa ühendus (kui pole duplikaat ja on naabertulbad).
     *
     * @param {Object} from
     * @param {Object} to
     * @param {Boolean} silent
     * @return {Boolean}
     */
    Player.prototype.addConnection = function(from, to, silent) {
        if (Math.abs(from.col - to.col) !== 1) {
            if (!silent) {
                this.announce(this.config.strings.invalidTarget);
            }
            return false;
        }
        var conn = makeConnection(from, to);
        var key = connKey(conn);
        if (this.seen[key]) {
            if (!silent) {
                this.announce(this.config.strings.duplicate);
            }
            return false;
        }
        this.seen[key] = true;
        this.connections.push(conn);
        if (!silent) {
            this.sync();
            this.redraw();
        }
        return true;
    };

    /**
     * Eemalda ühendus võtme järgi.
     *
     * @param {String} key
     */
    Player.prototype.removeConnection = function(key) {
        this.connections = this.connections.filter(function(conn) {
            return connKey(conn) !== key;
        });
        delete this.seen[key];
        this.sync();
        this.redraw();
    };

    /**
     * Seo sündmused.
     */
    Player.prototype.bind = function() {
        var self = this;

        if (!this.readonly) {
            this.container.querySelectorAll('.cc-cell').forEach(function(cell) {
                cell.addEventListener('click', function() {
                    self.onCellClick(cell);
                });
            });

            var clear = this.container.querySelector('.cc-clear');
            if (clear) {
                clear.addEventListener('click', function() {
                    self.connections = [];
                    self.seen = {};
                    self.active = null;
                    self.clearSelection();
                    self.sync();
                    self.redraw();
                });
            }
        }

        window.addEventListener('resize', function() {
            self.redraw();
        });

        // Jälgi tööala laiust: Moodle'i kursusemenüü/plokisahtli klappimine muudab
        // laiust CSS-paigutusega (ilma window-resize'ita), joonte asukoht peab kaasa liikuma.
        if (typeof ResizeObserver !== 'undefined' && this.stage) {
            var observer = new ResizeObserver(function() {
                if (self.resizeScheduled) {
                    return;
                }
                self.resizeScheduled = true;
                var reflow = function() {
                    self.resizeScheduled = false;
                    self.redraw();
                };
                if (window.requestAnimationFrame) {
                    window.requestAnimationFrame(reflow);
                } else {
                    reflow();
                }
            });
            observer.observe(this.stage);
            this.resizeObserver = observer;
        }

        // Eelvaatejoon valitud lahtrist kursorini (nagu H5P-s).
        if (!this.readonly && this.stage) {
            this.stage.addEventListener('mousemove', function(e) {
                if (!self.active) {
                    return;
                }
                var rect = self.stage.getBoundingClientRect();
                self.previewPoint = {x: e.clientX - rect.left, y: e.clientY - rect.top};
                self.redraw();
            });
        }
    };

    /**
     * Lahtri klikk: vali või ühenda.
     *
     * @param {Element} cell
     */
    Player.prototype.onCellClick = function(cell) {
        var pos = {col: parseInt(cell.getAttribute('data-col'), 10),
                   row: parseInt(cell.getAttribute('data-row'), 10)};

        if (!this.active) {
            this.select(pos);
            return;
        }

        if (this.active.col === pos.col && this.active.row === pos.row) {
            this.active = null;
            this.clearSelection();
            this.redraw();
            return;
        }

        if (Math.abs(this.active.col - pos.col) !== 1) {
            this.announce(this.config.strings.invalidTarget);
            return;
        }

        this.addConnection(this.active, pos);
        this.active = null;
        this.clearSelection();
        this.redraw();
    };

    /**
     * Vali lahter ja tõsta esile kehtivad sihtmärgid.
     *
     * @param {Object} pos
     */
    Player.prototype.select = function(pos) {
        this.active = pos;
        this.clearSelection();
        var cells = this.container.querySelectorAll('.cc-cell');
        cells.forEach(function(cell) {
            var c = parseInt(cell.getAttribute('data-col'), 10);
            var r = parseInt(cell.getAttribute('data-row'), 10);
            if (c === pos.col && r === pos.row) {
                cell.classList.add('cc-cell-selected');
                cell.setAttribute('aria-pressed', 'true');
            } else if (Math.abs(c - pos.col) === 1) {
                cell.classList.add('cc-cell-valid-target');
            }
        });
        this.announce(this.config.strings.selected);
    };

    /**
     * Eemalda kõik valikuklassid.
     */
    Player.prototype.clearSelection = function() {
        this.previewPoint = null;
        this.container.querySelectorAll('.cc-cell').forEach(function(cell) {
            cell.classList.remove('cc-cell-selected', 'cc-cell-valid-target');
            cell.setAttribute('aria-pressed', 'false');
        });
    };

    /**
     * Kirjuta praegused ühendused peidetud väljale.
     */
    Player.prototype.sync = function() {
        if (this.input) {
            this.input.value = JSON.stringify(this.connections);
        }
    };

    /**
     * Joonista kõik jooned uuesti.
     */
    Player.prototype.redraw = function() {
        if (!this.svg || !this.stage) {
            return;
        }
        while (this.svg.firstChild) {
            this.svg.removeChild(this.svg.firstChild);
        }

        var self = this;
        var reveal = this.config.reveal;

        this.connections.forEach(function(conn) {
            var cls = 'cc-line';
            var status = null;
            if (reveal) {
                status = self.correctMap[connKey(conn)] ? 'correct' : 'incorrect';
                cls += ' cc-line-' + status;
            }
            self.drawPath(conn, cls, !reveal);
            if (status) {
                self.drawFeedbackIcon(conn, status);
            }
        });

        // Paljasta puuduvad õiged ühendused.
        if (this.config.showKey) {
            (this.config.answerKey || []).forEach(function(item) {
                var conn = makeConnection(item.from, item.to);
                if (!self.seen[connKey(conn)]) {
                    self.drawPath(conn, 'cc-line cc-line-missing', false);
                }
            });
        }

        // Eelvaatejoon valitud lahtrist kursorini.
        if (!this.readonly && this.active && this.previewPoint) {
            var start = this.edgePointTowardPoint(this.active, this.previewPoint);
            if (start) {
                var path = document.createElementNS(NS, 'path');
                path.setAttribute('d', this.pathDefinition(start, this.previewPoint));
                path.setAttribute('class', 'cc-line-preview');
                this.svg.appendChild(path);
            }
        }
    };

    /**
     * Lahtri serva ankurpunkt antud koordinaadi suunas.
     *
     * @param {Object} pos
     * @param {Object} point
     * @return {Object|null}
     */
    Player.prototype.edgePointTowardPoint = function(pos, point) {
        var cell = this.findCell(pos);
        if (!cell) {
            return null;
        }
        var rect = cell.getBoundingClientRect();
        var stageRect = this.stage.getBoundingClientRect();
        var centerX = rect.left + rect.width / 2 - stageRect.left;
        var centerY = rect.top + rect.height / 2 - stageRect.top;
        if (this.layoutMode === 'rows') {
            return {x: centerX, y: point.y < centerY ? rect.top - stageRect.top : rect.bottom - stageRect.top};
        }
        return {x: point.x < centerX ? rect.left - stageRect.left : rect.right - stageRect.left, y: centerY};
    };

    /**
     * Joonista üks joon.
     *
     * @param {Object} conn
     * @param {String} className
     * @param {Boolean} clickable
     */
    Player.prototype.drawPath = function(conn, className, clickable) {
        var start = this.edgePoint(conn.from, conn.to);
        var end = this.edgePoint(conn.to, conn.from);
        if (!start || !end) {
            return;
        }
        var path = document.createElementNS(NS, 'path');
        path.setAttribute('d', this.pathDefinition(start, end));
        path.setAttribute('class', className);

        if (clickable && !this.readonly) {
            var self = this;
            var key = connKey(conn);
            path.style.pointerEvents = 'stroke';
            path.style.cursor = 'pointer';
            path.addEventListener('click', function(e) {
                e.preventDefault();
                self.removeConnection(key);
            });
        }
        this.svg.appendChild(path);
    };

    /**
     * Joonista tagasiside-ikoon (✓ õige, × vale) joone keskele (nagu H5P-s).
     *
     * @param {Object} conn
     * @param {String} status 'correct' või 'incorrect'
     */
    Player.prototype.drawFeedbackIcon = function(conn, status) {
        var start = this.edgePoint(conn.from, conn.to);
        var end = this.edgePoint(conn.to, conn.from);
        if (!start || !end) {
            return;
        }
        var mid = this.pathMidpoint(start, end);
        var group = document.createElementNS(NS, 'g');
        group.setAttribute('class', 'cc-feedback-icon cc-feedback-icon-' + status);
        group.setAttribute('aria-hidden', 'true');
        group.setAttribute('transform', 'translate(' + mid.x + ' ' + mid.y + ')');

        var circle = document.createElementNS(NS, 'circle');
        circle.setAttribute('r', '11');
        circle.setAttribute('cx', '0');
        circle.setAttribute('cy', '0');

        var text = document.createElementNS(NS, 'text');
        text.setAttribute('x', '0');
        text.setAttribute('y', '0');
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('dominant-baseline', 'central');
        text.textContent = status === 'correct' ? '\u2713' : '\u00d7';

        group.appendChild(circle);
        group.appendChild(text);
        this.svg.appendChild(group);
    };

    /**
     * Joone keskpunkt (t=0.5) — sirge korral keskmine, kaardus korral Bézier' keskpunkt.
     *
     * @param {Object} start
     * @param {Object} end
     * @return {Object}
     */
    Player.prototype.pathMidpoint = function(start, end) {
        if (this.lineStyle === 'straight') {
            return {x: (start.x + end.x) / 2, y: (start.y + end.y) / 2};
        }
        var c1;
        var c2;
        if (this.layoutMode === 'rows') {
            var dy = Math.max(40, Math.abs(end.y - start.y) * 0.45);
            c1 = {x: start.x, y: start.y + (end.y > start.y ? dy : -dy)};
            c2 = {x: end.x, y: end.y + (end.y > start.y ? -dy : dy)};
        } else {
            var dx = Math.max(40, Math.abs(end.x - start.x) * 0.45);
            c1 = {x: start.x + (end.x > start.x ? dx : -dx), y: start.y};
            c2 = {x: end.x + (end.x > start.x ? -dx : dx), y: end.y};
        }
        // Kuupbézier' punkt t=0.5: (P0 + 3P1 + 3P2 + P3) / 8.
        return {
            x: (start.x + 3 * c1.x + 3 * c2.x + end.x) / 8,
            y: (start.y + 3 * c1.y + 3 * c2.y + end.y) / 8
        };
    };

    /**
     * Leia lahtri serva ankurpunkt teise lahtri suunas.
     *
     * @param {Object} pos
     * @param {Object} other
     * @return {Object|null}
     */
    Player.prototype.edgePoint = function(pos, other) {
        var cell = this.findCell(pos);
        var otherCell = this.findCell(other);
        if (!cell || !otherCell) {
            return null;
        }
        var rect = cell.getBoundingClientRect();
        var stageRect = this.stage.getBoundingClientRect();
        var otherRect = otherCell.getBoundingClientRect();
        var centerX = rect.left + rect.width / 2 - stageRect.left;
        var centerY = rect.top + rect.height / 2 - stageRect.top;
        var otherCenterX = otherRect.left + otherRect.width / 2 - stageRect.left;
        var otherCenterY = otherRect.top + otherRect.height / 2 - stageRect.top;

        if (this.layoutMode === 'rows') {
            return {
                x: centerX,
                y: otherCenterY < centerY ? rect.top - stageRect.top : rect.bottom - stageRect.top
            };
        }
        return {
            x: otherCenterX < centerX ? rect.left - stageRect.left : rect.right - stageRect.left,
            y: centerY
        };
    };

    /**
     * Joone d-atribuut (sirge või kaardus).
     *
     * @param {Object} start
     * @param {Object} end
     * @return {String}
     */
    Player.prototype.pathDefinition = function(start, end) {
        if (this.lineStyle === 'straight') {
            return 'M ' + start.x + ' ' + start.y + ' L ' + end.x + ' ' + end.y;
        }
        if (this.layoutMode === 'rows') {
            var dy = Math.max(40, Math.abs(end.y - start.y) * 0.45);
            return 'M ' + start.x + ' ' + start.y +
                ' C ' + start.x + ' ' + (start.y + (end.y > start.y ? dy : -dy)) +
                ' ' + end.x + ' ' + (end.y + (end.y > start.y ? -dy : dy)) +
                ' ' + end.x + ' ' + end.y;
        }
        var dx = Math.max(40, Math.abs(end.x - start.x) * 0.45);
        return 'M ' + start.x + ' ' + start.y +
            ' C ' + (start.x + (end.x > start.x ? dx : -dx)) + ' ' + start.y +
            ' ' + (end.x + (end.x > start.x ? -dx : dx)) + ' ' + end.y +
            ' ' + end.x + ' ' + end.y;
    };

    /**
     * @param {Object} pos
     * @return {Element|null}
     */
    Player.prototype.findCell = function(pos) {
        return this.container.querySelector(
            '.cc-cell[data-col="' + pos.col + '"][data-row="' + pos.row + '"]');
    };

    /**
     * Teata sõeluuringu lugejale.
     *
     * @param {String} message
     */
    Player.prototype.announce = function(message) {
        if (this.status && message) {
            this.status.textContent = message;
        }
    };

    return {
        /**
         * Käivita mängija.
         *
         * @param {Object} config
         */
        init: function(config) {
            // Oota, et paigutus oleks valmis, enne joonte joonistamist.
            var start = function() {
                new Player(config);
            };
            if (document.readyState === 'complete') {
                start();
            } else {
                window.addEventListener('load', start);
            }
        }
    };
});
