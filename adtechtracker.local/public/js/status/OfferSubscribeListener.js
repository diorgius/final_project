/**
 * Класс изменения количества подписок у админа и рекламщика 
 */
class OfferSubscribeListener {

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
                
                if (event.action === 'subscribed') {
                    const offer = document.getElementById(event.offer_id);
                    console.log(offer);

                }

                if (event.action === 'unsubscribed') {
                    console.log('отписка');


                }


            });
    }

}

new OfferSubscribeListener();