/**
 * Класс изменения положения оффера в зависимости от статуса
 */
class OfferStatusListener {

    constructor(activeOffers, deactiveOffers, subscriptions) {
        this.activeZone = document.querySelector(activeOffers);
        this.deactiveZone = document.querySelector(deactiveOffers);
        this.subscriptionsZone = document.querySelector(subscriptions);

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
                    item.classList.remove('deactive-offers__item');
                    item.classList.add('active-offers__item');
                }
                return;
            } else if (offer.status === 0) {
                if (item) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('active-offers__item');
                    item.classList.add('deactive-offers__item');
                }
                return;
            }
        }

        if (role === 'advertiser' && offer.sender_role === 'admin') {
            // если пользователь рекламщик и админ изменил статус оффера, то перемещаем его
            if (offer.status === 1) {
                if (item) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('deactive-offers__item');
                    item.classList.add('active-offers__item');
                }
                return;
            } else if (offer.status === 0) {
                if (item) {
                    this.deactiveZone.appendChild(item);
                    item.classList.remove('active-offers__item');
                    item.classList.add('deactive-offers__item');
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
                    `<p class="font-semibold">Рекламодатель: ${offer.advertiser}</p>
                    <p class="font-semibold">Наименование: ${offer.name}</p>
                    <p class="font-semibold">Тема: ${offer.theme}</p>
                    <p class="font-semibold">Цена: ${offer.price.toFixed(2)} р. за переход</p>
                    <a href="#" class="offer-url hidden__item font-semibold text-xl text-blue-600" title=${offer.url} target="_blank">Реферальная ссылка</a>`
                    if (offer.subscribe === 0) {
                        divOffer.className = 'offers__item deactive-offers__item';
                        this.deactiveZone.appendChild(divOffer);
                    } else {
                        divOffer.className = 'offers__item active-offers__item';
                        this.subscriptionsZone.appendChild(divOffer);
                        const url = divOffer.querySelector('.offer-url');
                        setTimeout(() => {
                            url.classList.remove('hidden__item');
                        }, 1500);
                    }
                    window.offerStatus.setupItem(divOffer);
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

new OfferStatusListener('.active-offers', '.deactive-offers', '.subscriptions');