class OfferStatusListener {

    constructor() {

        this.activeZone = document.querySelector('.active-offers');
        this.deactiveZone = document.querySelector('.deactive-offers');

        this.subscribe();

    }

    subscribe() {

        Echo.channel('offers')
            .subscribed(() => {
                console.log('Subscribed to offers');
            })
            .listen('.offer.status.changed', (event) => {
                console.log('Status changed', event);
            });
            // .listen('.offer.status.changed', (event) => {

            //     console.log('Status changed', event);

            //     // this.updateCard(event);

            // });

    }

    // updateCard(card) {

    //     const element = document.querySelector(
    //         `[data-card-id="${card.id}"]`
    //     );

    //     if (card.status === 'active') {

    //         if (element) {

    //             this.activeZone.appendChild(element);

    //             element.classList.remove(
    //                 'deactive-offers__item'
    //             );

    //             element.classList.add(
    //                 'active-offers__item'
    //             );

    //         }

    //         return;

    //     }

    //     if (card.status === 'inactive') {

    //         if (element) {

    //             this.deactiveZone.appendChild(element);

    //             element.classList.remove(
    //                 'active-offers__item'
    //             );

    //             element.classList.add(
    //                 'deactive-offers__item'
    //             );

    //         }

    //     }

    // }

}

new OfferStatusListener();