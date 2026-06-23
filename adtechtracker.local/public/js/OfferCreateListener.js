/**
 * Класс добавления оффера у админа, если добавлен новый оффер рекламщиком
 */
class OfferCreateListener {

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
            divOffer.className = 'offers__item deactive-offers__item';
            divOffer.innerHTML =
                `<form method="POST" action="/admin/offers/${offer.id}" >
                    <input type="hidden" name="_token" value="${this.csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="flex items-center justify-center mt-4" >
                        <button class="absolute bottom-0 right-0 m-1 text-2xl" title="Удалить">&#10008;</button>
                    </div>
                </form >
                <p class="font-semibold">Рекламодатель: ${offer.advertiser}</p>
                <p class="font-semibold">Наименование: ${offer.name}</p>
                <p class="font-semibold">Тема: ${offer.theme}</p>
                <p class="font-semibold">URL: ${offer.url}</p>
                <p class="font-semibold">Цена: ${offer.price} р. за переход</p>
                <p class="font-semibold">Подписчиков: 0</p>`
            this.deactiveZone.appendChild(divOffer);
            window.offerStatus.setupItem(divOffer);
            return;
        }
    }
}

new OfferCreateListener('.deactive-offers');