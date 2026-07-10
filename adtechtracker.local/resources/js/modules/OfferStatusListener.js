/**
 * Класс изменения положения оффера в зависимости от статуса
 */
import DragItem from './DragItem.js';

export default class OfferStatusListener {

    constructor(activeOffers, deactiveOffers, subscriptions, unsubscriptions) {
        this.activeZone = document.querySelector(activeOffers);
        this.deactiveZone = document.querySelector(deactiveOffers);

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
                console.log('Subscribed to event: .offer.status.changed');
            })
            .listen('.offer.status.changed', (event) => {

                // если случилось, вызываем метод обновления оффера
                this.updateOffer(event, role);
            });
    }

    // обновление оффера
    updateOffer(offer, role) {

        // находим оффер
        const item = document.getElementById(offer.id);

        // если пользователь admin и рекламщик изменил статус оффера, то обновляем его
        if (role === 'admin') {
            if (item) {
                const actions = item.querySelector('.offer-actions');
                if (offer.status === 1) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('offer-deactive');
                    item.classList.add('offer-active');
                    actions.classList.add('hidden');
                    return;
                } else if (offer.status === 0) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('offer-active');
                    item.classList.add('offer-deactive');
                    actions.classList.remove('hidden');
                    return;
                }
            }
        }

        // если пользователь рекламщик и админ изменил статус оффера, то обновляем его
        if (role === 'advertiser' && offer.sender_role === 'admin') {
            if (item) {
                const actions = item.querySelector('.offer-actions');
                if (offer.status === 1) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('offer-deactive');
                    item.classList.add('offer-active');
                    actions.classList.add('hidden');
                    return;
                } else if (offer.status === 0) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('offer-active');
                    item.classList.add('offer-deactive');
                    actions.classList.remove('hidden');
                    return;
                }
            }
        }
    }
}