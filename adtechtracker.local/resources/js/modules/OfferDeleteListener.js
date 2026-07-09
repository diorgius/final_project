/**
 * Класс удаления оффера, если админ или рекламщик его удалил
 */
export default class OfferDeleteListener {

    constructor(offers) {
        this.offersZone = document.querySelector(offers);

        // вызываем слушатель
        this.listener();
    }

    // слушаем событие
    listener() {

        const role = window.userRole;
        
        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.delete');
            })
            .listen('.offer.delete', (event) => {

                // если случилось, вызываем метод удаления оффера
                this.deleteOffer(event);
            });
    }

    deleteOffer(offer) {
        document.getElementById(offer.id)?.remove();
    }
}