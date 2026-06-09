class Status {

    constructor(itemSelector, zoneSelector) {
        this.items = document.querySelectorAll(itemSelector);
        this.zones = document.querySelectorAll(zoneSelector);

        this.init();
    }

    // Метод записи статуса в БД
    async updateStatus(itemId, status) {

        try {
            const response = await fetch(`/advertiser/offers/${itemId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content
                },
                body: JSON.stringify({
                    status: status
                })
            });

            return await response.json();

        } catch (error) {
            console.error(error);
        }
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

            zone.addEventListener("drop", async e => {
                e.preventDefault();

                const itemId = e.dataTransfer.getData("id");
                const item = document.getElementById(itemId);

                if (!item) return;
                let status = null;
                
                // Убираем стили
                item.classList.remove("active-offers__item", "deactive-offers__item");

                if (zone.classList.contains("active-offers")) {
                    status = 1;
                    item.classList.add("active-offers__item");
                }

                if (zone.classList.contains("deactive-offers")) {
                    status = 0;
                    item.classList.add("deactive-offers__item");
                }

                // Сохраняем в БД
                const result = await this.updateStatus(itemId, status);

                if (!result?.success) {
                    console.log('Ошибка сохранения');
                    return;
                }

                // Перемещаем элемент
                zone.appendChild(item);
            });
        });
    }
}

new Status(".offers__item", ".offers");
