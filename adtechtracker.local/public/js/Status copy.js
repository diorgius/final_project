/**
 * Класс изменения статуса оффера drug and drop
 */
class Status {

    constructor(itemSelector, zoneSelector) {
        this.items = document.querySelectorAll(itemSelector);
        this.zones = document.querySelectorAll(zoneSelector);

        this.init();
    }

    // Метод записи статуса в БД и отправки сообщения reverb
    async updateStatus(itemId, status) {

        const role = window.userRole;

        try {
            const response = await fetch(`/${role}/offers/${itemId}/status`, {
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

    // Метод получения элемента в том числе и для вновь созданного через websocket, что все элементы были draggeble
    setupItem(item) {
        item.draggable = true;
        item.addEventListener('dragstart', e => {
            e.dataTransfer.setData('id', item.id);
            const currentStatus = item.closest('.active-offers') ? 'active' : 'deactive';
            e.dataTransfer.setData('oldStatus', currentStatus);
        });
    }

    // Метод перемещения элементов
    init() {
        
        this.items.forEach(item => {
            this.setupItem(item);
        });
        this.zones.forEach(zone => {
            zone.addEventListener('dragover', e => {
                e.preventDefault();
            });

            zone.addEventListener('drop', async e => {
                e.preventDefault();
                const itemId = e.dataTransfer.getData('id');
                const item = document.getElementById(itemId);
                const oldStatus = e.dataTransfer.getData('oldStatus');

                if (!item) return;

                // Проверяем новый статус
                const newStatus = zone.classList.contains('active-offers') ? 'active' : 'deactive';

                // Если ничего не изменилось
                if (oldStatus === newStatus) {
                    return;
                }

                // Убираем стили
                item.classList.remove('active-offers__item', 'deactive-offers__item');
                
                // Устанавливаем статус
                const status = zone.classList.contains('active-offers') ? 1 : 0;

                if (zone.classList.contains('active-offers')) {
                    item.classList.add('active-offers__item');
                }

                if (zone.classList.contains('deactive-offers')) {
                    item.classList.add('deactive-offers__item');
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

// Глобальный экземпляр класса
window.offerStatus = new Status('.offers__item', '.offers');
