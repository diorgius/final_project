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

        this.listener();
    }

    listener() {
        const role = window.userRole;
        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.status.changed');
            })
            .listen('.offer.status.changed', (event) => {
                console.log('Status changed', event);

                this.updateOffer(event, role);
            });
    }

    updateOffer(offer, role) {
        // находим оффер
        const item = document.getElementById(offer.id);
        // у админа перемещаем оффер
        if (role === 'admin') {
            if (offer.status === 1) {
                if (item) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('offer-deactive');
                    item.classList.add('offer-active');
                }
                return;
            } else if (offer.status === 0) {
                if (item) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('offer-active');
                    item.classList.add('offer-deactive');
                }
                return;
            }
        }

        if (role === 'advertiser' && offer.sender_role === 'admin') {
            // если пользователь рекламщик и админ изменил статус оффера, то перемещаем его
            if (offer.status === 1) {
                if (item) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('offer-deactive');
                    item.classList.add('offer-active');
                }
                return;
            } else if (offer.status === 0) {
                if (item) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('offer-active');
                    item.classList.add('offer-deactive');
                }
                return;
            }
        }

        if (role === 'webmaster') {
            // если пользователь вебмастер и был добавлен новый активный оффер, то отображаем его
            if (offer.status === 1) {
                if (!item) {
                    // Отображаем оффер
                    const divOffer = document.createElement('div');
                    divOffer.setAttribute('id', `${offer.id}`);
                    divOffer.innerHTML =
                        `<p class="font-semibold">${__('advertiser')}: <span class="font-light">${offer.advertiser}</span></p>
                        <p class="font-semibold">${__('offer_name')}: <span class="font-light">${offer.name}</span></p>
                        <p class="font-semibold">${__('offer_theme')}: <span class="font-light">${offer.theme}</span></p>
                        <p class="font-semibold">${__('offer_price')}: <span class="font-light">${offer.price.toFixed(2)}</span></p>
                        <a href="#" class="offer-url hidden font-semibold text-xl text-blue-600" title=${offer.url} target="_blank">${__('referral_link')}</a>`
                    if (offer.subscribe === 0) {
                        divOffer.className = 'offers__item offer-item offer-deactive';
                        this.unsubscriptionsZone.appendChild(divOffer);
                    } else {
                        divOffer.className = 'offers__item offer-item offer-active';
                        this.subscriptionsZone.appendChild(divOffer);
                        const url = divOffer.querySelector('.offer-url');
                        setTimeout(() => {
                            url.classList.remove('hidden');
                        }, 1500);
                    }
                    DragItem.setupItem(divOffer, '.subscriptions');
                }
                return;
                // если оффер есть и его отключили, то удаляем его
            } else if (offer.status === 0) {
                console.log(item);
                if (item) {
                    item.remove();
                }
                return;
            }
        }
    }
}

// new OfferStatusListener('.active-offers', '.deactive-offers', '.subscriptions', '.unsubscriptions');