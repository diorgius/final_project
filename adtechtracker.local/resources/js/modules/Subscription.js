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

    // метод записи подписки в БД и отправки сообщения reverb
    async subscribe(itemId, type) {

        //получаем роль пользователя
        const role = window.userRole;

        // отправляем данные на бэк
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

            return await response.json();
            
        } catch (error) {
            console.error(error);
        }
    }

    // метод перемещения элементов
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

                // проверяем новый статус
                const newStatus = zone.classList.contains('subscriptions') ? 'active' : 'deactive';

                // если ничего не изменилось
                if (oldStatus === newStatus) {
                    return;
                }

                // убираем стили
                item.classList.remove('offer-active', 'offer-deactive');
                
                const url = item.querySelector('.offer-url');

                // если зона подписок
                if (zone.classList.contains('subscriptions')) {
                    
                    // записываем в БД
                    const result = await this.subscribe(itemId, 'subscribe');
                    
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

                    // меняем класс
                    item.classList.add('offer-active');

                }

                // если зона отписок
                if (zone.classList.contains('unsubscriptions')) {

                    // записываем в БД
                    const result = await this.subscribe(itemId, 'unsubscribe');
  
                    if (!result?.success) {
                        console.log('Ошибка сохранения');
                        return;
                    }

                    // прячем ссылку
                    url.classList.add('hidden');

                    // меняем класс
                    item.classList.add('offer-deactive');

                }

                // перемещаем элемент
                zone.appendChild(item);
            });
        });
    }
}