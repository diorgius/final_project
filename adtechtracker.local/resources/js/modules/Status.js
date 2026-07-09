/**
 * Класс изменения статуса оффера drug&drop
 */
import DragItem from './DragItem.js';

export default class Status {

    constructor(itemSelector, zoneSelector) {
        this.items = document.querySelectorAll(itemSelector);
        this.zones = document.querySelectorAll(zoneSelector);

        this.init();
    }

    // метод записи статуса в БД и отправки сообщения reverb
    async updateStatus(itemId, status) {

        //получаем роль пользователя
        const role = window.userRole;

        // отправляем данные на бэк
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

    // метод перемещения элементов
    init() {
        this.items.forEach(item => {
            DragItem.setupItem(item, '.active-offers');
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
                const actions = item.querySelector('.offer-actions');

                if (!item) return;

                // проверяем новый статус
                const newStatus = zone.classList.contains('active-offers') ? 'active' : 'deactive';

                // если ничего не изменилось
                if (oldStatus === newStatus) {
                    return;
                }

                // убираем стили
                item.classList.remove('offer-active', 'offer-deactive');
                
                // устанавливаем статус
                const status = zone.classList.contains('active-offers') ? 1 : 0;

                // в зависимости от статуса меняем класс
                if (status === 1) {
                    item.classList.add('offer-active');
                    actions.classList.add('hidden');
                } else {
                    item.classList.add('offer-deactive');
                    actions.classList.remove('hidden');
                }

                // сохраняем в БД
                const result = await this.updateStatus(itemId, status);

                if (!result?.success) {
                    console.log('Ошибка сохранения');
                    return;
                }

                // перемещаем элемент
                zone.appendChild(item);
            });
        });
    }
}