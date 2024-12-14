function openMenu(menu) {
    document.getElementById(menu).style.visibility = "visible";
}
function closeMenu(menu) {
    document.getElementById(menu).style.visibility = "hidden";
}

function navigate(page) {
    window.open(page, "_top");
}

function toggleUserCard() {
    const userCard = document.getElementById("user_card");
    if (userCard.style.display === "none" || userCard.style.display === "") {
        userCard.style.display = "block";
    } else {
        userCard.style.display = "none";
    }
}

function showFormOption(element, elementToWatch, value) {
    var currentValue = document.getElementById(elementToWatch).value;
    console.log(currentValue);

    document.getElementById(element).style.display = (
        currentValue === value ? 'block' : 'none')
}
