/**
 * Класс добавления оффера у админа, если добавлен новый оффер рекламщиком
 */
import DragItem from './DragItem.js';

export default class OfferCreateListener {

    constructor(activeOffers, deactiveOffers) {
        this.activeZone = document.querySelector(activeOffers);
        this.deactiveZone = document.querySelector(deactiveOffers);
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // вызываем слушатель
        this.listener();
    }

    // слушаем событие
    listener() {

        Echo.private(`offers.admin`)
        
            .subscribed(() => {
                console.log('Subscribed to channel: offer.admin');
                console.log('Subscribed to event: .offer.create');
            })
            .listen('.offer.create', (event) => {

                // если случилось, вызываем метод создания оффера
                this.createOffer(event);
            });
    }

    // создаем оффер
    createOffer(offer) {

        // ищем оффер
        const item = document.getElementById(`${offer.id}`);
        
        // если оффер уже есть, но рекламодатель его отредактировал, произошло обновление данных
        if (item) {
            item.remove();
        }

        // создаем оффер
        const divOffer = document.createElement('div');
        divOffer.setAttribute('id', `${offer.id}`);
        divOffer.className = `offers__item offer-item ${offer.status === 1 ? 'offer-active' : 'offer-deactive'}`;
        divOffer.innerHTML =
            `<div class="offer-actions ${offer.status ? 'hidden' : ''}">
                <form method="POST" action="/admin/offers/${offer.id}" >
                    <input type="hidden" name="_token" value="${this.csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                </form >
            </div>
            <p class="font-semibold">${__('advertiser')}: <span class="font-light">${offer.advertiser}</span></p>
            <p class="font-semibold">${__('offer_name')}: <span class="font-light">${offer.name}</span></p>
            <p class="font-semibold">${__('offer_theme')}: <span class="font-light">${offer.theme}</span></p>
            <p class="font-semibold">URL: <span class="font-light">${offer.url}</span></p>
            <p class="font-semibold">${__('offer_price')}: <span class="font-light">${offer.price}</span></p>
            <p class="font-semibold">${__('subscribers')}: <span class="subscribers font-light">0</span></p>`

        this.deactiveZone.appendChild(divOffer);

        DragItem.setupItem(divOffer, '.active-offers');
        return;
    }
}