/**
 * Класс изменения положения оффера в зависимости от статуса
 */
import DragItem from './DragItem.js';

export default class OfferStatusListener {

    constructor(activeOffers, deactiveOffers, subscriptions, unsubscriptions) {
        this.activeZone = document.querySelector(activeOffers);
        this.deactiveZone = document.querySelector(deactiveOffers);
        this.subscriptionsZone = document.querySelector(subscriptions);
        this.unsubscriptionsZone = document.querySelector(unsubscriptions);
        
        // вызываем слушатель
        this.listener();
    }

    // слушаем событие
    listener() {
        const role = window.userRole;
        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.status.changed');
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

        // у админа обновляем оффер
        if (role === 'admin') {
            const actions = item.querySelector('.offer-actions');
            if (offer.status === 1) {
                if (item) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('offer-deactive');
                    item.classList.add('offer-active');
                    actions.classList.add('hidden');
                }
                return;
            } else if (offer.status === 0) {
                if (item) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('offer-active');
                    item.classList.add('offer-deactive');
                    actions.classList.remove('hidden');
                }
                return;
            }
        }

        // если пользователь рекламщик или админ изменил статус оффера, то обновляем его
        if (role === 'advertiser' && offer.sender_role === 'admin') {
            if (item) {
                const actions = item.querySelector('.offer-actions');
                if (offer.status === 1) {
                    if (item) {
                        this.activeZone.appendChild(item);
                        item.classList.remove('offer-deactive');
                        item.classList.add('offer-active');
                        actions.classList.add('hidden');
                    }
                    return;
                } else if (offer.status === 0) {
                    if (item) {
                        this.deactiveZone.appendChild(item);
                        item.classList.remove('offer-active');
                        item.classList.add('offer-deactive');
                        actions.classList.remove('hidden');
                    }
                    return;
                }
            }
        }

        // если пользователь вебмастер и был добавлен новый активный оффер, то отображаем его
        if (role === 'webmaster') {
            console.log(offer);
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
                        <a href="#" class="offer-url hidden font-semibold text-xl text-blue-600" title=${offer.url} target="_blank">${__('referral_link')}</a>`
                    // если без подписки, то в зону доступных подписок    
                    if (offer.subscribe === 0) {
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
}