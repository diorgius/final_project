/**
 * Класс проверки ранее созданных офферов и вывода модального окна для выбора действия
 */
export default class OfferCheck {

    constructor() {

        this.offerCreateForm = document.querySelector('#offer-create');

        if (!this.offerCreateForm) {
            return;
        }

        this.offerCreateForm.addEventListener(
            'submit',
            this.submit.bind(this)
        );

    }

    async submit(event) {

        event.preventDefault();

        // const formData = new FormData(this.offerCreateForm);
        // const response = await fetch('/advertiser/offers/check', {
        //     method: 'POST',
        //     headers: {
        //         'X-CSRF-TOKEN': document
        //             .querySelector('meta[name="csrf-token"]')
        //             .content,
        //         'Accept': 'application/json',
        //     },
        //     body: formData,
        // });

        // const data = await response.json();

        try {
            const data = await this.checkDeletedOffer(
                new FormData(this.offerCreateForm)
            );
            
            console.log(data);
            
            if (!data.offer) {
                this.offerCreateForm.submit();
                return;
            }

            // this.showRestoreModal(data.offer);
            
        } catch (error) {
            console.error(error);
        }

    }

    async checkDeletedOffer(formData) {

        const response = await fetch('/advertiser/offers/check', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,
                'Accept': 'application/json',
            },
            body: formData,
        });


        // if (!response.ok) {
        //     throw new Error('Ошибка проверки оффера');
        // }   

        return await response.json();
    }

    showRestoreModal(data) {
            
    }

    createNewOffer() {
            
    }

    restoreOffer(id) {

    }

}