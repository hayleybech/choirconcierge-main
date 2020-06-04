/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/risers/risers.ts":
/*!**********************************************!*\
  !*** ./resources/assets/js/risers/risers.ts ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __classPrivateFieldSet = (this && this.__classPrivateFieldSet) || function (receiver, privateMap, value) {
    if (!privateMap.has(receiver)) {
        throw new TypeError("attempted to set private field on non-instance");
    }
    privateMap.set(receiver, value);
    return value;
};
var __classPrivateFieldGet = (this && this.__classPrivateFieldGet) || function (receiver, privateMap) {
    if (!privateMap.has(receiver)) {
        throw new TypeError("attempted to get private field on non-instance");
    }
    return privateMap.get(receiver);
};
var _snap, _canvas_height, _canvas_width, _origin, _origin_modifier, _total_width_deg, _num_rows, _num_cols, _risers_start_radius, _risers_end_radius, _risers_start_angle, _row_height_radius, _col_width_deg, _style, _edges;
Object.defineProperty(exports, "__esModule", { value: true });
class Risers {
    constructor(selector, height, width) {
        _snap.set(this, void 0);
        // The full canvas size - the risers themselves only use part of the canvas
        _canvas_height.set(this, 500);
        _canvas_width.set(this, 1000);
        // The origin point for the concentric arcs.
        _origin.set(this, {
            x: null,
            y: null
        });
        // Given the canvas size, we'll calculate the origin using the modifier
        _origin_modifier.set(this, {
            x: 0.5,
            y: 1.55
        });
        // Riser sizing
        _total_width_deg.set(this, 70); // Total arc width in degrees
        _num_rows.set(this, 4); // How many rows? We'll draw (num_rows + 1) arcs vertically.
        _num_cols.set(this, 3); // How many rows? We'll draw (n_cols) arcs horizontally.
        _risers_start_radius.set(this, void 0);
        _risers_end_radius.set(this, void 0);
        _risers_start_angle.set(this, void 0);
        _row_height_radius.set(this, void 0);
        _col_width_deg.set(this, void 0);
        // Line styles
        _style.set(this, {
            fill: 'none',
            stroke: 'black',
            strokeWidth: 1
        });
        // The vertical lines
        _edges.set(this, void 0);
        __classPrivateFieldSet(this, _snap, Snap(selector));
        __classPrivateFieldSet(this, _canvas_height, height);
        __classPrivateFieldSet(this, _canvas_width, width);
        __classPrivateFieldSet(this, _origin, {
            x: __classPrivateFieldGet(this, _canvas_width) * __classPrivateFieldGet(this, _origin_modifier).x,
            y: __classPrivateFieldGet(this, _canvas_height) * __classPrivateFieldGet(this, _origin_modifier).y
        });
        __classPrivateFieldSet(this, _edges, new Array(__classPrivateFieldGet(this, _num_cols) + 1).fill(null).map(() => ({
            start: null,
            end: null
        })));
        this.calcSizes();
    }
    draw() {
        for (let col = 0; col < __classPrivateFieldGet(this, _num_cols); col++) {
            for (let row = 0; row <= __classPrivateFieldGet(this, _num_rows); row++) {
                const arc = this.createArc(row, col);
                const arcPath = __classPrivateFieldGet(this, _snap).path(arc.toString());
                arcPath.attr(__classPrivateFieldGet(this, _style));
                this.calcCellEdges(row, col, arc);
            }
        }
        this.drawEdges();
    }
    drawEdges() {
        for (let i = 0; i <= __classPrivateFieldGet(this, _num_cols); i++) {
            const edge = __classPrivateFieldGet(this, _edges)[i];
            const line = __classPrivateFieldGet(this, _snap).line(edge.start.x, edge.start.y, edge.end.x, edge.end.y);
            line.attr(__classPrivateFieldGet(this, _style));
        }
    }
    createArc(row, col) {
        // Start point (radius) for current row's arcs
        const row_start_radius = __classPrivateFieldGet(this, _risers_start_radius) + (row * __classPrivateFieldGet(this, _row_height_radius));
        // Start angle for current arc
        const col_start_angle = __classPrivateFieldGet(this, _risers_start_angle) + (__classPrivateFieldGet(this, _col_width_deg) * col);
        const col_end_angle = col_start_angle + __classPrivateFieldGet(this, _col_width_deg);
        // Create the arc
        return new Arc(__classPrivateFieldGet(this, _origin).x, __classPrivateFieldGet(this, _origin).y, row_start_radius, col_start_angle, col_end_angle);
    }
    calcSizes() {
        // Start and radius for the rows
        __classPrivateFieldSet(this, _risers_start_radius, __classPrivateFieldGet(this, _canvas_height) * 1.05);
        __classPrivateFieldSet(this, _risers_end_radius, __classPrivateFieldGet(this, _canvas_height) * 0.4);
        // Distance (radius) between rows
        __classPrivateFieldSet(this, _row_height_radius, __classPrivateFieldGet(this, _risers_end_radius) / __classPrivateFieldGet(this, _num_rows));
        // Start angle for cols
        __classPrivateFieldSet(this, _risers_start_angle, 0 - (__classPrivateFieldGet(this, _total_width_deg) / 2));
        __classPrivateFieldSet(this, _col_width_deg, __classPrivateFieldGet(this, _total_width_deg) / __classPrivateFieldGet(this, _num_cols));
    }
    calcCellEdges(row, col, arc) {
        const arc_l = arc.getCartesian().end;
        const arc_r = arc.getCartesian().start;
        // Save left edges
        if (row === 0) {
            __classPrivateFieldGet(this, _edges)[col].start = arc_l;
        }
        else if (row === __classPrivateFieldGet(this, _num_rows)) {
            __classPrivateFieldGet(this, _edges)[col].end = arc_l;
        }
        // Save far right edge
        if (col === (__classPrivateFieldGet(this, _num_cols) - 1)) {
            if (row === 0) {
                __classPrivateFieldGet(this, _edges)[col + 1].start = arc_r;
            }
            else if (row === __classPrivateFieldGet(this, _num_rows)) {
                __classPrivateFieldGet(this, _edges)[col + 1].end = arc_r;
            }
        }
    }
}
exports.RiserFrame = Risers;
_snap = new WeakMap(), _canvas_height = new WeakMap(), _canvas_width = new WeakMap(), _origin = new WeakMap(), _origin_modifier = new WeakMap(), _total_width_deg = new WeakMap(), _num_rows = new WeakMap(), _num_cols = new WeakMap(), _risers_start_radius = new WeakMap(), _risers_end_radius = new WeakMap(), _risers_start_angle = new WeakMap(), _row_height_radius = new WeakMap(), _col_width_deg = new WeakMap(), _style = new WeakMap(), _edges = new WeakMap();


/***/ }),

/***/ 2:
/*!****************************************************!*\
  !*** multi ./resources/assets/js/risers/risers.ts ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/vagrant/choirconcierge/resources/assets/js/risers/risers.ts */"./resources/assets/js/risers/risers.ts");


/***/ })

/******/ });