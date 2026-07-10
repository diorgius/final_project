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

        // получаем роль пользователя
        const role = window.userRole;
        
        Echo.private(`offers.${role}`)
        
            .subscribed(() => {
                console.log(`Subscribed to channel: offer.${role}`);
                console.log('Subscribed to event: .offer.delete');
            })
            .listen('.offer.delete', (event) => {

                // если случилось, вызываем метод удаления оффера
                this.deleteOffer(event);
            });
    }

    deleteOffer(offer) {

        // если есть, то удаляем
        document.getElementById(offer.id)?.remove();
    }
}