/**
 * Класс изменения статуса оффера drug and drop
 */
import DragItem from './DragItem.js';

export default class Subscription {

    constructor(itemSelector, zoneSelector) {
        this.items = document.querySelectorAll(itemSelector);
        this.zones = document.querySelectorAll(zoneSelector);

        this.init();
    }

    // Метод записи подписки в БД и отправки сообщения reverb
    async subscribe(itemId, type) {

        const role = window.userRole;

        try {
            const userId = window.userId;
            const response = await fetch(`/${role}/offers/${itemId}/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content
                },
                body: JSON.stringify({

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

  
    // Метод перемещения элементов
    init() {
        console.log('Subscription drop');
        this.items.forEach(item => {
            DragItem.setupItem(item, '.subscriptions');
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
                item.classList.remove('offer-active', 'offer-deactive');
                
                const url = item.querySelector('.offer-url');

                if (zone.classList.contains('subscriptions')) {
                    
                    // записываем в БД
                    const result = await this.subscribe(itemId, 'subscribe');
                    
                    console.log('Subscription response:', result);

                    if (!result?.success) {
                        console.log('Ошибка сохранения');
                        return;
                    }

                    // вставляем ссылку
                    url.href = `/r/${result.ref_code}`;
                    
                    // таймер появления ссылки
                    setTimeout(() => {
                        url.classList.remove('hidden');
                    }, 1500);

                    item.classList.add('offer-active');

                }

                if (zone.classList.contains('unsubscriptions')) {

                    // Записываем в БД
                    const result = await this.subscribe(itemId, 'unsubscribe');
  
                    console.log('Subscription response:', result);
                    
                    if (!result?.success) {
                        console.log('Ошибка сохранения');
                        return;
                    }

                    url.classList.add('hidden');
                    item.classList.add('offer-deactive');

                }

                // Перемещаем элемент
                zone.appendChild(item);
            });
        });
    }
}

// new Subscription('.offers__item', '.subscriptions, .unsubscriptions');
