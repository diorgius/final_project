/**
 * Класс изменения положения оффера в зависимости от статуса
 */
class OfferStatusListener {

    constructor(activeOffers, deactiveOffers) {

        this.activeZone = document.querySelector(activeOffers );
        this.deactiveZone = document.querySelector(deactiveOffers);

        this.listener();

    }

    listener() {

        const role = window.userRole;

        Echo.channel(`offers.${role}`)
            .subscribed(() => {
                console.log(`Subscribed to offers ${role}`);
            })
            .listen('.offer.status.changed', (event) => {
                console.log('Status changed', event);

                this.updateOffer(event, role);
            });

    }

    // а для того чтобы отлавливать новые офферы, надо делать новый эвент при создании карточки и в новом классе js его получать и обрабатывать

    updateOffer(offer, role) {
        const item = document.getElementById(`${offer.id}`);
        if (role === 'admin') {
            console.log(item);
            if (offer.status === 1) {
                if (item) {
                    this.activeZone.appendChild(item);
                    item.classList.remove('deactive-offers__item');
                    item.classList.add('active-offers__item');
                } else {
                    // тут либо рисовать карточку
                    // item = document.createElement('div');
                    // item.id = offer.id;
                    // item.textContent = offer.name;
                    // и тд
                    // либо перегружать страницу
                    location.reload();
                    return;
                    // либо делать метод и запрашивать из БД
                    // const response = await fetch(
                    //     `/offers/${offer.id}/card`
                    //     // и тд
                    // );

                    // const html = await response.text();

                    // вставить html
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
            console.log(item);
            if (offer.status === 1) {
                if (!item) {
                    const divOffers = document.querySelector('.deactive-offers')
                    
                    // надо рисовать карточку
                    divOffers.innerHTML +=
                    `<div id="${offer.id}" class="offers__item deactive-offers__item" draggable="true">
                        <p class="font-semibold">Рекламодатель: "${offer.advertiser}"</p>
                        <p class="font-semibold">Наименование: "${offer.name}"</p>
                        <p class="font-semibold">Тема: "${offer.theme}"</p>
                        <p class="font-semibold">URL: "${offer.url}"</p>
                        <p>Цена: "${offer.price}" р. за переход</p>
                    </div>`;
                }
                return;
            } else if (offer.status === 0) {
                if (item) {
                    this.deactiveZone.removeChild(item);
                }
                return;
            }
        }
    }
}

new OfferStatusListener('.active-offers', '.deactive-offers');