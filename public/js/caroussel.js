Array.from(document.getElementsByTagName("multi")).forEach(e => function () {
    let next = e.next();
    if (!next.length) {
        next = e.siblings(":first");
    }
    next.children(":first-child").clone().appendTo(e);

    for (let i = 0; i < 2; i++) {
        next = next.next();
        if (!next.length) {
            next = e.siblings(":first");
        }

        next.children(":first-child").clone().appendTo(e);
    }
})