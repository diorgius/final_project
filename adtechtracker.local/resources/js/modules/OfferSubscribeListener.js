/**
 * Класс изменения количества подписок у админа и рекламщика 
 */
export default class OfferSubscribeListener {

    constructor() {

        // вызываем слушатель
        this.listener();
    }

    // слушаем событие
    listener() {

        // получаем роль пользователя
        const role = window.userRole;
        
        Echo.private(`offers.${role}`)
        
            .subscribed(() => {
                console.log(`Subscribed to channel: offer.${role}`);
                console.log('Subscribed to event: .offer.subscribe.changed');
            })
            .listen('.offer.subscribe.changed', (event) => {

                // если случилось, ищем оффер
                const item = document.getElementById(event.offer_id);
                
                //если нашли, то изменяем у него количество подписчиков
                if (item) {
                    const subscribers = item.querySelector('.subscribers');
                    subscribers.textContent = `${(event.subscribe_count)}`;
                }
            });
    }
}