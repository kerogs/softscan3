console.log("main.ts -> OK");

const bodyElementssss = document.querySelector("body");

// click on body
bodyElementssss.addEventListener("click", () => {
    const littlePopup = document.querySelectorAll(".littlePopup");

    // si on a cliqué sur littlePopup, alors on la retire
    littlePopup.forEach(littlePopupItem => {
        littlePopupItem.addEventListener("click", () => {
            littlePopupItem.remove();
        })
    });
})