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

        const role = window.userRole;
        
        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.subscribe.changed');
            })
            .listen('.offer.subscribe.changed', (event) => {

                // если случилось, ищем оффер
                const offer = document.getElementById(event.offer_id);
                
                //если нашли, то изменяем у него количество подписчиков
                if (offer) {
                    const subscribers = offer.querySelector('.subscribers');
                    subscribers.textContent = `${(event.subscribe_count)}`;
                }
            });
    }
}