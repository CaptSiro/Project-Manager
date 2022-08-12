const main = $(".main");

let MAX_COLUMNS = Math.min(Math.floor((main.clientHeight - 50) / 150), Math.floor((main.clientWidth - 50) / 150));
const projects = $(".projects");
const contextMenu = $(".projects-manipulation");
contextMenu.querySelector(".cancel").addEventListener("click", evt => contextMenu.classList.remove("show"));


let targetTop = 0;
projects.addEventListener("scroll", evt => {
  const newTop = targetTop - projects.scrollTop;
  const min = projects.getBoundingClientRect().top;
  const max = min + projects.clientHeight - contextMenu.scrollHeight;
  const clamped = clamp(min, max, newTop);

  contextMenu.style.top = clamped + "px";

  lastScroll = projects.scrollTop;
});
/**
 * @param {HTMLElement} child 
 * @param {string} className 
 */
function getParentByClassName (child, className) {
  if (child.classList.contains(className)) return child;

  let element = child;
  while (!element.classList.contains(className)) {
    if (element.tagName === "HTML") {
      return;
    }

    element = element.parentElement;
  }

  return element;
}
/**
 * @param {MouseEvent} evt 
 */
function positionContextMenu (evt) {
  contextMenu.style.top = evt.clientY + "px";
  targetTop = evt.clientY - evt.target.getBoundingClientRect().top + evt.target.offsetTop + projects.getBoundingClientRect().top;
  contextMenu.style.left = evt.clientX + "px";

  contextMenu.children[0].focus();
}
function calcGrid (ignored, size = undefined) {
  MAX_COLUMNS = Math.min(Math.floor((main.clientHeight - 50) / 150), Math.floor((main.clientWidth - 50) / 150));

  
  let columns = MAX_COLUMNS;
  for (let i = 1; i < MAX_COLUMNS; i++) {
    if ((size ?? projects.children.length) <= Math.pow(i, 2)) {
      columns = i;
      break;
    }
  }

  projects.style.gridTemplateColumns = `repeat(${columns}, 150px)`;
  projects.style.height = (columns * 150) + "px";
  projects.style.width = ((columns * 150) + ((((size ?? projects.children.length) + 1) > Math.pow(MAX_COLUMNS, 2)) ? 10 : 0)) + "px";
}
function projectBuilder (raw) {
  return {
    className: ["project", "center"],
    content: [{
      className: "filling",
      content: {
        className: "percentage",
        content: (raw.completionRate) ? Number(raw.completionRate * 100).toFixed(2) : "0" + "%"
      },
      modify: e => {
        e.style.width = ((raw.completionRate) ? (raw.completionRate * 100) : "0") + "%";
      }
    }, {
      name: "h5",
      content: raw.name
    }],
    listeners: {
      click: evt => {
        console.log("open: " + raw.ID);
      },
      contextmenu: evt => {
        evt.preventDefault();
        contextMenu.classList.add("show");
        contextMenu.classList.remove("add");
        contextMenu.children[0].value = raw.name;
        positionContextMenu(evt);

        const { ID } = raw;
        contextMenu.querySelector(".submit").onclick = event => {
          if (contextMenu.children[0].value === "") {
            alert("Project must have a name.");
            return;
          }

          fetchEX(
            "projects-rename-POST",
            txt => {
              if (txt == "true") {
                const proj = getParentByClassName(evt.target, "project");
                proj.querySelector('h5').innerText = contextMenu.children[0].value;
                contextMenu.classList.remove("show");
              }
            },
            {
              handlerType: "nonHTMLText",
              method: "POST",
              body: toFormData({ ID, name: contextMenu.children[0].value })
            }
          );
        };

        contextMenu.querySelector(".delete").onclick = event => {
          if (confirm("Do you want to delete project: " + raw.name)) {
            fetchEX(
              "projects-delete-POST",
              txt => {
                if (txt == "true") {
                  const proj = getParentByClassName(evt.target, "project");

                  proj.classList.remove("show");
                  setTimeout(() => {
                    projects.removeChild(proj);
                    calcGrid();
                  }, 510);
                  contextMenu.classList.remove("show");
                }
              },
              {
                handlerType: "nonHTMLText",
                method: "POST",
                body: toFormData({ ID })
              }
            );
          }
        };
      }
    }
  }
}




window.addEventListener("resize", calcGrid);
window.addEventListener("load", evt => {
  fetchEX(
    "projects-all-GET",
    projectsArray => {
      calcGrid(null, projectsArray.length + 1);
      projects.textContent = "";

      const children = htmlCollection(projectsArray, projectBuilder);

      children.push(html({
        className: ["project", "center", "add"],
        content: [{
          name: "h5",
          content: "+"
        }],
        listeners: {
          click: evt => {
            contextMenu.classList.add("show");
            contextMenu.classList.add("add");
            contextMenu.children[0].value = "";
            positionContextMenu(evt);

            contextMenu.querySelector(".submit").onclick = evt => {
              if (contextMenu.children[0].value === "") {
                alert("Project must have a name.");
                return;
              }
              
              fetchEX(
                "projects-create-POST",
                proj => {
                  const projHTML = html(projectBuilder(proj));
                  const plus = projects.children[projects.children.length - 1];
                  projects.insertBefore(projHTML, plus);
                  calcGrid();

                  setTimeout(() => {
                    projHTML.classList.add("show");
                  }, 10);

                  contextMenu.classList.remove("show");
                },
                {
                  method: "POST",
                  body: toFormData({ name: contextMenu.children[0].value })
                }
              );
            };
          },
          contextmenu: evt => {
            evt.preventDefault();
          }
        }
      }));

      projects.append(...children);

      setTimeout(async () => {
        for (let i = 0; i < children.length; i++) {
          await sleep(25);
          children[i].classList.add("show");
        }
      }, 200);
    }
  );
});