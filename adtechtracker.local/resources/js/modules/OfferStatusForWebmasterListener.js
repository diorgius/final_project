/**
 * Класс изменения положения оффера у вебмастера в зависимости от статуса
 */
import DragItem from './DragItem.js';

export default class OfferStatusForWebmasterListener {

    constructor(subscriptions, unsubscriptions) {
        this.subscriptionsZone = document.querySelector(subscriptions);
        this.unsubscriptionsZone = document.querySelector(unsubscriptions);

        // вызываем слушатель
        this.listener();
    }

    // слушаем событие
    listener() {

        // получаем id пользователя
        const userId = window.userId;

        Echo.private(`offers.webmaster.${userId}`)
        
            .subscribed(() => {
                console.log('Subscribed to channel: offer.webmaster');
                console.log('Subscribed to event: .offer.status.for.webmaster.changed');
            })
            .listen('.offer.status.for.webmaster.changed', (event) => {

                // если случилось, вызываем метод обновления оффера
                this.updateOffer(event.offer);
            });
    }

    // обновление оффера
    updateOffer(offer) {

        // ищем оффер
        const item = document.getElementById(offer.id);

        // если был добавлен новый активный оффер, то отображаем его
        if (offer.status === 1) {
            if (!item) {
                // отображаем оффер
                const divOffer = document.createElement('div');
                divOffer.setAttribute('id', `${offer.id}`);
                divOffer.innerHTML =
                    `<p class="font-semibold">${__('advertiser')}: <span class="font-light">${offer.advertiser}</span></p>
                        <p class="font-semibold">${__('offer_name')}: <span class="font-light">${offer.name}</span></p>
                        <p class="font-semibold">${__('offer_theme')}: <span class="font-light">${offer.theme}</span></p>
                        <p class="font-semibold">${__('offer_price')}: <span class="font-light">${offer.price.toFixed(2)}</span></p>
                        <a href="/r/${offer.ref_code}" class="offer-url hidden font-semibold text-xl text-blue-600" title=${offer.url} target="_blank">${__('referral_link')}</a>`
                // если без подписки, то в зону доступных подписок    
                if (!offer.subscribed) {
                    divOffer.className = 'offers__item offer-item offer-deactive';
                    this.unsubscriptionsZone.appendChild(divOffer);
                    // если с подпиской, то в зону подписок
                } else {
                    divOffer.className = 'offers__item offer-item offer-active';
                    this.subscriptionsZone.appendChild(divOffer);
                    const url = divOffer.querySelector('.offer-url');

                    // показываем ссылку по таймеру
                    setTimeout(() => {
                        url.classList.remove('hidden');
                    }, 1500);
                }
                DragItem.setupItem(divOffer, '.subscriptions');
            }
            return;

        // если оффер есть и его отключили, то удаляем его
        } else if (offer.status === 0) {
            if (item) {
                item.remove();
            }
            return;
        }
    }
}