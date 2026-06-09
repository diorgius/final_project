class Status {

    constructor(itemSelector, zoneSelector) {
        this.items = document.querySelectorAll(itemSelector);
        this.zones = document.querySelectorAll(zoneSelector);

        this.init();
    }

    init() {
        this.items.forEach(item => {
            item.draggable = true;

            item.addEventListener("dragstart", e => {
                e.dataTransfer.setData("id", item.id);
            });
        });

        this.zones.forEach(zone => {
            zone.addEventListener("dragover", e => {
                e.preventDefault();
            });

            zone.addEventListener("drop", e => {
                e.preventDefault();

                const itemId = e.dataTransfer.getData("id");
                const item = document.getElementById(itemId);

                if (!item) return;

                // Удаляем старые стили
                item.classList.remove("active-offers__item", "deactive-offers__item");

                // Перемещаем элемент
                zone.appendChild(item);

                // Назначаем новый стиль
                if (zone.classList.contains("active-offers")) {
                    item.classList.add("active-offers__item");
                }

                if (zone.classList.contains("deactive-offers")) {
                    item.classList.add("deactive-offers__item");
                }
            });
        });
    }
}

new Status(".offers__item", ".offers");
