/**
 * Класс удаления оффера, если админ или рекламщик его удалил
 */
class OfferDeleteListener {

    constructor(offers) {
        this.offersZone = document.querySelector(offers);
        this.listener();
    }

    listener() {
        const role = window.userRole;
        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.delete');
            })
            .listen('.offer.delete', (event) => {
                console.log('Delete offer', event);

                this.deleteOffer(event);
            });
    }

    deleteOffer(offer) {
        document.getElementById(offer.id)?.remove();
    }
}

new OfferDeleteListener('.offers');