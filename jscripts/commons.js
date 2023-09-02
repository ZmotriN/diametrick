const ready = (evt) => {
    document.addEventListener("DOMContentLoaded", (e) => {  evt(e); });
}


const bind = (elm, evt, clb) => {
    if(typeof elm == 'string') {
        document.querySelectorAll(elm).forEach((item) => {
            item.addEventListener(evt, clb);
        });
    } else {
        elm.addEventListener(evt, clb);
    }
}


const query = (selector, all=false) => {
    if(all) return document.querySelectorAll(selector);
    else return document.querySelector(selector);
}


const click = (elm, evt) => {
    if(typeof elm == 'string') elm = query(elm);
    elm.addEventListener('click', evt);
}


const redirect = (path='') => {
    document.location.href = root + path;
}


const redirectReferer = () => {
    let link = root;
    const baseuri = new URL(document.baseURI);
    const referer = new URL(document.referrer, document.baseURI);
    if(referer.hostname == baseuri.hostname && referer.pathname != baseuri.pathname) link = referer.href;
    document.location.href = link;
};


const striptags = (str) => {
    return str.replace(/(<([^>]+)>)/gi, "");
}


const count = (arr) => {
    return arr.filter(function(v){ return true; }).length;
}


const lowslug = (str) => {
	return str
		.trim()
		.normalize("NFD")
		.replace(/[\u0300-\u036f]/g, "")
		.toLowerCase()
		.replace(/[^a-z0-9\s\-]/g, "")
		.replace(/[\s\t]+/g, "")
	;
}


const copyToClipboard = async (textToCopy) => {
    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(textToCopy);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        textArea.style.position = "absolute";
        textArea.style.left = "-999999px";
        document.body.prepend(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
        } catch (error) {
            console.error(error);
        } finally {
            textArea.remove();
        }
    };
}


const forceKeyPressUppercase = (e) => {
    let el = e.target;
    let charInput = e.keyCode;
    if((charInput >= 97) && (charInput <= 122)) {
        if(!e.ctrlKey && !e.metaKey && !e.altKey) {
            let newChar = charInput - 32;
            let start = el.selectionStart;
            let end = el.selectionEnd;
            el.value = el.value.substring(0, start) + String.fromCharCode(newChar) + el.value.substring(end);
            el.setSelectionRange(start+1, start+1);
            e.preventDefault();
        }
    }
};


const decodeEntities = (function() {
    let element = document.createElement('div');
    let entity = /&(?:#x[a-f0-9]+|#[0-9]+|[a-z0-9]+);?/ig;
    return function decodeHTMLEntities(str) {
        str = str.replace(entity, function(m) {
            element.innerHTML = m;
            return element.textContent;
        });
        element.textContent = '';
        return str;
    }
})();


const isInViewport = (el, partial=false) => {
    const { top, left, bottom, right } = el.getBoundingClientRect();
    const { innerHeight, innerWidth } = window;
    return partial
        ? ((((top > 0 && top < innerHeight) || (bottom > 0 && bottom < innerHeight))) || (top < 0 && bottom > innerHeight)) &&
          (((left > 0 && left < innerWidth) || (right > 0 && right < innerWidth)) || (left < 0 && right > innerWidth))
        : top >= 0 && left >= 0 && bottom <= innerHeight && right <= innerWidth;
};


const post = (obj, debug=false) => {
    let url = (new URL(document.baseURI)).pathname;
    return fetch(url, {
        method: "POST",
        body: JSON.stringify(obj)
    })
    .then((response) => response.text())
    .then((responseData) => {
        if(debug) console.log(responseData);
        let response = JSON.parse(responseData);
        if(response.success == undefined) return {
            success: false,
            errmsg: "Réponse du serveur invalide.",
        };
        return response;
    })
    .catch(error => {
        return {
            success: false,
            errmsg: "Format de réponse du serveur invalide.",
        };
    });
}

const geturl = (url, debug=false) => {
    return fetch(url)
    .then((response) => response.text())
    .then((responseData) => {
        if(debug) console.log(responseData);
        return JSON.parse(responseData);
    })
    .catch(error => console.warn(error));
}

