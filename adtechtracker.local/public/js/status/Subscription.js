/**
 * Класс изменения статуса оффера drug and drop
 */
class Subscription {

    constructor(itemSelector, zoneSelector) {
        this.items = document.querySelectorAll(itemSelector);
        this.zones = document.querySelectorAll(zoneSelector);

        this.init();
    }

    // Метод записи подписки в БД и отправки сообщения reverb
    async updateStatus(itemId) {

        const role = window.userRole;

        try {
            const userId = window.userId;
            const response = await fetch(`/${role}/offers/${itemId}/subscription`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content
                },
                body: JSON.stringify({
                    offerId: itemId,
                    userId: userId
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

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
            const currentStatus = item.closest('.subscriptions') ? 'active' : 'deactive';
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
                const newStatus = zone.classList.contains('subscriptions') ? 'active' : 'deactive';

                // Если ничего не изменилось
                if (oldStatus === newStatus) {
                    return;
                }

                // Убираем стили
                item.classList.remove('active-offers__item', 'deactive-offers__item');
                
                // Устанавливаем статус
                const status = zone.classList.contains('subscriptions') ? 1: 0;

                const url = item.querySelector('.offer-url');

                if (zone.classList.contains('subscriptions')) {
                    // url.classList.remove('hidden__item');
                    setTimeout(() => {
                        url.classList.remove('hidden__item');
                    }, 1500);
                    item.classList.add('active-offers__item');
                }

                if (zone.classList.contains('deactive-offers')) {
                    url.classList.add('hidden__item');
                    item.classList.add('deactive-offers__item');
                }

                // Сохраняем в БД
                const result = await this.updateStatus(itemId);

                console.log('Subscription response:', result);

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
window.offerStatus = new Subscription('.offers__item', '.offers');
