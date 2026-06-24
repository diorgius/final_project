/**
 * Класс изменения количества подписок у админа и рекламщика 
 */
export default class OfferSubscribeListener {

    constructor() {
        // this.offersZone = document.querySelector(offers);
        this.listener();
    }

    listener() {
        const role = window.userRole;
        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.subscribe.changed');
            })
            .listen('.offer.subscribe.changed', (event) => {
                console.log('Subscribe change', event);
                
                // if (event.action === 'subscribed') {
                    const offer = document.getElementById(event.offer_id);
                    const subscribers = offer.querySelector('.subscribers');
                    subscribers.textContent = `Подписчиков: ${(event.subscribe_count)}`;
                // }

                // if (event.action === 'unsubscribed') {
                //     const offer = document.getElementById(event.offer_id);
                //     const subscribers = offer.querySelector('.subscribers');
                //     subscribers.innerText = `Подписчиков: ${(event.subscribe_count)}`;
                // }
                                        

            });
    }
}

// new OfferSubscribeListener();