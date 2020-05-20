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

/***/ "./app/Modules/Shop/js/cart.js":
/*!*************************************!*\
  !*** ./app/Modules/Shop/js/cart.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

document.addEventListener('DOMContentLoaded', function () {
  // id модального окна
  var modalID = 'cart_modal'; // Проверяем подключен ли jQuery

  if (window.jQuery) {
    // Функция показа корзины, принимает содержимое корзины, в ответе на ajax
    var showCart = function showCart(cart, modalID) {
      var modal = document.getElementById(modalID),
          modalInstance = new Bootstrap.Modal(modal); // Вставим в модальное окно содержимое корзины

      $('#' + modalID + ' .modal-body').html(cart); // Открыть модальное окно

      modalInstance.show();
      var cartQty = $('#cart_modal_qty').text(),
          cartSum = $('#cart_modal_sum').text(); // Вставляем кол-во из корзины в кнопку вызова
      //$('.cart_count_qty').text(cartQty)
      // Вставляем сумму из корзины в кнопку вызова

      $('.cart_count_sum').text(cartSum ? cartSum + ' ₽' : '');
    };

    // Если есть класс .no_js, то отключаем JS
    if (!$('div').hasClass('no_js')) {
      // Показать корзину по клику на .cart_show
      $('.cart_show').on('click', function (e) {
        e.preventDefault();
        $.ajax({
          type: 'GET',
          url: '/cart/show',
          success: function success(res) {
            showCart(res, modalID);
          },
          error: function error() {
            alert(translations['something_went_wrong']);
          }
        });
      }); // Добавить товар в корзину по клику на .cart_plus

      $(document).on('click', '.cart_plus', function (e) {
        e.preventDefault();
        var $this = $(this),
            id = $this.data('id');

        if (id) {
          $.ajax({
            type: 'GET',
            url: '/cart/' + id + '/plus',
            //data: {id: id},
            success: function success(res) {
              // Товар не найден
              if (!res) {
                alert(translations['something_went_wrong']);
              }

              showCart(res, modalID);
            },
            error: function error() {
              alert(translations['something_went_wrong']);
            }
          });
        }
      }); // Отминусовать товар из корзины по клику на .cart_minus

      $(document).on('click', '.cart_minus', function (e) {
        e.preventDefault();
        var $this = $(this),
            id = $this.data('id');

        if (id) {
          $.ajax({
            type: 'GET',
            url: '/cart/' + id + '/minus',
            //data: {id: id},
            success: function success(res) {
              // Товар не найден
              if (!res) {
                alert(translations['something_went_wrong']);
              }

              showCart(res, modalID);
            },
            error: function error() {
              alert(translations['something_went_wrong']);
            }
          });
        }
      }); // Удалить товар из корзину по клику на .cart_destroy

      $(document).on('click', '.cart_destroy', function (e) {
        e.preventDefault();
        var $this = $(this),
            id = $this.data('id');

        if (id) {
          $.ajax({
            type: 'GET',
            url: '/cart/' + id + '/destroy',
            success: function success(res) {
              // Товар не найден
              if (!res) {
                alert(translations['something_went_wrong']);
              }

              showCart(res, modalID);
            },
            error: function error() {
              alert(translations['something_went_wrong']);
            }
          });
        }
      });
    }
  }
}, false);

/***/ }),

/***/ "./app/Modules/Shop/js/filter.js":
/*!***************************************!*\
  !*** ./app/Modules/Shop/js/filter.js ***!
  \***************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _js_message__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../../js/message */ "./app/Modules/js/message.js");

document.addEventListener('DOMContentLoaded', function () {
  var filterClass = 'filter',
      productsClass = 'products';
  body.on('change', '.' + filterClass + ' input', function () {
    var checked = $('.' + filterClass + ' input:checked');
    var data = ''; // Получаем отмеченные input

    checked.each(function () {
      data += this.value + ',';
    });

    if (data) {
      $.ajax({
        url: location.href,
        type: 'GET',
        data: {
          filter: data
        },
        beforeSend: function beforeSend() {
          spinner.fadeIn(300, function () {
            $('.' + productsClass).hide();
          });
        },
        success: function success(res) {
          spinner.delay(500).fadeOut(300, function () {
            $('.' + productsClass).html(res).fadeIn(); // Работаем с адресной строкой объектом history

            var url = location.search.replace(/filter(.+?)(&|$)/g, ''),
                newUrl = location.pathname + url + (location.search ? '&' : '?') + 'filter=' + data; // Если будет дубль, то заменяем его

            newUrl = newUrl.replace('&&', '&');
            newUrl = newUrl.replace('?&', '?'); // Отправляем в url

            history.pushState({}, '', newUrl);
          });
        },
        error: function error() {
          _js_message__WEBPACK_IMPORTED_MODULE_0__["default"].error(translations['something_went_wrong']);
        }
      });
    } else {
      // Перезагрузим страницу
      window.location = location.pathname;
    }
  });
  body.on('click', '.' + filterClass + ' .reset', function () {
    window.location = location.pathname;
  });
}, false);

/***/ }),

/***/ "./app/Modules/Shop/js/index.js":
/*!**************************************!*\
  !*** ./app/Modules/Shop/js/index.js ***!
  \**************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _cart__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./cart */ "./app/Modules/Shop/js/cart.js");
/* harmony import */ var _cart__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_cart__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _filter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./filter */ "./app/Modules/Shop/js/filter.js");
/* harmony import */ var _script__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./script */ "./app/Modules/Shop/js/script.js");
/* harmony import */ var _script__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_script__WEBPACK_IMPORTED_MODULE_2__);




/***/ }),

/***/ "./app/Modules/Shop/js/script.js":
/*!***************************************!*\
  !*** ./app/Modules/Shop/js/script.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {



/***/ }),

/***/ "./app/Modules/js/message.js":
/*!***********************************!*\
  !*** ./app/Modules/js/message.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var timeout = 4000;

function alert(text) {
  var ms = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : timeout;
  var alertClass = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'danger';
  var getAlert = document.querySelector('#get-alert'),
      html = "\n        <div class=\"row alert-js\">\n            <div class=\"col-md-10 offset-md-1 anime-from-center\">\n                <div class=\"alert alert-".concat(alertClass, " py-4 px-5\" role=\"alert\">\n                    <span>").concat(text, "</span>\n                </div>\n            </div>\n        </div>");
  getAlert.innerHTML = html;
  setTimeout(function () {
    getAlert.innerHTML = '';
  }, ms);
}

function hideAlert() {
  var ms = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : timeout - 500;
  var alert = document.querySelector('.alert-js > div');

  if (alert) {
    setTimeout(function () {
      alert.classList.remove('anime-from-center');
      alert.classList.add('anime-to-center');
    }, ms);
  }
}

/* harmony default export */ __webpack_exports__["default"] = ({
  error: function error(text) {
    var ms = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var reload = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    ms = !ms ? timeout : ms;
    alert(text, ms);
    hideAlert();

    if (reload) {
      setTimeout(function () {
        location.reload();
      }, ms);
    }
  },
  success: function success(text) {
    var ms = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var reload = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    ms = !ms ? timeout : ms;
    alert(text, ms, 'success');
    hideAlert();

    if (reload) {
      setTimeout(function () {
        location.reload();
      }, ms);
    }
  },
  warning: function warning(text) {
    var ms = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var reload = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    ms = !ms ? timeout : ms;
    alert(text, ms, 'warning');
    hideAlert();

    if (reload) {
      setTimeout(function () {
        location.reload();
      }, ms);
    }
  },
  info: function info(text) {
    var ms = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var reload = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    ms = !ms ? timeout : ms;
    alert(text, ms, 'info');
    hideAlert();

    if (reload) {
      setTimeout(function () {
        location.reload();
      }, ms);
    }
  }
});

/***/ }),

/***/ 2:
/*!********************************************!*\
  !*** multi ./app/Modules/Shop/js/index.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/dimitriyyuliya/yandex.disk/laravel/app/Modules/Shop/js/index.js */"./app/Modules/Shop/js/index.js");


/***/ })

/******/ });