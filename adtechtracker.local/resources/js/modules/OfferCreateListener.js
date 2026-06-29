/**
 * Класс добавления оффера у админа, если добавлен новый оффер рекламщиком
 */
import DragItem from './DragItem.js';

export default class OfferCreateListener {

    constructor(deactiveOffers) {
        this.deactiveZone = document.querySelector(deactiveOffers);
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.listener();
    }

    listener() {
        Echo.channel(`offers.admin`)
            .subscribed(() => {
                console.log('Subscribed to chenal: .offer.create');
            })
            .listen('.offer.create', (event) => {
                console.log('Create offer', event);

                this.createOffer(event);
            });
    }

    createOffer(offer) {
        // ищем оффер
        const item = document.getElementById(`${offer.id}`);
        if (!item) {
            const divOffer = document.createElement('div');
            divOffer.setAttribute('id', `${offer.id}`);
            divOffer.className = 'offers__item offer-item offer-deactive';
            divOffer.innerHTML =
                `<form method="POST" action="/admin/offers/${offer.id}" >
                    <input type="hidden" name="_token" value="${this.csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="flex items-center justify-center mt-4" >
                        <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                    </div>
                </form >
                <p class="font-semibold">${__('advertiser')}: <span class="font-light">${offer.advertiser}</span></p>
                <p class="font-semibold">${__('offer_name')}: <span class="font-light">${offer.name}</span></p>
                <p class="font-semibold">${__('offer_theme')}: <span class="font-light">${offer.theme}</span></p>
                <p class="font-semibold">URL: <span class="font-light">${offer.url}</span></p>
                <p class="font-semibold">${__('offer_price')}: <span class="font-light">${offer.price}</span></p>
                <p class="subscribers font-semibold">${__('subscribers')}: <span class="font-light">0</span></p>`
            this.deactiveZone.appendChild(divOffer);
            DragItem.setupItem(divOffer, '.active-offers');
            return;
        }
    }
}

// new OfferCreateListener('.deactive-offers');