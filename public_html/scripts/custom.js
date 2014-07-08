/**********************************
/* JavaScript
/**********************************/

/**
* Date.now() in <IE9
*
* @return Date
*/
Date.now = Date.now || function() { return +new Date; };

/**
* Compares the values of two Arrays.
* Ordering does not matter.
*
* @param {Array}
* @param int undefined = equals, 1 = starts with, 2 = ends with
* @return Boolean True if both Arrays hold the same values
*/
function compareArrays(array1, array2, flag){
    if(array1.length != array2.length){
        return false;
    }

    var a = array1.sort(),
        b = array2.sort();

    for(var i = 0; array2[i]; i++){
        if(flag === undefined && a[i].match('^' + b[i] + '$') == null)
            return false;
        else if(flag == 1 && a[i].match('^' + b[i]) == null)
            return false;
        else if(flag == 2 && a[i].match(b[i] + '$') == null)
            return false;
    }
    return true;
}

/**
* Checks whether the second array holds
* all the values of the first array.
*
* @param {Array}
* @return Boolean True if Array 2 holds the same values as Array 1
*/
function arrayContainsArray(array1, array2){
    for(var i = 0; array1[i]; i++){
        if(array2.indexOf(array1[i]) == -1)
            return false;
    }
    return true;
}

/**
* Removes empty array values.
*
* @param {Array}
* @return {Array}
*/
function removeEmptyArrayValues(array){
    return $.grep(array, function(n){ return(n); });
}

/**
* Trims whitespace from the left and right
* of all array values.
* Indexes is not effected.
*
* @param {Array}
* @return {Array}
*/
function trimArrayWhiteSpace(array){
    $.each(array, function (id, value){
        array[id] = $.trim(value);
    });

    return array;
}

/**
* Remove duplicate array values
* Indexes are not effected.
*
* @param {Array}
* @return {Array}
*/
function uniqueArray(array){
    if($.isArray(array)){
        var dupes = {}; var len, i;

        for(i = 0, len = array.length; i < len; i++){
            var test = array[i].toString();
            if(dupes[test]){
                array.splice(i, 1);
                len--;
                i--;
            } else
                dupes[test] = true;
        }
    } else
        return(array);

    return(array);
}

/**
* Checks to see if an Object
* is a number.
*
* @param {Object} Object to check
* @return Boolean True if value is a number
*/
function isNumber(o){
    return ! isNaN (o-0);
}

/**
* Decodes an HTML String.
*
*
* @param {String}
* @return String
*/
function decode_html(str){
    str = str.replace(/&amp;/g, '&'); //must be first
    str = str.replace(/&gt;/g, '>');
    str = str.replace(/&lt;/g, '<');
    str = str.replace(/&quot;/g, '"');
    str = str.replace(/&#039;/g, "'");
    return str;
}

/**
* Encodes an HTML String.
*
*
* @param {String}
* @return String
*/
function encode_html(str){
    str = str.replace(/&/g, '%26'); //must be first
    str = str.replace(/#/g, '%23');
    str = str.replace(/\+/g, '%2B');
    str = str.replace(/>/g, '&gt;');
    str = str.replace(/</g, '&lt;');
    str = str.replace(/"/g, '&quot;');
    str = str.replace(/'/g, '&#039;');
    str = str.replace(/ /g, '+'); //must be last
    return str;
}

/**
* Removes HTML from a String.
*
*
* @param {String}
* @return String
*/
function removeHTML(value){
    return $.trim(value.replace(/(<([^>]+)>)/ig, ''));
}

/**
* Escapes a String ready for use
* with a Regular Expression
*
* @param {String}
* @return String
*/
function escapeRegexExpression(str){
    return str.replace(/[-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

/**
* Resets a form back to its original, populated values.
*
*
* @param {String}
*/
function resetForm(form){
    $(form).find('input:text, input:password, input:file, select, textarea').val('');
    $(form).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
}

/**
* Delays when a function runs.
*
* @return callback
*/
var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

/**
* Regex Selector
* All backslashes must be escaped/doubled.
* Case insensitive or remove 'i'.
*
* @param {Element, Attribute, Regular Expression}
* @return Element
*/
jQuery.expr[':'].regex = function(element, index, match){
    var matchParams = match[3].split(','),
    validLabels = /^(data|css):/,
    attr = {method: matchParams[0].match(validLabels) ? matchParams[0].split(':')[0] : 'attr',  property: matchParams.shift().replace(validLabels,'')},
    regexFlags = 'ig',
    regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g, ''), regexFlags);
    return regex.test(jQuery(element)[attr.method](attr.property));
}

/**
* Starts With Selector
* Use with .filter(':startsWith(text to search)')
* Case insensitive
*
* @param {Element, Index, Match}
* @return Element
*/
jQuery.expr[':'].startsWith = function(element, index, match) {
    return jQuery(element).text().toLowerCase().indexOf(match[3].toLowerCase()) === 0;
}

/**
* Allows you to save a value
* against an elements data storage.
*/
$.fn.saveValue = function(){
    return this.each(function(){
        var value = $(this).val();

        if($(this).hasAttr('id') && $(this).attr('id').indexOf('token-input-') != -1)
            value = $('input[name="' + $(this).attr('id').replace('token-input-', 'hidden-') + '"]').val();

        $(this).data('defaultText', $(this).val());
    });
};

/**
* Allows you to reset the default value
* for an individual input field.
*/
$.fn.resetValue = function(){
    return this.each(function(){
        $(this).val($(this).data('defaultText'));
    });
};

/**
* onFocus(), push blinker to the end
* of any text in the input or textarea.
*/
$.fn.focusAtEnd = function(){
    return this.each(function(){
        $(this).focus()
        if(this.setSelectionRange){
            var len = $(this).val().length * 2;
            this.setSelectionRange(len, len);
        } else {
            $(this).val($(this).val());
        }

        //this.scrollTop = 999999;
    });
}

/**
* Checks for the existence of attributes.
*/
$.fn.hasAttr = function(name){
   return this.attr(name) !== undefined;
}

/**
* Uses a CSS3 Transition - if supported - instead
* of jQuery's animate() for better performance.
*
* Relies on Modernizr
*/
$.fn.moveIt = function(pixels){
    var transition = 'margin-top 2s ease';

    $(this).bind('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function(){ /*...*/ }); //don't use on()

    if($('.csstransitions').length > 0){
        if(!$(this).is(':visible'))
            return $(this).css({
            'margin-top': pixels,
            '-webkit-transition': transition,
            '-moz-transition': transition,
            '-o-transition': transition,
            'transition': transition
            }).fadeIn();

        return $(this).css({
        'margin-top': pixels,
        '-webkit-transition': transition,
        '-moz-transition': transition,
        '-o-transition': transition,
        'transition': transition
        });
    } else
        return $(this).animate({'marginTop': pixels}, {duration: 'slow', complete: function(){ if(!$(this).is(':visible')) $(this).fadeIn() } });
}

/**
* Does element come before this
*
* @param $(element)
* @return Boolean
*/
$.fn.isBefore = function(sel) {
    return this.nextAll(sel).length !== 0;
}

/**
* Does element come after this
*
* @param $(element)
* @return Boolean
*/
$.fn.isAfter = function(sel) {
    return this.prevAll(sel).length !== 0;
}

/**
* Return difference of two arrays.
*
* @param {Array}
* @return Array
*/
Array.prototype.diff = function(a){
    return this.filter(function(i){return !(a.indexOf(i) > -1);});
}

/**
* indexOf() for browsers that don't support it.
* Returns the first index at which a given element
* can be found in the array.
*
* @param {Array}
* @return -1 if not present
*/
if(!Array.prototype.indexOf){
    Array.prototype.indexOf = function(elt /*, from*/){
        var len = this.length >>> 0;

        var from = Number(arguments[1]) || 0;
        from = (from < 0) ? Math.ceil(from) : Math.floor(from);
        if(from < 0)
            from += len;

        for(; from < len; from++){
            if(from in this && this[from] === elt)
                return from;
        }

        return -1;
    }
}

/**
* filter() for browsers that don't support it.
* Creates a new array with all elements that
* pass the test implemented by the provided function.
*
* @param {Array}
* @return {Array}
*/
if(!Array.prototype.filter){
    Array.prototype.filter = function(fun /*, thisp */){
        "use strict";

        if(this === void 0 || this === null)
            throw new TypeError();

        var t = Object(this);
        var len = t.length >>> 0;

        if(typeof fun !== "function")
            throw new TypeError();

        var res = [];
        var thisp = arguments[1];
        for(var i = 0; i < len; i++){
            if(i in t){
                var val = t[i]; // in case fun mutates this

                if(fun.call(thisp, val, i, t))
                    res.push(val);
            }
        }

        return res;
    }
}

//HTML5
function isAttributeSupported(tagName, attrName){
    var value = false;
    var input = document.createElement(tagName);
    if(attrName in input)
        value = true;
    delete input;
    return value;
}
//if(!isAttributeSupported('input', 'placeholder'))
//if(!isAttributeSupported('input', 'pattern'))

function isInputTypeSupported(typeName){
    var input = document.createElement('input');
    input.setAttribute('type', typeName);
    var value = (input.type !== 'text');
    delete input;
    return value;
}
//if(!isInputTypeSupported('email'))

var correctedViewportWidth = (function (win, docElem){
    var mM = win['matchMedia'] || win['msMatchMedia'], client = docElem['clientWidth'], inner = win['innerWidth'];
    return mM && client < inner && true === mM('(min-width:' + inner + 'px)')['matches'] ? function () { return win['innerWidth'] } : function () { return docElem['clientWidth'] }
}(window, document.documentElement));

//referenced in functions.inc.php ~Line 750
function get_joining_words(){
    return [
    'a', 'all', 'am', 'an', 'and', 'any', 'are', 'as', 'at', 'be', 'but', 'can', 'did', 'do', 'does', 'for', 'from',
    'had', 'has', 'have', 'here', 'how', 'i', 'if', 'in', 'is', 'it', 'no', 'not', 'of', 'on', 'or', 'so', 'that',
    'the', 'then', 'there', 'this', 'to', 'too', 'up', 'use', 'what', 'when', 'where', 'who', 'why', 'with', 'you'
    ];
}