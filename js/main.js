/**
 * 
 * @param {String} css 
 * @returns {HTMLElement}
 */
const $ = css => document.querySelector(css);
/**
 * 
 * @param {String} css 
 * @returns {NodeListOf<Element>}
 */
const $$ = css => document.querySelectorAll(css);
 
/**
 * @typedef HTMLAttributes
 * @prop {String=} src
 * @prop {String=} alt
 * @prop {String=} id
 * @prop {String=} style
 * @prop {String=} type
 * @prop {String=} value
 */
/**
 * @typedef HTMLEventListeners
 * @prop {(event: Event)=>void=} load
 * @prop {(event: Event)=>void=} resize
 * @prop {(event: Event)=>void=} input
 * @prop {(event: Event)=>void=} submit
 * @prop {(event: KeyboardEvent)=>void=} keydown
 * @prop {(event: KeyboardEvent)=>void=} keyup
 * @prop {(event: KeyboardEvent)=>void=} keypress
 * @prop {(event: MouseEvent)=>void=} click
 * @prop {(event: MouseEvent)=>void=} contextmenu
 * @prop {(event: MouseEvent)=>void=} dblclick
 * @prop {(event: MouseEvent)=>void=} mousedown
 * @prop {(event: MouseEvent)=>void=} mousemove
 * @prop {(event: MouseEvent)=>void=} mouseleave
 * @prop {(event: MouseEvent)=>void=} mouseover
 * @prop {(event: MouseEvent)=>void=} mouseup
 * @prop {(event: MouseEvent)=>void=} mousewheel
 * @prop {(event: MouseEvent)=>void=} wheel
 * @prop {(event: DragEvent)=>void=} drag
 * @prop {(event: DragEvent)=>void=} scroll
 * @prop {(event: ClipboardEvent)=>void=} copy
 * @prop {(event: ClipboardEvent)=>void=} cut
 * @prop {(event: ClipboardEvent)=>void=} paste
 * @prop {(event: Event)=>void=} ended
 * @prop {(event: Event)=>void=} error
 * @prop {(event: Event)=>void=} loadeddata
 * @prop {(event: Event)=>void=} pause
 * @prop {(event: Event)=>void=} play
 */
/**
 * @typedef HTMLDescription
 * @prop {keyof HTMLElementTagNameMap=} name Setting name to undefined will resolve to default tag name: "div"
 * @prop {String|String[]=} className
 * @prop {HTMLAttributes=} attributes 
 * @prop {HTMLEventListeners=} listeners 
 * @prop {String|String[]|HTMLDescription|HTMLDescription[]|HTMLElement|HTMLElement[]=} content
 * @prop {(element: HTMLElement)=>void=} modify
 */
/**
 * From given description creates an HTMLElement and returns it
 * @param {HTMLDescription} description 
 * @returns {HTMLElement}
 */
const html = function (description) {
  if (description === undefined) {
    throw new Error('No parameters were passed into html function.')
  }

  if (description.name === undefined || typeof description.name !== 'string') {
    description.name = "div";
  }

  const element = document.createElement(description.name);

  if (description.className) {
    if (description.className instanceof Array) {
      for (let i = 0; i < description.className.length; i++) {
        element.classList.add(description.className[i]);
      }
    } else {
      element.classList.add(description.className);
    }
  }

  if (description.attributes) {
    if (description.attributes instanceof Object) {
      for (const attr in description.attributes) {
        element.setAttribute(attr, description.attributes[attr]);
      }
    }
  }

  if (description.listeners) {
    if (description.listeners instanceof Object) {
      for (const event in description.listeners) {
        element.addEventListener(event,  description.listeners[event]);
      }
    }
  }

  if (description.content) {
    if (typeof description.content === 'string') {
      element.appendChild(document.createTextNode(description.content));
    } else if (description.content instanceof HTMLElement) {
      element.appendChild(description.content);
    } else if (description.content instanceof Object && !(description.content instanceof Array)) {
      element.appendChild(html(description.content));
    } else if (description.content instanceof Array) {
      for (let i = 0; i < description.content.length; i++) {
        if (typeof description.content[i] === 'string') {
          element.appendChild(document.createTextNode(description.content[i]))
        } else if (description.content[i] instanceof HTMLElement) {
          element.appendChild(description.content[i]);
        } else {
          element.appendChild(html(description.content[i]));
        }
      }
    }
  }

  if (description.modify) {
    description.modify(element);
  }

  return element;
}
 
/**
 * For each item in `rawArray` calls `builder` callback and from returned description creates HTMLElement
 * @template {any} T
 * @param {Array<T>} rawArray 
 * @param {(rawItem: T) => HTMLDescription} builder 
 * @returns {HTMLElement[]}
 */
const htmlCollection = function (rawArray, builder) {
  return rawArray.map(rawItem => html(builder(rawItem)));
}
 
/**
 * Clamps number between given bounds
 * @param {Number} min 
 * @param {Number} max 
 * @param {Number} number 
 * @returns {Number}
 */
const clamp = (min, max, number) => {
  if (number < min) return min;
  return (number > max) ? max : number;
}
 
/**
 * Adds delay to running code synchronously
 * @param {Number} ms 
 * @returns 
 */
const sleep = (ms) => new Promise ((resolve, reject) => setTimeout(() => resolve(), ms));



/**
 * 
 * @param {URL|string} address Set `fetchOption.isUrl` to `true` if the address is url. Default for address is just a name of file in EndPoint directory.
 * @param {(val: any)=>void=} handler 
 * @param {RequestInit & {isUrl: boolean=, handlerType: "json"|"nonHTMLText"|"plainText"=}=} options 
 * @param {(reason: any, txt: string)=>void=} errorHandler 
 */
const fetchEX = async (address, handler, options, errorHandler) => {
  if (options?.isUrl !== true) {
    address = 'http://localhost/project-manager/EndPoints/' + ((address.endsWith(".php"))
      ? address
      : (address + ".php"));
  }



  $("p.PHP-Exception")?.classList.remove("throw");
  errorHandler = errorHandler ?? ((reas, txt) => {
    const exceptionCon = $("pre.PHP-Exception");
    exceptionCon.innerHTML = "<h5>" + reas + ":</h5>" + "<br>" + txt;
    exceptionCon.classList.add("throw");
  });



  const response = (await fetch(address, options));
  const responseText = response.clone().text();

  if (response.ok) {
    if (options) {
      const txt = await responseText;
      if (options.handlerType === "nonHTMLText") {
        if (txt[0] == "<") {
          errorHandler("PHP Exception", txt);
          return;
        }

        handler(txt);
      }

      if (options.handlerType === "plainText") {
        handler(txt);
      }
    }

    if (options === undefined || options.handlerType === "json" || options.handlerType === undefined) {
      response.json()
        .then(json => {
          if (json.errorMessage) {
            throw new Error(response.status + ": " + json.errorMessage);
          }
        
          handler(json);
        })
        .catch(reason => {
          console.log(reason);
          responseText.then(txt => {
            errorHandler(reason, txt);
          });
        });
    }    
  } else {
    const txt = await responseText;
    if (txt[0] = "<") {
      errorHandler("PHP Exception", txt);
      return;
    }

    throw new Error(response.status + ": " + txt);
  }
}


/**
 * Creates FormData object from given object
 * @param {any} obj 
 * @returns {FormData}
 */
const toFormData = obj => {
  const fd = new FormData();
  for (const k in obj) {
    fd.append(k, obj[k]);
  }
  return fd;
};